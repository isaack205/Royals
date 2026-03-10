<?php

// Include the database connection

include('db.php');

// Start the session to use cart functionality

session_start();


// Get the search query from the URL or AJAX request

$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

// Handle AJAX request for product suggestions

if (!empty($searchQuery)) {

    // Use wildcards (%) to search for the query anywhere in the product name

    // include price so suggestions can display it

    $sql = "SELECT id, name, price_ksh FROM products WHERE name LIKE '%$searchQuery%' LIMIT 20";

    $stmt = $connection->prepare($sql);

    $stmt->execute();

    $result = $stmt->get_result();

    // Prepare the response for suggestions

    $suggestions = [];

    while ($row = $result->fetch_assoc()) {

        $suggestions[] = [

            'id' => $row['id'],

            'name' => $row['name'],

            'price_ksh' => $row['price_ksh']

        ];

    }

    // Output the suggestions as a JSON response

    echo json_encode($suggestions);

    exit;

}

// Handle page load: Fetch filtered products based on the query

$products = [];

if ($searchQuery) {

    // Use wildcards (%) to search for the query anywhere in the product name

    $sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%'";

    $stmt = $connection->prepare($sql);

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $products[] = $row;

    }

}

?>