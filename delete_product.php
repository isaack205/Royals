<?php
session_start();
include('db.php');



// Check if product ID is passed
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Delete product from database
    $deleteQuery = "DELETE FROM products WHERE id = $productId";
    if ($connection->query($deleteQuery)) {
        $_SESSION['success'] = "Product deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete product. Please try again.";
    }
}

header("Location: admin_products.php");
exit;
?>
