<?php
require_once '../db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($query)) {
    echo json_encode(['error' => 'Empty query', 'success' => false]);
    exit;
}

try {
    // Debug: Check if connection exists
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Debug: Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
    if ($tableCheck->rowCount() == 0) {
        throw new Exception('Products table does not exist');
    }

    // Debug: Check columns in products table
    $columnCheck = $conn->query("SHOW COLUMNS FROM products");
    $columns = $columnCheck->fetchAll(PDO::FETCH_COLUMN);
    $requiredColumns = ['id', 'name', 'image', 'description'];
    foreach ($requiredColumns as $col) {
        if (!in_array($col, $columns)) {
            throw new Exception("Missing required column: $col");
        }
    }

    // Prepare the query with proper column names
    $stmt = $conn->prepare("
        SELECT id, name, image 
        FROM products 
        WHERE name LIKE :query 
        OR description LIKE :query 
        LIMIT 8
    ");
    
    $searchTerm = "%$query%";
    $stmt->bindParam(':query', $searchTerm);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log the results
    error_log("Search results for '$query': " . print_r($results, true));
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    error_log("Database error in search_suggestions.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage(), 'success' => false]);
} catch (Exception $e) {
    error_log("Error in search_suggestions.php: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage(), 'success' => false]);
}