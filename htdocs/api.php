<?php

// Enable CORS (if you're accessing from a different domain)

header('Access-Control-Allow-Origin: *');

header('Content-Type: application/json');

// Simulate a data count (e.g., from a database query)

$count = 10; // Example static count

// Return the count as JSON

echo json_encode(["count" => $count]);

?>

