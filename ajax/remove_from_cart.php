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
    $index = filter_input(INPUT_POST, 'index', FILTER_SANITIZE_NUMBER_INT);
    
    if ($index !== false && isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        $response['success'] = true;
        $response['message'] = 'Item removed from cart';
        
        // Calculate updated cart metrics
        $response['cartCount'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
        
        $response['cartTotal'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    } elseif (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $response['cartCount'] = 0;
        $response['cartTotal'] = 0.00;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>