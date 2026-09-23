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

$pageTitle = htmlspecialchars($p['name']) . ' — ACM Asia Cosmetics';
$metaDesc  = 'Buy ' . $p['name'] . ' by ACM Asia Cosmetics. Rs. ' . number_format($p['price']) . '. Fast delivery across Pakistan.';
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
        <div class="product-detail-img">🧴</div>
        <?php if ($p['sku']): ?>
        <div style="margin-top:14px;font-size:12px;color:var(--muted)">SKU: <?= htmlspecialchars($p['sku']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div>
        <div class="product-detail-cat"><?= htmlspecialchars($p['category']) ?></div>
        <h1 class="product-detail-name"><?= htmlspecialchars($p['name']) ?></h1>
        <div class="product-detail-price">
          Rs. <?= number_format($p['price']) ?>
          <small>/ piece (inc. tax)</small>
        </div>

        <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px">
          <span class="<?= $p['stock'] > 0 ? 'stock-in' : 'stock-out' ?>" style="font-size:14px;font-weight:600">
            <?= $p['stock'] > 0 ? '✓ In Stock (' . $p['stock'] . ' available)' : '✗ Out of Stock' ?>
          </span>
        </div>

        <div class="product-detail-desc">
          Premium quality <?= htmlspecialchars(strtolower($p['name'])) ?> by ACM Asia Cosmetics. Made with carefully selected ingredients for the best results. Suitable for all skin types. Manufactured in Pakistan under strict quality control.
        </div>

        <?php if ($p['stock'] > 0): ?>
        <form action="/order" method="GET">
          <input type="hidden" name="product" value="<?= $p['id'] ?>">
          <div class="qty-selector">
            <span style="font-size:14px;font-weight:600;color:var(--navy)">Quantity:</span>
            <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
            <span class="qty-num" id="qtyNum">1</span>
            <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
          </div>
          <input type="hidden" name="qty" id="qtyInput" value="1">
          <div style="display:flex;gap:12px;flex-wrap:wrap">
            <button type="submit" class="btn btn-primary btn-lg" style="flex:1">Order Now →</button>
            <a href="https://wa.me/923000000000?text=I+want+to+order+<?= urlencode($p['name']) ?>+(Rs.+<?= $p['price'] ?>)" target="_blank" class="btn btn-whatsapp btn-lg">💬 WhatsApp</a>
          </div>
        </form>
        <?php else: ?>
        <a href="https://wa.me/923000000000?text=Is+<?= urlencode($p['name']) ?>+available?" target="_blank" class="btn btn-whatsapp btn-lg">💬 Ask on WhatsApp</a>
        <?php endif; ?>

        <!-- Features -->
        <div style="margin-top:28px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">✅ Authentic Product</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">🚚 Fast Delivery</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">🔄 Easy Returns</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">💰 Best Price</div>
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
            <div class="product-name"><?= htmlspecialchars($r['name']) ?></div>
            <div class="product-price">Rs. <?= number_format($r['price']) ?></div>
            <div class="product-footer">
              <span class="product-stock stock-in">✓ In Stock</span>
              <button class="product-order-btn" onclick="event.stopPropagation();window.location='/order?product=<?= $r['id'] ?>'">Order</button>
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
function changeQty(d) {
  const el = document.getElementById('qtyNum');
  const inp = document.getElementById('qtyInput');
  let v = Math.max(1, parseInt(el.textContent) + d);
  el.textContent = v;
  inp.value = v;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
