<?php
session_start();
include('db.php');

// Check if the order was successfully placed
if (!isset($_SESSION['order_id'])) {
    header("Location: index.php");  // Redirect to homepage if order ID is not set
    exit;
}

// Get the order details
$orderId = $_SESSION['order_id'];
$sql = "SELECT * FROM orders_made WHERE id = $orderId";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    // Calculate expected delivery date (5 days from now)
    $expectedDeliveryDate = date('Y-m-d', strtotime('+5 days'));
} else {
    // If no order found, redirect to the homepage
    header("Location: index.php");
    exit;
}

unset($_SESSION['order_id']); // Clear the session variable after showing confirmation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        /* Styles matching your previous design */
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .confirmation-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: ;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #4ea8de;
            font-size: 2em;
            margin-bottom: 20px;
        }

        .thank-you {
            font-size: 1.3em;
            color: #c9d1d9;
        }

        .details {
            margin: 20px 0;
            font-size: 1.2em;
        }

        .details p {
            margin: 8px 0;
        }

        .details strong {
            color: #4ea8de;
        }

        .redirect-message {
            margin-top: 30px;
            color: #555;
            font-size: 1.1em;
        }

        .message {
            margin-top: 20px;
            color: #ffcc00;
            font-size: 1.1em;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <h1>Thank You for Your Order!</h1>
    <p class="thank-you">Hello, <?php echo htmlspecialchars($order['name']); ?>!</p>
    <p>Thank you for shopping with us. Your order is being processed and will be ready for delivery by <strong><?php echo $expectedDeliveryDate; ?></strong>.</p>
    
    <div class="details">
        <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
        <p><strong>Total Price:</strong> KSh <?php echo number_format($order['total_price'], 2); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['location']); ?></p>
    </div>

    <p class="redirect-message">You will be redirected to your orders page shortly...</p>
</div>

<script>
    // Redirect to orders.php after 5 seconds
    setTimeout(function() {
        window.location.href = 'orders.php';
    }, 5000);
</script>

</body>
</html>
