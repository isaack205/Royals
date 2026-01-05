<?php
/**
 * Paystack Payment Initialization - Kenya/KES
 */

require_once('../auth/auth_helper.php');
require_once('config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$amount = floatval($input['amount'] ?? 0);
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');

// Enhanced validation with detailed logging
error_log('=== PAYSTACK INITIALIZATION REQUEST ===');
error_log('Raw input: ' . json_encode($input, JSON_PRETTY_PRINT));
error_log('Email received: "' . $email . '"');
error_log('Email length: ' . strlen($email));
error_log('Amount: ' . $amount);
error_log('Name: ' . $name);
error_log('Phone: ' . $phone);

$validation_errors = [];

// Validate email
if (empty($email)) {
    $validation_errors['email'] = 'Email is empty or not provided';
    error_log('✗ Validation failed: Email is empty');
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $validation_errors['email'] = 'Email format is invalid: "' . $email . '"';
    error_log('✗ Validation failed: Invalid email format - "' . $email . '"');
} else {
    error_log('✓ Email validation passed: ' . $email);
}

// Validate amount
if ($amount < 3) {
    $validation_errors['amount'] = 'Amount too small (minimum KES 3), received: ' . $amount;
    error_log('✗ Validation failed: Amount too small - ' . $amount);
} else {
    error_log('✓ Amount validation passed: ' . $amount);
}

// Return validation errors if any
if (!empty($validation_errors)) {
    error_log('✗ Validation failed with errors: ' . json_encode($validation_errors));
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'validation_errors' => $validation_errors,
        'received_data' => [
            'email' => $email,
            'email_length' => strlen($email),
            'amount' => $amount,
            'name' => $name,
            'phone' => $phone
        ]
    ]);
    exit;
}


// Generate reference
$reference = 'BX-' . time() . '-' . bin2hex(random_bytes(4));

// Save to session
$_SESSION['pending_paystack'] = [
    'reference' => $reference,
    'email' => $email,
    'name' => $name,
    'phone' => $phone,
    'amount' => $amount,
    'shipping_method' => $input['shipping_method'] ?? '',
    'shipping_cost' => floatval($input['shipping_cost'] ?? 0)
];

// Prepare Paystack request (KES in cents)
$payload = [
    'email' => $email,
    'amount' => intval($amount * 100), // Convert KES to cents
    'currency' => PAYSTACK_CURRENCY, // KES
    'reference' => $reference,
    'callback_url' => PAYSTACK_CALLBACK_URL . '?reference=' . urlencode($reference),
    'channels' => PAYSTACK_CHANNELS, // card + mobile_money (M-PESA)
    'metadata' => [
        'customer_name' => $name,
        'phone' => $phone
    ]
];

// Call Paystack
$ch = curl_init(PAYSTACK_INIT_URL);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

// Debug logging
error_log('=== PAYSTACK INIT REQUEST ===');
error_log('Payload sent: ' . json_encode($payload, JSON_PRETTY_PRINT));
error_log('Currency in payload: ' . PAYSTACK_CURRENCY);
error_log('=== PAYSTACK RESPONSE ===');
error_log('HTTP Code: ' . $httpCode);
error_log('Full Response: ' . $response);

// Return
if ($httpCode === 200 && isset($result['status']) && $result['status']) {
    echo json_encode([
        'success' => true,
        'access_code' => $result['data']['access_code'],
        'reference' => $reference
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Payment initialization failed',
        'error_details' => $result
    ]);
}
?>