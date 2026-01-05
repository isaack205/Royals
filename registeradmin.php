<?php
// Start the session to track user login status
session_start();

// Include the database connection
include('db.php');

// Check if the admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: home.php'); // Redirect to the homepage if logged in
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password
    if ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // SQL query to check if the email already exists in the admins table
        $sql = "SELECT * FROM admins WHERE email = ?";

        if ($stmt = $connection->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param('s', $email);

            // Execute the query
            $stmt->execute();
            $result = $stmt->get_result();

            // If email already exists
            if ($result->num_rows > 0) {
                $error = "Email is already registered.";
            } else {
                // SQL query to insert new admin
                $insert_sql = "INSERT INTO admins (username, email, password) VALUES (?, ?, ?)";
                
                if ($stmt_insert = $connection->prepare($insert_sql)) {
                    // Bind parameters
                    $stmt_insert->bind_param('sss', $username, $email, $hashed_password);

                    // Execute the query to insert the new admin
                    if ($stmt_insert->execute()) {
                        $_SESSION['admin_id'] = $stmt_insert->insert_id;
                        $_SESSION['email'] = $email;

                        // Redirect to homepage or admin dashboard after successful registration
                        header('Location: home.php');
                        exit();
                    } else {
                        $error = "Error: Unable to register the admin.";
                    }
                }
            }

            // Close statement
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - BrandX</title>
    <link rel="stylesheet" href="style98.css">
    <style>
         /* General Styling */
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #0d1117; /* Dark background */
            color: #c9d1d9; /* Light text */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Login Container */
        .login-container {
            background-color: none; /* Dark background */
            color: #c9d1d9; /* Light text */
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            border: 1px solid #4ea8de; /* Green border */
        }

        /* Brand Name */
        .brand-name {
            font-size: 36px;
            font-weight: bold;
            color: #4ea8de; /* Light blue color for brand */
            margin-bottom: 20px;
        }

        /* Login Title */
        .login-title {
            font-size: 24px;
            color: #ff6600; /* Orange color for title */
            margin-bottom: 20px;
        }

        /* Input Fields */
        input[type="email"], input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            background-color: #0d1117; /* Dark background */
            border: 1px solid  #4ea8de; /* Green border */
            color: #c9d1d9; /* Light text */
            font-size: 16px;
        }

        input[type="text"]:focus, input[type="password"]:focus,input[type="email"]:focus {
            border: 1px solid #4ea8de; /* Blue border on focus */
            outline: none;
        }

        /* Submit Button */
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            background-color:  #4ea8de; /* Green background for button */
            color: white;
            font-size: 18px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background:none; /* Darker green on hover */
            border: 1px solid  #4ea8de;
        }

        /* Error Message */
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            background-color: #f8d7da;
            color: #721c24;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Create an Admin Account</h2>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">

            <input type="text" name="username" placeholder="Enter your username" required><br>
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm your password" required><br>
            <button type="submit">Register</button>
        </form>

        <p>Already have an admin account? <a href="adminlogin.php">Login here</a></p>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>
