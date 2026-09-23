<?php
require_once __DIR__ . '/includes/db.php';

// Add image column if not exists
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT ''");
    echo "✅ image column added<br>";
} catch (PDOException $e) {
    echo "ℹ️ " . $e->getMessage() . "<br>";
}

// Show all products so user can see what's in DB
$products = $pdo->query("SELECT id, name, category, image FROM products ORDER BY category, name")->fetchAll();
echo "<br><b>Products in database:</b><br><pre>";
foreach ($products as $p) {
    echo "ID:{$p['id']} | {$p['category']} | {$p['name']} | img:{$p['image']}\n";
}
echo "</pre>";
echo "<br>Done. You can delete this file after running.";
