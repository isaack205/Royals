<?php
// Start the session
session_start();

// Destroy all session data to log the user out
session_unset();
session_destroy();

// Redirect to homepage after logout
header("Location: home.php");
exit;
?>
