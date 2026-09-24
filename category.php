<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/cat-urls.php';

$cat = $_GET['cat'] ?? '';
if (!$cat || !isset($CAT_URLS[$cat])) { header('Location: /products'); exit; }

$stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY name");
$stmt->execute([$cat]);
$products = $stmt->fetchAll();

$catMeta = [
  'Creams' => [
    'banner'  => 'slide-creams.png',
    'tagline' => 'Premium Face & Skin Creams',
    'sub'     => 'Vitamin C · Anti-Aging · Fairness · Moisturizing · Sunblock',
    'desc'    => 'ACM Asia Cosmetics manufactures a complete range of premium face and skin creams in Pakistan. From brightening and anti-aging formulas to fairness, moisturizing, and sun protection — our cream range covers every skin need. Each product is manufactured in our ISO-certified Lahore facility using pharmaceutical-grade ingredients. We offer private label manufacturing with full branding, custom formulation, and flexible MOQ starting from 100 pieces per variant.',
    'faqs'    => [
      ['q'=>'What types of face creams does ACM manufacture?','a'=>'ACM manufactures 17+ face and skin cream variants including Vitamin C Cream, Anti-Aging Cream, Fairness Cream, Brightening Cream, Night Cream, Moisturizing Cream, BB Cream, Sunblock SPF 30/50/80, Dark Spot Cream, Collagen Cream, Hydrating Cream, Soothing Cream, Under Eye Cream, and more.'],
      ['q'=>'Can I get custom formulations for face creams?','a'=>'Yes, our R&D team develops custom cream formulas tailored to your brand requirements, target audience, and skin concerns. We handle everything from formulation to final packaging.'],
      ['q'=>'What is the minimum order quantity for creams?','a'=>'Our standard MOQ is 100 pieces per variant. Bulk discounts are available for orders of 500 pieces or more.'],
      ['q'=>'Do your skin creams meet Pakistani regulatory standards?','a'=>'All ACM skin creams are manufactured under ISO quality standards and comply with DRAP regulations for cosmetic products in Pakistan.'],
    ],
  ],
  'Serums' => [
    'banner'  => 'slide-serums.png',
    'tagline' => 'Advanced Skin Treatment Serums',
    'sub'     => 'Hyaluronic Acid · Niacinamide · Glutathione · Vitamin C · Alpha Arbutin',
    'desc'    => 'ACM\'s skin treatment serum range combines powerful active ingredients with advanced delivery systems. Our serums include Hyaluronic Acid, Niacinamide, Glutathione, Vitamin C, Alpha Arbutin, Glycolic Acid, and Zinc PCA — each formulated for maximum skin penetration and efficacy. Manufactured in our Lahore facility under strict quality control, our serums are ideal for private label brands targeting the growing premium skincare market in Pakistan and internationally.',
    'faqs'    => [
      ['q'=>'What serum variants does ACM manufacture?','a'=>'We manufacture 11 serum variants including Hyaluronic Acid Serum, Niacinamide Serum, Glutathione Serum, Vitamin C Serum, Alpha Arbutin Serum, Glycolic Acid Serum, Zinc PCA Serum, All-in-One Serum, Multipurpose Serum, and more.'],
      ['q'=>'Are your serums suitable for all skin types?','a'=>'Yes, we formulate serums for all skin types — oily, dry, combination, and sensitive. Our team can customize formulas for specific skin concerns.'],
      ['q'=>'What packaging options are available for serums?','a'=>'We offer serums in dropper bottles, pump bottles, and tubes. Custom packaging with your brand logo and design is available for all variants.'],
      ['q'=>'Can I private label your serums under my brand?','a'=>'Absolutely. We specialize in private label manufacturing — you provide the branding and we handle production, filling, and packaging from start to finish.'],
    ],
  ],
  'Face Wash' => [
    'banner'  => 'slide-facewash.png',
    'tagline' => 'Daily Cleansing Face Wash Range',
    'sub'     => 'Foaming · Charcoal · Gold · Turmeric · Vitamin C · Rice · Herbal',
    'desc'    => 'ACM manufactures 14+ face wash variants for every skin type and concern. From gentle foaming and gel cleansers to activated charcoal, gold, and turmeric formulas — our face washes deliver deep cleansing without stripping the skin\'s natural moisture. All variants are available for private label manufacturing with custom formulations, fragrances, and packaging for brands across Pakistan and international markets.',
    'faqs'    => [
      ['q'=>'How many face wash variants does ACM offer?','a'=>'ACM offers 14+ face wash variants including Foaming, Gel, Charcoal, Gold, Turmeric, Rice, Vitamin C, Vitamin B3, Herbal, Men\'s, Brightening, Anti-Acne, Creamy, and Pearly Shine face washes.'],
      ['q'=>'Are your face washes suitable for sensitive skin?','a'=>'Yes, we have specific formulas for sensitive skin including our Creamy Face Wash and gentle herbal variants. Custom hypoallergenic formulations are also available.'],
      ['q'=>'Do you offer sulfate-free face wash manufacturing?','a'=>'Yes, we manufacture sulfate-free, paraben-free, and dermatologist-tested face wash formulas as per your brand and market requirements.'],
      ['q'=>'What is the shelf life of your face washes?','a'=>'Our face washes typically have a 24-month shelf life under standard storage conditions. Detailed stability data is available on request.'],
    ],
  ],
  'Petroleum Jelly' => [
    'banner'  => 'slide-pjelly.png',
    'tagline' => 'Multi-Purpose Petroleum Jelly',
    'sub'     => 'Original · Colored · Scented · Pharmaceutical Grade',
    'desc'    => 'ACM manufactures pharmaceutical-grade petroleum jelly in three core variants: Original (clear), Colored (tinted), and Scented. Our petroleum jelly is widely used for skin protection, lip care, wound healing, baby care, and dry skin relief. Manufactured using USP/BP-grade raw materials, our petroleum jelly is available for private label branding with custom jar sizes, colors, and fragrances.',
    'faqs'    => [
      ['q'=>'What petroleum jelly variants does ACM manufacture?','a'=>'We manufacture three variants: Original (clear), Colored (tinted in various shades), and Scented (with custom fragrances). Custom formulations are also available.'],
      ['q'=>'Is your petroleum jelly safe for babies?','a'=>'Yes, our original petroleum jelly is pharmaceutical-grade and gentle enough for baby skin care, diaper rash protection, and cradle cap treatment.'],
      ['q'=>'Can I get petroleum jelly in different jar sizes?','a'=>'Yes, we offer petroleum jelly in 50g, 100g, 250g, and 500g jar sizes. Custom sizes and packaging designs are available for bulk orders.'],
      ['q'=>'Is your petroleum jelly USP/BP grade?','a'=>'Yes, we use pharmaceutical-grade petroleum jelly meeting USP and BP standards as the base material in our manufacturing process.'],
    ],
  ],
  'Lotions' => [
    'banner'  => 'slide-lotions-v2.png',
    'tagline' => 'Moisturizing Body Lotions',
    'sub'     => 'Brightening · Vitamin C · Vitamin E · Niacinamide · Honey · Aloe Vera',
    'desc'    => 'ACM\'s body lotion range features 10 variants enriched with active ingredients for deep moisturization and skin nourishment. From brightening Vitamin C and protective Vitamin E to soothing Aloe Vera, Honey, and Cocoa Butter — our lotions cater to all skin types and body care needs. Available for private label manufacturing with custom fragrances, packaging, and formulations for retail and professional markets.',
    'faqs'    => [
      ['q'=>'What body lotion variants does ACM offer?','a'=>'We offer 10 body lotion variants: Brightening Lotion, Vitamin C Lotion, Vitamin E Lotion, Niacinamide Lotion, Honey Lotion, Aloe Vera Lotion, Cocoa Butter Lotion, Lavender Lotion, Multi-Purpose Lotion, and Cleansing Lotion.'],
      ['q'=>'Can I get custom scented body lotions?','a'=>'Yes, we create custom fragrances and scent profiles for body lotions. Our perfumers work with you to develop your brand\'s signature scent.'],
      ['q'=>'What packaging is available for body lotions?','a'=>'We offer lotion bottles, pump dispensers, squeeze tubes, and jars in various sizes. Custom packaging design with your branding is available.'],
      ['q'=>'Are your lotions dermatologist tested?','a'=>'Yes, our lotion formulations are tested for skin safety. Dermatologist test reports are available on request for private label orders.'],
    ],
  ],
  'Shampoo & Conditioner' => [
    'banner'  => 'slide-shampoo.png',
    'tagline' => 'Professional Hair Cleansing Range',
    'sub'     => 'Keratin · Anti-Dandruff · Sulphate-Free · Argan · Amla · Herbal',
    'desc'    => 'ACM\'s shampoo and conditioner range offers 15+ variants targeting every hair type and concern. From Keratin Repair and Argan Oil nourishment to Anti-Dandruff treatment and Sulphate-Free gentle cleansing — our hair care products deliver salon-quality results at scale. We offer complete private label hair care solutions with custom formulations, fragrances, and branded packaging for retail and professional markets.',
    'faqs'    => [
      ['q'=>'What shampoo variants does ACM manufacture?','a'=>'We manufacture 15+ shampoo variants including Keratin Shampoo, Herbal Shampoo, Anti-Dandruff Shampoo, Sulphate-Free Shampoo, Argan Oil Shampoo, Onion Shampoo, Color Protect Shampoo, Amla Shampoo, Men\'s Shampoo, Deep Cleansing Shampoo, Leave-in Conditioner, and more.'],
      ['q'=>'Do you manufacture sulfate-free shampoos?','a'=>'Yes, we manufacture sulfate-free, paraben-free, and silicone-free shampoo formulas for brands targeting organic and natural hair care markets.'],
      ['q'=>'Can you manufacture shampoo and conditioner as a set?','a'=>'Absolutely. We manufacture matching shampoo and conditioner sets with complementary formulas, ideal for complete private label hair care collections.'],
      ['q'=>'What sizes are available for shampoo bottles?','a'=>'We offer shampoo in 200ml, 300ml, 500ml, and 1L packaging. Sachet and travel-size packaging are also available for promotional use.'],
    ],
  ],
  'Baby & Kids' => [
    'banner'  => 'slide-kids.png',
    'tagline' => 'Gentle Baby & Kids Care',
    'sub'     => 'Baby Lotion · Baby Shampoo · Baby Cream · Body Wash · Baby Oil',
    'desc'    => 'ACM\'s baby and kids range is formulated with the gentlest ingredients for delicate skin. Our complete baby care line includes Baby Lotion, Baby Cream, Baby Shampoo, Baby Body Wash, Baby Face Wash, Baby Oil, Baby Petroleum Jelly, and Kids Shampoo — all manufactured without harsh chemicals, sulfates, or parabens for safe everyday use from newborn onwards.',
    'faqs'    => [
      ['q'=>'Are ACM baby products safe for newborns?','a'=>'Yes, our baby products are formulated with hypoallergenic, gentle ingredients free from sulfates and parabens. They are tested to be safe for newborn and sensitive baby skin.'],
      ['q'=>'What baby products does ACM manufacture?','a'=>'We manufacture 8 baby products: Baby Lotion, Baby Cream, Baby Shampoo, Baby Face Wash, Baby Body Wash, Baby Oil, Baby Petroleum Jelly, and Kids Shampoo.'],
      ['q'=>'Can I get tear-free baby shampoo?','a'=>'Yes, our standard baby shampoo is formulated to be tear-free and mild around the eyes, safe for daily use on infants and toddlers.'],
      ['q'=>'Do your baby products have dermatologist approval?','a'=>'Yes, our baby care formulations are dermatologically tested and designed to meet international safety standards for baby cosmetics.'],
    ],
  ],
  'Hair Care' => [
    'banner'  => null,
    'hero_bg' => 'var(--navy)',
    'hero_img'=> 'sh-keratin.png',
    'tagline' => 'Complete Hair Care Solutions',
    'sub'     => 'Hair Tonic · Hair Serum · Hair Oil · Hair Gel · Hair Cream',
    'desc'    => 'ACM\'s Hair Care range delivers targeted solutions for hair growth, strengthening, and styling. Our products include Hair Tonic, Hair Serum, Hair Oil, Hair Gel, and Hair Cream — each formulated with active ingredients like biotin, keratin, castor, and essential oils. Available for private label manufacturing with custom blends and professional packaging for men\'s grooming and salon brands.',
    'faqs'    => [
      ['q'=>'What hair care products does ACM manufacture?','a'=>'We manufacture 5 hair care products: Hair Tonic (for growth), Hair Serum (for smoothing), Hair Oil (for nourishment), Hair Gel (for styling), and Hair Cream (for conditioning and frizz control).'],
      ['q'=>'Can I get a custom hair oil blend?','a'=>'Yes, we create custom hair oil blends combining natural oils like coconut, argan, castor, jojoba, and essential oils based on your formula requirements.'],
      ['q'=>'Is your hair gel suitable for all hair types?','a'=>'Our hair gel is formulated for all hair types. We can customize the hold level (light, medium, or strong) and add specific ingredients for your target market.'],
      ['q'=>'Do you offer organic hair care formulations?','a'=>'Yes, we offer natural and organic hair care formulations using plant-based ingredients, free from silicones and synthetic chemicals.'],
    ],
  ],
  'Essential Oils' => [
    'banner'  => null,
    'hero_bg' => '#0a1a3e',
    'hero_img'=> 'lot-aloe-vera.png',
    'tagline' => 'Pure Natural Essential Oils',
    'sub'     => 'Coconut · Argan · Jojoba · Kalonji · Neem · Moringa · Amla · Onion',
    'desc'    => 'ACM manufactures 13 pure natural essential oils, cold-pressed and minimally processed to retain their natural potency. Our oil range includes Coconut, Argan, Jojoba, Neem, Moringa, Kalonji (Black Seed), Amla, Onion, Aloe Vera, Castor, Olive, Mustard, and Tea Tree oils. All oils are available for private label in glass dropper bottles, amber glass, and plastic packaging with custom branding.',
    'faqs'    => [
      ['q'=>'What essential oils does ACM manufacture?','a'=>'We manufacture 13 essential oil variants: Coconut Oil, Argan Oil, Jojoba Oil, Neem Oil, Moringa Oil, Kalonji (Black Seed) Oil, Amla Oil, Onion Oil, Aloe Vera Oil, Castor Oil, Olive Oil, Mustard Oil, and Tea Tree Oil.'],
      ['q'=>'Are your essential oils pure and undiluted?','a'=>'Yes, we manufacture pure, high-quality essential oils from natural plant sources. We supply 100% pure undiluted oils as well as diluted carrier oil blends as required.'],
      ['q'=>'What packaging is available for essential oils?','a'=>'We offer glass dropper bottles (10ml, 30ml), amber glass bottles (50ml, 100ml), and larger plastic bottles (200ml+). Custom label and packaging design is available.'],
      ['q'=>'Can I get a custom essential oil blend?','a'=>'Yes, we create custom oil blends for hair care, skin care, and wellness. Our team formulates based on your specific requirements and target benefits.'],
    ],
  ],
  'Facial' => [
    'banner'  => null,
    'hero_bg' => '#0f1f4a',
    'hero_img'=> 'fw-charcoal.png',
    'tagline' => 'Professional Facial Treatments',
    'sub'     => 'Charcoal Mask · Clay Mask · Brightening Mask · Face Scrub · Face Toner',
    'desc'    => 'ACM\'s facial care range provides professional-grade treatments for deep cleansing, exfoliation, and skin rejuvenation. Our Facial range includes Charcoal Mask, Clay Mask, Brightening Mask, Face Scrub, Face Pack, and Face Toner — all formulated for salon-quality results at home. Available for private label manufacturing with custom formulations and branded packaging for both retail and professional beauty markets.',
    'faqs'    => [
      ['q'=>'What facial products does ACM manufacture?','a'=>'We manufacture 6 facial care products: Charcoal Face Mask, Clay Face Mask, Brightening Face Mask, Face Scrub, Face Pack, and Face Toner. Custom formulations are also available.'],
      ['q'=>'Are your facial masks suitable for salon use?','a'=>'Yes, our facial products are formulated for both retail consumer use and professional salon applications. Larger professional-size packaging is available.'],
      ['q'=>'Can I get a custom face scrub formula?','a'=>'Yes, we develop custom face scrub formulas with various exfoliating agents including walnut shell powder, sugar crystals, and natural microbeads.'],
      ['q'=>'Are your facial products free from harmful chemicals?','a'=>'Yes, all facial care products are formulated without harmful chemicals. We use dermatologist-approved, safe ingredients suitable for regular use on all skin types.'],
    ],
  ],
];

