<?php
session_start();
include('header.php');
include('db.php');

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Initialize checkout session
if (!isset($_SESSION['checkout_stage'])) {
    $_SESSION['checkout_stage'] = 1;
    $_SESSION['checkout_data'] = [];
}

// Handle going back to previous step
if (isset($_GET['prev']) && is_numeric($_GET['prev'])) {
    $prevStage = (int)$_GET['prev'];
    if ($prevStage >= 1 && $prevStage < $_SESSION['checkout_stage']) {
        $_SESSION['checkout_stage'] = $prevStage;
        header('Location: checkout.php');
        exit();
    }
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_SESSION['checkout_stage']) {
            case 1: // Name, Email and Password
                $name = htmlspecialchars(trim($_POST['name']));
                $email = htmlspecialchars(trim($_POST['email']));
                $password = $_POST['password'];
                
                // Validate email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Please enter a valid email address");
                }
                
                // Check if client exists
                $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
                $stmt->execute([$email]);
                $client = $stmt->fetch();
                
                if ($client) {
                    // Verify password if client exists
                    if (!password_verify($password, $client['password'])) {
                        throw new Exception("Incorrect password for this email address");
                    }
                    
                    // Update client details if they exist
                    $stmt = $pdo->prepare("UPDATE clients SET name = ? WHERE client_id = ?");
                    $stmt->execute([$name, $client['client_id']]);
                    
                    // Set client session
                    $_SESSION['client_id'] = $client['client_id'];
                    $_SESSION['client_name'] = $client['name'];
                    $_SESSION['client_email'] = $client['email'];
                } else {
                    // Create new client if doesn't exist
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO clients (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $hashed_password]);
                    
                    // Set client session
                    $client_id = $pdo->lastInsertId();
                    $_SESSION['client_id'] = $client_id;
                    $_SESSION['client_name'] = $name;
                    $_SESSION['client_email'] = $email;
                }
                
                $_SESSION['checkout_data']['name'] = $name;
                $_SESSION['checkout_data']['email'] = $email;
                $_SESSION['checkout_stage'] = 2;
                break;
                
            case 2: // Phone and Address
                $phone = htmlspecialchars(trim($_POST['phone']));
                $address = htmlspecialchars(trim($_POST['address']));
                
                // Update client details if logged in
                if (isset($_SESSION['client_id'])) {
                    $stmt = $pdo->prepare("UPDATE clients SET phone = ?, address = ? WHERE client_id = ?");
                    $stmt->execute([$phone, $address, $_SESSION['client_id']]);
                }
                
                $_SESSION['checkout_data']['phone'] = $phone;
                $_SESSION['checkout_data']['address'] = $address;
                $_SESSION['checkout_stage'] = 3;
                break;
                
            case 3: // Payment and Finalize
                // Save order to database
                $stmt = $pdo->prepare("INSERT INTO mycheckout 
                    (client_id, session_id, customer_name, customer_email, customer_phone, shipping_address, payment_method, order_total, order_items) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 0;
                
                $stmt->execute([
                    $client_id,
                    session_id(),
                    $_SESSION['checkout_data']['name'],
                    $_SESSION['checkout_data']['email'],
                    $_SESSION['checkout_data']['phone'],
                    $_SESSION['checkout_data']['address'],
                    'on_delivery', // Default payment method
                    $total,
                    json_encode($_SESSION['cart'])
                ]);
                
                $order_id = $pdo->lastInsertId();
                
                // Prepare success data and clear session
                $_SESSION['order_success'] = [
                    'order_id' => $order_id,
                    'customer_name' => $_SESSION['checkout_data']['name']
                ];
                unset($_SESSION['cart']);
                unset($_SESSION['checkout_stage']);
                unset($_SESSION['checkout_data']);
                
                // Redirect to success page
                header('Location: checkout_success.php');
                exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// If we're at stage 3 but missing data, reset to stage 1
if ($_SESSION['checkout_stage'] == 3 && 
    (empty($_SESSION['checkout_data']['name']) || 
     empty($_SESSION['checkout_data']['email']) || 
     empty($_SESSION['checkout_data']['phone']) || 
     empty($_SESSION['checkout_data']['address']))) {
    $_SESSION['checkout_stage'] = 1;
    $_SESSION['checkout_data'] = [];
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - BrandX</title>
    <style>

      /* Base styles from your cart */
    :root {
        --cart-accent: #00d2ff;
        --cart-danger: #ff4757;
        --cart-success: #2ed573;
        --cart-warning: #ffa502;
        --cart-light: #f8f9fa;
        --cart-dark: #212529;
        --cart-shadow: 0 15px 30px rgba(0,0,0,0.1);
        --transition-speed: 0.3s;
    }

    /* Checkout Container */
    .checkout-container {
        max-width: 1200px;
        margin: 3rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2.5rem;
    }
    
    /* Progress Bar */
    .checkout-progress {
        grid-column: 1 / -1;
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .checkout-progress::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--border-color);
        z-index: -1;
    }
    
    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 1;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--border-color);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .step-number.active {
        background: var(--accent);
        color: white;
        transform: scale(1.1);
    }
    
    .step-number.completed {
        background: var(--cart-success);
        color: white;
    }
    
    .step-label {
        font-size: 0.9rem;
        color: var(--text-secondary);
        transition: all 0.3s ease;
    }
    
    .step-label.active {
        color: var(--accent);
        font-weight: 600;
    }
    
    .step-label.completed {
        color: var(--cart-success);
    }
    
    /* Checkout Form */
    .checkout-form {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: var(--cart-shadow);
    }
    
    .form-step {
        display: none;
        animation: fadeIn 0.3s ease-out;
    }
    
    .form-step.active {
        display: block;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text);
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--background);
        color: var(--text);
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.1);
    }
    
    /* Buttons */
    .btn-next {
        background: var(--accent);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .btn-next:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
    }
    
    .btn-prev {
        background: var(--card-bg);
        color: var(--text);
        border: 1px solid var(--border-color);
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-right: 1rem;
    }
    
    .btn-prev:hover {
        background: var(--border-color);
    }
    
    /* Order Summary */
    .order-summary {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: var(--cart-shadow);
        position: sticky;
        top: 2rem;
        height: fit-content;
    }
    
    .summary-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.8rem;
        color: var(--text);
    }
    
    .summary-title i {
        color: var(--accent);
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed var(--border-color);
        color: var(--text);
    }
    
    .summary-total {
        font-weight: 700;
        font-size: 1.3rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 2px solid var(--accent);
        color: var(--text);
    }
    
    /* Order Items */
    .order-items {
        margin-top: 1.5rem;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .order-item {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .order-item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .order-item-details {
        flex: 1;
    }
    
    .order-item-name {
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text);
    }
    
    .order-item-price {
        font-size: 0.9rem;
        color: var(--accent);
        margin-top: 0.3rem;
    }
    
    .order-item-qty {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }
    
    /* Payment Method */
    .payment-method {
        background: var(--card-bg);
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        margin-top: 1.5rem;
    }
    
    .payment-method-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .payment-option {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .payment-option:hover {
        border-color: var(--accent);
    }
    
    .payment-option.selected {
        border-color: var(--accent);
        background: rgba(0, 210, 255, 0.05);
    }
    
    .payment-option i {
        font-size: 1.5rem;
        color: var(--accent);
    }
    
    .payment-option-details {
        flex: 1;
    }
    
    .payment-option-name {
        font-weight: 500;
        color: var(--text);
    }
    
    .payment-option-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 0.3rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .checkout-container {
            grid-template-columns: 1fr;
            padding: 0 1rem;
        }
        
        .checkout-progress {
            display: none;
        }
        
        .order-summary {
            position: static;
            margin-top: 2rem;
        }
    }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* [Previous CSS styles remain the same] */
    .password-toggle {
        position: relative;
    }
    .password-toggle input {
        padding-right: 40px;
    }
    .password-toggle .toggle-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
    }
    .password-strength {
        height: 4px;
        background: #eee;
        margin-top: 5px;
        border-radius: 2px;
        overflow: hidden;
    }
    .password-strength-bar {
        height: 100%;
        width: 0%;
        background: #ff4757;
        transition: width 0.3s;
    }
    </style>
