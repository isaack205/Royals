<?php
/**
 * Auth Helper - Session Management
 * Ensures session is started for all pages that include this file
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in (client session)
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['client_id']) && !empty($_SESSION['client_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId()
{
    return $_SESSION['client_id'] ?? null;
}

/**
 * Get current user email
 * @return string|null
 */
function getCurrentUserEmail()
{
    return $_SESSION['client_email'] ?? null;
}

/**
 * Get current user name
 * @return string|null
 */
function getCurrentUserName()
{
    return $_SESSION['client_name'] ?? null;
}
?>