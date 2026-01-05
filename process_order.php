<?php
/**
 * Process Order - Pay on Delivery
 * Handles orders that don't use Paystack payment
 */

require_once('auth/auth_helper.php');
require_once('db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$email = trim($input['email'] ?? '');
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$address = trim($input['address'] ?? '');
$amount = floatval($input['amount'] ?? 0);
$shippingMethod = $input['shipping_method'] ?? 'Standard';
$shippingCost = floatval($input['shipping_cost'] ?? 0);

// Validate required fields
if (empty($email) || empty($name) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check cart
$cartData = $_SESSION['cart'] ?? [];
if (empty($cartData)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Build order items array
$orderItemsArray = [];
foreach ($cartData as $item) {
    $orderItemsArray[] = [
        'product_id' => $item['product_id'] ?? '',
        'product_name' => $item['name'] ?? $item['product_name'] ?? 'Product',
        'quantity' => intval($item['quantity'] ?? 1),
        'price' => floatval($item['price'] ?? 0),
        'image' => $item['image'] ?? 'default.jpg'
    ];
}
$orderItemsJson = json_encode($orderItemsArray);

try {
    $stmt = $pdo->prepare("
        INSERT INTO mycheckout 
        (client_id, session_id, customer_name, customer_email, customer_phone, 
         shipping_address, payment_method, order_total, order_items, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $clientId = $_SESSION['client_id'] ?? 0;
    $sessionId = session_id();
    $paymentMethod = 'Cash on Delivery';
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

    error_log('✓ Pay on Delivery order created! Order ID: ' . $orderId);
    error_log('Customer: ' . $name . ' (' . $email . ')');
    error_log('Amount: KES ' . $amount);

    $_SESSION['order_success'] = [
        'order_id' => $orderId,
        'amount' => $amount,
        'customer_name' => $name,
        'email' => $email
    ];

    // Clear cart
    unset($_SESSION['cart']);
    unset($_SESSION['checkout_data']);

    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    error_log('Pay on Delivery Order Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()]);
}
?>