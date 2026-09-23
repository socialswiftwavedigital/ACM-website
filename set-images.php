<?php
require_once __DIR__ . '/includes/db.php';

$updates = [
    1  => 'cream-anti-freckles.png',   // ACM Whitening Cream
    2  => 'cream-vitamin-b3.png',      // ACM Moisturizing Cream
    3  => 'serum-vitamin-c.png',       // ACM Vitamin C Serum
    4  => 'serum-hyaluronic.png',      // ACM Hyaluronic Serum
    5  => 'fw-charcoal.png',           // ACM Charcoal Face Wash
    6  => 'fw-creamy.png',             // ACM Gentle Face Wash
    7  => 'pj-original.png',           // ACM Petroleum Jelly 250ml
    8  => 'lot-multi-purpose.png',     // ACM Body Lotion
    9  => 'sh-keratin.png',            // ACM Keratin Shampoo
    10 => 'bk-lotion.png',             // ACM Baby Lotion
];

$stmt = $pdo->prepare("UPDATE products SET image = ? WHERE id = ?");
foreach ($updates as $id => $img) {
    $stmt->execute([$img, $id]);
    echo "✅ ID:$id → $img<br>";
}
echo "<br><b>Done! Delete this file now.</b>";
