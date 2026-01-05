<?php
session_start();
include('db.php'); // Include your database connection

$response = ['status' => 'error', 'message' => '']; // Default response structure

// Check if it's a POST request and contains the required data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if email is valid
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        // Check if password and confirm password match
        $response['message'] = "Passwords do not match.";
    } else {
        // Check if the email already exists in the database
        $sql = "SELECT * FROM customers WHERE email = ?";
        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $response['message'] = "Email is already registered.";
            } else {
                // Insert the new user into the database
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO customers (name, email, password) VALUES (?, ?, ?)";
                if ($stmt = $connection->prepare($sql)) {
                    $stmt->bind_param('sss', $name, $email, $hashed_password);
                    if ($stmt->execute()) {
                        // After successful registration, log the user in
                        $_SESSION['user_id'] = $connection->insert_id; // Get the user ID of the newly registered user
                        $_SESSION['email'] = $email;

                        $response['status'] = 'success';
                        $response['message'] = 'Registration successful!';
                    } else {
                        $response['message'] = "Error: " . $connection->error;
                    }
                }
            }

            $stmt->close();
        }
    }
}

$connection->close(); // Close the database connection

// Return the response as JSON
echo json_encode($response);
?>
