<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'ACM Asia Cosmetics & Manufactures — Pakistan\'s Premier Cosmetics Manufacturer';
$metaDesc  = 'ACM Asia Cosmetics & Manufactures: Pakistan\'s leading private label cosmetics manufacturer. Creams, serums, face wash, lotions & more. OEM manufacturing for your brand.';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Slider -->
<section class="hero-slider" id="hero-slider">
  <?php
  $slides = [
    ['tag'=>'SKINCARE','title'=>'CREAMS','lines'=>['Vitamin C · Anti-Aging · Anti-Acne','Anti-Freckles · Vitamin B3'],'btn'=>'EXPLORE CREAMS','link'=>'/products?cat=Creams','img'=>'slide-creams.png'],
    ['tag'=>'TREATMENT','title'=>'SERUMS','lines'=>['Hyaluronic Acid · Vitamin C · Niacinamide','Glutathione · Alpha Arbutin · Zinc PCA'],'btn'=>'EXPLORE SERUMS','link'=>'/products?cat=Serums','img'=>'slide-serums.png'],
    ['tag'=>'CLEANSING','title'=>'FACE WASH','lines'=>['Foaming · Charcoal · Gold · Turmeric','Vitamin C · Rice · Herbal · 14 Variants'],'btn'=>'EXPLORE FACE WASH','link'=>'/products?cat=Face+Wash','img'=>'slide-facewash.png'],
    ['tag'=>'PROTECTION','title'=>'PETROLEUM<br>JELLY','lines'=>['Colored · Original · Scented','Pharmaceutical Grade Skin Care'],'btn'=>'EXPLORE PETROLEUM JELLY','link'=>'/products?cat=Petroleum+Jelly','img'=>'slide-pjelly.png'],
    ['tag'=>'MOISTURISING','title'=>'LOTIONS','lines'=>['Brightening · Niacinamide · Vitamin C','Honey · Aloe Vera · 10 Variants'],'btn'=>'EXPLORE LOTIONS','link'=>'/products?cat=Lotions','img'=>'slide-lotions.png'],
    ['tag'=>'HAIR CARE','title'=>'SHAMPOO<br>& CONDITIONER','lines'=>['Keratin · Anti-Dandruff · Amla','Anti-Lice · Conditioner · 15 Variants'],'btn'=>'EXPLORE SHAMPOO','link'=>'/products?cat=Shampoo+%26+Conditioner','img'=>'slide-shampoo.png'],
    ['tag'=>'BABY CARE','title'=>'BABY &<br>KIDS','lines'=>['Baby Lotion · Baby Cream · Baby Shampoo','Body Wash · Face Wash · Gentle Care'],'btn'=>'EXPLORE BABY RANGE','link'=>'/products?cat=Baby+%26+Kids','img'=>'slide-kids.png'],
  ];
  foreach ($slides as $i => $s): ?>
  <div class="slide <?= $i===0?'active':'' ?>" data-index="<?= $i ?>" style="background-image:url('/images/<?= $s['img'] ?>')">
    <div class="slide-overlay"></div>
    <div class="slide-content">
      <div class="slide-tag"><?= $s['tag'] ?></div>
      <h1 class="slide-title"><?= $s['title'] ?></h1>
      <div class="slide-variants">
        <?php foreach($s['lines'] as $line): ?>
        <p><?= $line ?></p>
        <?php endforeach; ?>
      </div>
      <a href="<?= $s['link'] ?>" class="btn-slide"><?= $s['btn'] ?></a>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Controls -->
  <div class="slider-dots">
    <?php foreach($slides as $i=>$s): ?>
    <button class="dot <?= $i===0?'active':'' ?>" onclick="goSlide(<?= $i ?>)"></button>
    <?php endforeach; ?>
  </div>
  <button class="slider-arrow slider-prev" onclick="prevSlide()">&#8249;</button>
  <button class="slider-arrow slider-next" onclick="nextSlide()">&#8250;</button>
</section>

