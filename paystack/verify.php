<?php
/**
 * Paystack Payment Verification - Kenya/KES
 */

require_once('../auth/auth_helper.php');
require_once('../db.php');
require_once('config.php');

$reference = $_GET['reference'] ?? '';

error_log('=== PAYSTACK VERIFY START ===');
error_log('Reference: ' . $reference);

if (!$reference) {
    error_log('✗ No reference provided');
    header('Location: ../checkout.php?error=missing_reference');
    exit;
}

// Verify with Paystack
$ch = curl_init(PAYSTACK_VERIFY_URL . $reference);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

error_log('Paystack API Response: ' . $response);

if (!isset($result['status']) || !$result['status'] || $result['data']['status'] !== 'success') {
    error_log('✗ Payment verification failed');
    error_log('Result: ' . json_encode($result));
    header('Location: ../checkout.php?error=payment_failed');
    exit;
}

error_log('✓ Payment verified successfully');

// Update transaction status in database
try {
    $paymentData = $result['data'];
    try {
        $updateStmt = $pdo->prepare("UPDATE paystack_transactions SET status = 'success', channel = ?, gateway_response = ? WHERE reference = ?");
        $updateStmt->execute([$paymentData['channel'] ?? null, $paymentData['gateway_response'] ?? null, $reference]);
    } catch (PDOException $e) {
        // Columns might not exist, just update status
        $updateStmt = $pdo->prepare("UPDATE paystack_transactions SET status = 'success' WHERE reference = ?");
        $updateStmt->execute([$reference]);
    }
    error_log('Transaction status updated to success for: ' . $reference);
} catch (Exception $e) {
    error_log('Failed to update transaction status: ' . $e->getMessage());
}

// Get session data
$paymentInfo = $_SESSION['pending_paystack'] ?? [];
$cartData = $_SESSION['cart'] ?? [];

error_log('Session pending_paystack: ' . json_encode($paymentInfo));
error_log('Session cart: ' . json_encode($cartData));
error_log('Cart item count: ' . count($cartData));

if (empty($cartData)) {
    error_log('✗ Cart is empty - cannot create order');
    error_log('Available session keys: ' . implode(', ', array_keys($_SESSION)));
    header('Location: ../checkout.php?error=cart_expired');
    exit;
}

// Prepare order data
$name = $paymentInfo['name'] ?? 'Unknown';
$email = $paymentInfo['email'] ?? '';
$phone = $paymentInfo['phone'] ?? '';
$address = $paymentInfo['address'] ?? 'No address';
$amount = $result['data']['amount'] / 100; // Convert cents back to KES
$shippingMethod = $paymentInfo['shipping_method'] ?? 'Standard';

// Build order items as JSON array (matching format from process_order.php)
$orderItemsArray = [];
foreach ($cartData as $item) {
    $orderItemsArray[] = [
        'product_name' => $item['name'] ?? $item['product_name'] ?? 'Product',
        'quantity' => intval($item['quantity'] ?? 1),
        'price' => floatval($item['price'] ?? 0),
        'image' => $item['image'] ?? 'default.jpg'
    ];
}
$orderItemsJson = json_encode($orderItemsArray);

// Create order
try {
    $stmt = $pdo->prepare("
        INSERT INTO mycheckout 
        (client_id, session_id, customer_name, customer_email, customer_phone, 
         shipping_address, payment_method, order_total, order_items, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid')
    ");

    $clientId = $_SESSION['client_id'] ?? 0;
    $sessionId = session_id();
    $paymentMethod = 'Paystack - KES (Ref: ' . $reference . ')';
    $fullAddress = $address . "\n\nShipping: " . $shippingMethod;

    $stmt->execute([
        $clientId,
        $sessionId,
        $name,
        $email,
        $phone,
        $fullAddress,
        $paymentMethod,
        $amount,
        $orderItemsJson
    ]);

    $orderId = $pdo->lastInsertId();

    error_log('✓ Order created successfully! Order ID: ' . $orderId);
    error_log('Customer: ' . $name . ' (' . $email . ')');
    error_log('Amount: KES ' . $amount);

    $_SESSION['order_success'] = [
        'order_id' => $orderId,
        'amount' => $amount,
        'customer_name' => $name,
        'email' => $email
    ];

    unset($_SESSION['cart']);
    unset($_SESSION['checkout_data']);
    unset($_SESSION['pending_paystack']);

    header('Location: ../checkout_success.php');
    exit;

} catch (Exception $e) {
    error_log('Paystack Order Error: ' . $e->getMessage());
    header('Location: ../checkout.php?error=order_failed');
    exit;
}
?>