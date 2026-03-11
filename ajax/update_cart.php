<?php
require_once __DIR__ . '/../lock_guard.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'cartCount' => 0, 'cartTotal' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $change = filter_input(INPUT_POST, 'change', FILTER_SANITIZE_NUMBER_INT);
    $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
    $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
    
    if ($productId && isset($_SESSION['cart']) && ($change == 1 || $change == -1)) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $productId && $item['size'] == $size && $item['color'] == $color) {
                $newQuantity = $item['quantity'] + $change;
                if ($newQuantity < 1) $newQuantity = 1;
                $item['quantity'] = $newQuantity;
                
                $response['success'] = true;
                break;
            }
        }
        
        // Calculate updated cart metrics
        $response['cartCount'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
        
        $response['cartTotal'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>