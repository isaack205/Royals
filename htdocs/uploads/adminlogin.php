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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to check if the email exists in the admins table
    $sql = "SELECT * FROM admins WHERE email = ?";

    if ($stmt = $connection->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('s', $email);

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the email exists
        if ($result->num_rows > 0) {
            // Fetch admin data
            $admin = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['email'] = $admin['email'];

                // Redirect to the homepage or admin dashboard after successful login
                header('Location: admin_dashboard.php');
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this email.";
        }

        // Close the statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BrandX</title>
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
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            background-color: #0d1117; /* Dark background */
            border: 1px solid  #4ea8de; /* Green border */
            color: #c9d1d9; /* Light text */
            font-size: 16px;
        }

        input[type="email"]:focus, input[type="password"]:focus {
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
            .logo img,

.off-screen-menu .menu-logo img {

    background: transparent; /* Ensure transparency */

    display: block;

    margin: 0 auto; /* Center horizontally */

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

/* Center the Menu Logo */

.off-screen-menu .menu-logo {

    text-align: center; /* Center the container */

    margin: 20px 0;

}

.off-screen-menu .menu-logo img {

    height: 60px; /* Adjust size as needed */

    width: auto;

}
    </style>
</head>
<body>
    <div class="login-container">
            <div class="logo">

            <a href="home.php">

                <img src="uploads/logo.png" alt="BrandX Logo">

            </a>

        </div>
        <h2>Admin Login</h2>
            

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <button type="submit">Login</button>
        </form>

        <p>Problem logging in? <a href="https://wa.me/254777992666?text=Hello,%20I%20have%20a%20problem%20loging%20in%20as%20admin" target="_blank">
    Contact</a> your 

developer</p>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>
