<?php
require_once __DIR__ . '/../lock_guard.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $count = 0;
} else {
    $count = array_reduce($_SESSION['cart'], function($carry, $item) {
        return $carry + ($item['quantity'] ?? 1);
    }, 0);
}

header('Content-Type: application/json');
echo json_encode(['cartCount' => (int)$count]);
exit;
?>
