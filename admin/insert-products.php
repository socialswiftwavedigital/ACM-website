<?php
require_once __DIR__ . '/includes/db.php';

$pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS image VARCHAR(200) DEFAULT ''");
$pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS variants VARCHAR(500) DEFAULT ''");
$pdo->exec("DELETE FROM products");
$pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");

$products = [
// ── Creams ────────────────────────────────────────────────────
['Anti-Acne Cream',           'Creams', 'ACM-CR-001',  850, 100, 10, 'ni-cream-anti-acne.png'],
['Anti Aging Cream',          'Creams', 'ACM-CR-002', 1100, 100, 10, 'ni-cream-anti-aging.png'],
['Anti-Freckles Cream',       'Creams', 'ACM-CR-003',  900, 100, 10, 'ni-cream-anti-freckles.png'],
['Vitamin B-3 Cream',         'Creams', 'ACM-CR-004',  750, 100, 10, 'ni-cream-vitamin-b3.png'],
['Vitamin C Cream',           'Creams', 'ACM-CR-005', 1200, 100, 10, 'ni-cream-vitamin-c.png'],
// ── Serums ────────────────────────────────────────────────────
['Vitamin C Serum',           'Serums', 'ACM-SR-001', 1200, 100, 10, 'ni-serum-vitamin-c.png'],
['Hyaluronic Acid Serum',     'Serums', 'ACM-SR-002', 1100, 100, 10, 'ni-serum-hyaluronic.png'],
['Vitamin B5 Serum',          'Serums', 'ACM-SR-003', 1400, 100, 10, 'ni-serum-vitamin-b5.png'],
['Alpha Arbutin Serum',       'Serums', 'ACM-SR-004', 1350, 100, 10, 'ni-serum-alpha-arbutin.png'],
['Niacinamide Serum',         'Serums', 'ACM-SR-005', 1150, 100, 10, 'ni-serum-niacinamide.png'],
['Glutathione Serum',         'Serums', 'ACM-SR-006', 1500, 100, 10, 'ni-serum-glutathione.png'],
['Salicylic Acid Serum',      'Serums', 'ACM-SR-007', 1300, 100, 10, 'ni-serum-salicylic.png'],
['Zinc PCA Serum',            'Serums', 'ACM-SR-008', 1600, 100, 10, 'ni-serum-zinc-pca.png'],
// ── Face Wash ─────────────────────────────────────────────────
['Charcoal Face Wash',        'Face Wash', 'ACM-FW-001', 450, 100, 10, 'ni-fw-charcoal.png'],
['Brightening Face Wash',     'Face Wash', 'ACM-FW-002', 400, 100, 10, 'ni-fw-brightening.png'],
['Foaming Face Wash',         'Face Wash', 'ACM-FW-003', 380, 100, 10, 'ni-fw-foaming.png'],
['Herbal Face Wash',          'Face Wash', 'ACM-FW-004', 420, 100, 10, 'ni-fw-herbal.png'],
['Creamy Face Wash',          'Face Wash', 'ACM-FW-005', 360, 100, 10, 'ni-fw-creamy.png'],
['Gel Face Wash',             'Face Wash', 'ACM-FW-006', 480, 100, 10, 'ni-fw-gel.png'],
['Anti Acne Face Wash',       'Face Wash', 'ACM-FW-007', 450, 100, 10, 'ni-fw-anti-acne.png'],
['Vitamin C Face Wash',       'Face Wash', 'ACM-FW-008', 420, 100, 10, 'ni-fw-vitamin-c.png'],
['Turmeric Face Wash',        'Face Wash', 'ACM-FW-009', 400, 100, 10, 'ni-fw-turmeric.png'],
['Gold Face Wash',            'Face Wash', 'ACM-FW-010', 500, 100, 10, 'ni-fw-gold.png'],
['Vitamin B-3 Face Wash',     'Face Wash', 'ACM-FW-011', 420, 100, 10, 'ni-fw-vitamin-b3.png'],
['Rice Face Wash',            'Face Wash', 'ACM-FW-012', 380, 100, 10, 'ni-fw-rice.png'],
['Pearly Shine Face Wash',    'Face Wash', 'ACM-FW-013', 460, 100, 10, 'ni-fw-pearly-shine.png'],
['Mens Face Wash',            'Face Wash', 'ACM-FW-014', 440, 100, 10, 'ni-fw-mens.png'],
// ── Petroleum Jelly ───────────────────────────────────────────
['Original Petroleum Jelly',  'Petroleum Jelly', 'ACM-PJ-001', 130, 100, 10, 'ni-pj-original.png'],
['Colored Petroleum Jelly',   'Petroleum Jelly', 'ACM-PJ-002', 160, 100, 10, 'ni-pj-colored.png'],
['Scented Petroleum Jelly',   'Petroleum Jelly', 'ACM-PJ-003', 180, 100, 10, 'ni-pj-scented.png'],
// ── Lotions ───────────────────────────────────────────────────
['Aloe Vera Lotion',          'Lotions', 'ACM-LT-001', 620, 100, 10, 'ni-lot-aloe-vera.png'],
['Brightening Lotion',        'Lotions', 'ACM-LT-002', 750, 100, 10, 'ni-lot-brightening.png'],
['Cocoa Butter Lotion',       'Lotions', 'ACM-LT-003', 680, 100, 10, 'ni-lot-coco-butter.png'],
['Honey Lotion',              'Lotions', 'ACM-LT-004', 720, 100, 10, 'ni-lot-honey.png'],
['Lavender Lotion',           'Lotions', 'ACM-LT-005', 700, 100, 10, 'ni-lot-lavender.png'],
['Multi-Purpose Lotion',      'Lotions', 'ACM-LT-006', 650, 100, 10, 'ni-lot-multi-purpose.png'],
['Vitamin E Lotion',          'Lotions', 'ACM-LT-007', 600, 100, 10, 'ni-lot-vitamin-e.png'],
['Cleansing Lotion',          'Lotions', 'ACM-LT-008', 580, 100, 10, 'ni-lot-cleansing.png'],
['Niacinamide Lotion',        'Lotions', 'ACM-LT-009', 780, 100, 10, 'ni-lot-niacinamide.png'],
['Vitamin C Lotion',          'Lotions', 'ACM-LT-010', 760, 100, 10, 'ni-lot-vitamin-c.png'],
// ── Shampoo & Conditioner ─────────────────────────────────────
['Keratin Shampoo',           'Shampoo & Conditioner', 'ACM-SH-001', 550, 100, 10, 'ni-sh-keratin.png'],
['Anti-Dandruff Shampoo',     'Shampoo & Conditioner', 'ACM-SH-002', 500, 100, 10, 'ni-sh-anti-dandruff.png'],
['Onion Shampoo',             'Shampoo & Conditioner', 'ACM-SH-003', 580, 100, 10, 'ni-sh-onion.png'],
['Coconut Shampoo',           'Shampoo & Conditioner', 'ACM-SH-004', 480, 100, 10, 'ni-sh-coconut.png'],
['Egg Shampoo',               'Shampoo & Conditioner', 'ACM-SH-005', 520, 100, 10, 'ni-sh-egg.png'],
['Strengthening Shampoo',     'Shampoo & Conditioner', 'ACM-SH-006', 600, 100, 10, 'ni-sh-strengthening.png'],
['Almond Shampoo',            'Shampoo & Conditioner', 'ACM-SH-007', 650, 100, 10, 'ni-sh-almond.png'],
['Rice Shampoo',              'Shampoo & Conditioner', 'ACM-SH-008', 480, 100, 10, 'ni-sh-rice.png'],
['Herbal Shampoo',            'Shampoo & Conditioner', 'ACM-SH-009', 460, 100, 10, 'ni-sh-herbal.png'],
['Mens Shampoo',              'Shampoo & Conditioner', 'ACM-SH-010', 500, 100, 10, 'ni-sh-mens.png'],
['Sulfate-Free Shampoo',      'Shampoo & Conditioner', 'ACM-SH-011', 620, 100, 10, 'ni-sh-sulfate-free.png'],
// ── Baby & Kids ───────────────────────────────────────────────
['Baby Lotion',               'Baby & Kids', 'ACM-BK-001', 480, 100, 10, 'ni-bk-lotion.png'],
['Baby Oil',                  'Baby & Kids', 'ACM-BK-002', 420, 100, 10, 'ni-bk-oil.png'],
['Baby Shampoo',              'Baby & Kids', 'ACM-BK-003', 450, 100, 10, 'ni-bk-shampoo.png'],
['Baby Cream',                'Baby & Kids', 'ACM-BK-004', 380, 100, 10, 'ni-bk-cream.png'],
['Baby Face Wash',            'Baby & Kids', 'ACM-BK-005', 350, 100, 10, 'ni-bk-facewash.png'],
['Baby Body Wash',            'Baby & Kids', 'ACM-BK-006', 500, 100, 10, 'ni-bk-body-wash.png'],
['Kids Shampoo',              'Baby & Kids', 'ACM-BK-007', 450, 100, 10, 'ni-bk-kids-shampoo.png'],
['Baby Petroleum Jelly',      'Baby & Kids', 'ACM-BK-008', 300, 100, 10, 'ni-bk-pjelly.png'],
// ── Hair Care ─────────────────────────────────────────────────
['Argan Hair Oil',            'Hair Care', 'ACM-HC-001', 750, 100, 10, 'ni-hc-1.png'],
['Hair Cream',                'Hair Care', 'ACM-HC-002', 900, 100, 10, 'ni-hc-2.png'],
['Hair Gel',                  'Hair Care', 'ACM-HC-003', 680, 100, 10, 'ni-hc-3.png'],
['Hair Serum',                'Hair Care', 'ACM-HC-004', 650, 100, 10, 'ni-hc-4.png'],
['Hair Tonic',                'Hair Care', 'ACM-HC-005', 700, 100, 10, 'ni-hc-5.png'],
// ── Essential Oils (no images yet) ────────────────────────────
['Rose Essential Oil',        'Essential Oils', 'ACM-EO-001', 1200, 100, 10, ''],
['Lavender Essential Oil',    'Essential Oils', 'ACM-EO-002', 1100, 100, 10, ''],
['Tea Tree Essential Oil',    'Essential Oils', 'ACM-EO-003', 1000, 100, 10, ''],
['Peppermint Essential Oil',  'Essential Oils', 'ACM-EO-004',  950, 100, 10, ''],
['Eucalyptus Essential Oil',  'Essential Oils', 'ACM-EO-005',  900, 100, 10, ''],
['Almond Oil',                'Essential Oils', 'ACM-EO-006',  800, 100, 10, ''],
['Jojoba Oil',                'Essential Oils', 'ACM-EO-007', 1050, 100, 10, ''],
// ── Facial (no images yet) ────────────────────────────────────
['Facial Scrub',              'Facial', 'ACM-FA-001', 580, 100, 10, ''],
['Facial Clay Mask',          'Facial', 'ACM-FA-002', 650, 100, 10, ''],
['Facial Toner',              'Facial', 'ACM-FA-003', 520, 100, 10, ''],
['Facial Moisturizer',        'Facial', 'ACM-FA-004', 700, 100, 10, ''],
['Facial Cleanser',           'Facial', 'ACM-FA-005', 480, 100, 10, ''],
['Facial Exfoliator',         'Facial', 'ACM-FA-006', 620, 100, 10, ''],
['Sheet Mask',                'Facial', 'ACM-FA-007', 850, 100, 10, ''],
];

