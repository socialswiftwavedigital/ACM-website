<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$cat    = $_GET['cat'] ?? '';
$search = trim($_GET['q'] ?? '');

$where = ['1=1'];
$params = [];

if ($cat) { $where[] = 'category = ?'; $params[] = $cat; }
if ($search) { $where[] = 'name LIKE ?'; $params[] = "%$search%"; }

$sql = "SELECT * FROM products WHERE " . implode(' AND ', $where) . " ORDER BY category, name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = ($cat ? $cat . ' — ' : '') . 'Products — ACM Asia Cosmetics';
$metaDesc  = 'Browse all ACM skincare products: creams, serums, face wash, lotions & more. Best prices, authentic products, fast delivery across Pakistan.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Products <?= $cat ? '/ ' . htmlspecialchars($cat) : '' ?></div>
    <h1>Our Products</h1>
    <p>Premium skincare & beauty products for every skin type</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <!-- Search + Filter -->
    <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:28px">
      <form method="GET" style="flex:1;min-width:220px">
        <?php if($cat): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
        <div style="display:flex;gap:8px">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search products..." style="flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:14px;outline:none">
          <button type="submit" class="btn btn-navy">Search</button>
        </div>
      </form>
      <span style="font-size:13px;color:var(--muted)"><?= count($products) ?> product<?= count($products)!==1?'s':'' ?> found</span>
    </div>

    <!-- Category Pills -->
    <div class="cat-pills">
      <a href="/products" class="cat-pill <?= !$cat?'active':'' ?>">All</a>
      <?php foreach ($cats as $c): ?>
      <a href="/products?cat=<?= urlencode($c) ?>" class="cat-pill <?= $cat===$c?'active':'' ?>"><?= htmlspecialchars($c) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 0;color:var(--muted)">
      <div style="font-size:48px;margin-bottom:16px">🔍</div>
      <div style="font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px">No products found</div>
      <a href="/products" class="btn btn-navy" style="margin-top:16px">View All</a>
    </div>
    <?php else: ?>
    <div class="products-grid">
      <?php foreach ($products as $p): ?>
      <div class="product-card" onclick="window.location='/product?id=<?= $p['id'] ?>'">
        <div class="product-img">
          <div class="product-img-placeholder">🧴</div>
          <?php if ($p['stock'] === 0): ?>
          <div class="product-badge" style="background:var(--muted)">Out of Stock</div>
          <?php elseif ($p['stock'] <= $p['low_stock_threshold']): ?>
          <div class="product-badge">Low Stock</div>
          <?php endif; ?>
        </div>
        <div class="product-body">
          <div class="product-cat"><?= htmlspecialchars($p['category']) ?></div>
          <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="product-price">Rs. <?= number_format($p['price']) ?> <span>/ piece</span></div>
          <div class="product-footer">
            <span class="product-stock <?= $p['stock'] > 0 ? 'stock-in' : 'stock-out' ?>">
              <?= $p['stock'] > 0 ? '✓ In Stock' : '✗ Out of Stock' ?>
            </span>
            <?php if ($p['stock'] > 0): ?>
            <button class="product-order-btn" onclick="event.stopPropagation();window.location='/order?product=<?= $p['id'] ?>'">Order</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- CTA -->
<section style="background:var(--navy);padding:50px 0;text-align:center">
  <div class="container">
    <h2 style="color:#fff;font-size:26px;font-weight:800;margin-bottom:12px">Need help choosing?</h2>
    <p style="color:rgba(255,255,255,.6);margin-bottom:24px">Chat with us on WhatsApp — we'll help you pick the right product for your skin.</p>
    <a href="https://wa.me/923000000000" class="btn btn-whatsapp btn-lg" target="_blank">💬 Ask on WhatsApp</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
