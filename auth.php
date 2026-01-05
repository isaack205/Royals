<?php
// Include the database connection file
include('db.php');

// Function to check if the user is logged in via cookie
function checkAuth() {
    // Check if the user is logged in via cookie
    if (isset($_COOKIE['user_email'])) {
        return $_COOKIE['user_email'];  // Return the user's email if cookie is present
    }
    return false;  // User is not logged in
}

// Function to handle login (set cookie with database validation)
function handleLogin($email, $password) {
    global $conn;  // Use the database connection from db.php

    // Sanitize the input to prevent SQL injection
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    // Query the database to find the user by email
    $query = "SELECT * FROM customers WHERE email = '$email'";
    $result = $conn->query($query);

    // If the user is found and the password matches
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password (Assuming you have hashed passwords in your database)
        if (password_verify($password, $user['password'])) {
            // Set the cookie with the user's email and ID (for 30 days)
            setLoginCookie($user['email'], $user['id']);
            return true;  // Login successful
        }
    }

    return false;  // Invalid credentials
}

// Function to set a login cookie (for the user)
function setLoginCookie($email, $user_id, $expireInDays = 30) {
    // Set a cookie with the user's email and ID (for 30 days)
    $cookie_value = json_encode([
        'user_email' => $email,
        'user_id' => $user_id
    ]);

    setcookie("user_data", $cookie_value, time() + ($expireInDays * 24 * 60 * 60), "/");  // Cookie expires in 30 days
}

// Function to handle logout (delete the login cookie)
function handleLogout() {
    // Delete the cookie by setting its expiration time to the past
    setcookie("user_data", "", time() - 3600, "/");
}
?>
