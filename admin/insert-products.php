<?php
require_once __DIR__ . '/includes/db.php';

// Delete existing products
$pdo->exec("DELETE FROM products");
$pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");

// All products — stock 100 each, low_stock_threshold 10
$products = [
// ── Creams ────────────────────────────────────────────────────
['ACM Whitening Cream 50g',          'Creams', 'ACM-CR-001',  850, 100, 10],
['ACM Moisturizing Cream 50g',       'Creams', 'ACM-CR-002',  750, 100, 10],
['ACM Fairness Cream 50g',           'Creams', 'ACM-CR-003',  900, 100, 10],
['ACM Night Repair Cream 50g',       'Creams', 'ACM-CR-004', 1100, 100, 10],
['ACM Day Cream SPF15 50g',          'Creams', 'ACM-CR-005',  950, 100, 10],
['ACM Anti-Aging Cream 50g',         'Creams', 'ACM-CR-006', 1300, 100, 10],
['ACM Bleaching Cream 30g',          'Creams', 'ACM-CR-007',  650, 100, 10],
['ACM Under Eye Cream 15g',          'Creams', 'ACM-CR-008',  750, 100, 10],
['ACM Aloe Vera Cream 50g',          'Creams', 'ACM-CR-009',  600, 100, 10],
['ACM Glutathione Cream 50g',        'Creams', 'ACM-CR-010', 1200, 100, 10],

// ── Serums ────────────────────────────────────────────────────
['ACM Vitamin C Serum 30ml',         'Serums', 'ACM-SR-001', 1200, 100, 10],
['ACM Hyaluronic Acid Serum 30ml',   'Serums', 'ACM-SR-002', 1100, 100, 10],
['ACM Retinol Serum 30ml',           'Serums', 'ACM-SR-003', 1400, 100, 10],
['ACM Brightening Serum 30ml',       'Serums', 'ACM-SR-004', 1350, 100, 10],
['ACM Niacinamide Serum 30ml',       'Serums', 'ACM-SR-005', 1150, 100, 10],
['ACM Anti-Aging Serum 30ml',        'Serums', 'ACM-SR-006', 1500, 100, 10],
['ACM Collagen Booster Serum 30ml',  'Serums', 'ACM-SR-007', 1600, 100, 10],

// ── Face Wash ─────────────────────────────────────────────────
['ACM Charcoal Face Wash 100ml',      'Face Wash', 'ACM-FW-001', 450, 100, 10],
['ACM Whitening Face Wash 100ml',     'Face Wash', 'ACM-FW-002', 400, 100, 10],
['ACM Gentle Foam Face Wash 100ml',   'Face Wash', 'ACM-FW-003', 380, 100, 10],
['ACM Neem Face Wash 100ml',          'Face Wash', 'ACM-FW-004', 420, 100, 10],
['ACM Aloe Vera Face Wash 100ml',     'Face Wash', 'ACM-FW-005', 360, 100, 10],
['ACM Salicylic Acid Face Wash 100ml','Face Wash', 'ACM-FW-006', 480, 100, 10],
['ACM Anti-Acne Face Wash 100ml',     'Face Wash', 'ACM-FW-007', 450, 100, 10],

// ── Petroleum Jelly ───────────────────────────────────────────
['ACM Petroleum Jelly 25ml',          'Petroleum Jelly', 'ACM-PJ-001',  80, 100, 10],
['ACM Petroleum Jelly 50ml',          'Petroleum Jelly', 'ACM-PJ-002', 130, 100, 10],
['ACM Petroleum Jelly 100ml',         'Petroleum Jelly', 'ACM-PJ-003', 180, 100, 10],
['ACM Petroleum Jelly 250ml',         'Petroleum Jelly', 'ACM-PJ-004', 350, 100, 10],
['ACM Petroleum Jelly 500g',          'Petroleum Jelly', 'ACM-PJ-005', 600, 100, 10],
['ACM Vitamin E Petroleum Jelly 50ml','Petroleum Jelly', 'ACM-PJ-006', 180, 100, 10],
['ACM Rose Petroleum Jelly 50ml',     'Petroleum Jelly', 'ACM-PJ-007', 160, 100, 10],

// ── Lotions ───────────────────────────────────────────────────
['ACM Body Lotion 200ml',             'Lotions', 'ACM-LT-001', 650, 100, 10],
['ACM Whitening Body Lotion 200ml',   'Lotions', 'ACM-LT-002', 750, 100, 10],
['ACM Moisturizing Lotion 200ml',     'Lotions', 'ACM-LT-003', 600, 100, 10],
['ACM Summer Cool Lotion 200ml',      'Lotions', 'ACM-LT-004', 700, 100, 10],
['ACM Winter Care Lotion 200ml',      'Lotions', 'ACM-LT-005', 680, 100, 10],
['ACM Aloe Vera Body Lotion 200ml',   'Lotions', 'ACM-LT-006', 620, 100, 10],
['ACM Milk & Honey Lotion 200ml',     'Lotions', 'ACM-LT-007', 720, 100, 10],

// ── Shampoo & Conditioner ─────────────────────────────────────
['ACM Keratin Shampoo 200ml',              'Shampoo & Conditioner', 'ACM-SH-001', 550, 100, 10],
['ACM Anti-Dandruff Shampoo 200ml',        'Shampoo & Conditioner', 'ACM-SH-002', 500, 100, 10],
['ACM Hair Fall Control Shampoo 200ml',    'Shampoo & Conditioner', 'ACM-SH-003', 580, 100, 10],
['ACM Moisturizing Shampoo 200ml',         'Shampoo & Conditioner', 'ACM-SH-004', 480, 100, 10],
['ACM Protein Conditioner 200ml',          'Shampoo & Conditioner', 'ACM-SH-005', 520, 100, 10],
['ACM Deep Conditioning Mask 200ml',       'Shampoo & Conditioner', 'ACM-SH-006', 600, 100, 10],
['ACM 2-in-1 Shampoo & Conditioner 200ml','Shampoo & Conditioner', 'ACM-SH-007', 650, 100, 10],

// ── Baby & Kids ───────────────────────────────────────────────
['ACM Baby Lotion 200ml',   'Baby & Kids', 'ACM-BK-001', 480, 100, 10],
['ACM Baby Oil 100ml',      'Baby & Kids', 'ACM-BK-002', 420, 100, 10],
['ACM Baby Shampoo 200ml',  'Baby & Kids', 'ACM-BK-003', 450, 100, 10],
['ACM Baby Cream 50g',      'Baby & Kids', 'ACM-BK-004', 380, 100, 10],
['ACM Baby Powder 100g',    'Baby & Kids', 'ACM-BK-005', 300, 100, 10],
['ACM Baby Soap 75g',       'Baby & Kids', 'ACM-BK-006', 250, 100, 10],
['ACM Kids Body Wash 200ml','Baby & Kids', 'ACM-BK-007', 500, 100, 10],

// ── Hair Care ─────────────────────────────────────────────────
['ACM Hair Growth Oil 100ml',   'Hair Care', 'ACM-HC-001', 750, 100, 10],
['ACM Argan Hair Oil 50ml',     'Hair Care', 'ACM-HC-002', 900, 100, 10],
['ACM Coconut Hair Mask 200g',  'Hair Care', 'ACM-HC-003', 680, 100, 10],
['ACM Hair Serum 50ml',         'Hair Care', 'ACM-HC-004', 650, 100, 10],
['ACM Onion Hair Oil 100ml',    'Hair Care', 'ACM-HC-005', 700, 100, 10],
['ACM Hair Tonic 100ml',        'Hair Care', 'ACM-HC-006', 580, 100, 10],
['ACM Castor Hair Oil 100ml',   'Hair Care', 'ACM-HC-007', 620, 100, 10],

// ── Essential Oils ────────────────────────────────────────────
['ACM Rose Essential Oil 10ml',      'Essential Oils', 'ACM-EO-001', 1200, 100, 10],
['ACM Lavender Essential Oil 10ml',  'Essential Oils', 'ACM-EO-002', 1100, 100, 10],
['ACM Tea Tree Essential Oil 10ml',  'Essential Oils', 'ACM-EO-003', 1000, 100, 10],
['ACM Peppermint Essential Oil 10ml','Essential Oils', 'ACM-EO-004',  950, 100, 10],
['ACM Eucalyptus Essential Oil 10ml','Essential Oils', 'ACM-EO-005',  900, 100, 10],
['ACM Almond Oil 30ml',              'Essential Oils', 'ACM-EO-006',  800, 100, 10],
['ACM Jojoba Oil 30ml',              'Essential Oils', 'ACM-EO-007', 1050, 100, 10],

// ── Facial ────────────────────────────────────────────────────
['ACM Facial Scrub 75ml',          'Facial', 'ACM-FA-001', 580, 100, 10],
['ACM Facial Clay Mask 75g',       'Facial', 'ACM-FA-002', 650, 100, 10],
['ACM Facial Toner 100ml',         'Facial', 'ACM-FA-003', 520, 100, 10],
['ACM Facial Moisturizer 50ml',    'Facial', 'ACM-FA-004', 700, 100, 10],
['ACM Facial Cleanser 100ml',      'Facial', 'ACM-FA-005', 480, 100, 10],
['ACM Facial Exfoliator 75ml',     'Facial', 'ACM-FA-006', 620, 100, 10],
['ACM Sheet Mask Pack of 5',       'Facial', 'ACM-FA-007', 850, 100, 10],
];

$ins = $pdo->prepare("INSERT INTO products (name,category,sku,price,stock,low_stock_threshold) VALUES (?,?,?,?,?,?)");
$count = 0;
foreach ($products as $p) { $ins->execute($p); $count++; }
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Products Inserted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>body{font-family:Poppins,sans-serif;max-width:600px;margin:60px auto;padding:20px;color:#0D2258;text-align:center}
.ok{background:#F0FDF4;color:#166534;padding:24px;border-radius:12px;margin-bottom:20px;font-size:18px;font-weight:700}
a{display:inline-block;margin-top:16px;background:#DC2626;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:600}
</style></head><body>
<div class="ok">✓ <?= $count ?> products inserted!<br><small style="font-size:13px;font-weight:400;opacity:.7">10 categories · 100 units each</small></div>
<a href="/admin/products">→ View Products in Admin</a>
<p style="margin-top:20px;font-size:12px;color:#94a3b8">⚠️ Delete this file from server after use</p>
</body></html>
