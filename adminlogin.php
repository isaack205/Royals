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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #00d2ff;
            --text: #ffffff;
            --text-secondary: #888888;
            --danger: #ff4757;
            --success: #2ed573;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--primary);
            color: var(--text);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: var(--secondary);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            margin-bottom: 1.5rem;
        }
        
        .logo img {
            max-width: 120px;
            height: auto;
        }
        
        h2 {
            color: var(--accent);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .error {
            background-color: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1.2rem;
            border-left: 4px solid var(--danger);
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
        }
        
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: var(--text-secondary);
        }
        
        button {
            background-color: var(--accent);
            color: var(--primary);
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        button:hover {
            background-color: #00b8e6;
            transform: translateY(-2px);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-secondary);
        }
        
        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .divider span {
            padding: 0 10px;
            font-size: 0.9rem;
        }
        
        .forgot-link {
            color: var(--accent);
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 1rem;
            display: inline-block;
            transition: color 0.3s;
        }
        
        .forgot-link:hover {
            color: #00b8e6;
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
        
        /* Animation for form elements */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-container > * {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .logo {
            animation-delay: 0.1s;
        }
        
        h2 {
            animation-delay: 0.2s;
        }
        
        form {
            animation-delay: 0.3s;
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
        <h2>Admin Login</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="divider"><span>OR</span></div>
        
        <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>