$d = $catMeta[$cat];
$pageTitle = $d['tagline'] . ' — ACM Asia Cosmetics Pakistan';
$metaDesc  = 'Buy wholesale ' . $cat . ' from ACM Asia Cosmetics Pakistan. Private label manufacturing, MOQ 100 pcs. ' . substr(strip_tags($d['desc']), 0, 130);
require_once __DIR__ . '/includes/header.php';
?>

<?php
/* ── HERO BANNER ─────────────────────────────── */
if ($d['banner']):
?>
<div class="cat-hero" style="background-image:url('/images/<?= htmlspecialchars($d['banner']) ?>')">
  <div class="cat-hero-overlay"></div>
  <div class="container cat-hero-content">
    <div class="cat-hero-tag"><?= htmlspecialchars($cat) ?></div>
    <h1 class="cat-hero-title"><?= htmlspecialchars($d['tagline']) ?></h1>
    <p class="cat-hero-sub"><?= htmlspecialchars($d['sub']) ?></p>
    <div class="cat-hero-actions">
      <a href="https://wa.me/923255129241?text=<?= urlencode('I want to enquire about ' . $cat) ?>" target="_blank" class="btn-wa-lg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        WhatsApp Enquiry
      </a>
      <a href="/order" class="btn-get-quote">Get A Quote →</a>
    </div>
  </div>
