<?php
session_start();
include('db.php');  // Database connection

// Check if a removal request was made
if (isset($_GET['remove'])) {
    $orderIdToRemove = intval($_GET['remove']);  // Get the order ID to remove

    // Delete the order from the mycheckout table
    $deleteOrderQuery = "DELETE FROM mycheckout WHERE id = $orderIdToRemove";
    if ($connection->query($deleteOrderQuery)) {
        // Successfully deleted the order, redirect to avoid resubmission on refresh
        header("Location: orders_made.php");
        exit;
    } else {
        echo "Error deleting order: " . $connection->error;
    }
}

// Query to fetch all orders from mycheckout table
$query = "SELECT id AS order_id, customer_name, customer_email AS email, customer_phone AS phone, 
                 shipping_address, order_total AS total_price, created_at AS order_date, 
                 order_items, payment_method, status
          FROM mycheckout
          ORDER BY created_at DESC";
$result = $connection->query($query);

// Check if the query was successful
if (!$result) {
    die("Error in SQL query: " . $connection->error);
}

// Fetch all orders into an array
$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    $orders = [];
}

// Include the header
include('adminheader.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Made - BrandX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #00d2ff;
            --text: #ffffff;
            --text-secondary: #888888;
            --danger: #ff4757;
            --success: #2ed573;
            --card-bg: #1e1e1e;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--primary);
            color: var(--text);
            line-height: 1.6;
        }

        .orders-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            margin-top: 70px;
            /* Account for fixed header */
        }

        header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--secondary) 0%, #252525 100%);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        header h1 {
            font-size: 2.2rem;
            color: var(--accent);
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            margin: 0.2rem;
        }

        .view-btn {
            background-color: rgba(46, 213, 115, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .view-btn:hover {
            background-color: var(--success);
            color: var(--primary);
        }

        .remove-btn {
            background-color: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .remove-btn:hover {
            background-color: var(--danger);
            color: white;
        }

        .customer-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .customer-link:hover {
            color: var(--success);
            text-decoration: underline;
        }

        .no-orders {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
            font-style: italic;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: rgba(255, 159, 67, 0.2);
            color: #ff9f43;
        }

        .status-completed {
            background-color: rgba(46, 213, 115, 0.2);
            color: #2ed573;
        }

        .status-cancelled {
            background-color: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }

        /* Responsive table */
        @media (max-width: 1200px) {
            .orders-container {
                padding: 1rem;
                overflow-x: auto;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            th,
            td {
                padding: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8rem;
            }

            th,
            td {
                padding: 0.6rem;
                font-size: 0.9rem;
            }

            .action-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table {
            animation: fadeIn 0.6s ease-out;
        }

        /* Table row animations */
        tr {
            transition: transform 0.3s, background-color 0.3s;
        }

        tr:hover {
            transform: translateX(5px);
        }
    </style>
</head>

<body>

    <div class="orders-container">
        <header>
            <h1><i class="fas fa-shopping-bag"></i> Orders Management</h1>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Delivery Location</th>
                    <th>Total Price (KSH)</th>
                    <th>Order Date</th>
                    <th>Products</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    // Parse order_items JSON to get product names
                    $products = [];
                    if (!empty($order['order_items'])) {
                        $items = json_decode($order['order_items'], true);
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $productName = $item['product_name'] ?? 'Unknown Product';
                                $quantity = $item['quantity'] ?? 1;
                                $products[] = $productName . ' (x' . $quantity . ')';
                            }
                        }
                    }
                    $productList = !empty($products) ? implode(', ', $products) : 'N/A';
                    
                    // Extract shipping address (remove the "\n\nShipping:" part if present)
                    $address = explode("\n\n", $order['shipping_address'])[0];
                    ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td>
                            <a href="customer_details.php?id=<?php echo $order['order_id']; ?>" class="customer-link">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['customer_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['phone']); ?></td>
                        <td><?php echo htmlspecialchars($address); ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                        <td><?php echo htmlspecialchars($productList); ?></td>
                        <td><?php 
                            $paymentMethod = $order['payment_method'];
                            if (strpos($paymentMethod, 'Paystack') !== false) {
                                echo '<span style="color: #00d2ff;">💳 Paystack</span>';
                            } else {
                                echo '<span style="color: #2ed573;">💵 Cash on Delivery</span>';
                            }
                        ?></td>
                        <td>
                            <?php 
                            $status = strtolower($order['status']);
                            if ($status === 'paid') {
                                echo '<span class="status-badge status-completed">Paid</span>';
                            } elseif ($status === 'pending') {
                                echo '<span class="status-badge status-pending">Pending</span>';
                            } else {
                                echo '<span class="status-badge">' . htmlspecialchars(ucfirst($status)) . '</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="customer_details.php?id=<?php echo $order['order_id']; ?>" class="action-btn view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="?remove=<?php echo $order['order_id']; ?>" class="action-btn remove-btn" onclick="return confirm('Are you sure you want to remove this order?');">
                                <i class="fas fa-trash"></i> Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="no-orders">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                        No orders have been placed yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        </table>
    </div>

</body></html>