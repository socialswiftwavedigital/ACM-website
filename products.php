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
        $dn = $p['name']; ?>
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
            <button class="product-order-btn" onclick="event.stopPropagation();openQuoteModal(<?= $p['id'] ?>)">Get A Quote</button>
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
    <p style="color:rgba(255,255,255,.6);margin-bottom:24px">Chat with us on WhatsApp or request a quote — we'll help you find the right product for your brand.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
      <a href="https://wa.me/923255129241" target="_blank" style="display:inline-flex;align-items:center;gap:10px;background:#25D366;color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;padding:14px 28px;border-radius:8px;text-decoration:none;box-shadow:0 4px 20px rgba(37,211,102,.35)">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        WhatsApp Now
      </a>
      <button onclick="openQuoteModal()" style="display:inline-flex;align-items:center;background:var(--red);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;padding:14px 28px;border-radius:8px;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(220,38,38,.3)">Get A Quote →</button>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
