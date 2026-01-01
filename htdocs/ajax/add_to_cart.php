<?php
session_start();

$response = ['success' => false, 'cartCount' => 0, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $productName = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
    
    if ($productId && $productName && $price) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $productId) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'price' => $price,
                'image' => $image,
                'quantity' => 1
            ];
        }
        
        $response['success'] = true;
        $response['message'] = 'Product added to cart';
        $response['cartCount'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
    } else {
        $response['message'] = 'Invalid product data';
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>