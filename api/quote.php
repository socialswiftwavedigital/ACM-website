<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$name    = trim($_POST['name']    ?? '');
$company = trim($_POST['company'] ?? '');
$phone   = trim($_POST['phone']   ?? '');
$email   = trim($_POST['email']   ?? '');
$city    = trim($_POST['city']    ?? '');
$pid     = (int)($_POST['product_id'] ?? 0);
$qty     = (int)($_POST['qty'] ?? 100);
$custom  = (int)($_POST['custom_qty'] ?? 0);
$message = trim($_POST['message'] ?? '');
$finalQty = $custom > 0 ? $custom : $qty;
if ($finalQty < 100) $finalQty = 100;

if (!$name || !$phone || !$pid) {
    echo json_encode(['success' => false, 'error' => 'Please fill in Name, Phone and Product fields.']);
    exit;
}

$productName = '';
$row = $pdo->prepare("SELECT name FROM products WHERE id = ?");
$row->execute([$pid]);
$pr = $row->fetch();
if ($pr) $productName = preg_replace('/^ACM\s+/i', '', $pr['name']);

$quoteNum = 'ACM-Q-' . strtoupper(substr(uniqid(), -6));
$items    = [['name' => $productName, 'qty' => $finalQty, 'price' => 0]];
$addr     = $city ?: 'Not specified';
$notes    = "Company: $company\nQty: $finalQty pcs\nMessage: $message";

$pdo->prepare("INSERT INTO orders (order_number,customer_name,customer_email,customer_phone,customer_address,items,total,source,notes) VALUES (?,?,?,?,?,?,0,'quote',?)")
    ->execute([$quoteNum, $name, $email, $phone, $addr, json_encode($items), $notes]);

$adminBody = "New Quote Request: $quoteNum\n\nName: $name\nCompany: $company\nPhone: $phone\nEmail: $email\nCity: $city\nProduct: $productName\nQuantity: $finalQty pcs\nMessage:\n$message\n\nView: " . ADMIN_URL;
$headers   = "From: " . MAIL_FROM . "\r\nReply-To: " . ($email ?: MAIL_FROM) . "\r\nContent-Type: text/plain; charset=UTF-8\r\n";
@mail(ADMIN_EMAIL, "Quote Request $quoteNum — ACM", $adminBody, $headers);

if ($email) {
    $custBody = "Dear $name,\n\nThank you for your quote request!\n\nReference: $quoteNum\nProduct: $productName\nQuantity: $finalQty pcs\n\nOur team will get back to you within 24 hours.\n\nACM Asia Cosmetics & Manufactures Pvt. Ltd.\nPhone: +92 325 5129241";
    @mail($email, "Quote Request Received — $quoteNum", $custBody, "From: ACM Asia Cosmetics <" . MAIL_FROM . ">");
}

echo json_encode(['success' => true, 'ref' => $quoteNum, 'product' => $productName, 'qty' => $finalQty]);
