<?php
session_start();
include('../db.php');

$response = [
    'success' => false, 
    'message' => 'Item not found in cart',
    'cartCount' => 0,
    'cartTotal' => 0.00
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    
    if ($productId && isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $productId) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                
                $response['success'] = true;
                $response['message'] = 'Item removed from cart';
                break;
            }
        }
        
        // Calculate updated cart metrics
        $response['cartCount'] = count($_SESSION['cart']);
        
        $response['cartTotal'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>