<?php
// Include database connection
include 'db.php';

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id); // "i" denotes an integer

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "User deleted successfully!";
        header("Location: registeredusers.php"); // Redirect to admin panel after deletion
        exit();
    } else {
        echo "Error deleting user: " . $connection->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No user ID provided!";
}

// Close the database connection
$connection->close();
?>
