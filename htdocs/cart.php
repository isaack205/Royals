<?php
session_start();
include('header.php');
?>

<!DOCTYPE html>
<html lang="en" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Luxury Cart - BrandX</title>
    <style>
         :root {
            --cart-accent: #00d2ff;
            --cart-danger: #ff4757;
            --cart-success: #2ed573;
            --cart-warning: #ffa502;
            --cart-light: #f8f9fa;
            --cart-dark: #212529;
            --cart-shadow: 0 15px 30px rgba(0,0,0,0.1);
            --transition-speed: 0.3s;
        }

        /* Base Styles */
        body {
            background-color: var(--background);
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
        }

        /* Luxury Cart Container */
        .luxury-cart {
            max-width: 1400px;
            margin: 3rem auto;
            padding: 0 2rem;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cart Header */
        .cart-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .cart-header h1 {
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

        .cart-header::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), transparent);
            margin: 1rem auto;
        }

        /* Cart Grid Layout */
        .cart-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2.5rem;
        }

        @media (max-width: 1024px) {
            .cart-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cart Items List */
        .cart-items {
            display: grid;
            gap: 1.8rem;
        }

        /* Cart Item Card */
        .cart-item {
            display: grid;
            grid-template-columns: 150px 1fr auto;
            gap: 2rem;
            align-items: center;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--cart-shadow);
            transition: all var(--transition-speed) ease;
            position: relative;
            height: 200px;
            overflow: hidden;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .cart-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--accent), #3a7bd5);
        }

        .cart-item-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform var(--transition-speed) ease;
        }

        .cart-item-image:hover {
            transform: scale(1.03);
        }

        .cart-item-details {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .cart-item-name {
            font-weight: 600;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }

        .cart-item-price {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .item-total {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1.5rem;
        }

        /* Quantity Controls */
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            background: var(--primary-light);
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background-color: var(--accent);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(0,210,255,0.5);
        }

        .quantity-value {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        /* Remove Button */
        .remove-item {
            background: var(--cart-danger);
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all var(--transition-speed) ease;
        }

        .remove-item:hover {
            background: #ff6b81;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,71,87,0.3);
        }

        /* Cart Summary */
        .cart-summary {
            padding: 2.5rem;
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--cart-shadow);
            position: sticky;
            top: 2rem;
            height: fit-content;
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .summary-title i {
            color: var(--accent);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed var(--border-color);
        }

        .summary-total {
            font-weight: 700;
            font-size: 1.3rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid var(--accent);
        }

        /* Checkout Button */
        .checkout-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, var(--accent), #3a7bd5);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 2rem;
            transition: all var(--transition-speed) ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            text-decoration: none;
        }

        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,210,255,0.3);
        }

        /* Empty Cart State */
        .empty-cart {
            text-align: center;
            padding: 5rem 0;
            grid-column: 1 / -1;
            animation: fadeIn 0.8s ease-out;
        }

        .empty-cart i {
            font-size: 5rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .empty-cart h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--text);
        }

        .empty-cart p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .continue-shopping {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: var(--accent);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }

        .continue-shopping:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,210,255,0.3);
        }

        /* Custom Notification System */
        .custom-notification {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: var(--card-bg);
            color: var(--text);
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 10000;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 90%;
            pointer-events: none;
        }

        .custom-notification.active {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
            pointer-events: auto;
        }

        .custom-notification.success {
            border-left: 4px solid var(--cart-success);
        }

        .custom-notification.error {
            border-left: 4px solid var(--cart-danger);
        }

        .custom-notification.warning {
            border-left: 4px solid var(--cart-warning);
        }

        .custom-notification i {
            font-size: 1.5rem;
        }

        .custom-notification.success i {
            color: var(--cart-success);
        }

        .custom-notification.error i {
            color: var(--cart-danger);
        }

        .custom-notification.warning i {
            color: var(--cart-warning);
        }

        .custom-notification .notification-content {
            display: flex;
            flex-direction: column;
        }

        .custom-notification .notification-message {
            font-weight: 500;
        }

        .custom-notification .notification-action {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 3px;
        }

       /* Responsive Adjustments - Mobile Layout */
