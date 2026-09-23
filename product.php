<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: /products'); exit; }

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { header('Location: /products'); exit; }

// Related products
$rel = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? AND stock > 0 LIMIT 4");
$rel->execute([$p['category'], $id]);
$related = $rel->fetchAll();

$displayName = preg_replace('/^ACM\s+/i', '', $p['name']);
$pageTitle = htmlspecialchars($displayName) . ' — ACM Asia Cosmetics';
$metaDesc  = 'Order ' . $displayName . ' from ACM Asia Cosmetics. Minimum order 100 pcs. Bulk manufacturing available across Pakistan.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="/">Home</a> /
      <a href="/products">Products</a> /
      <a href="/products?cat=<?= urlencode($p['category']) ?>"><?= htmlspecialchars($p['category']) ?></a> /
      <?= htmlspecialchars($p['name']) ?>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="product-detail-grid">
      <!-- Image -->
      <div>
        <div class="product-detail-img">
          <?php if (!empty($p['image'])): ?>
          <img src="/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($displayName) ?>">
          <?php else: ?>🧴<?php endif; ?>
        </div>
        <?php if ($p['sku']): ?>
        <div style="margin-top:14px;font-size:12px;color:var(--muted)">SKU: <?= htmlspecialchars($p['sku']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div>
        <div class="product-detail-cat"><?= htmlspecialchars($p['category']) ?></div>
        <h1 class="product-detail-name"><?= htmlspecialchars($displayName) ?></h1>

        <!-- MOQ Info -->
        <div class="moq-info-box">
          <div class="moq-info-row">
            <span class="moq-label">Minimum Order Qty</span>
            <span class="moq-value">100 pieces</span>
          </div>
          <div class="moq-info-row">
            <span class="moq-label">Bulk Discount</span>
            <span class="moq-value">Available on bulk orders</span>
          </div>
          <div class="moq-info-row">
            <span class="moq-label">Availability</span>
            <span class="moq-value stock-in">✓ Available</span>
          </div>
        </div>

        <div class="product-detail-desc">
          Premium quality <?= htmlspecialchars(strtolower($displayName)) ?> manufactured by ACM Asia Cosmetics. Made with carefully selected ingredients for the best results. Suitable for all skin types. Manufactured in Pakistan under strict quality control.
        </div>

        <!-- MOQ Selector -->
        <div class="moq-selector-wrap">
          <div style="font-size:14px;font-weight:700;color:var(--navy);margin-bottom:10px">Select Order Quantity (pieces)</div>
          <div class="moq-options">
            <?php foreach ([100,200,300,500,1000] as $qty): ?>
            <label class="moq-option <?= $qty===100?'selected':'' ?>">
              <input type="radio" name="moq_qty" value="<?= $qty ?>" <?= $qty===100?'checked':'' ?> onchange="updateMoq(<?= $qty ?>)">
              <span class="moq-qty-num"><?= $qty ?></span>
              <span class="moq-qty-label">pcs</span>
              <?php if($qty>=500): ?><span class="moq-discount-tag">Bulk</span><?php endif; ?>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:20px">
          <a id="waEnquireBtn" href="https://wa.me/923255129241?text=I+want+to+enquire+about+<?= urlencode($displayName) ?>+%28100+pcs%29" target="_blank" class="btn btn-whatsapp btn-lg" style="flex:1">💬 Enquire on WhatsApp</a>
        </div>

        <!-- Features -->
        <div style="margin-top:28px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">✅ Quality Certified</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">🏭 OEM Available</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">🏷️ Private Label</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">📦 Bulk Orders</div>
        </div>
      </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
    <div style="margin-top:70px">
      <h2 style="font-size:24px;font-weight:800;color:var(--navy);margin-bottom:24px">More from <?= htmlspecialchars($p['category']) ?></h2>
      <div class="products-grid">
        <?php foreach ($related as $r): ?>
        <div class="product-card" onclick="window.location='/product?id=<?= $r['id'] ?>'">
          <div class="product-img"><div class="product-img-placeholder">🧴</div></div>
          <div class="product-body">
            <div class="product-cat"><?= htmlspecialchars($r['category']) ?></div>
            <div class="product-name"><?= htmlspecialchars(preg_replace('/^ACM\s+/i', '', $r['name'])) ?></div>
            <div class="product-moq">
              <span class="moq-badge">Min. 100 pcs</span>
            </div>
            <div class="product-footer">
              <span class="product-stock stock-in">✓ Available</span>
              <button class="product-order-btn" onclick="event.stopPropagation();window.location='/product?id=<?= $r['id'] ?>'">Enquire</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<script>
const productName = <?= json_encode($displayName) ?>;
function updateMoq(qty) {
  document.querySelectorAll('.moq-option').forEach(el => el.classList.remove('selected'));
  const sel = document.querySelector('.moq-option input[value="' + qty + '"]');
  if (sel) sel.closest('.moq-option').classList.add('selected');
  const msg = 'I want to enquire about ' + productName + ' (' + qty + ' pcs)';
  document.getElementById('waEnquireBtn').href = 'https://wa.me/923255129241?text=' + encodeURIComponent(msg);
}
document.querySelectorAll('.moq-option').forEach(el => {
  el.addEventListener('click', function() {
    const val = this.querySelector('input').value;
    updateMoq(parseInt(val));
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
