<?php
// API endpoint: receives orders from acmpvtltd.com
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://acmpvtltd.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

// Validate required fields
$required = ['customer_name', 'customer_phone', 'customer_address', 'items', 'total'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: $field"]);
        exit;
    }
}

// Generate order number
$orderNumber = 'ACM-' . strtoupper(substr(uniqid(), -6));

// Save or create customer
$custEmail = filter_var($data['customer_email'] ?? '', FILTER_VALIDATE_EMAIL) ? $data['customer_email'] : '';
if ($custEmail) {
    $cust = $pdo->prepare("SELECT id FROM customers WHERE email=?");
    $cust->execute([$custEmail]);
    if (!$cust->fetch()) {
        $pdo->prepare("INSERT INTO customers (name,email,phone,city,total_orders,total_spent) VALUES (?,?,?,?,1,?)")
            ->execute([$data['customer_name'], $custEmail, $data['customer_phone'], $data['city'] ?? '', $data['total']]);
    } else {
        $pdo->prepare("UPDATE customers SET total_orders=total_orders+1, total_spent=total_spent+? WHERE email=?")
            ->execute([$data['total'], $custEmail]);
    }
}

// Insert order
$items = is_array($data['items']) ? json_encode($data['items']) : $data['items'];
$stmt  = $pdo->prepare("INSERT INTO orders (order_number,customer_name,customer_email,customer_phone,customer_address,items,total,source) VALUES (?,?,?,?,?,?,?,?)");
$stmt->execute([
    $orderNumber,
    $data['customer_name'],
    $custEmail,
    $data['customer_phone'],
    $data['customer_address'],
    $items,
    (float)$data['total'],
    $data['source'] ?? 'website'
]);
$orderId = $pdo->lastInsertId();

// Email to admin
$adminBody = "New Order: $orderNumber\n\nCustomer: {$data['customer_name']}\nPhone: {$data['customer_phone']}\nAddress: {$data['customer_address']}\nTotal: Rs. " . number_format($data['total']) . "\n\nView: " . ADMIN_URL . "/orders?id=$orderId";
mail(ADMIN_EMAIL, "New Order $orderNumber — ACM", $adminBody, "From: " . MAIL_FROM);

// Email to customer
if ($custEmail) {
    $custBody = "Dear {$data['customer_name']},\n\nThank you for your order!\n\nOrder: $orderNumber\nTotal: Rs. " . number_format($data['total']) . "\n\nWe will process your order shortly.\n\nACM Asia Cosmetics\nwww.acmpvtltd.com";
    mail($custEmail, "Order Confirmed — $orderNumber", $custBody, "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">");
}

echo json_encode(['success' => true, 'order_number' => $orderNumber, 'order_id' => $orderId]);