</head>
<body>
    <div class="luxury-cart">
        <div class="cart-header">
            <h1>Secure Checkout</h1>
            <p>Complete your luxury purchase</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="margin: 20px auto; max-width: 800px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-container">
            <div class="checkout-progress">
                <div class="progress-step">
                    <div class="step-number <?= $_SESSION['checkout_stage'] >= 1 ? ($_SESSION['checkout_stage'] > 1 ? 'completed' : 'active') : '' ?>">1</div>
                    <div class="step-label <?= $_SESSION['checkout_stage'] >= 1 ? ($_SESSION['checkout_stage'] > 1 ? 'completed' : 'active') : '' ?>">Your Information</div>
                </div>
                <div class="progress-step">
                    <div class="step-number <?= $_SESSION['checkout_stage'] >= 2 ? ($_SESSION['checkout_stage'] > 2 ? 'completed' : 'active') : '' ?>">2</div>
                    <div class="step-label <?= $_SESSION['checkout_stage'] >= 2 ? ($_SESSION['checkout_stage'] > 2 ? 'completed' : 'active') : '' ?>">Shipping Details</div>
                </div>
                <div class="progress-step">
                    <div class="step-number <?= $_SESSION['checkout_stage'] >= 3 ? 'active' : '' ?>">3</div>
                    <div class="step-label <?= $_SESSION['checkout_stage'] >= 3 ? 'active' : '' ?>">Complete Order</div>
                </div>
            </div>
            
            <div class="checkout-form">
                <!-- Step 1: Name, Email and Password -->
                <form method="POST" class="form-step <?= $_SESSION['checkout_stage'] == 1 ? 'active' : '' ?>">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?= htmlspecialchars($_SESSION['checkout_data']['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($_SESSION['checkout_data']['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-toggle">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <span class="toggle-icon" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="password-strength-bar"></div>
                        </div>
                        <small class="text-muted">Use this password to log in later</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-next">
                            Continue <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
                
                <!-- [Rest of the form steps remain the same] -->
                <!-- Step 2: Phone and Address -->
                <form method="POST" class="form-step <?= $_SESSION['checkout_stage'] == 2 ? 'active' : '' ?>">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($_SESSION['checkout_data']['phone'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea id="address" name="address" class="form-control" rows="4" required><?= htmlspecialchars($_SESSION['checkout_data']['address'] ?? '') ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-prev" onclick="window.location.href='checkout.php?prev=1'">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn-next">
                            Continue <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Step 3: Review and Payment -->
                <form method="POST" class="form-step <?= $_SESSION['checkout_stage'] == 3 ? 'active' : '' ?>">
                    <div class="form-group">
                        <h3>Review Your Order</h3>
                        <p>Please review your information before completing your purchase.</p>
                        
                        <div class="review-details" style="margin-top: 1.5rem; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: 8px;">
                            <h4 style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-user"></i> Customer Information
                            </h4>
                            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['checkout_data']['name'] ?? '') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['checkout_data']['email'] ?? '') ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['checkout_data']['phone'] ?? '') ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($_SESSION['checkout_data']['address'] ?? '') ?></p>
                        </div>
                    </div>
                    
                    <div class="payment-method">
                        <div class="payment-method-title">
                            <i class="fas fa-credit-card"></i> Payment Method
                        </div>
                        <div class="payment-option selected">
                            <i class="fas fa-truck"></i>
                            <div class="payment-option-details">
                                <div class="payment-option-name">Cash on Delivery</div>
                                <div class="payment-option-desc">Pay when you receive your order</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-prev" onclick="window.location.href='checkout.php?prev=2'">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn-next">
                            Complete Order <i class="fas fa-lock"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- [Order summary section remains the same] -->
            <div class="order-summary">
                <h3 class="summary-title"><i class="fas fa-receipt"></i> Order Summary</h3>
                
                <div class="summary-row">
                    <span>Subtotal (<?= count($_SESSION['cart']) ?> items)</span>
                    <span>Ksh <?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span>Included</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>Ksh <?= number_format($total, 2) ?></span>
                </div>
                
                <div class="order-items">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="order-item">
                            <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="order-item-image">
                            <div class="order-item-details">
                                <div class="order-item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div class="order-item-price">Ksh <?= number_format($item['price'], 2) ?></div>
                                <div class="order-item-qty">Qty: <?= $item['quantity'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Password visibility toggle
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-icon i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('password-strength-bar');
        let strength = 0;
        
        if (password.length > 0) strength += 20;
        if (password.length >= 8) strength += 20;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 20;
        if (/[^A-Za-z0-9]/.test(password)) strength += 20;
        
        strengthBar.style.width = strength + '%';
        
        // Change color based on strength
        if (strength < 40) {
            strengthBar.style.background = '#ff4757'; // Red
        } else if (strength < 80) {
            strengthBar.style.background = '#ffa502'; // Orange
        } else {
            strengthBar.style.background = '#2ed573'; // Green
        }
    });
    
    // Payment method selection
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });
    </script>
    
    <?php include('footer.php'); ?>
</body>
</html>