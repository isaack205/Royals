<?php
session_start();
include('header.php');

if (!isset($_SESSION['order_success'])) {
    header('Location: home.php');
    exit();
}

$order_id = $_SESSION['order_success']['order_id'];
$customer_name = $_SESSION['order_success']['customer_name'];
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - BrandX</title>
    <style>
    .order-success {
        max-width: 800px;
        margin: 3rem auto;
        padding: 3rem;
        text-align: center;
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: var(--cart-shadow);
        animation: fadeIn 0.5s ease-out;
    }
    
    .order-success i {
        font-size: 5rem;
        color: var(--accent);
        margin-bottom: 1.5rem;
    }
    
    .order-success h1 {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, var(--accent), #3a7bd5);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
    }
    
    .order-success p {
        color: var(--text);
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    
    .order-details {
        margin: 2rem 0;
        padding: 1.5rem;
        background: rgba(0, 210, 255, 0.05);
        border-radius: 12px;
        border-left: 4px solid var(--accent);
        text-align: left;
    }
    
    .order-number {
        font-weight: 600;
        color: var(--accent);
    }
    
    .btn-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .btn {
        display: inline-block;
        padding: 0.8rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all var(--transition-speed) ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, var(--accent), #3a7bd5);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,210,255,0.3);
    }
    
    .btn-secondary {
        background: var(--card-bg);
        color: var(--text);
        border: 1px solid var(--border-color);
    }
    
    .btn-secondary:hover {
        background: var(--border-color);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .order-success {
            padding: 2rem 1rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="luxury-cart">
        <div class="order-success">
            <i class="fas fa-check-circle"></i>
            <h1>Thank You, <?= htmlspecialchars($customer_name) ?>!</h1>
            <p>Your order has been successfully placed and is being processed.</p>
            
            <div class="order-details">
                <h3 style="font-size: 1.3rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.8rem;">
                    <i class="fas fa-receipt"></i> Order Details
                </h3>
                <p><strong>Order Number:</strong> <span class="order-number">#<?= $order_id ?></span></p>
                <p style="margin-top: 0.5rem;">We've sent a confirmation to your email with all the details.</p>
                <p style="margin-top: 0.5rem;">For any questions, please contact our customer support.</p>
            </div>
            
            <div class="btn-group">
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> View Your Orders
                </a>
                <a href="home.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>
</body>
</html>