</div>
<?php else: ?>
<div class="cat-hero cat-hero-plain" style="background:<?= $d['hero_bg'] ?>">
  <div class="container cat-hero-content">
    <div>
      <div class="cat-hero-tag"><?= htmlspecialchars($cat) ?></div>
      <h1 class="cat-hero-title"><?= htmlspecialchars($d['tagline']) ?></h1>
      <p class="cat-hero-sub"><?= htmlspecialchars($d['sub']) ?></p>
      <div class="cat-hero-actions">
        <a href="https://wa.me/923255129241?text=<?= urlencode('I want to enquire about ' . $cat) ?>" target="_blank" class="btn-wa-lg">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
          WhatsApp Enquiry
        </a>
        <a href="/order" class="btn-get-quote">Get A Quote →</a>
      </div>
    </div>
    <?php if (!empty($d['hero_img'])): ?>
    <img src="/images/<?= htmlspecialchars($d['hero_img']) ?>" alt="<?= htmlspecialchars($cat) ?>" class="cat-hero-product-img">
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Breadcrumb -->
<div style="background:#f8f9fc;border-bottom:1px solid var(--border);padding:10px 0">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / <a href="/products">Products</a> / <?= htmlspecialchars($cat) ?></div>
  </div>
</div>

<!-- Products -->
<section class="section">
  <div class="container">
    <div class="cat-section-hdr">
      <div>
        <p class="cat-section-tag">OUR RANGE</p>
        <h2 class="cat-section-title"><?= htmlspecialchars($d['tagline']) ?></h2>
      </div>
      <span class="cat-count-badge"><?= count($products) ?> Products</span>
    </div>

    <?php if (empty($products)): ?>
    <div style="text-align:center;padding:60px 0;color:var(--muted)">
      <div style="font-size:40px;margin-bottom:12px">🧴</div>
      <div style="font-size:16px;font-weight:700;color:var(--navy)">Coming Soon</div>
    </div>
    <?php else: ?>
    <div class="products-grid">
      <?php foreach ($products as $p):
        $displayName = preg_replace('/^ACM\s+/i', '', $p['name']); ?>
      <div class="product-card" onclick="window.location='/product?id=<?= $p['id'] ?>'">
        <div class="product-img">
          <?php if (!empty($p['image'])): ?>
          <img src="/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <?php else: ?>
          <div class="product-img-placeholder">🧴</div>
          <?php endif; ?>
          <?php if ($p['stock'] <= $p['low_stock_threshold'] && $p['stock'] > 0): ?>
          <div class="product-badge">Low Stock</div>
          <?php endif; ?>
        </div>
        <div class="product-body">
          <div class="product-cat"><?= htmlspecialchars($p['category']) ?></div>
          <div class="product-name"><?= htmlspecialchars($displayName) ?></div>
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

