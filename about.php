<?php
$pageTitle = 'About Us — ACM Asia Cosmetics & Manufactures';
$metaDesc  = 'Learn about ACM Asia Cosmetics & Manufactures — Pakistan\'s trusted skincare brand. Our story, mission, and commitment to quality.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero" style="text-align:center;padding:80px 0">
  <div class="container">
    <div class="section-badge" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8)">Our Story</div>
    <h1 style="margin-top:12px">About ACM Asia Cosmetics</h1>
    <p>Pakistan's trusted name in premium skincare & beauty</p>
  </div>
</div>

<!-- Mission -->
<section class="section" style="background:#fff">
  <div class="container about-grid">
    <div class="about-img">🌿</div>
    <div class="about-content">
      <div class="section-badge">Who We Are</div>
      <h2>Made in Pakistan, Trusted Nationwide</h2>
      <p>ACM Asia Cosmetics & Manufactures is a leading Pakistani cosmetics brand dedicated to producing high-quality, affordable skincare and beauty products for every skin type.</p>
      <p>Founded with a vision to make premium skincare accessible to everyone in Pakistan, we have grown from a small local manufacturer to a trusted name across the country.</p>
      <p>Our products are manufactured under strict quality control standards using carefully selected ingredients that are safe, effective, and suitable for all skin types.</p>
      <div class="stats-row">
        <div class="stat-box"><div class="stat-box-num">10+</div><div class="stat-box-label">Products</div></div>
        <div class="stat-box"><div class="stat-box-num">5K+</div><div class="stat-box-label">Customers</div></div>
        <div class="stat-box"><div class="stat-box-num">5+</div><div class="stat-box-label">Years</div></div>
      </div>
    </div>
  </div>
</section>

<!-- Values -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">Our Values</div>
      <h2 class="section-title">Why Choose ACM?</h2>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🔬</div>
        <div class="feature-title">Quality First</div>
        <div class="feature-desc">Every product is tested and verified before reaching you. We never compromise on quality.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🌿</div>
        <div class="feature-title">Safe Ingredients</div>
        <div class="feature-desc">We use dermatologically safe ingredients suitable for all skin types — even sensitive skin.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🇵🇰</div>
        <div class="feature-title">Made in Pakistan</div>
        <div class="feature-desc">Proudly made in Pakistan. Supporting local industry while delivering world-class quality.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💰</div>
        <div class="feature-title">Affordable</div>
        <div class="feature-desc">Premium quality doesn't have to be expensive. We keep prices fair for every Pakistani.</div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section style="background:var(--navy);padding:70px 0;text-align:center">
  <div class="container">
    <h2 style="color:#fff;font-weight:800;font-size:32px;margin-bottom:12px">Start Your Skincare Journey</h2>
    <p style="color:rgba(255,255,255,.6);margin-bottom:28px;font-size:16px">Explore our full range of products and find what works for your skin.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
      <a href="/products" class="btn btn-primary btn-lg">Shop Now →</a>
      <a href="/contact" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2)">Contact Us</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