<!-- Category Ticker -->
<div class="cat-ticker">
  <div class="cat-ticker-track">
    <?php
    $cats = 'CREAMS ✦ SERUMS ✦ FACE WASH ✦ PETROLEUM JELLY ✦ BODY LOTIONS ✦ SHAMPOO & CONDITIONER ✦ HAIR CARE ✦ ESSENTIAL OILS ✦ BABY & KIDS ✦ FACIAL ✦ ACM ASIA COSMETICS & MANUFACTURES ✦ &nbsp;&nbsp;';
    echo str_repeat("<span>$cats</span>", 3);
    ?>
  </div>
</div>

<!-- About Section -->
<section class="about-section">
  <div class="about-wrap">
    <div class="about-text reveal-left">
      <div class="section-eyebrow">WHO WE ARE</div>
      <h2 class="about-heading">YOUR TRUSTED<br><span>BEAUTY PARTNER</span></h2>
      <p>ACM Asia Cosmetics & Manufactures Private Limited is Pakistan's premium cosmetics manufacturing company. We formulate, produce and supply world-class beauty products that meet the highest international quality standards.</p>
      <p>From concept to finished product, our state-of-the-art facility handles everything: R&D, formulation, filling, packaging and quality control, all under one roof.</p>
      <div class="about-stats">
        <div class="astat"><div class="astat-num">100+</div><div class="astat-label">PRODUCTS</div></div>
        <div class="astat"><div class="astat-num">10</div><div class="astat-label">CATEGORIES</div></div>
        <div class="astat"><div class="astat-num">100%</div><div class="astat-label">QUALITY TESTED</div></div>
        <div class="astat"><div class="astat-num">10+</div><div class="astat-label">YRS EXPERIENCE</div></div>
      </div>
    </div>
    <div class="about-img-wrap reveal-right">
      <div class="about-product-grid">
        <div class="apg-item apg-wide"><img src="/images/slide-creams.png" alt="Creams"></div>
        <div class="apg-item"><img src="/images/serum-hyaluronic.png" alt="Serum"></div>
        <div class="apg-item"><img src="/images/fw-charcoal.png" alt="Face Wash"></div>
        <div class="apg-item"><img src="/images/lot-brightening.png" alt="Lotion"></div>
        <div class="apg-item"><img src="/images/pj-original.png" alt="Petroleum Jelly"></div>
        <div class="apg-item"><img src="/images/sh-keratin.png" alt="Shampoo"></div>
        <div class="apg-item"><img src="/images/kids-baby-lotion.png" alt="Baby"></div>
        <div class="apg-item"><img src="/images/cream-anti-aging.png" alt="Cream"></div>
      </div>
    </div>
  </div>
</section>

