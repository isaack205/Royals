<?php
require_once __DIR__ . '/lock_guard.php';

// Sync timezone with user's computer EAT (UTC+3) timezone
date_default_timezone_set('Africa/Nairobi');

// $host = "sql103.infinityfree.com";
// $dbname = "if0_38161163_brandx";
// $username = "if0_38161163";
// $password = "put your own password i deleted mine for security reasons";
$host = "127.0.0.1";    // Using '127.0.0.1' is more stable than 'localhost' on some systems
$dbname = "brandx";     // Make sure this matches the schema name in MySQL Workbench
$username = "root";     // Default MySQL username
$password = "Isaac.254"; // Enter the password you set for MySQL Workbench



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Keep the mysqli connection for legacy code if needed
$connection = new mysqli($host, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>