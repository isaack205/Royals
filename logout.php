<?php
require_once __DIR__ . '/lock_guard.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Destroy all session data to log the user out
session_unset();
session_destroy();

// Redirect to homepage after logout
header("Location: index.php");
exit;
?>
