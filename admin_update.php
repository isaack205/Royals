<?php
session_start();
include('db.php');  // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die('You must be logged in as an admin to access this page.');
}

// Get the order ID from the URL
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($orderId == 0) {
    die('Invalid order ID.');
}

// Query to get the order details
$query = "SELECT * FROM orders_made WHERE id = $orderId";
$orderResult = $connection->query($query);

// Check if the query was successful
if (!$orderResult) {
    die("Error in SQL query: " . $connection->error);
}

$order = $orderResult->fetch_assoc();
if (!$order) {
    die("Order not found.");
}

// Update order status when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    if (!empty($status)) {
        // Update the order status in the database
        $updateQuery = "UPDATE orders_made SET status = '$status' WHERE id = $orderId";
        if ($connection->query($updateQuery)) {
            // Send an email notification to the customer about the update
            $to = $order['email'];
            $subject = "Order Status Update";
            $message = "Dear " . $order['name'] . ",\n\nYour order (ID: $orderId) status has been updated to: $status.\n\nThank you.";
            mail($to, $subject, $message);  // Send email
            
            // Redirect back to the orders page with a success message
            header("Location: orders_made.php?message=Order status updated successfully.");
            exit;
        } else {
            echo "Error updating order status: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <link rel="stylesheet" href="sty.css">
    <script src="script.js"></script>
   
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: Arial, sans-serif;
        }

        .update-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #222;
            border-radius: 8px;
        }

        .update-container form {
            display: flex;
            flex-direction: column;
        }

        .update-container input, .update-container select, .update-container textarea {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .update-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
<header>
    <h1>Update Order Status</h1>
</header>

<div class="update-container">
    <h2>Order ID: <?php echo $order['id']; ?></h2>
    <form method="POST">
        <label for="status">Order Status:</label>
        <select name="status" id="status" required>
            <option value="">Select Status</option>
            <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
            <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
        </select>
        <button type="submit">Update Status</button>
    </form>
</div>

</body>
</html>
