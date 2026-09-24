<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/cat-urls.php';

$search = trim($_GET['q'] ?? '');

// Load ALL products (JS handles category filtering on this page)
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY category, name");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, name");
}
$products = $stmt->fetchAll();
$cats = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'All Products — ACM Asia Cosmetics Pakistan';
$metaDesc  = 'Browse all ACM Asia Cosmetics products: creams, serums, face wash, lotions, shampoo, baby care and more. Private label manufacturing from MOQ 100 pcs.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="/">Home</a> / Products
      <?php if ($search): ?> / Search: <?= htmlspecialchars($search) ?><?php endif; ?>
    </div>
    <h1>Our Products</h1>
    <p>100+ premium skincare &amp; beauty products — private label manufacturing</p>
  </div>
</div>

<section class="section">
  <div class="container">

    <!-- Search -->
    <form id="searchForm" style="margin-bottom:24px" onsubmit="return doSearch(this)">
      <div style="display:flex;gap:8px;max-width:480px">
        <input type="text" name="q" id="searchInput" value="<?= htmlspecialchars($search) ?>" placeholder="Search products..." style="flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:14px;outline:none">
        <button type="submit" class="btn btn-navy">Search</button>
      </div>
    </form>
    <script>
    function doSearch(f) {
      var q = f.querySelector('[name="q"]').value.trim();
      window.location.href = q ? '/products/' + encodeURIComponent(q) : '/products';
      return false;
    }
    </script>

    <?php if ($search): ?>
    <!-- Search results: no JS filter, show as-is -->
    <p style="font-size:13px;color:var(--muted);margin-bottom:20px"><?= count($products) ?> result<?= count($products)!==1?'s':'' ?> for "<?= htmlspecialchars($search) ?>" &nbsp;<a href="/products" style="color:var(--navy);font-weight:700">Clear ✕</a></p>
    <?php else: ?>
    <!-- Category filter pills (JS only — stay on this page) -->
    <div class="cat-pills" style="margin-bottom:28px" id="catPills">
      <button class="cat-pill active" data-filter="all" onclick="filterCat('all',this)">All</button>
      <?php foreach ($cats as $c): ?>
      <button class="cat-pill" data-filter="<?= htmlspecialchars($c) ?>" onclick="filterCat(<?= json_encode($c) ?>,this)"><?= htmlspecialchars($c) ?></button>
      <?php endforeach; ?>
    </div>
    <p id="catCount" style="font-size:13px;color:var(--muted);margin-bottom:20px"><?= count($products) ?> products</p>
    <?php endif; ?>

    <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 0;color:var(--muted)">
      <div style="font-size:48px;margin-bottom:16px">🔍</div>
      <div style="font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px">No products found</div>
      <a href="/products" class="btn btn-navy" style="margin-top:16px">View All</a>
    </div>
    <?php else: ?>
    <div class="products-grid" id="productsGrid">
      <?php foreach ($products as $p):
        $dn = preg_replace('/^ACM\s+/i', '', $p['name']); ?>
      <div class="product-card" data-cat="<?= htmlspecialchars($p['category']) ?>" onclick="window.location='/product?id=<?= $p['id'] ?>'">
        <div class="product-img">
          <?php if (!empty($p['image'])): ?>
          <img src="/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <?php else: ?>
          <div class="product-img-placeholder">🧴</div>
          <?php endif; ?>
          <?php if ($p['stock'] === 0): ?>
          <div class="product-badge" style="background:var(--muted)">Out of Stock</div>
          <?php elseif ($p['stock'] <= $p['low_stock_threshold']): ?>
          <div class="product-badge">Low Stock</div>
          <?php endif; ?>
        </div>
        <div class="product-body">
          <div class="product-cat"><?= htmlspecialchars($p['category']) ?></div>
          <div class="product-name"><?= htmlspecialchars($dn) ?></div>
          <?php if (!empty($p['variants'])): ?>
          <div class="product-variants">
            <?php foreach(explode(',', $p['variants']) as $v): ?>
            <span class="variant-chip"><?= htmlspecialchars(trim($v)) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="product-moq">
            <span class="moq-badge">Min. Order: 100 pcs</span>
            <span class="moq-bulk">Bulk Available</span>
          </div>
          <div class="product-footer">
            <span class="product-stock stock-in">✓ Available</span>
            <button class="product-order-btn" onclick="event.stopPropagation();window.location='/product?id=<?= $p['id'] ?>'">Get A Quote</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<script>
function filterCat(cat, btn) {
  document.querySelectorAll('#catPills .cat-pill').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  var cards = document.querySelectorAll('#productsGrid .product-card');
  var shown = 0;
  cards.forEach(function(card) {
    var match = cat === 'all' || card.dataset.cat === cat;
    card.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('catCount').textContent = shown + ' product' + (shown !== 1 ? 's' : '');
}
</script>

<!-- CTA -->
<section style="background:var(--navy);padding:50px 0;text-align:center">
  <div class="container">
    <h2 style="color:#fff;font-size:26px;font-weight:800;margin-bottom:12px">Need help choosing?</h2>
    <p style="color:rgba(255,255,255,.6);margin-bottom:24px">Chat with us on WhatsApp — we'll help you find the right product for your brand.</p>
    <a href="https://wa.me/923255129241" class="btn btn-whatsapp btn-lg" target="_blank">💬 Ask on WhatsApp</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
