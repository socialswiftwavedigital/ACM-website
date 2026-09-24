<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle   = 'Products & Stock';
$currentPage = 'products';

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update_stock') {
        $pdo->prepare("UPDATE products SET stock=?, updated_at=NOW() WHERE id=?")->execute([(int)$_POST['stock'], (int)$_POST['product_id']]);
        header('Location: /admin/products?updated=1'); exit;
    }
    if ($_POST['action'] === 'add_product') {
        $pdo->prepare("INSERT INTO products (name,category,sku,price,stock,low_stock_threshold) VALUES (?,?,?,?,?,?)")
            ->execute([$_POST['name'],$_POST['category'],$_POST['sku'],(float)$_POST['price'],(int)$_POST['stock'],(int)($_POST['threshold'] ?? 10)]);
        header('Location: /admin/products?added=1'); exit;
    }
}

$cat = $_GET['cat'] ?? 'all';
$q   = trim($_GET['q'] ?? '');
$where = []; $params = [];
if ($cat !== 'all') { $where[] = 'category=?'; $params[] = $cat; }
if ($q) { $where[] = '(name LIKE ? OR sku LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
$sql = 'SELECT * FROM products' . ($where ? ' WHERE '.implode(' AND ',$where) : '') . ' ORDER BY category, name';
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$lowCount  = $pdo->query("SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= low_stock_threshold")->fetchColumn();
$outCount  = $pdo->query("SELECT COUNT(*) FROM products WHERE stock = 0")->fetchColumn();

include __DIR__ . '/includes/header.php';
?>
<div style="display:flex;min-height:100vh">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main">
  <div class="topbar">
    <div>
      <div class="page-title">Products & Stock</div>
      <div class="page-sub">Manage inventory and restock alerts</div>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='flex'">+ Add Product</button>
  </div>

  <div class="content-section" style="padding-top:20px">
    <?php if ($lowCount > 0): ?>
    <div class="alert alert-warning">⚠️ <?= $lowCount ?> product<?= $lowCount>1?'s':'' ?> running low on stock.</div>
    <?php endif; ?>
    <?php if ($outCount > 0): ?>
    <div class="alert alert-danger">🚫 <?= $outCount ?> product<?= $outCount>1?'s':'' ?> out of stock.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['updated'])): ?><div class="alert alert-success">✓ Stock updated.</div><?php endif; ?>
    <?php if (!empty($_GET['added'])): ?><div class="alert alert-success">✓ Product added.</div><?php endif; ?>

    <!-- Category filter -->
    <div class="filter-tabs">
      <a href="/admin/products" class="filter-tab <?= $cat==='all'?'active':'' ?>">All</a>
      <?php foreach ($categories as $c): ?>
      <a href="/admin/products?cat=<?= urlencode($c) ?>" class="filter-tab <?= $cat===$c?'active':'' ?>"><?= htmlspecialchars($c) ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Search -->
    <div class="search-bar">
      <form method="GET" style="display:flex;gap:8px">
        <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
        <input type="text" name="q" placeholder="Search product name or SKU..." value="<?= htmlspecialchars($q) ?>" style="max-width:300px">
        <button type="submit" class="btn btn-outline btn-sm">Search</button>
        <?php if ($q): ?><a href="/admin/products?cat=<?= $cat ?>" class="btn btn-ghost btn-sm">✕</a><?php endif; ?>
      </form>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Update Stock</th></tr>
          </thead>
          <tbody>
          <?php if (empty($products)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">No products found. Add one above.</td></tr>
          <?php else: ?>
          <?php foreach ($products as $p):
            if ($p['stock'] == 0) $st = ['out','Out of Stock'];
            elseif ($p['stock'] <= $p['low_stock_threshold']) $st = ['low','Low Stock'];
            else $st = ['ok','In Stock'];
          ?>
          <tr>
            <td style="font-weight:600"><?= htmlspecialchars($p['name']) ?></td>
            <td style="color:var(--muted);font-size:12px"><?= htmlspecialchars($p['sku']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td style="font-variant-numeric:tabular-nums">Rs. <?= number_format($p['price']) ?></td>
            <td style="font-weight:700;font-variant-numeric:tabular-nums"><?= $p['stock'] ?></td>
            <td><span class="badge badge-<?= $st[0] ?>"><?= $st[1] ?></span></td>
            <td>
              <form method="POST" style="display:flex;gap:6px;align-items:center">
                <input type="hidden" name="action"     value="update_stock">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <input type="number" name="stock" value="<?= $p['stock'] ?>" min="0" style="width:70px;text-align:center;padding:5px 8px">
                <button type="submit" class="btn btn-outline btn-sm">Save</button>
              </form>
            </td>
          </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</div>

<!-- Add Product Modal -->
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center">
  <div style="background:var(--card);border-radius:14px;padding:28px;width:420px;max-width:95%">
    <div style="display:flex;justify-content:space-between;margin-bottom:20px">
      <div style="font-size:16px;font-weight:700">Add New Product</div>
      <button onclick="document.getElementById('addModal').style.display='none'" class="btn btn-ghost btn-sm">✕</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add_product">
      <div class="form-group"><label>Product Name</label><input type="text" name="name" required placeholder="ACM Whitening Cream"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group"><label>Category</label><input type="text" name="category" required placeholder="Creams"></div>
        <div class="form-group"><label>SKU</label><input type="text" name="sku" placeholder="ACM-CR-001"></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div class="form-group"><label>Price (Rs.)</label><input type="number" name="price" required placeholder="850" min="0"></div>
        <div class="form-group"><label>Stock (units)</label><input type="number" name="stock" required placeholder="50" min="0"></div>
        <div class="form-group"><label>Low Stock At</label><input type="number" name="threshold" placeholder="10" min="1"></div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px">Add Product</button>
    </form>
  </div>
</div>
</body></html>
