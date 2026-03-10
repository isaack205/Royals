<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Get the customer/order ID from the URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id === 0) {
    die("Invalid customer ID.");
}

// Fetch order details from mycheckout table
$order_query = "SELECT *, 
                customer_name as name, 
                customer_email as email, 
                customer_phone as phone,
                customer_phone2 as phone2,
                shipping_address as location,
                created_at as order_date 
                FROM mycheckout WHERE id = $order_id";
$order_result = $connection->query($order_query);

if (!$order_result || $order_result->num_rows === 0) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// Parse order items from JSON in mycheckout table
$order_items = [];
if (!empty($order['order_items'])) {
    $items = json_decode($order['order_items'], true);
    if (is_array($items)) {
        $order_items = $items;
    }
}

// Include the header
include('adminheader.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details - BrandX</title>
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
            --warning: #ff9f43;
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
        
        .customer-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem;
            margin-top: 70px; /* Account for fixed header */
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
        }
        
        .page-header h1 {
            font-size: 2rem;
            color: var(--accent);
            display: flex;
            align-items: center;
        }
        
        .page-header h1 i {
            margin-right: 0.8rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.7rem 1.2rem;
            background-color: var(--secondary);
            color: var(--text);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
            border: 1px solid var(--border-color);
        }
        
        .back-btn:hover {
            background-color: var(--accent);
            color: var(--primary);
        }
        
        .back-btn i {
            margin-right: 0.5rem;
        }
        
        .customer-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 2.2rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
        }

        .meta-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.2rem;
        }

        .meta-chip {
            background: rgba(0, 210, 255, 0.08);
            border: 1px solid rgba(0, 210, 255, 0.2);
            border-radius: 10px;
            padding: 0.8rem 0.9rem;
        }

        .meta-chip .meta-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            display: block;
        }

        .meta-chip .meta-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-title {
            font-size: 1.4rem;
            color: var(--accent);
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 0.8rem;
        }
        
        .order-id {
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .order-items {
            margin-top: 2rem;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .items-table th {
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .product-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
        }
        
        .product-name {
            font-weight: 500;
            margin-bottom: 0.3rem;
        }
        
        .product-id {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .total-row {
            font-weight: 600;
            background-color: rgba(0, 210, 255, 0.05);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: flex-end;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--accent);
            color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #00b8e6;
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            color: var(--text);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .btn-danger {
            background-color: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .btn-danger:hover {
            background-color: var(--danger);
            color: white;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
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
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            display: block;
        }
        
        @media (max-width: 768px) {
            .customer-container {
                padding: 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .page-header h1 {
                font-size: 1.6rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }

            .meta-strip {
                grid-template-columns: 1fr;
            }
            
            .items-table {
                display: block;
                overflow-x: auto;
            }
            
            .product-cell {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .product-image {
                width: 50px;
                height: 50px;
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
        
        .customer-card {
            animation: fadeIn 0.6s ease-out;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            body {
                background: white;
                color: black;
                margin: 0;
            }

            .customer-container {
                position: static;
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 0;
                overflow: visible !important;
            }

            /* Print only the document content */
            body * {
                visibility: hidden !important;
            }

            .customer-container,
            .customer-container * {
                visibility: visible !important;
            }

            /* Hide non-printable elements */
            .back-btn,
            .action-buttons,
            .no-print {
                display: none !important;
            }

            /* Hide screen page header/footer-like UI in print */
            .page-header {
                display: none !important;
            }

            /* Print header - Company Info */
            .print-only {
                display: block !important;
            }

            .invoice-header {
                text-align: center;
                margin-bottom: 0.6rem;
                padding-bottom: 0.45rem;
                border-bottom: 2px solid #000;
            }

            .invoice-header h1 {
                font-size: 1.8rem;
                margin-bottom: 0.35rem;
                color: #000;
            }

            .invoice-header p {
                margin: 0.2rem 0;
                color: #333;
            }

            .page-header {
                border-bottom: 2px solid #000;
            }

            .page-header h1 {
                color: #000;
            }

            .customer-card {
                box-shadow: none;
                border: 1px solid #000;
                page-break-inside: auto;
                break-inside: auto;
                border-radius: 0;
                padding: 1rem;
                margin-bottom: 0;
            }

            .meta-strip,
            .info-grid,
            .order-items {
                page-break-inside: auto;
                break-inside: auto;
            }

            .card-title {
                color: #000;
            }

            .info-label {
                color: #555;
            }

            .info-value {
                color: #000;
            }

            .items-table th {
                background: #f0f0f0 !important;
                color: #000 !important;
                border: 1px solid #000;
                font-size: 11px;
                padding: 6px;
            }

            .items-table td {
                border: 1px solid #000;
                color: #000;
                font-size: 10px;
                padding: 6px;
                vertical-align: top;
                overflow-wrap: anywhere;
            }

            .items-table {
                width: 100%;
                table-layout: fixed;
                display: table !important;
                overflow: visible !important;
            }

            .items-table thead {
                display: table-header-group;
            }

            .items-table tfoot {
                display: table-footer-group;
            }

            .status-badge {
                border: 1px solid #000;
                color: #000 !important;
                background: transparent !important;
            }

            .order-id {
                border: 1px solid #000;
                color: #000 !important;
                background: transparent !important;
            }

            .meta-chip {
                background: #f7f7f7 !important;
                border: 1px solid #ccc;
            }

            .meta-chip .meta-value {
                color: #000;
            }

            tr {
                page-break-inside: auto;
                break-inside: auto;
            }
        }

        .print-only {
            display: none;
        }

        .product-variant {
            display: inline-block;
            margin-top: 0.3rem;
            padding: 0.2rem 0.6rem;
            background: rgba(0, 210, 255, 0.1);
            border-radius: 4px;
            font-size: 0.85rem;
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="customer-container">
        <!-- Print-only invoice header -->
        <div class="invoice-header print-only">
            <h1>ROYALS</h1>
            <p><strong>INVOICE / ORDER DETAILS</strong></p>
            <p>Phone: +254 777 992 666 | Email: info@royals.com</p>
            <p>Generated: <?php echo date('F j, Y, g:i a'); ?></p>
        </div>

        <div class="page-header">
            <h1><i class="fas fa-user-circle"></i> Order Details</h1>
            <a href="orders_made.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Orders</a>
        </div>
        
        <div class="customer-card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-receipt"></i> Order Information</h2>
                <span class="order-id">Order #<?php echo $order['id']; ?></span>
            </div>

            <div class="meta-strip">
                <div class="meta-chip">
                    <span class="meta-label">Payment Method</span>
                    <span class="meta-value"><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></span>
                </div>
                <div class="meta-chip">
                    <span class="meta-label">Order Status</span>
                    <span class="meta-value"><?php echo htmlspecialchars(ucfirst($order['status'] ?? 'pending')); ?></span>
                </div>
                <div class="meta-chip">
                    <span class="meta-label">Order Total</span>
                    <span class="meta-value">KSH <?php echo number_format(floatval($order['order_total'] ?? 0), 2); ?></span>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Customer Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['name']); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value">
                        <?php 
                        echo htmlspecialchars($order['phone']); 
                        if (!empty($order['phone2'])) {
                            echo '<br><small style="color: #8b92a7; font-size: 0.9em;">Alt: ' . htmlspecialchars($order['phone2']) . '</small>';
                        }
                        ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Delivery Location</span>
                    <span class="info-value"><?php echo isset($order['location']) ? htmlspecialchars($order['location']) : 'N/A'; ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Order Date</span>
                    <span class="info-value"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Order Status</span>
                    <?php
                    $status = strtolower($order['status'] ?? 'pending');
                    $statusClass = 'status-pending';
                    if ($status === 'paid' || $status === 'completed') {
                        $statusClass = 'status-completed';
                    } elseif ($status === 'cancelled') {
                        $statusClass = 'status-cancelled';
                    }
                    ?>
                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                </div>
            </div>
            
            <div class="order-items">
                <h3 class="card-title"><i class="fas fa-box-open"></i> Order Items</h3>
                
                <?php if (!empty($order_items)): ?>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal = 0;
                            foreach ($order_items as $item): 
                                $itemPrice = floatval($item['price'] ?? 0);
                                $quantity = intval($item['quantity'] ?? 1);
                                $item_total = $itemPrice * $quantity;
                                $subtotal += $item_total;
                                $imagePath = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : 'https://via.placeholder.com/60x60?text=No+Image';
                                
                                $size = $item['size'] ?? '';
                                $color = $item['color'] ?? '';
                            ?>
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?>" class="product-image" onerror="this.src='https://via.placeholder.com/60x60?text=Image+Error'">
                                            <div class="product-info">
                                                <span class="product-name"><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($size) || !empty($color)): ?>
                                            <?php if (!empty($size)): ?>
                                                <span class="product-variant">Size: <?php echo htmlspecialchars($size); ?></span><br>
                                            <?php endif; ?>
                                            <?php if (!empty($color)): ?>
                                                <span class="product-variant">Color: <?php echo htmlspecialchars($color); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #888;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $quantity; ?></td>
                                    <td>KSH <?php echo number_format($itemPrice, 2); ?></td>
                                    <td>KSH <?php echo number_format($item_total, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;"><strong>Order Total:</strong></td>
                                <td><strong>KSH <?php echo number_format($order['order_total'] ?? $subtotal, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>No items found for this order.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <p class="no-print" style="margin-right: auto; color: #8b92a7; font-size: 0.86rem; align-self: center;">
                    Tip: In print settings, disable browser "Headers and footers" for a clean invoice.
                </p>
                <button onclick="window.print()" class="btn btn-primary no-print">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="orders_made.php" class="btn btn-secondary no-print">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
                <a href="orders_made.php?remove=<?php echo $order['id']; ?>" class="btn btn-danger no-print" onclick="return confirm('Are you sure you want to delete this order?');">
                    <i class="fas fa-trash"></i> Delete Order
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>