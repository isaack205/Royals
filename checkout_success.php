<?php
/**
 * Checkout Success Page
 * Displays order confirmation after successful checkout
 */

require_once('auth/auth_helper.php');
require_once('db.php');

// Check if order was successful
if (!isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit();
}

$orderSuccess = $_SESSION['order_success'];
$orderId = $orderSuccess['order_id'] ?? 0;
$amount = $orderSuccess['amount'] ?? 0;
$customerName = $orderSuccess['customer_name'] ?? 'Customer';
$email = $orderSuccess['email'] ?? '';

// Clear the success session
unset($_SESSION['order_success']);

include('header.php');
?>

<style>
    :root {
        --success-accent: #2ed573;
        --cart-accent: #00d2ff;
        --cart-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    html[data-theme="dark"] {
        --cart-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }

    .success-container {
        max-width: 800px;
        margin: 3rem auto;
        padding: 0 2rem;
        text-align: center;
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .success-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 2rem;
        background: linear-gradient(135deg, var(--success-accent), #26de81);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: scaleIn 0.5s ease-out 0.2s both;
        box-shadow: 0 10px 30px rgba(46, 213, 115, 0.3);
    }

    @keyframes scaleIn {
        from {
            transform: scale(0);
        }

        to {
            transform: scale(1);
        }
    }

    .success-icon i {
        font-size: 3.5rem;
        color: white;
        animation: checkmark 0.8s ease-out 0.5s both;
    }

    @keyframes checkmark {
        0% {
            transform: scale(0) rotate(-45deg);
        }

        50% {
            transform: scale(1.2) rotate(0deg);
        }

        100% {
            transform: scale(1) rotate(0deg);
        }
    }

    .success-content {
        background: var(--card-bg);
        padding: 3rem 2rem;
        border-radius: 16px;
        box-shadow: var(--cart-shadow);
        margin-bottom: 2rem;
        border: 1px solid var(--border-light);
    }

    .success-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, var(--success-accent), var(--cart-accent));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .success-message {
        font-size: 1.2rem;
        color: var(--text-secondary);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .order-details {
        background: var(--border-light);
        padding: 2rem;
        border-radius: 12px;
        margin: 2rem 0;
        text-align: left;
    }

    .order-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px dashed var(--border-color);
        color: var(--text);
    }

    .order-detail-row:last-child {
        border-bottom: none;
    }

    .order-detail-label {
        font-weight: 500;
        color: var(--text-secondary);
    }

    .order-detail-value {
        font-weight: 600;
        color: var(--accent);
    }

    .order-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--cart-accent);
    }

    .info-box {
        background: rgba(0, 210, 255, 0.1);
        border-left: 4px solid var(--cart-accent);
        padding: 1.5rem;
        border-radius: 8px;
        margin: 2rem 0;
        text-align: left;
    }

    .info-box h3 {
        color: var(--text);
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-box i {
        color: var(--cart-accent);
    }

    .info-box p {
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.9rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-primary {
        background: linear-gradient(45deg, var(--cart-accent), #3a7bd5);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 210, 255, 0.3);
    }

    .btn-secondary {
        background: var(--card-bg);
        color: var(--text);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--border-light);
        transform: translateY(-2px);
    }

    .email-sent {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--success-accent);
        margin-top: 1rem;
        font-size: 0.9rem;
    }

    .email-sent i {
        font-size: 1.2rem;
    }

    .help-section {
        margin-top: 3rem;
        padding: 2rem;
        background: var(--border-light);
        border-radius: 12px;
        text-align: left;
    }

    .help-section h3 {
        color: var(--text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .help-section h3 i {
        color: var(--cart-accent);
    }

    .help-section p {
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    .contact-info {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .contact-info>div strong {
        color: var(--text);
        display: block;
        margin-bottom: 0.3rem;
    }

    .contact-info>div span {
        color: var(--text-secondary);
    }

    @media (max-width: 768px) {
        .success-container {
            padding: 0 1rem;
        }

        .success-title {
            font-size: 2rem;
        }

        .success-content {
            padding: 2rem 1.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="success-container">
    <div class="success-icon">
        <i class="fas fa-check"></i>
    </div>

    <div class="success-content">
        <h1 class="success-title">Order Placed Successfully!</h1>
        <p class="success-message">
            Thank you, <strong><?= htmlspecialchars($customerName) ?></strong>! Your order has been received and is
            being processed.
        </p>

        <div class="order-details">
            <div class="order-detail-row">
                <span class="order-detail-label">Order Number</span>
                <span class="order-number">#<?= str_pad($orderId, 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Order Date</span>
                <span class="order-detail-value"><?= date('F j, Y') ?></span>
            </div>
            <?php if ($amount > 0): ?>
                <div class="order-detail-row">
                    <span class="order-detail-label">Order Total</span>
                    <span class="order-detail-value">Ksh <?= number_format($amount, 2) ?></span>
                </div>
            <?php endif; ?>
            <div class="order-detail-row">
                <span class="order-detail-label">Payment Method</span>
                <span class="order-detail-value"><?php
                // Try to get payment method from order
                try {
                    $stmt = $pdo->prepare("SELECT payment_method FROM mycheckout WHERE id = ?");
                    $stmt->execute([$orderId]);
                    $orderInfo = $stmt->fetch();
                    $paymentMethod = $orderInfo['payment_method'] ?? 'Cash on Delivery';
                    if (strpos($paymentMethod, 'Paystack') !== false) {
                        echo 'Paystack (Card/M-PESA)';
                    } else {
                        echo 'Cash on Delivery';
                    }
                } catch (Exception $e) {
                    echo 'Cash on Delivery';
                }
                ?></span>
            </div>
        </div>

        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> What's Next?</h3>
            <p>
                We're preparing your order for delivery. You'll receive a confirmation via SMS/WhatsApp shortly with
                your order details and tracking information. Our team will contact you to confirm delivery details.
            </p>
        </div>

        <?php if (!empty($email)): ?>
            <div class="email-sent">
                <i class="fas fa-envelope-circle-check"></i>
                <span>Order details sent to <?= htmlspecialchars($email) ?></span>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="orders.php" class="btn btn-primary">
                <i class="fas fa-receipt"></i> View My Orders
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
    // Optional: Add confetti animation on success
    // Include confetti library if you want this effect
</script>

<?php include('footer.php'); ?>