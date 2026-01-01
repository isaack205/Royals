<?php
// Start the session to track user login status
session_start();

// Include the database connection
include('db.php');

// Include the authentication handler
include('auth.php');

// Check if the user is logged in via cookie
$user_data = checkAuth();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to the homepage if logged in
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];  // Added name field
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password
    if ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // SQL query to check if the email already exists
        $sql = "SELECT * FROM customers WHERE email = ?";

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
                // SQL query to insert new user, including name
                $insert_sql = "INSERT INTO customers (name, email, password) VALUES (?, ?, ?)";

                if ($stmt_insert = $connection->prepare($insert_sql)) {
                    // Bind parameters (name, email, password)
                    $stmt_insert->bind_param('sss', $name, $email, $hashed_password);

                    // Execute the query to insert new user
                    if ($stmt_insert->execute()) {
                        $user_id = $stmt_insert->insert_id;
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['email'] = $email;
                        $_SESSION['name'] = $name; // Store name in session

                        // Redirect to checkout or the page they were on before
                        header('Location: checkout.php');
                        exit();
                    } else {
                        $error = "Error: Unable to register.";
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
    <title>Register - BrandX</title>
    <link rel="stylesheet" href="stylehv.css">
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

        /* Register Container */
        .login-container {
            background-color: black; /* Dark background */
            color: #c9d1d9; /* Light text */
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            border: 1px solid #4ea8de;
        }

        /* Brand Name */
        .brand-name {
            font-size: 36px;
            font-weight: bold;
            color: #4ea8de; /* Light blue color for brand */
            margin-bottom: 20px;
        }

        /* Register Title */
        .register-title {
            font-size: 24px;
            color: #ff6600; /* Orange color for title */
            margin-bottom: 20px;
        }
        input[type="name"], input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            background-color: #0d1117; /* Dark background */
            border: 1px solid #4ea8de;
            color: #c9d1d9; /* Light text */
            font-size: 16px;
        }
        /* Input Fields */
        input[type="email"], input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            background-color: #0d1117; /* Dark background */
            border: 1px solid #4ea8de;
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
            border: 1px solid #4ea8de;
            color: #4ea8de;
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
        /* Center the Header Logo */

.logo {

    text-align: center; /* Center the container */

    padding: 10px 0;

}

.logo img {

    height: 70px; /* Adjust size as needed */

    width: auto;

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
      <div class="logo">

            <a href="home.php">

                <img src="uploads/brandxlogo.png" alt="BrandX Logo">

            </a>

        </div>
        <h2>Create an Account</h2>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
        <input type="name" name="name" placeholder="Enter your Name" required><br>
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm your password" required><br>
            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>
