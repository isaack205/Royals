<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Your cart is empty</p>
          </div>';
    exit;
}

foreach ($_SESSION['cart'] as $item) {
    echo '<div class="cart-item" data-product-id="'.$item['product_id'].'">
            <img src="uploads/'.$item['image'].'" class="cart-item-image" alt="'.$item['product_name'].'">
            <div class="cart-item-details">
                <div class="cart-item-name">'.htmlspecialchars($item['product_name']).'</div>
                <div class="cart-item-price">Ksh '.number_format($item['price'], 2).'</div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn quantity-decrease">-</button>
                    <span class="quantity-value">'.$item['quantity'].'</span>
                    <button class="quantity-btn quantity-increase">+</button>
                </div>
                <button class="remove-item">Remove</button>
            </div>
          </div>';
}
?>