<!-- Product Range -->
<section class="range-section">
  <div class="range-wrap">
    <div class="section-eyebrow">OUR RANGE</div>
    <h2 class="range-heading">COMPLETE BEAUTY RANGE</h2>
    <div class="range-grid">
      <?php
      $range = [
        ['variants'=>'20','tag'=>'SKINCARE','cat'=>'CREAMS','desc'=>'Vitamin C, Night, Fairness, BB Cream, Moisturizer, Sunblock SPF 30–80, Soothing Lotion & more.','img'=>'cream-vitamin-c.png','link'=>'/products?cat=Creams'],
        ['variants'=>'11','tag'=>'TREATMENT','cat'=>'SERUMS','desc'=>'Hyaluronic Acid, Glycolic Acid, All-in-One, Multipurpose, Glutathione, Niacinamide & more.','img'=>'serum-hyaluronic.png','link'=>'/products?cat=Serums'],
        ['variants'=>'14','tag'=>'CLEANSING','cat'=>'FACE WASH','desc'=>'Foaming, Gel, Charcoal, Gold, Turmeric, Rice, Vitamin C & more: for every skin type.','img'=>'fw-charcoal.png','link'=>'/products?cat=Face+Wash'],
        ['variants'=>'3','tag'=>'PROTECTION','cat'=>'PETROLEUM JELLY','desc'=>'Colored, Original & Scented: pharmaceutical grade petroleum jelly for skin & lip care.','img'=>'pj-original.png','link'=>'/products?cat=Petroleum+Jelly'],
        ['variants'=>'10','tag'=>'MOISTURISING','cat'=>'LOTIONS','desc'=>'Brightening, Niacinamide, Vitamin C, Honey, Cocoa Butter, Aloe Vera & more body lotions.','img'=>'lot-brightening.png','link'=>'/products?cat=Lotions'],
        ['variants'=>'23','tag'=>'HAIR CLEANSING','cat'=>'SHAMPOO & CONDITIONER','desc'=>'Keratin, Deep, Leave-in, Argan, Onion, Color Protect & complete hair range.','img'=>'sh-keratin.png','link'=>'/products?cat=Shampoo+%26+Conditioner'],
        ['variants'=>'8','tag'=>'BABY CARE','cat'=>'BABY & KIDS','desc'=>'Baby Lotion, Cream, Shampoo, Face Wash, Body Wash, Oil & more: gentle care for little ones.','img'=>'kids-baby-lotion.png','link'=>'/products?cat=Baby+%26+Kids'],
        ['variants'=>'5','tag'=>'HAIR SOLUTIONS','cat'=>'HAIR CARE','desc'=>'Hair Tonic, Hair Serum, Hair Oil, Hair Gel & Hair Cream: complete hair styling & treatment.','img'=>'sh-mens.png','link'=>'/products?cat=Hair+Care'],
        ['variants'=>'13','tag'=>'NATURAL OILS','cat'=>'ESSENTIAL OILS','desc'=>'Coconut, Argan, Jojoba, Neem, Moringa, Kalonji, Amla, Onion & more pure natural oils.','img'=>'lot-aloe-vera.png','link'=>'/products?cat=Essential+Oils'],
        ['variants'=>'6','tag'=>'SKIN CARE','cat'=>'FACIAL','desc'=>'Charcoal Mask, Clay Mask, Brightening Mask, Face Scrub, Face Pack & Face Toner.','img'=>'fw-charcoal.png','link'=>'/products?cat=Facial'],
      ];
      foreach($range as $i => $r): ?>
      <div class="range-card reveal" style="transition-delay:<?= ($i%3)*.12 ?>s" onclick="window.location='<?= $r['link'] ?>'">
        <div class="range-card-img">
          <img src="/images/<?= $r['img'] ?>" alt="<?= $r['cat'] ?>" onerror="this.style.opacity='.1'">
        </div>
        <div class="range-card-body">
          <div class="range-variants"><?= $r['variants'] ?> VARIANTS</div>
          <div class="range-tag"><?= $r['tag'] ?></div>
          <div class="range-cat"><?= $r['cat'] ?></div>
          <p class="range-desc"><?= $r['desc'] ?></p>
          <span class="range-link">VIEW MORE →</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Why ACM -->
