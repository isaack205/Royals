<?php
/**
 * Save order details to session AND database before payment
 * Used with direct PaystackPop.setup() integration
 */

require_once('../auth/auth_helper.php');
require_once('../db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$reference = $input['reference'] ?? '';
$email = $input['email'] ?? '';
$name = $input['name'] ?? '';
$phone = $input['phone'] ?? '';
$address = $input['address'] ?? '';
$amount = floatval($input['amount'] ?? 0);
$shippingMethod = $input['shipping_method'] ?? '';
$shippingCost = floatval($input['shipping_cost'] ?? 0);

// Save pending order to session
$_SESSION['pending_paystack'] = [
    'reference' => $reference,
    'email' => $email,
    'name' => $name,
    'phone' => $phone,
    'address' => $address,
    'amount' => $amount,
    'shipping_method' => $shippingMethod,
    'shipping_cost' => $shippingCost
];

// Also save to paystack_transactions table for admin dashboard
try {
    $stmt = $pdo->prepare("
        INSERT INTO paystack_transactions 
        (reference, email, amount, status, created_at) 
        VALUES (?, ?, ?, 'pending', NOW())
        ON DUPLICATE KEY UPDATE email = VALUES(email), amount = VALUES(amount)
    ");
    $stmt->execute([$reference, $email, $amount]);
    error_log('Transaction saved to database: ' . $reference);
} catch (Exception $e) {
    error_log('Failed to save transaction to database: ' . $e->getMessage());
}

error_log('Order details saved to session for reference: ' . $reference);

echo json_encode(['success' => true, 'reference' => $reference]);
?>