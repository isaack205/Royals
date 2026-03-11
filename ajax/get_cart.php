<?php
require_once __DIR__ . '/../lock_guard.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Your cart is empty</p>
          </div>';
    exit;
}

foreach ($_SESSION['cart'] as $index => $item) {
    $sizeAttr = !empty($item['size']) ? ' data-size="'.htmlspecialchars($item['size']).'"' : '';
    $colorAttr = !empty($item['color']) ? ' data-color="'.htmlspecialchars($item['color']).'"' : '';
    echo '<div class="cart-item" data-product-id="'.$item['product_id'].'" data-index="'.$index.'"'.$sizeAttr.$colorAttr.'>
            <img src="uploads/'.$item['image'].'" class="cart-item-image" alt="'.$item['product_name'].'">
            <div class="cart-item-details">
                <div class="cart-item-name">'.htmlspecialchars($item['product_name']).'</div>
                <div class="cart-item-price">Ksh '.number_format($item['price'], 2).'</div>';
    if (!empty($item['size'])) {
        echo '<div class="cart-item-size">Size: '.htmlspecialchars($item['size']).'</div>';
    }
    if (!empty($item['color'])) {
        echo '<div class="cart-item-color">Color: '.htmlspecialchars($item['color']).'</div>';
    }
    echo '        <div class="cart-item-quantity">
                    <button class="quantity-btn quantity-decrease">-</button>
                    <span class="quantity-value">'.$item['quantity'].'</span>
                    <button class="quantity-btn quantity-increase">+</button>
                </div>
                <button class="remove-item">Remove</button>
            </div>
          </div>';
}
?>