<?php
// Include database connection
include('db.php');

// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// Include authentication handler
include('auth.php');

// Initialize variables
$errors = [];
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);
    
    // Validate inputs
    if (empty($email)) {
        $errors['login_email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['login_email'] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors['login_password'] = "Password is required";
    }
    
    // If no errors, attempt login
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                
                // Handle "remember me" functionality
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60); // 30 days from now
                    
                    $updateSql = "UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?";
                    $updateStmt = $connection->prepare($updateSql);
                    $updateStmt->bind_param("ssi", $token, $expiry, $user['id']);
                    $updateStmt->execute();
                    
                    setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/');
                }
                
                // Redirect to home page
                header("Location: home.php");
                exit();
            } else {
                $errors['login'] = "Invalid email or password";
            }
        } else {
            $errors['login'] = "Invalid email or password";
        }
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['reg_email']);
    $password = trim($_POST['reg_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }
    
    if (empty($email)) {
        $errors['reg_email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['reg_email'] = "Invalid email format";
    } else {
        // Check if email already exists
        $checkEmail = "SELECT id FROM users WHERE email = ?";
        $stmt = $connection->prepare($checkEmail);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['reg_email'] = "Email already registered";
        }
    }
    
    if (empty($password)) {
        $errors['reg_password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['reg_password'] = "Password must be at least 8 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    // If no errors, register user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
            // Clear form fields
            $name = $email = $password = $confirm_password = '';
        } else {
            $errors['register'] = "Registration failed. Please try again.";
        }
    }
}
// Include the header
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BrandX</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background);
    color: var(--text);
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    padding-top: 90px;
    flex-direction: column;
}
@media (max-width: 768px) {
    body{
        padding-top: 150px;
    }
 }
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-grow: 1;
    padding: 2rem;
}

.auth-container {
    width: 100%;
    max-width: 420px;
    background-color: var(--card-bg);
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
    padding: 2.5rem;
    border: 1px solid var(--border-color);
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.logo {
    text-align: center;
    margin-bottom: 1.5rem;
}

.logo img {
    height: 30px;
    width: auto;
}

.form-toggle {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
    gap: 10px;
}

.toggle-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 5px 15px;
    transition: all 0.3s;
    position: relative;
}

.toggle-btn::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background-color: var(--primary);
    transition: width 0.3s;
}

.toggle-btn.active {
    color: var(--primary);
}

.toggle-btn.active::after {
    width: 100%;
}

.form-container {
    display: none;
}

.form-container.active {
    display: block;
}

h2 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    text-align: center;
    color: var(--text);
    font-weight: 700;
}

.subtitle {
    color: var(--text-secondary);
    font-size: 0.95rem;
    text-align: center;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text);
}

.input-group {
    position: relative;
}

.form-input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 3rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background-color: var(--background);
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.remember-me input {
    accent-color: var(--primary);
}

.btn {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(45deg, var(--primary), #3a7bd5);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
}

.error {
    color: var(--danger);
    font-size: 0.9rem;
    margin-top: 5px;
    display: block;
}

.success {
    color: var(--success);
    font-size: 1rem;
    text-align: center;
    margin-bottom: 20px;
    padding: 10px;
    background-color: rgba(46, 213, 115, 0.1);
    border-radius: 5px;
}

.forgot-password {
    text-align: right;
    margin-top: 10px;
}

.forgot-password a {
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s;
}

.forgot-password a:hover {
    color: var(--primary);
}

.switch-form {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.95rem;
    color: var(--text-secondary);
}

.switch-form a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.switch-form a:hover {
    color: var(--text);
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 40px;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
}

.theme-toggle {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    border: none;
    z-index: 100;
    transition: all 0.3s ease;
}

.theme-toggle:hover {
    transform: scale(1.1);
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .auth-container {
        padding: 1.5rem;
    }

    h2 {
        font-size: 1.5rem;
    }
}

/* Error states */
.form-group.error .form-input {
    border-color: var(--danger);
}

.form-group.error .input-icon {
    color: var(--danger);
}

/* Form Container Styling */
.form-container {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}

/* Form Group Styling */
.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

/* Label Styling */
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text);
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

