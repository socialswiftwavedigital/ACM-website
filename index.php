<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'ACM Asia Cosmetics & Manufactures — Premium Skincare Pakistan';
$metaDesc  = 'Shop premium skincare & beauty products by ACM Asia Cosmetics. Whitening creams, serums, face wash & more. Made in Pakistan, delivered nationwide.';

// Fetch featured products
$featured = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY id LIMIT 8")->fetchAll();

// Fetch categories
$cats = $pdo->query("SELECT DISTINCT category FROM products WHERE stock > 0 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Hero ── -->
<section class="hero">
  <div class="container hero-inner">
    <div class="hero-content">
      <div class="hero-badge">🌿 100% Premium Quality</div>
      <h1 class="hero-title">
        Glow Naturally with<br>
        <span>ACM Cosmetics</span>
      </h1>
      <p class="hero-sub">
        Discover our range of premium skincare & beauty products. Crafted with the finest ingredients, trusted by thousands across Pakistan.
      </p>
      <div class="hero-ctas">
        <a href="/products" class="btn btn-primary btn-lg">Shop Now →</a>
        <a href="/order" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2)">Place Order</a>
      </div>
      <div class="hero-stats">
        <div>
          <div class="hero-stat-num">10+</div>
          <div class="hero-stat-label">Products</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.1)"></div>
        <div>
          <div class="hero-stat-num">5K+</div>
          <div class="hero-stat-label">Happy Customers</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.1)"></div>
        <div>
          <div class="hero-stat-num">100%</div>
          <div class="hero-stat-label">Authentic</div>
        </div>
      </div>
    </div>
    <div class="hero-img">
      <img src="/assets/hero.jpg" alt="ACM Products" onerror="this.parentElement.style.display='none'">
    </div>
  </div>
</section>

<!-- ── USPs ── -->
<section class="section-sm" style="background:#fff;border-bottom:1px solid var(--border)">
  <div class="container">
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🚚</div>
        <div class="feature-title">Fast Delivery</div>
        <div class="feature-desc">Nationwide delivery in 2-3 business days. Free shipping above Rs. 2,000.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">✅</div>
        <div class="feature-title">100% Authentic</div>
        <div class="feature-desc">All products are genuine & dermatologically tested. No fakes, guaranteed.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💰</div>
        <div class="feature-title">Best Prices</div>
        <div class="feature-desc">Direct from manufacturer. No middleman = best price for you.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💬</div>
        <div class="feature-title">24/7 Support</div>
        <div class="feature-desc">WhatsApp us anytime. Our team is always ready to help you.</div>
      </div>
    </div>
  </div>
</section>

<!-- ── Featured Products ── -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">Our Products</div>
      <h2 class="section-title">Best Selling Products</h2>
      <p class="section-sub">Shop our most popular skincare & beauty products trusted by thousands of customers.</p>
    </div>

    <!-- Category Filter -->
    <div class="cat-pills">
      <a href="/products" class="cat-pill active">All Products</a>
      <?php foreach ($cats as $cat): ?>
      <a href="/products?cat=<?= urlencode($cat) ?>" class="cat-pill"><?= htmlspecialchars($cat) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="products-grid">
      <?php foreach ($featured as $p): ?>
      <div class="product-card" onclick="window.location='/product?id=<?= $p['id'] ?>'">
        <div class="product-img">
          <div class="product-img-placeholder">🧴</div>
          <?php if ($p['stock'] <= $p['low_stock_threshold']): ?>
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
            <button class="product-order-btn" onclick="event.stopPropagation();window.location='/order?product=<?= $p['id'] ?>'">
              Order Now
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:40px">
      <a href="/products" class="btn btn-navy btn-lg">View All Products →</a>
    </div>
  </div>
</section>

<!-- ── CTA Banner ── -->
<section style="background:linear-gradient(135deg,var(--navy),#1a3580);padding:70px 0;text-align:center">
  <div class="container">
    <h2 style="font-size:clamp(24px,3vw,38px);font-weight:800;color:#fff;margin-bottom:14px">Ready to Glow?</h2>
    <p style="color:rgba(255,255,255,.6);font-size:16px;margin-bottom:32px">Place your order now & get it delivered to your doorstep.</p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
      <a href="/order" class="btn btn-primary btn-lg">Place Order Now</a>
      <a href="https://wa.me/923000000000" class="btn btn-lg btn-whatsapp" target="_blank">💬 Order via WhatsApp</a>
    </div>
  </div>
</section>

<!-- ── About Snippet ── -->
<section class="section" style="background:#fff">
  <div class="container about-grid">
    <div class="about-img">🏭</div>
    <div class="about-content">
      <div class="section-badge">About ACM</div>
      <h2>Pakistan's Trusted Cosmetics Brand</h2>
      <p>ACM Asia Cosmetics & Manufactures is a leading Pakistani cosmetics brand committed to producing high-quality, affordable skincare and beauty products.</p>
      <p>Our products are crafted using premium ingredients and manufactured under strict quality control standards, ensuring safety and effectiveness for all skin types.</p>
      <div class="stats-row">
        <div class="stat-box">
          <div class="stat-box-num">10+</div>
          <div class="stat-box-label">Products</div>
        </div>
        <div class="stat-box">
          <div class="stat-box-num">5K+</div>
          <div class="stat-box-label">Customers</div>
        </div>
        <div class="stat-box">
          <div class="stat-box-num">5+</div>
          <div class="stat-box-label">Years</div>
        </div>
      </div>
      <a href="/about" class="btn btn-navy" style="margin-top:24px">Read Our Story →</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
