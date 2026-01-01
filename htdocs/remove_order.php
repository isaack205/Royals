<?php
include('db.php');

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Delete the order
    $delete_query = "DELETE FROM orders WHERE id = $orderId";
    $connection->query($delete_query);

    // Delete associated order items
    $delete_items_query = "DELETE FROM order_items WHERE order_id = $orderId";
    $connection->query($delete_items_query);

    header('Location: orders_made.php');
    exit();
}
?>
