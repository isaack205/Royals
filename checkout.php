<?php
/**
 * Checkout Page - Multi-Step with Paystack Integration
 * Steps: Information → Shipping → Review → Payment
 */

require_once('auth/auth_helper.php');
require_once('db.php');
require_once('paystack/config.php');

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Store checkout data in session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['complete_order'])) {
    $_SESSION['checkout_data'] = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'country' => $_POST['country'] ?? 'Kenya',
        'address' => $_POST['address'] ?? '',
        'apartment' => $_POST['apartment'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? ''
    ];
}

// Calculate totals
$subtotal = 0;
$itemCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $itemCount += $item['quantity'];
}

// Initial shipping - logic updated by JS later
$shippingCost = $subtotal >= 20000 ? 0 : 500;
$grandTotal = $subtotal + $shippingCost;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Royals</title>
    <link rel="icon" type="image/png" href="uploads/android-chrome-192x192.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<script>
    // Auto-apply theme from main site (localStorage key = 'theme')
    (function () {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
</script>



<style>
    /* Dark theme (default) */
    :root,
    html[data-theme="dark"] {
        --checkout-accent: #00d2ff;
        --checkout-bg: #0a0e27;
        --checkout-card: #1a1f3a;
        --checkout-border: #2a3150;
        --checkout-text: #e1e8f0;
        --checkout-secondary: #8b92a7;
        --accent: #00d2ff;
        --primary-bg: #0a0e27;
        --card-bg: #1a1f3a;
        --border-color: #2a3150;
        --border-light: #2a3150;
        --text: #e1e8f0;
        --text-secondary: #8b92a7;
    }

    /* Light theme */
    html[data-theme="light"] {
        --checkout-bg: #f5f7fa;
        --checkout-card: #ffffff;
        --checkout-border: #e1e8ed;
        --checkout-text: #0a0e27;
        --checkout-secondary: #6b7280;
        --accent: #00d2ff;
        --primary-bg: #f5f7fa;
        --card-bg: #ffffff;
        --border-color: #e1e8ed;
        --border-light: #e1e8ed;
        --text: #0a0e27;
        --text-secondary: #6b7280;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    body {
        background: var(--checkout-bg);
        color: var(--checkout-text);
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .checkout-wrapper {
        background: var(--checkout-bg);
        min-height: 100vh;
        padding: 2rem 0 3rem 0;
    }

    .checkout-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .review-section {
        background: var(--checkout-card);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--checkout-border);
    }

    .section-header {
        margin-bottom: 2rem;
    }

    .section-header h2 {
        color: var(--checkout-text);
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .section-header p {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .info-block {
        background: var(--border-light);
        border-radius: 12px;
        padding: 1.8rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .info-block h3 {
        color: var(--text);
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-block h3 i {
        color: var(--accent);
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 1.2rem;
    }

    .form-group label {
        display: block;
        color: var(--text);
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1rem;
        background: var(--primary-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text);
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.1);
    }

    .form-row {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .form-row .form-group {
        margin-bottom: 1.2rem;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Shipping Options */
    .shipping-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .shipping-option {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border: 2px solid var(--checkout-border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: var(--checkout-card);
    }

    .shipping-option:hover {
        border-color: var(--checkout-accent);
        background: rgba(0, 210, 255, 0.02);
    }

    .shipping-option input[type="radio"] {
        margin-top: 0.25rem;
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--checkout-accent);
    }

    .shipping-option:has(input:checked) {
        border-color: var(--checkout-accent);
        background: rgba(0, 210, 255, 0.05);
        box-shadow: 0 0 0 1px var(--checkout-accent);
    }

    .option-content {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .zone-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .zone-name {
        font-weight: 600;
        font-size: 1rem;
        color: var(--checkout-text);
    }

    .zone-areas {
        font-size: 0.85rem;
        color: var(--checkout-secondary);
        line-height: 1.4;
    }

    .zone-price {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--checkout-text);
        white-space: nowrap;
    }

    /* Order Summary */
    .order-summary-full {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        border: 1px solid var(--border-light);
    }

    .order-items-list {
        margin-bottom: 2rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .order-item-full {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1rem;
        background: var(--primary-bg);
        border-radius: 8px;
        margin-bottom: 0.8rem;
    }

    .order-item-full img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .order-item-full .item-details {
        flex: 1;
    }

    .order-item-full h4 {
        margin: 0 0 0.5rem 0;
        color: var(--text);
        font-size: 1.1rem;
    }

    .order-item-full .item-meta {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }

    .order-item-full .item-price-total {
        text-align: right;
    }

    .order-item-full .item-price-total strong {
        display: block;
        color: var(--accent);
        font-size: 1.2rem;
        margin-bottom: 0.3rem;
    }

    .order-totals {
        border-top: 2px solid var(--border-color);
        padding-top: 1.5rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        color: var(--text);
        font-size: 1rem;
    }

    /* Payment Summary Section */
    .payment-summary-section {
        background: var(--checkout-card);
        border: 1px solid var(--checkout-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .payment-method-section {
        background: var(--checkout-card);
        border: 1px solid var(--checkout-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .paystack-info {
        border: 2px solid var(--checkout-accent);
        border-radius: 12px;
        padding: 1.25rem;
        background: rgba(0, 210, 255, 0.02);
    }

    .payment-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--checkout-accent);
        margin-bottom: 0.75rem;
    }

    /* Buttons */
    .checkout-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-back {
        padding: 0.9rem 1.5rem;
        background: var(--card-bg);
        color: var(--text);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-back:hover {
        background: var(--border-light);
    }

    .btn-complete {
        flex: 1;
        padding: 0.9rem 2rem;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-complete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 210, 255, 0.4);
    }

    /* Checkout Steps */
    .checkout-step {
        display: none;
    }

    .checkout-step.active {
        display: block;
    }

    .step-number.active {
        background: var(--checkout-accent) !important;
        color: white !important;
        box-shadow: 0 0 0 4px rgba(0, 210, 255, 0.15);
    }

    /* Payment Overlay */
    .payment-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .payment-overlay.active {
        display: flex;
    }

    .payment-modal {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 3rem;
        text-align: center;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .loading-bar {
        width: 100%;
        height: 4px;
        background: var(--border-color);
        border-radius: 4px;
        overflow: hidden;
        margin: 0 auto 1.5rem;
        position: relative;
    }

    .loading-bar::after {
        content: '';
        display: block;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg,
                transparent,
                var(--accent),
                #3a7bd5,
                var(--accent),
                transparent);
        border-radius: 4px;
        animation: loadingSlide 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        box-shadow: 0 0 10px var(--accent), 0 0 20px rgba(0, 210, 255, 0.3);
    }

    @keyframes loadingSlide {
        0% {
            transform: translateX(-100%);
            opacity: 0.7;
        }

        50% {
            opacity: 1;
        }

        100% {
            transform: translateX(200%);
            opacity: 0.7;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .checkout-wrapper {
            padding: 1rem 0.5rem;
        }

        .checkout-container {
            padding: 0 0.5rem;
        }

        .section-header h2 {
            font-size: 1.3rem;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .form-row .form-group {
            width: 100%;
        }

        .info-block {
            padding: 1rem;
        }

        .review-section {
            padding: 1.5rem 1rem;
            border-radius: 12px;
        }

        .option-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .zone-price {
            align-self: flex-end;
            margin-top: 0.5rem;
        }

        .checkout-actions {
            flex-direction: column;
            gap: 0.8rem;
        }

        .btn-back,
        .btn-complete {
            width: 100%;
            justify-content: center;
        }

        .order-item-full {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-item-full img {
            width: 100%;
            height: auto;
        }

    }

    /* Desktop-specific button sizing */
    @media (min-width: 769px) {
        .btn-complete {
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            max-width: 280px;
        }

        .paystack-info .btn-complete {
            max-width: 100%;
            width: auto;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }
    }
</style>

<body>
    <div class="checkout-wrapper">

        <!-- Progress Stepper -->
        <div id="stepper" style="display: flex; align-items: center; justify-content: center; padding: 1.5rem 0 2rem;">
            <?php
            $steps = ["Information", "Shipping", "Review", "Payment"];
            foreach ($steps as $i => $name):
                $num = $i + 1; ?>
                <div class="step-item" style="display: flex; flex-direction: column; align-items: center; gap: 0.4rem;">
                    <div id="num-<?= $num ?>" class="step-number <?= $num == 1 ? 'active' : '' ?>"
                        style="width: 36px; height: 36px; border-radius: 50%; background: var(--checkout-card); border: 2px solid var(--checkout-border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: var(--checkout-secondary);">
                        <?= $num ?>
                    </div>
                    <div style="font-size: 12px; font-weight: 500; color: var(--checkout-secondary);"><?= $name ?></div>
                </div>
                <?php if ($num < 4): ?>
                    <div
                        style="width: 40px; height: 2px; background: var(--checkout-border); margin: 0 0.5rem; position: relative; top: -10px;">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="checkout-container">
            <div class="review-section">
                <form method="POST" id="checkoutForm">

                    <!-- STEP 1: INFORMATION -->
                    <div class="checkout-step active" data-step="1">
                        <div class="info-block">
                            <h3><i class="fas fa-user"></i> Contact Details</h3>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($_SESSION['checkout_data']['email'] ?? '') ?>" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group" style="flex: 1;">
                                    <label>First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control"
                                        value="<?= htmlspecialchars($_SESSION['checkout_data']['first_name'] ?? '') ?>"
                                        required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control"
                                        value="<?= htmlspecialchars($_SESSION['checkout_data']['last_name'] ?? '') ?>"
                                        required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" id="address" name="address" class="form-control"
                                    value="<?= htmlspecialchars($_SESSION['checkout_data']['address'] ?? '') ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Apartment, suite, etc. (optional)</label>
                                <input type="text" id="apartment" name="apartment" class="form-control"
                                    value="<?= htmlspecialchars($_SESSION['checkout_data']['apartment'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="<?= htmlspecialchars($_SESSION['checkout_data']['phone'] ?? '') ?>" required>
                            </div>

                            <!-- Save Info Checkbox -->
                            <div class="save-info-section"
                                style="margin-top: 1.5rem; padding: 1rem; background: rgba(0, 210, 255, 0.08); border-radius: 8px; border: 1px solid rgba(0, 210, 255, 0.2);">
                                <label
                                    style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; font-size: 0.95rem;">
                                    <input type="checkbox" id="saveInfo"
                                        style="width: 18px; height: 18px; accent-color: var(--checkout-accent); cursor: pointer;">
                                    <span style="color: var(--checkout-text);">
                                        <i class="fas fa-bookmark"
                                            style="margin-right: 0.3rem; color: var(--checkout-accent);"></i>
                                        Save my information for faster checkout
                                    </span>
                                </label>
                                <p
                                    style="margin: 0.5rem 0 0 2rem; font-size: 0.8rem; color: var(--checkout-secondary);">
                                    Your info will be saved on this device for next time</p>
                            </div>
                        </div>
                        <div class="checkout-actions">
                            <button type="button" class="btn-complete" onclick="goToStep(2)">Continue to
                                Shipping</button>
                            <a href="cart.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Cart</a>
                        </div>
                    </div>

                    <!-- STEP 2: SHIPPING -->
                    <div class="checkout-step" data-step="2">
                        <div class="section-header">
                            <h2>Shipping Method</h2>
                        </div>
                        <div class="shipping-options">
                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="200" checked>
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Nairobi Zone E</span>
                                        <span class="zone-areas">(Riara rd, Ngong rd, Kilimani, Valley Arcade)</span>
                                    </div>
                                    <span class="zone-price">KES 200.00</span>
                                </div>
                            </label>

                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="300">
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Nairobi Zone D</span>
                                        <span class="zone-areas">(Lavington, Westlands, Upperhill, Naivasha Rd,
                                            Kileleshwa, Madaraka, Nairobi West, CBD)</span>
                                    </div>
                                    <span class="zone-price">KES 300.00</span>
                                </div>
                            </label>

                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="450">
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Nairobi Zone C</span>
                                        <span class="zone-areas">(Karen, Parklands, Spring Valley, Lower Kabete, Uthiru,
                                            Kangemi, Langata, South B & C)</span>
                                    </div>
                                    <span class="zone-price">KES 450.00</span>
                                </div>
                            </label>

                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="500">
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Parcel Fees</span>
                                        <span class="zone-areas">(Outside Nairobi Shipping)</span>
                                    </div>
                                    <span class="zone-price">KES 500.00</span>
                                </div>
                            </label>

                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="600">
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Nairobi Zone B</span>
                                        <span class="zone-areas">(Dagoretti, Ruaka, Kitusuru, Runda, Ngong, Roysambu,
                                            Kasarani, Kiambu rd, Kahawa, Kinoo)</span>
                                    </div>
                                    <span class="zone-price">KES 600.00</span>
                                </div>
                            </label>

                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="1000">
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Nairobi Zone A</span>
                                        <span class="zone-areas">(Ruiru, Syokimau, Juja, Kitengela, Embakasi, Utawala
                                            and environs)</span>
                                    </div>
                                    <span class="zone-price">KES 1,000.00</span>
                                </div>
                            </label>

                            <label class="shipping-option">
                                <input type="radio" name="shipping_zone" value="3500">
                                <div class="option-content">
                                    <div class="zone-info">
                                        <span class="zone-name">Standard International Shipping</span>
                                        <span class="zone-areas">(Europe, USA, ROW - approx 25 Euros)</span>
                                    </div>
                                    <span class="zone-price">KES 3,500.00</span>
                                </div>
                            </label>
                        </div>
                        <div class="checkout-actions">
                            <button type="button" class="btn-complete" onclick="goToStep(3)">Continue to Review</button>
                            <button type="button" class="btn-back" onclick="goToStep(1)"><i
                                    class="fas fa-arrow-left"></i> Back</button>
                        </div>
                    </div>

                    <!-- STEP 3: REVIEW -->
                    <div class="checkout-step" data-step="3">
                        <div class="section-header">
                            <h2>Review Your Order</h2>
                            <p>Please review your items before processing payment.</p>
                        </div>
                        <div class="order-summary-full">
                            <div class="order-items-list">
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <div class="order-item-full">
                                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>"
                                            onerror="this.src='uploads/placeholder.jpg'; this.onerror=null;">
                                        <div class="item-details">
                                            <h4><?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'Product') ?>
                                            </h4>
                                            <p class="item-meta">Qty: <?= $item['quantity'] ?></p>
                                        </div>
                                        <div class="item-price-total">
                                            <strong>Ksh <?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="order-totals">
                                <div class="total-row">
                                    <span>Subtotal</span>
                                    <span id="display-subtotal">Ksh <?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="total-row">
                                    <span>Shipping</span>
                                    <span class="shipping-amount">Ksh 200</span>
                                </div>
                                <div class="total-row"
                                    style="font-weight: 700; font-size: 1.2rem; border-top: 2px solid var(--checkout-accent); margin-top: 1rem; padding-top: 1rem;">
                                    <span>Total</span>
                                    <span class="final-total" style="color: var(--checkout-accent);"></span>
                                </div>
                            </div>
                        </div>
                        <div class="checkout-actions">
                            <button type="button" class="btn-complete" onclick="goToStep(4)">Continue to
                                Payment</button>
                            <button type="button" class="btn-back" onclick="goToStep(2)"><i
                                    class="fas fa-arrow-left"></i> Back</button>
                        </div>
                    </div>

                    <!-- STEP 4: PAYMENT -->
                    <div class="checkout-step" data-step="4">
                        <div class="section-header">
                            <h2>Complete Order</h2>
                        </div>
                        <div class="payment-summary-section">
                            <!-- Display Mode (default) -->
                            <div id="summary-display">
                                <p style="margin: 0.5rem 0;"><strong>Contact:</strong> <span id="sum-email"></span></p>
                                <p style="margin: 0.5rem 0;"><strong>Ship to:</strong> <span id="sum-address"></span>
                                </p>
                                <p style="margin: 0.5rem 0;"><strong>Shipping:</strong> <span id="sum-shipping"></span>
                                </p>
                                <button type="button" onclick="toggleEditMode(true)"
                                    style="background: none; border: 1px solid var(--checkout-accent); color: var(--checkout-accent); padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; margin-top: 0.75rem; font-size: 0.9rem;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>

                            <!-- Edit Mode (hidden by default) -->
                            <div id="summary-edit" style="display: none;">
                                <div style="margin-bottom: 1rem;">
                                    <label
                                        style="font-weight: 600; font-size: 0.85rem; color: var(--checkout-secondary); display: block; margin-bottom: 0.3rem;">Contact
                                        Email</label>
                                    <input type="email" id="payment-email" class="form-control"
                                        style="width: 100%; padding: 0.7rem; background: var(--primary-bg); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text); font-size: 0.95rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label
                                        style="font-weight: 600; font-size: 0.85rem; color: var(--checkout-secondary); display: block; margin-bottom: 0.3rem;">Ship
                                        To</label>
                                    <input type="text" id="payment-address" class="form-control"
                                        style="width: 100%; padding: 0.7rem; background: var(--primary-bg); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text); font-size: 0.95rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label
                                        style="font-weight: 600; font-size: 0.85rem; color: var(--checkout-secondary); display: block; margin-bottom: 0.3rem;">Shipping
                                        Zone</label>
                                    <select id="payment-shipping" class="form-control"
                                        onchange="updatePaymentShipping()"
                                        style="width: 100%; padding: 0.7rem; background: var(--primary-bg); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text); font-size: 0.95rem;">
                                        <option value="200">Nairobi Zone E - KES 200</option>
                                        <option value="300">Nairobi Zone D - KES 300</option>
                                        <option value="450">Nairobi Zone C - KES 450</option>
                                        <option value="500">Parcel (Outside Nairobi) - KES 500</option>
                                        <option value="600">Nairobi Zone B - KES 600</option>
                                        <option value="1000">Nairobi Zone A - KES 1,000</option>
                                        <option value="3500">International - KES 3,500</option>
                                    </select>
                                </div>
                                <button type="button" onclick="toggleEditMode(false)"
                                    style="background: var(--checkout-accent); border: none; color: white; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; margin-top: 0.5rem; font-size: 0.9rem;">
                                    <i class="fas fa-check"></i> Done
                                </button>
                            </div>

                            <div class="total-row"
                                style="font-weight: 700; font-size: 1.2rem; border-top: 2px solid var(--checkout-accent); margin-top: 1rem; padding-top: 1rem; display: flex; justify-content: space-between;">
                                <span>Total</span>
                                <span class="final-total" style="color: var(--checkout-accent);"></span>
                            </div>
                        </div>

                        <div class="payment-method-section">
                            <!-- Paystack Online Payment -->
                            <div class="paystack-info"
                                style="padding: 1.5rem; background: rgba(108, 154, 100, 0.1); border-radius: 8px; border: 2px solid;">
                                <p
                                    style="font-size: 0.9rem; margin: 0.5rem 0; color: var(--checkout-secondary); text-align: center;">
                                    Pay with Card or M-PESA
                                </p>
                                <button type="button" class="btn-complete" onclick="payWithPaystack()"
                                    style="background: #f04a5bff; margin-top: 0.75rem;">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </button>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="checkout-actions"
                            style="margin-bottom: 1.5rem; border-top: 1px solid var(--checkout-border); padding-top: 1rem;">
                            <button type="button" class="btn-back" onclick="goToStep(3)">
                                <i class="fas fa-arrow-left"></i> Back to Review
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Processing Overlay -->
    <div class="payment-overlay" id="paymentOverlay">
        <div class="payment-modal">
            <div class="loading-bar"></div>
            <h3>Please Wait...</h3>
            <p style="color: var(--checkout-secondary); margin-top: 0.5rem; font-size: 0.95rem;">Your order is being
                processed</p>
            <p style="color: var(--checkout-secondary); font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.8;">Do not
                close this window</p>
        </div>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        const subtotal = <?= $subtotal ?>;

        function validateForm() {
            const required = ['email', 'first_name', 'last_name', 'address', 'phone'];
            let valid = true;
            required.forEach(id => {
                const el = document.getElementById(id);
                if (!el || !el.value.trim()) {
                    valid = false;
                    if (el) el.style.borderColor = '#ff4757';
                } else {
                    if (el) el.style.borderColor = '';
                }
            });
            if (!valid) alert('Please complete all required contact details.');
            return valid;
        }

        function goToStep(step) {
            // Validate Step 1 (Info) if moving forward past it
            if (step > 1) {
                if (!validateForm()) return;
            }

            // Hide all steps
            document.querySelectorAll('.checkout-step').forEach(s => s.classList.remove('active'));
            // Show target step
            document.querySelector(`.checkout-step[data-step="${step}"]`).classList.add('active');

            // Update Stepper UI
            const stepItems = document.querySelectorAll('.step-item');
            const lines = document.querySelectorAll('#stepper > div[style*="height: 2px"]');

            stepItems.forEach((item, idx) => {
                const stepNum = idx + 1;
                const numberEl = item.querySelector('.step-number');
                const labelEl = item.querySelector('div:last-child');

                numberEl.className = 'step-number';
                numberEl.style.background = 'var(--checkout-card)';
                numberEl.style.borderColor = 'var(--checkout-border)';
                numberEl.style.color = 'var(--checkout-secondary)';
                numberEl.style.boxShadow = 'none';
                numberEl.textContent = stepNum;
                labelEl.style.fontWeight = '500';
                labelEl.style.color = 'var(--checkout-secondary)';

                if (stepNum < step) {
                    numberEl.classList.add('active');
                    numberEl.style.background = 'var(--checkout-accent)';
                    numberEl.style.borderColor = 'var(--checkout-accent)';
                    numberEl.style.color = 'white';
                    numberEl.textContent = '✓';
                    labelEl.style.fontWeight = '600';
                    labelEl.style.color = 'var(--checkout-text)';
                } else if (stepNum === step) {
                    numberEl.classList.add('active');
                    numberEl.style.background = 'var(--checkout-accent)';
                    numberEl.style.borderColor = 'var(--checkout-accent)';
                    numberEl.style.color = 'white';
                    numberEl.style.boxShadow = '0 0 0 4px rgba(0, 210, 255, 0.15)';
                    labelEl.style.fontWeight = '600';
                    labelEl.style.color = 'var(--checkout-text)';
                }
            });

            lines.forEach((line, idx) => {
                if (idx + 1 < step) {
                    line.style.background = 'var(--checkout-accent)';
                } else {
                    line.style.background = 'var(--checkout-border)';
                }
            });

            // If moving to step 4, populate editable fields
            if (step === 4) {
                document.getElementById('payment-email').value = document.getElementById('email').value;
                const apt = document.getElementById('apartment').value.trim();
                const addr = document.getElementById('address').value + (apt ? ', ' + apt : '');
                document.getElementById('payment-address').value = addr;

                // Sync shipping zone selection
                const selectedZone = document.querySelector('input[name="shipping_zone"]:checked');
                document.getElementById('payment-shipping').value = selectedZone.value;

                // Populate display mode text
                document.getElementById('sum-email').textContent = document.getElementById('email').value;
                document.getElementById('sum-address').textContent = addr;
                const shipCost = parseFloat(selectedZone.value);
                document.getElementById('sum-shipping').textContent = 'Ksh ' + shipCost.toLocaleString();

                // Reset to display mode
                document.getElementById('summary-display').style.display = 'block';
                document.getElementById('summary-edit').style.display = 'none';

                updatePaymentShipping();
            }

            // Update browser history for back button
            history.pushState({ step: step }, '', '#step' + step);
            window.scrollTo(0, 0);
        }

        // Handle browser back button
        let currentStep = 1;
        window.addEventListener('popstate', function (e) {
            if (e.state && e.state.step) {
                const targetStep = e.state.step;
                // Go to that step without pushing to history again
                document.querySelectorAll('.checkout-step').forEach(s => s.classList.remove('active'));
                document.querySelector(`.checkout-step[data-step="${targetStep}"]`).classList.add('active');
                currentStep = targetStep;
                window.scrollTo(0, 0);
            } else {
                // No state means user wants to leave checkout - redirect to cart
                window.location.href = 'cart.php';
            }
        });

        // Toggle between display and edit mode on payment step
        function toggleEditMode(showEdit) {
            const displayDiv = document.getElementById('summary-display');
            const editDiv = document.getElementById('summary-edit');

            if (showEdit) {
                displayDiv.style.display = 'none';
                editDiv.style.display = 'block';
            } else {
                // Update display text with edited values
                document.getElementById('sum-email').textContent = document.getElementById('payment-email').value;
                document.getElementById('sum-address').textContent = document.getElementById('payment-address').value;
                const shippingSelect = document.getElementById('payment-shipping');
                const shipCost = parseFloat(shippingSelect.value);
                document.getElementById('sum-shipping').textContent = 'Ksh ' + shipCost.toLocaleString();

                displayDiv.style.display = 'block';
                editDiv.style.display = 'none';

                updatePaymentShipping();
            }
        }

        function updateShippingAndTotal() {
            const selectedZone = document.querySelector('input[name="shipping_zone"]:checked');
            const shipCost = parseFloat(selectedZone.value);
            const total = subtotal + shipCost;

            const formattedTotal = 'Ksh ' + total.toLocaleString();
            document.querySelectorAll('#display-total, .final-total').forEach(el => el.textContent = formattedTotal);
            document.querySelectorAll('.shipping-amount').forEach(el => el.textContent = 'Ksh ' + shipCost);
        }

        function updatePaymentShipping() {
            const shipCost = parseFloat(document.getElementById('payment-shipping').value);
            const total = subtotal + shipCost;
            const formattedTotal = 'Ksh ' + total.toLocaleString();
            document.querySelectorAll('.final-total').forEach(el => el.textContent = formattedTotal);
        }

        // Paystack Payment Function - Direct Integration
        function payWithPaystack() {
            // Use values from payment step editable fields
            const email = document.getElementById('payment-email').value.trim();
            const address = document.getElementById('payment-address').value.trim();
            const shipCost = parseFloat(document.getElementById('payment-shipping').value);

            const fname = document.getElementById('first_name').value.trim();
            const lname = document.getElementById('last_name').value.trim();
            const phone = document.getElementById('phone').value.trim();

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                document.getElementById('payment-email').focus();
                return;
            }

            if (!address) {
                alert('Please enter a shipping address.');
                document.getElementById('payment-address').focus();
                return;
            }

            const totalAmount = subtotal + shipCost;

            // Force integer amount (KES to kobo)
            const amountInKobo = Math.round(totalAmount * 100);

            // Generate unique reference
            const uniqueRef = 'BX-' + Date.now() + '-' + Math.floor(Math.random() * 1000000);

            // Get shipping method name from dropdown
            const shippingSelect = document.getElementById('payment-shipping');
            const shippingMethod = shippingSelect.options[shippingSelect.selectedIndex].text;

            console.log('=== PAYSTACK PAYMENT ===');
            console.log('Email:', email);
            console.log('Amount (KES):', totalAmount);
            console.log('Amount (kobo):', amountInKobo);
            console.log('Reference:', uniqueRef);

            // Save order details to session before payment
            fetch('paystack/save_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: email,
                    name: fname + ' ' + lname,
                    phone: phone,
                    address: address,
                    amount: totalAmount,
                    shipping_method: shippingMethod,
                    shipping_cost: shipCost,
                    reference: uniqueRef
                })
            }).then(() => {
                console.log('Order details saved to session');
            }).catch(err => {
                console.warn('Could not save order details:', err);
            });

            // Show loading overlay
            const overlay = document.getElementById('paymentOverlay');
            overlay.classList.add('active');

            // Delay to show the "Please Wait" message before Paystack opens
            setTimeout(() => {
                // Use Paystack Inline with direct parameters
                const handler = PaystackPop.setup({
                    key: '<?= PAYSTACK_PUBLIC_KEY ?>',
                    email: email,
                    amount: amountInKobo,
                    currency: 'KES',
                    ref: uniqueRef,
                    label: 'Royals',
                    metadata: {
                        customer_name: fname + ' ' + lname,
                        phone: phone,
                        shipping_method: shippingMethod,
                        shipping_cost: shipCost
                    },
                    callback: function (response) {
                        console.log('Payment successful!', response);
                        // Keep overlay visible during redirect
                        window.location.href = 'paystack/verify.php?reference=' + response.reference;
                    },
                    onClose: function () {
                        console.log('Payment window closed');
                        overlay.classList.remove('active');
                        alert('Payment cancelled. You can try again when ready.');
                    }
                });
                handler.openIframe();
                // Hide overlay only after popup opens
                setTimeout(() => overlay.classList.remove('active'), 500);
            }, 0); // No delay - overlay stays until popup loads
        }

        // Pay on Delivery Order
        function startPaymentProcess() {
            if (!validateForm()) return goToStep(1);

            const email = document.getElementById('email').value.trim();
            const fname = document.getElementById('first_name').value.trim();
            const lname = document.getElementById('last_name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const apartment = document.getElementById('apartment').value.trim();
            const address = document.getElementById('address').value.trim() + (apartment ? ', ' + apartment : '');

            const selectedZone = document.querySelector('input[name="shipping_zone"]:checked');
            const shipCost = parseFloat(selectedZone.value);

            const zoneContainer = selectedZone.closest('.shipping-option');
            const zoneName = zoneContainer.querySelector('.zone-name').textContent;
            const zoneAreas = zoneContainer.querySelector('.zone-areas').textContent;
            const shippingMethod = `${zoneName} ${zoneAreas}`;

            // Show loading
            const btn = document.querySelector('.btn-complete[onclick="startPaymentProcess()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            fetch('process_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: email,
                    name: fname + ' ' + lname,
                    phone: phone,
                    address: address,
                    amount: subtotal + shipCost,
                    shipping_method: shippingMethod,
                    shipping_cost: shipCost
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'checkout_success.php';
                    } else {
                        alert('Order Failed: ' + data.message);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error. Please try again.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }

        // Listen for shipping changes
        document.querySelectorAll('input[name="shipping_zone"]').forEach(input => {
            input.addEventListener('change', updateShippingAndTotal);
        });

        // ======= SAVE INFO FOR FASTER CHECKOUT =======
        const STORAGE_KEY = 'royals_checkout_info';

        // Load saved info on page load
        function loadSavedInfo() {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (data.email) document.getElementById('email').value = data.email;
                    if (data.first_name) document.getElementById('first_name').value = data.first_name;
                    if (data.last_name) document.getElementById('last_name').value = data.last_name;
                    if (data.address) document.getElementById('address').value = data.address;
                    if (data.apartment) document.getElementById('apartment').value = data.apartment;
                    if (data.phone) document.getElementById('phone').value = data.phone;
                    document.getElementById('saveInfo').checked = true;
                    console.log('✓ Loaded saved checkout info');
                } catch (e) {
                    console.warn('Could not parse saved info:', e);
                }
            }
        }

        // Save info to localStorage
        function saveCheckoutInfo() {
            const saveChecked = document.getElementById('saveInfo').checked;
            if (saveChecked) {
                const data = {
                    email: document.getElementById('email').value.trim(),
                    first_name: document.getElementById('first_name').value.trim(),
                    last_name: document.getElementById('last_name').value.trim(),
                    address: document.getElementById('address').value.trim(),
                    apartment: document.getElementById('apartment').value.trim(),
                    phone: document.getElementById('phone').value.trim(),
                    saved_at: new Date().toISOString()
                };
                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
                console.log('✓ Saved checkout info to localStorage');
            } else {
                // If unchecked, remove saved data
                localStorage.removeItem(STORAGE_KEY);
                console.log('✓ Cleared saved checkout info');
            }
        }

        // Clear saved info (for privacy)
        function clearSavedInfo() {
            localStorage.removeItem(STORAGE_KEY);
            document.getElementById('saveInfo').checked = false;
            console.log('✓ Cleared saved checkout info');
        }

        // Save when moving to next step
        const originalGoToStep = goToStep;
        goToStep = function (step) {
            if (step > 1) {
                saveCheckoutInfo();
            }
            originalGoToStep(step);
        };

        // Initialize
        updateShippingAndTotal();
        loadSavedInfo();
    </script>
</body>

</html>