$ins = $pdo->prepare("INSERT INTO products (name,category,sku,price,stock,low_stock_threshold,image) VALUES (?,?,?,?,?,?,?)");
$count = 0;
foreach ($products as $p) { $ins->execute($p); $count++; }
$withImg = count(array_filter($products, fn($p) => $p[6] !== ''));
$withoutImg = count(array_filter($products, fn($p) => $p[6] === ''));
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Done</title>
<style>body{font-family:sans-serif;max-width:560px;margin:60px auto;padding:20px;text-align:center}
.ok{background:#F0FDF4;color:#166534;padding:24px;border-radius:12px;margin-bottom:12px;font-size:18px;font-weight:700}
.warn{background:#FEF9C3;color:#854D0E;padding:12px 16px;border-radius:8px;font-size:13px;text-align:left;margin-bottom:16px}
a{display:inline-block;background:#DC2626;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:600}
</style></head><body>
<div class="ok">✓ <?=$count?> products inserted correctly!<br>
<small style="font-weight:400;font-size:13px"><?=$withImg?> with images · <?=$withoutImg?> need images</small></div>
<?php if($withoutImg>0):?><div class="warn">⚠️ Essential Oils + Facial = <?=$withoutImg?> products need images</div><?php endif;?>
<a href="/admin/products">View Products →</a>
<p style="margin-top:14px;font-size:11px;color:#94a3b8">Delete this file after use!</p>
</body></html>
