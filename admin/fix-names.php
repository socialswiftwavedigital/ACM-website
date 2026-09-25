<?php
require_once __DIR__ . '/includes/db.php';
$affected = $pdo->exec("UPDATE products SET name = TRIM(REPLACE(name, 'ACM ', '')) WHERE name LIKE 'ACM %'");
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Fix Names</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>body{font-family:Poppins,sans-serif;max-width:500px;margin:60px auto;padding:20px;text-align:center}
.ok{background:#F0FDF4;color:#166534;padding:24px;border-radius:12px;font-size:18px;font-weight:700;margin-bottom:16px}
a{display:inline-block;background:#DC2626;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:600}
</style></head><body>
<div class="ok">✓ <?= $affected ?> product names fixed — ACM prefix removed!</div>
<a href="/admin/products">→ View Products</a>
<p style="margin-top:16px;font-size:12px;color:#94a3b8">⚠️ Delete this file after use</p>
</body></html>
