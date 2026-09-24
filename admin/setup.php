<?php
require_once __DIR__ . '/includes/db.php';

$tables = [
"CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  category VARCHAR(100) DEFAULT '',
  sku VARCHAR(50) DEFAULT '',
  price DECIMAL(10,2) DEFAULT 0,
  stock INT DEFAULT 0,
  low_stock_threshold INT DEFAULT 10,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) DEFAULT '',
  phone VARCHAR(30) DEFAULT '',
  city VARCHAR(100) DEFAULT '',
  total_orders INT DEFAULT 0,
  total_spent DECIMAL(10,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(30) UNIQUE,
  customer_name VARCHAR(150),
  customer_email VARCHAR(200),
  customer_phone VARCHAR(30),
  customer_address TEXT,
  items JSON,
  total DECIMAL(10,2) DEFAULT 0,
  status ENUM('new','processing','shipped','delivered','cancelled') DEFAULT 'new',
  tracking_number VARCHAR(100) DEFAULT '',
  notes TEXT,
  source VARCHAR(50) DEFAULT 'website',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS email_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient VARCHAR(200),
  subject VARCHAR(255),
  body TEXT,
  status ENUM('sent','failed') DEFAULT 'sent',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$errors = []; $done = [];
foreach ($tables as $sql) {
    try { $pdo->exec($sql); $done[] = 'Table created OK'; }
    catch (PDOException $e) { $errors[] = $e->getMessage(); }
}

// Sample products
$exists = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
if (!$exists) {
    $sampleProducts = [
        ['ACM Whitening Cream',           'Creams',               'ACM-CR-001', 850,  45, 10],
        ['ACM Moisturizing Cream',         'Creams',               'ACM-CR-002', 750,  38, 10],
        ['ACM Vitamin C Serum',            'Serums',               'ACM-SR-001', 1200, 22, 8],
        ['ACM Hyaluronic Serum',           'Serums',               'ACM-SR-002', 1100, 5,  8],
        ['ACM Charcoal Face Wash',         'Face Wash',            'ACM-FW-001', 450,  60, 15],
        ['ACM Gentle Face Wash',           'Face Wash',            'ACM-FW-002', 400,  0,  15],
        ['ACM Petroleum Jelly 250ml',      'Petroleum Jelly',      'ACM-PJ-001', 180,  90, 20],
        ['ACM Body Lotion',                'Lotions',              'ACM-LT-001', 650,  30, 12],
        ['ACM Keratin Shampoo',            'Shampoo & Conditioner','ACM-SH-001', 550,  7,  10],
        ['ACM Baby Lotion',                'Baby & Kids',          'ACM-BK-001', 480,  25, 10],
    ];
    $ins = $pdo->prepare("INSERT INTO products (name,category,sku,price,stock,low_stock_threshold) VALUES (?,?,?,?,?,?)");
    foreach ($sampleProducts as $p) $ins->execute($p);
    $done[] = '10 sample products inserted';
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>ACM Setup</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
<style>body{font-family:Poppins,sans-serif;max-width:600px;margin:60px auto;padding:20px;color:#0D2158}.ok{color:#166534;background:#F0FDF4;padding:8px 12px;border-radius:6px;margin:4px 0;font-size:13px}.err{color:#991B1B;background:#FEF2F2;padding:8px 12px;border-radius:6px;margin:4px 0;font-size:13px}h1{margin-bottom:20px}</style>
</head>
<body>
<h1>🛠 ACM Database Setup</h1>
<?php foreach ($done as $d): ?><div class="ok">✓ <?= htmlspecialchars($d) ?></div><?php endforeach; ?>
<?php foreach ($errors as $e): ?><div class="err">✗ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<?php if (empty($errors)): ?>
<div style="margin-top:24px;padding:16px;background:#EFF6FF;border-radius:8px;border:1px solid #BFDBFE">
  <strong>Setup complete!</strong><br>
  <a href="/admin/login" style="color:#DC2626;font-weight:700">→ Go to Admin Panel</a><br><br>
  <strong>Login:</strong> admin / ACM@Admin2026<br>
  <span style="font-size:12px;color:#5A6A8A">Delete this file after setup for security.</span>
</div>
<?php endif; ?>
</body></html>