/* Input Field Styling */
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 0.85rem 1.25rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background-color: var(--card-bg);
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Input Focus State */
.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.form-group input[type="password"]:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
}

/* Error Message Styling */
.form-group .error {
    color: var(--danger);
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: block;
    animation: fadeIn 0.3s ease-out;
}

/* Remember Me Checkbox */
.remember-me {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.remember-me input[type="checkbox"] {
    margin-right: 0.5rem;
    accent-color: var(--primary);
    width: 1.1rem;
    height: 1.1rem;
}

.remember-me label {
    font-size: 0.95rem;
    color: var(--text-secondary);
    cursor: pointer;
}

/* Success Message */
.success {
    color: var(--success);
    font-size: 1rem;
    text-align: center;
    margin-bottom: 1.5rem;
    padding: 10px;
    background-color: rgba(46, 213, 115, 0.1);
    border-radius: 5px;
}

/* Form Title Styling */
.form-container h2 {
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    text-align: center;
    color: var(--text);
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Error States */
.form-group.error input {
    border-color: var(--danger);
    padding-right: 2.5rem;
}

/* Responsive Adjustments */
@media (max-width: 576px) {
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        padding: 0.75rem 1rem;
    }
    
    .form-container h2 {
        font-size: 1.5rem;
    }
}
</style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-image"></div>
            <div class="auth-forms">
                <div class="form-toggle">
                    <button class="toggle-btn" id="toggleLogin">Login</button>
                    <span> / </span>
                    <button class="toggle-btn" id="toggleRegister">Register</button>
                </div>
                
                <div class="logo">
                    <a href="home.php">
                        <img src="uploads/logo.svg" alt="BrandX Logo">
                    </a>
                </div>
                
                <!-- Login Form -->
                <div class="form-container active" id="loginForm">
                    <h2>Welcome Back</h2>
                    
                    <?php if (isset($errors['login'])): ?>
                        <div class="error" style="text-align: center; margin-bottom: 15px;"><?php echo $errors['login']; ?></div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            <?php if (isset($errors['login_email'])): ?>
                                <span class="error"><?php echo $errors['login_email']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                            <?php if (isset($errors['login_password'])): ?>
                                <span class="error"><?php echo $errors['login_password']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        
                        <button type="submit" name="login" class="btn">Login</button>
                      
                    </form>
                </div>
                
                <!-- Registration Form -->
                <div class="form-container" id="registerForm">
                    <h2>Create Account</h2>
                    
                    <?php if (!empty($success)): ?>
                        <div class="success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($errors['register'])): ?>
                        <div class="error" style="text-align: center; margin-bottom: 15px;"><?php echo $errors['register']; ?></div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <span class="error"><?php echo $errors['name']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="reg_email">Email</label>
                            <input type="email" id="reg_email" name="reg_email" value="<?php echo isset($_POST['reg_email']) ? htmlspecialchars($_POST['reg_email']) : ''; ?>" required>
                            <?php if (isset($errors['reg_email'])): ?>
                                <span class="error"><?php echo $errors['reg_email']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="reg_password">Password</label>
                            <input type="password" id="reg_password" name="reg_password" required>
                            <?php if (isset($errors['reg_password'])): ?>
                                <span class="error"><?php echo $errors['reg_password']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <span class="error"><?php echo $errors['confirm_password']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" name="register" class="btn">Register</button>
                        
                     
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle between login and register forms
        document.getElementById('toggleLogin').addEventListener('click', function() {
            document.getElementById('loginForm').classList.add('active');
            document.getElementById('registerForm').classList.remove('active');
        });
        
        document.getElementById('toggleRegister').addEventListener('click', function() {
            document.getElementById('registerForm').classList.add('active');
            document.getElementById('loginForm').classList.remove('active');
        });
        
        // Show register form if there are registration errors
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])): ?>
            document.getElementById('loginForm').classList.remove('active');
            document.getElementById('registerForm').classList.add('active');
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Include the footer
include('footer.php');

// Close the database connection
$connection->close();
?>