<section class="why-section">
  <div class="why-wrap">
    <div class="section-eyebrow">WHY ACM</div>
    <h2 class="why-heading">BUILT ON QUALITY<br>& TRUST</h2>
    <div class="why-grid">
      <div class="why-card reveal delay-1">
        <div class="why-icon">
          <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="20" width="40" height="24" rx="2" fill="rgba(220,38,38,.15)" stroke="#DC2626" stroke-width="1.5"/>
            <rect x="10" y="28" width="6" height="6" rx="1" fill="#DC2626"/>
            <rect x="21" y="28" width="6" height="6" rx="1" fill="#DC2626"/>
            <rect x="32" y="28" width="6" height="16" rx="1" fill="#DC2626"/>
            <path d="M4 20L24 6L44 20" stroke="#DC2626" stroke-width="1.5" stroke-linejoin="round"/>
            <rect x="18" y="34" width="12" height="10" rx="1" fill="rgba(220,38,38,.3)"/>
            <line x1="24" y1="6" x2="24" y2="2" stroke="#DC2626" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="24" cy="2" r="1.5" fill="#DC2626"/>
          </svg>
        </div>
        <h3>MODERN FACILITY</h3>
        <p>State-of-the-art manufacturing plant with automated filling, mixing and packaging lines.</p>
      </div>
      <div class="why-card reveal delay-2">
        <div class="why-icon">
          <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6h12v18l6 16H12L18 24V6z" fill="rgba(220,38,38,.12)" stroke="#DC2626" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M15 34h18" stroke="#DC2626" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M13 38h22" stroke="#DC2626" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="22" cy="30" r="2" fill="#DC2626"/>
            <circle cx="28" cy="28" r="1.5" fill="#DC2626" opacity=".7"/>
            <rect x="21" y="6" width="6" height="2" rx="1" fill="#DC2626"/>
            <line x1="24" y1="10" x2="24" y2="22" stroke="#DC2626" stroke-width="1" stroke-dasharray="2 2"/>
          </svg>
        </div>
        <h3>R&amp;D LAB</h3>
        <p>In-house research and development team constantly innovating superior formulations.</p>
      </div>
      <div class="why-card reveal delay-3">
        <div class="why-icon">
          <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 4L6 12v14c0 10 7.8 19.3 18 21 10.2-1.7 18-11 18-21V12L24 4z" fill="rgba(220,38,38,.12)" stroke="#DC2626" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M15 24l6 6 12-12" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3>QUALITY ASSURED</h3>
        <p>Every batch tested for safety, efficacy and stability before leaving our facility.</p>
      </div>
      <div class="why-card reveal delay-4">
        <div class="why-icon">
          <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8" y="14" width="32" height="26" rx="2" fill="rgba(220,38,38,.12)" stroke="#DC2626" stroke-width="1.5"/>
            <path d="M16 14v-2a8 8 0 0116 0v2" stroke="#DC2626" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="24" cy="27" r="4" fill="rgba(220,38,38,.3)" stroke="#DC2626" stroke-width="1.5"/>
            <line x1="24" y1="31" x2="24" y2="36" stroke="#DC2626" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <h3>PRIVATE LABEL</h3>
        <p>Full private label and custom branding services for your cosmetics brand.</p>
      </div>
    </div>
  </div>
</section>

<!-- Process -->
<section class="process-section">
  <div class="process-wrap">
    <div class="section-eyebrow">HOW WE WORK</div>
    <h2 class="process-heading">OUR PROCESS</h2>
    <div class="process-grid">
      <div class="process-step"><div class="step-num">01</div><h3>CONSULTATION</h3><p>We discuss your requirements, target market, budget and product specifications in detail.</p></div>
      <div class="process-step"><div class="step-num">02</div><h3>FORMULATION</h3><p>Our R&D team develops and refines the perfect formula matching your specifications.</p></div>
      <div class="process-step"><div class="step-num">03</div><h3>PRODUCTION</h3><p>Large-scale manufacturing with strict quality checks at every production stage.</p></div>
      <div class="process-step"><div class="step-num">04</div><h3>DELIVERY</h3><p>Packaged, labelled and delivered on time, ready for retail shelves immediately.</p></div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="cta-wrap">
    <h2>READY TO BUILD<br><span>YOUR BRAND?</span></h2>
    <p>Partner with ACM and bring your cosmetics vision to life with premium manufacturing.</p>
    <div class="cta-btns">
      <a href="/contact" class="btn-cta-primary">GET IN TOUCH</a>
      <a href="tel:+923255129241" class="btn-cta-outline">CALL US NOW</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
let cur = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let timer = setInterval(()=>nextSlide(), 5000);

function goSlide(n) {
  slides[cur].classList.remove('active');
  dots[cur].classList.remove('active');
  cur = (n + slides.length) % slides.length;
  slides[cur].classList.add('active');
  dots[cur].classList.add('active');
  clearInterval(timer);
  timer = setInterval(()=>nextSlide(), 5000);
}
function nextSlide() { goSlide(cur + 1); }
function prevSlide() { goSlide(cur - 1); }
</script>
