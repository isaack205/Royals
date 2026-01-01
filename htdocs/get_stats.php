<?php
include('db.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$stats = [
    'products' => $connection->query("SELECT COUNT(*) FROM products")->fetch_row()[0],
    'orders' => $connection->query("SELECT COUNT(*) FROM orders")->fetch_row()[0],
    'users' => $connection->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'featured' => $connection->query("SELECT COUNT(*) FROM featured_products")->fetch_row()[0]
];

header('Content-Type: application/json');
echo json_encode($stats);
?>