<!-- SEO Content -->
<section class="section" style="background:var(--offwhite)">
  <div class="container" style="max-width:860px">
    <p class="cat-section-tag">ABOUT</p>
    <h2 class="cat-section-title" style="margin-bottom:20px"><?= htmlspecialchars($cat) ?> Manufacturing in Pakistan</h2>
    <p style="font-size:15px;line-height:1.85;color:#444"><?= nl2br(htmlspecialchars($d['desc'])) ?></p>
    <div class="cat-features-grid">
      <div class="cat-feature"><span class="cat-feature-icon">🏭</span><div><strong>OEM Manufacturing</strong><br><span>Full-service contract manufacturing</span></div></div>
      <div class="cat-feature"><span class="cat-feature-icon">🏷️</span><div><strong>Private Label</strong><br><span>Your brand, our quality</span></div></div>
      <div class="cat-feature"><span class="cat-feature-icon">✅</span><div><strong>ISO Certified</strong><br><span>Strict quality at every step</span></div></div>
      <div class="cat-feature"><span class="cat-feature-icon">📦</span><div><strong>MOQ 100 Pcs</strong><br><span>Bulk discounts available</span></div></div>
    </div>
  </div>
</section>

<!-- FAQs -->
<section class="section">
  <div class="container" style="max-width:800px">
    <p class="cat-section-tag">FAQ</p>
    <h2 class="cat-section-title" style="margin-bottom:28px"><?= htmlspecialchars($cat) ?> — Frequently Asked Questions</h2>
    <div class="faq-list">
      <?php foreach ($d['faqs'] as $faq): ?>
      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          <?= htmlspecialchars($faq['q']) ?> <span class="faq-icon">+</span>
        </button>
        <div class="faq-a"><?= htmlspecialchars($faq['a']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section style="background:var(--navy);padding:60px 0;text-align:center">
  <div class="container">
    <h2 style="color:#fff;font-size:28px;font-weight:800;margin-bottom:10px">Ready to Order <?= htmlspecialchars($cat) ?>?</h2>
    <p style="color:rgba(255,255,255,.65);margin-bottom:28px;font-size:15px">Contact us on WhatsApp for pricing, samples, and private label options.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
      <a href="https://wa.me/923255129241?text=<?= urlencode('Hi, I want to order ' . $cat . ' (private label)') ?>" target="_blank" class="btn btn-whatsapp btn-lg">💬 WhatsApp Now</a>
      <a href="/order" class="btn btn-lg" style="background:#fff;color:var(--navy);font-weight:800">Get A Quote →</a>
    </div>
  </div>
</section>

<style>
/* ── Category Hero ───────────────────────── */
.cat-hero {
  position: relative;
  min-height: 420px;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
}
.cat-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(13,34,88,.85) 45%, rgba(13,34,88,.35));
}
.cat-hero-plain { min-height: 360px; }
.cat-hero-content {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
}
.cat-hero-tag {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 2px;
  color: var(--red);
  text-transform: uppercase;
  margin-bottom: 10px;
}
.cat-hero-title {
  font-size: clamp(28px, 5vw, 48px);
  font-weight: 900;
  color: #fff;
  line-height: 1.1;
  margin-bottom: 12px;
}
.cat-hero-sub {
  color: rgba(255,255,255,.75);
  font-size: 14px;
  margin-bottom: 28px;
}
.cat-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.cat-hero-product-img {
  width: 280px;
  max-width: 40%;
  object-fit: contain;
  border-radius: 12px;
  flex-shrink: 0;
}
@media(max-width:640px) {
  .cat-hero-product-img { display: none; }
  .cat-hero { min-height: 320px; }
}

