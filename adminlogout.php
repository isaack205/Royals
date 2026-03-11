<?php
require_once __DIR__ . '/lock_guard.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Destroy the session to log out the admin
session_destroy();

// Redirect to login page with a logout success message
header('Location: adminlogin.php?logout=success');
exit();
?>
