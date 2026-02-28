<?php
// orders.php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=orders.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$orders_query = "
    SELECT o.*, 
           COUNT(od.order_detail_id) as item_count,
           SUM(od.price * od.quantity) as total_amount
    FROM mycheckout o 
    LEFT JOIN order_details od ON o.id = od.order_id 
    WHERE o.client_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
";
$stmt = $connection->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

// Include header
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Royals</title>
    <style>
        :root {
            --primary: #00d2ff;
            --secondary: #3a7bd5;
            --success: #2ed573;
            --warning: #ffa502;
            --danger: #ff4757;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }

        .orders-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .page-title i {
            color: var(--primary);
        }

        .orders-filter {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1.5rem;
            border: 2px solid var(--primary);
            background: transparent;
            color: var(--primary);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--primary);
            color: white;
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .order-header {
            padding: 1.5rem;
            background: var(--light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .order-number {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
        }

        .order-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-pending {
            background: rgba(255, 165, 2, 0.1);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .status-processing {
            background: rgba(0, 210, 255, 0.1);
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .status-shipped {
            background: rgba(46, 213, 115, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .status-delivered {
            background: rgba(46, 213, 115, 0.2);
            color: var(--success);
            border: 2px solid var(--success);
        }

        .status-cancelled {
            background: rgba(255, 71, 87, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .order-details {
            padding: 1.5rem;
        }

        .order-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .summary-label {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 600;
        }

        .summary-value {
            font-size: 1rem;
            color: var(--dark);
            font-weight: 600;
        }

        .order-items {
            margin-top: 1.5rem;
        }

        .items-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .items-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .item-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--light);
            border-radius: 8px;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.3rem;
        }

        .item-details {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .item-price {
            font-weight: 700;
            color: var(--primary);
        }

        .order-actions {
            padding: 1.5rem;
            border-top: 1px solid var(--light-gray);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .empty-state p {
            margin-bottom: 2rem;
        }

        .tracking-timeline {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--light);
            border-radius: 8px;
        }

        .timeline-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 0 2rem;
        }

        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--light-gray);
            z-index: 1;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step-active .step-icon {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .step-completed .step-icon {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .step-label {
            font-size: 0.8rem;
            color: var(--gray);
            text-align: center;
        }

        .step-active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        .step-completed .step-label {
            color: var(--success);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-summary {
                grid-template-columns: 1fr;
            }

            .order-actions {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }

            .timeline-steps {
                flex-direction: column;
                gap: 2rem;
                margin: 0;
            }

            .timeline-steps::before {
                width: 3px;
                height: 100%;
                left: 20px;
                top: 0;
            }

            .timeline-step {
                flex-direction: row;
                gap: 1rem;
                align-items: flex-start;
            }

            .step-icon {
                margin-bottom: 0;
            }
        }

        /* Print Styles */
        @media print {
            /* hide interactive controls */
            .order-actions, .filter-btn, .page-header button {
                display: none !important;
            }

            /* make cards plain and add breaks between them so each prints on its own sheet */
            .order-card {
                box-shadow: none;
                border: 1px solid var(--light-gray);
                page-break-after: always;
                break-after: page;
                padding-bottom: 1rem;
            }

            /* avoid printing empty last break */
            .order-card:last-child {
                page-break-after: auto;
                break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-shopping-bag"></i>
                My Orders
            </h1>
            <button onclick="window.print()" class="action-btn btn-secondary">
                <i class="fas fa-print"></i> Print Orders
            </button>
        </div>

        <div class="orders-filter">
            <button class="filter-btn active" data-filter="all">All Orders</button>
            <button class="filter-btn" data-filter="pending">Pending</button>
            <button class="filter-btn" data-filter="processing">Processing</button>
            <button class="filter-btn" data-filter="shipped">Shipped</button>
            <button class="filter-btn" data-filter="delivered">Delivered</button>
            <button class="filter-btn" data-filter="cancelled">Cancelled</button>
        </div>

        <div class="orders-list">
            <?php if ($orders_result->num_rows > 0): ?>
                <?php while ($order = $orders_result->fetch_assoc()): ?>
                    <?php
                    // Get order items
                    $order_id = $order['id'];
                    $items_query = "SELECT * FROM order_details WHERE order_id = ?";
                    $stmt_items = $connection->prepare($items_query);
                    $stmt_items->bind_param("i", $order_id);
                    $stmt_items->execute();
                    $items_result = $stmt_items->get_result();
                    
                    // Determine status class
                    $status_class = 'status-' . $order['status'];
                    ?>
                    
                    <div class="order-card" data-status="<?php echo $order['status']; ?>">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-number">Order #<?php echo $order['id']; ?></div>
                                <div class="order-date">
                                    Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                </div>
                            </div>
                            <div class="order-status <?php echo $status_class; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="order-summary">
                                <div class="summary-item">
                                    <span class="summary-label">Total Amount</span>
                                    <span class="summary-value">Ksh <?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Payment Method</span>
                                    <span class="summary-value"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Items</span>
                                    <span class="summary-value"><?php echo $order['item_count']; ?> item(s)</span>
                                </div>
                            </div>

                            <div class="order-items">
                                <h4 class="items-title">Order Items</h4>
                                <div class="items-list">
                                    <?php while ($item = $items_result->fetch_assoc()): ?>
                                        <div class="item-card">
                                            <div class="item-image">
                                                <img src="uploads/<?php echo htmlspecialchars($item['image'] ?? 'default-product.jpg'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                            </div>
                                            <div class="item-info">
                                                <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                <div class="item-details">
                                                    Quantity: <?php echo $item['quantity']; ?> • 
                                                    Ksh <?php echo number_format($item['price'], 2); ?> each
                                                </div>
                                            </div>
                                            <div class="item-price">
                                                Ksh <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>

                            <!-- Order Tracking Timeline -->
                            <div class="tracking-timeline">
                                <h4 class="timeline-title">Order Tracking</h4>
                                <div class="timeline-steps">
                                    <?php
                                    $steps = [
                                        'pending' => ['icon' => 'fa-receipt', 'label' => 'Order Placed'],
                                        'processing' => ['icon' => 'fa-cog', 'label' => 'Processing'],
                                        'shipped' => ['icon' => 'fa-shipping-fast', 'label' => 'Shipped'],
                                        'delivered' => ['icon' => 'fa-check-circle', 'label' => 'Delivered']
                                    ];
                                    
                                    $current_status = $order['status'];
                                    $status_index = array_search($current_status, array_keys($steps));
                                    
                                    foreach ($steps as $status => $step):
                                        $step_class = '';
                                        if ($status_index >= array_search($status, array_keys($steps))) {
                                            $step_class = 'step-completed';
                                        }
                                        if ($status === $current_status) {
                                            $step_class = 'step-active';
                                        }
                                    ?>
                                        <div class="timeline-step <?php echo $step_class; ?>">
                                            <div class="step-icon">
                                                <i class="fas <?php echo $step['icon']; ?>"></i>
                                            </div>
                                            <span class="step-label"><?php echo $step['label']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="order-actions">
                            <button class="action-btn btn-primary" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="action-btn btn-secondary" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-times"></i> Cancel Order
                                </button>
                            <?php endif; ?>
                            <?php if ($order['status'] === 'delivered'): ?>
                                <button class="action-btn btn-secondary" onclick="reorder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-redo"></i> Reorder
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders with us yet. Start shopping to see your orders here!</p>
                    <a href="products.php" class="action-btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Filter orders by status
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active button
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filter orders
                document.querySelectorAll('.order-card').forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-status') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // View order details
        function viewOrderDetails(orderId) {
            // You can implement a modal or redirect to order details page
            window.location.href = `order_details.php?id=${orderId}`;
        }

        // Cancel order
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                fetch('ajax/cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order cancelled successfully');
                        location.reload();
                    } else {
                        alert('Error cancelling order: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error cancelling order');
                });
            }
        }

        // Reorder functionality
        function reorder(orderId) {
            fetch('ajax/reorder.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Items added to cart successfully');
                    window.location.href = 'cart.php';
                } else {
                    alert('Error adding items to cart: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding items to cart');
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // You can add any initialization code here
            console.log('Orders page loaded');
        });
    </script>

    <?php include('footer.php'); ?>
</body>
</html>