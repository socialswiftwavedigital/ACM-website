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
require_once __DIR__ . '/includes/cat-urls.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="/">Home</a> /
      <a href="/products">Products</a> /
      <a href="<?= catUrl($p['category']) ?>"><?= htmlspecialchars($p['category']) ?></a> /
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

        <?php
        $variantList = [];
        if (!empty($p['variants'])) {
            $variantList = array_map('trim', explode(',', $p['variants']));
        }
        $firstVariant = $variantList[0] ?? '';
        ?>

        <?php if (!empty($variantList)): ?>
        <!-- Variant Selector -->
        <div class="moq-selector-wrap" style="margin-bottom:18px">
          <div style="font-size:14px;font-weight:700;color:var(--navy);margin-bottom:10px">Select SPF</div>
          <div class="moq-options">
            <?php foreach ($variantList as $i => $v): ?>
            <label class="moq-option <?= $i===0?'selected':'' ?>">
              <input type="radio" name="product_variant" value="<?= htmlspecialchars($v) ?>" <?= $i===0?'checked':'' ?> onchange="updateVariant(this.value)">
              <span class="moq-qty-num" style="font-size:14px"><?= htmlspecialchars($v) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

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
          <a href="/order?product=<?= $p['id'] ?>" class="btn-get-quote">Get A Quote →</a>
          <a id="waEnquireBtn" href="https://wa.me/923255129241?text=<?= urlencode('I want to enquire about ' . $displayName . ($firstVariant ? ' ('.$firstVariant.')' : '') . ' (100 pcs)') ?>" target="_blank" class="btn-wa-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            WhatsApp
          </a>
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
              <button class="product-order-btn" onclick="event.stopPropagation();window.location='/product?id=<?= $r['id'] ?>'">Get A Quote</button>
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
let selectedVariant = <?= json_encode($firstVariant) ?>;
let selectedQty = 100;

function buildWaMsg() {
  const varPart = selectedVariant ? ' (' + selectedVariant + ')' : '';
  return 'I want to enquire about ' + productName + varPart + ' (' + selectedQty + ' pcs)';
}

function updateVariant(v) {
  selectedVariant = v;
  document.querySelectorAll('[name="product_variant"]').forEach(el => {
    el.closest('.moq-option').classList.toggle('selected', el.value === v);
  });
  document.getElementById('waEnquireBtn').href = 'https://wa.me/923255129241?text=' + encodeURIComponent(buildWaMsg());
}

function updateMoq(qty) {
  selectedQty = qty;
  document.querySelectorAll('[name="moq_qty"]').forEach(el => {
    el.closest('.moq-option').classList.toggle('selected', parseInt(el.value) === qty);
  });
  document.getElementById('waEnquireBtn').href = 'https://wa.me/923255129241?text=' + encodeURIComponent(buildWaMsg());
}

document.querySelectorAll('.moq-option').forEach(el => {
  el.addEventListener('click', function() {
    const input = this.querySelector('input');
    if (input.name === 'moq_qty') updateMoq(parseInt(input.value));
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
