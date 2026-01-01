<?php
// Start the session
session_start();

// Destroy the session to log out the admin
session_destroy();

// Redirect to login page with a logout success message
header('Location: adminlogin.php?logout=success');
exit();
?>