/* ── Section headers ─────────────────────── */
.cat-section-tag {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 2px;
  color: var(--red);
  text-transform: uppercase;
  margin-bottom: 8px;
}
.cat-section-title {
  font-size: clamp(22px, 4vw, 32px);
  font-weight: 900;
  color: var(--navy);
  margin-bottom: 32px;
}
.cat-section-hdr {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 32px;
  flex-wrap: wrap;
}
.cat-section-hdr .cat-section-title { margin-bottom: 0; }
.cat-count-badge {
  font-size: 13px;
  font-weight: 700;
  background: var(--navy);
  color: #fff;
  padding: 6px 16px;
  border-radius: 20px;
  white-space: nowrap;
  flex-shrink: 0;
}

/* ── Features grid ───────────────────────── */
.cat-features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-top: 36px;
}
.cat-feature {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  background: #fff;
  border-radius: 12px;
  padding: 18px 20px;
  box-shadow: 0 2px 8px rgba(13,34,88,.06);
}
.cat-feature-icon { font-size: 26px; flex-shrink: 0; }
.cat-feature strong { display: block; font-size: 14px; font-weight: 700; color: var(--navy); margin-bottom: 2px; }
.cat-feature span { font-size: 12px; color: var(--muted); }

/* ── FAQ reuse ───────────────────────────── */
.faq-list { display: flex; flex-direction: column; gap: 12px; }
.faq-item { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(13,34,88,.07); }
.faq-q { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 20px 24px; background: none; border: none; font-family: 'Poppins',sans-serif; font-size: 15px; font-weight: 700; color: var(--navy); cursor: pointer; text-align: left; }
.faq-icon { font-size: 22px; font-weight: 400; flex-shrink: 0; transition: transform .25s; }
.faq-a { max-height: 0; overflow: hidden; padding: 0 24px; font-size: 14px; color: #555; line-height: 1.75; transition: max-height .3s ease, padding .3s ease; }
.faq-item.open .faq-a { max-height: 200px; padding: 0 24px 20px; }
.faq-item.open .faq-icon { transform: rotate(45deg); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