@media (max-width: 768px) {
    .luxury-cart {
        padding: 0 1rem;
    }
    
    .cart-item {
        grid-template-columns: 100px 1fr;
        grid-template-rows: auto auto;
        grid-template-areas: 
            "image details"
            "image actions";
        height: auto;
        padding: 1.2rem;
        gap: 0.8rem;
        align-items: start;
    }
    
    .cart-item-image {
        grid-area: image;
        width: 100px;
        height: 100px;
        margin: 0;
        align-self: center;
    }
    
    .cart-item-details {
        grid-area: details;
        padding-top: 0;
        gap: 0.5rem;
    }
    
    .cart-item-name {
        font-size: 1.1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .cart-item-price {
        font-size: 1rem;
        margin: 0.2rem 0;
    }
    
    .cart-item-actions {
        grid-area: actions;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.8rem;
        padding-top: 0;
    }
    
    .quantity-control {
        margin: 0;
        padding: 0.3rem 0.8rem;
    }
    
    .remove-item {
        margin-left: 0;
        align-self: flex-start;
    }
    
    /* Keep original notification styling untouched */
    .custom-notification {
        width: 90%;
        text-align: center;
    }
    
    .cart-summary {
        position: static;
    }
}
    </style>
</head>
<body>
    <!-- Custom Notification Element -->
    <div class="custom-notification" id="customNotification">
        <i class="fas fa-check-circle"></i>
        <div class="notification-content">
            <span class="notification-message"></span>
            <span class="notification-action"></span>
        </div>
    </div>

    <div class="luxury-cart">
        <div class="cart-header">
            <h1>Your Luxury Selection</h1>
            <p>Review and finalize your premium items</p>
        </div>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-bag"></i>
                <h3>Your Luxury Cart Awaits</h3>
                <p>Discover our premium collection and fill your cart with exceptional items</p>
                <a href="home.php" class="continue-shopping">Explore Collection</a>
            </div>
        <?php else: ?>
            <div class="cart-grid">
                <div class="cart-items" id="cartItemsContainer">
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $index => $item): 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                        <div class="cart-item" data-product-id="<?= $item['product_id'] ?>" data-index="<?= $index ?>">
                            <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="cart-item-image">
                            
                            <div class="cart-item-details">
                                <div class="cart-item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div class="cart-item-price">Ksh <?= number_format($item['price'], 2) ?></div>
                                <div class="item-total">Ksh <?= number_format($itemTotal, 2) ?></div>
                            </div>
                            
                            <div class="cart-item-actions">
                                <div class="quantity-control">
                                    <button class="quantity-btn quantity-decrease">-</button>
                                    <span class="quantity-value"><?= $item['quantity'] ?></span>
                                    <button class="quantity-btn quantity-increase">+</button>
                                </div>
                                <button class="remove-item">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3 class="summary-title"><i class="fas fa-receipt"></i> Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<span id="itemCount"><?= count($_SESSION['cart']) ?></span> items)</span>
                        <span>Ksh <span id="subtotalAmount"><?= number_format($total, 2) ?></span></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>Ksh <span id="totalAmount"><?= number_format($total, 2) ?></span></span>
                    </div>
                    
                    <a href="checkout.php" class="checkout-btn">
                        <i class="fas fa-lock"></i> Secure Checkout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Enhanced Cart Functionality with AJAX Updates
    document.addEventListener('DOMContentLoaded', function() {
        const customNotification = document.getElementById('customNotification');
        const notificationMessage = customNotification.querySelector('.notification-message');
        const notificationAction = customNotification.querySelector('.notification-action');
        
        // Show custom notification
        function showCustomNotification(message, actionText = '', type = 'success') {
            customNotification.className = `custom-notification ${type}`;
            customNotification.querySelector('i').className = type === 'success' 
                ? 'fas fa-check-circle' 
                : 'fas fa-exclamation-circle';
                
            notificationMessage.textContent = message;
            notificationAction.textContent = actionText;
            
            customNotification.classList.add('active');
            
            setTimeout(() => {
                customNotification.classList.remove('active');
            }, 3000);
        }
        
        // Update quantity function
        function updateQuantity(productId, change, index) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('change', change);
            formData.append('index', index);
            
            fetch('ajax/update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the specific item's quantity display
                    const itemElement = document.querySelector(`.cart-item[data-index="${index}"]`);
                    if (itemElement) {
                        const quantityElement = itemElement.querySelector('.quantity-value');
                        const priceElement = itemElement.querySelector('.item-total');
                        const newQuantity = parseInt(quantityElement.textContent) + change;
                        
                        quantityElement.textContent = newQuantity;
                        priceElement.textContent = 'Ksh ' + (data.itemTotal || (newQuantity * parseFloat(priceElement.textContent.replace(/[^0-9.]/g, '')) / (newQuantity - change))).toFixed(2);
                    }
                    
                    // Update summary
                    document.getElementById('subtotalAmount').textContent = data.cartTotal.toFixed(2);
                    document.getElementById('totalAmount').textContent = data.cartTotal.toFixed(2);
                    document.getElementById('itemCount').textContent = data.cartCount;
                    
                    showCustomNotification(
                        `Quantity updated successfully`,
                        `Your cart has been updated`,
                        'success'
                    );
                } else {
                    showCustomNotification(
                        data.message || 'Failed to update quantity',
                        'Please try again',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomNotification(
                    'Error updating quantity',
                    'Please try again',
                    'error'
                );
            });
        }
        
        // Remove item function
        function removeCartItem(productId, index) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('index', index);
            
            fetch('ajax/remove_from_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove item from DOM with animation
                    const itemElement = document.querySelector(`.cart-item[data-index="${index}"]`);
                    if (itemElement) {
                        itemElement.style.transition = 'all 0.3s ease';
                        itemElement.style.opacity = '0';
                        itemElement.style.height = '0';
                        itemElement.style.padding = '0';
                        itemElement.style.margin = '0';
                        itemElement.style.border = '0';
                        itemElement.style.overflow = 'hidden';
                        
                        setTimeout(() => {
                            itemElement.remove();
                            
                            // Update summary
                            document.getElementById('subtotalAmount').textContent = data.cartTotal.toFixed(2);
                            document.getElementById('totalAmount').textContent = data.cartTotal.toFixed(2);
                            document.getElementById('itemCount').textContent = data.cartCount;
                            
                            // Check if cart is empty
                            if (data.cartCount === 0) {
                                location.reload(); // Reload to show empty cart state
                            }
                        }, 300);
                    }
                    
                    showCustomNotification(
                        'Item removed from cart',
                        'Your cart has been updated',
                        'success'
                    );
                } else {
                    showCustomNotification(
                        data.message || 'Failed to remove item',
                        'Please try again',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomNotification(
                    'Error removing item',
                    'Please try again',
                    'error'
                );
            });
        }
        
        // Event delegation for cart buttons
        document.addEventListener('click', function(e) {
            // Quantity increase
            if (e.target.classList.contains('quantity-increase')) {
                e.preventDefault();
                const item = e.target.closest('.cart-item');
                const productId = item.dataset.productId;
                const index = item.dataset.index;
                updateQuantity(productId, 1, index);
            }
            
            // Quantity decrease
            if (e.target.classList.contains('quantity-decrease')) {
                e.preventDefault();
                const item = e.target.closest('.cart-item');
                const productId = item.dataset.productId;
                const index = item.dataset.index;
                const currentQty = parseInt(item.querySelector('.quantity-value').textContent);
                
                if (currentQty > 1) {
                    updateQuantity(productId, -1, index);
                } else {
                    showCustomNotification(
                        'Minimum quantity is 1',
                        'Remove item if you no longer want it',
                        'error'
                    );
                }
            }
            
            // Remove item
            if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                e.preventDefault();
                const item = e.target.closest('.cart-item');
                const productId = item.dataset.productId;
                const index = item.dataset.index;
                
                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    removeCartItem(productId, index);
                }
            }
        });
    });
    </script>
    
    <?php include('footer.php'); ?>
</body>
</html>