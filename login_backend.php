<?php
session_start();
include('db.php'); // Include your database connection

$response = ['status' => 'error', 'message' => '']; // Default response structure

// Check if it's a POST request and contains the required data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL); // Sanitize email input
    $password = trim($_POST['password']);

    // Validate email format after sanitization
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format."; // Return error if email format is invalid
    } else {
        // SQL query to check if the email exists in the database
        $sql = "SELECT * FROM customers WHERE email = ?";
        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['email'] = $row['email'];

                    $response['status'] = 'success';
                    $response['message'] = 'Login successful';
                    $response['redirect_url'] = 'home.php'; // URL to redirect after login
                } else {
                    $response['message'] = "Invalid password."; // Password is incorrect
                }
            } else {
                $response['message'] = "No user found with that email."; // Email not found in database
            }

            $stmt->close();
        } else {
            $response['message'] = "Database error: " . $connection->error; // Error in the query
        }
    }
}

$connection->close(); // Close the database connection

// Return the response as JSON
echo json_encode($response);
?>
