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
    ['tag'=>'HAIR CARE','title'=>'SHAMPOO &<br>CONDITIONER','lines'=>['Keratin · Anti-Dandruff · Amla','Anti-Lice · Conditioner · 15 Variants'],'btn'=>'EXPLORE SHAMPOO','link'=>'/products?cat=Shampoo+%26+Conditioner','img'=>'slide-shampoo.png'],
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
    <div class="about-text">
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
    <div class="about-img-wrap">
      <img src="/images/bk-cream.png" alt="ACM Manufacturing" onerror="this.parentElement.style.display='none'">
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
        ['variants'=>'20','tag'=>'SKINCARE','cat'=>'CREAMS','desc'=>'Vitamin C, Night, Fairness, BB Cream, Moisturizer, Sunblock SPF 30–80, Soothing Lotion & more.','img'=>'bk-cream.png','link'=>'/products?cat=Creams'],
        ['variants'=>'11','tag'=>'TREATMENT','cat'=>'SERUMS','desc'=>'Hyaluronic Acid, Glycolic Acid, All-in-One, Multipurpose, Glutathione, Niacinamide & more.','img'=>'serum-vitamin-c.png','link'=>'/products?cat=Serums'],
        ['variants'=>'14','tag'=>'CLEANSING','cat'=>'FACE WASH','desc'=>'Foaming, Gel, Charcoal, Gold, Turmeric, Rice, Vitamin C & more: for every skin type.','img'=>'bk-facewash.png','link'=>'/products?cat=Face+Wash'],
        ['variants'=>'3','tag'=>'PROTECTION','cat'=>'PETROLEUM JELLY','desc'=>'Colored, Original & Scented: pharmaceutical grade petroleum jelly for skin & lip care.','img'=>'bk-pjelly.png','link'=>'/products?cat=Petroleum+Jelly'],
        ['variants'=>'10','tag'=>'MOISTURISING','cat'=>'LOTIONS','desc'=>'Brightening, Niacinamide, Vitamin C, Honey, Cocoa Butter, Aloe Vera & more body lotions.','img'=>'bk-lotion.png','link'=>'/products?cat=Lotions'],
        ['variants'=>'23','tag'=>'HAIR CLEANSING','cat'=>'SHAMPOO & CONDITIONER','desc'=>'Keratin, Deep, Leave-in, Argan, Onion, Color Protect & complete hair range.','img'=>'bk-shampoo.png','link'=>'/products?cat=Shampoo+%26+Conditioner'],
        ['variants'=>'8','tag'=>'BABY CARE','cat'=>'BABY & KIDS','desc'=>'Baby Lotion, Cream, Shampoo, Face Wash, Body Wash, Oil & more: gentle care for little ones.','img'=>'bk-kids-shampoo.png','link'=>'/products?cat=Baby+%26+Kids'],
        ['variants'=>'5','tag'=>'HAIR SOLUTIONS','cat'=>'HAIR CARE','desc'=>'Hair Tonic, Hair Serum, Hair Oil, Hair Gel & Hair Cream: complete hair styling & treatment.','img'=>'bk-body-wash.png','link'=>'/products'],
        ['variants'=>'13','tag'=>'NATURAL OILS','cat'=>'ESSENTIAL OILS','desc'=>'Coconut, Argan, Jojoba, Neem, Moringa, Kalonji, Amla, Onion & more pure natural oils.','img'=>'bk-oil.png','link'=>'/products'],
      ];
      foreach($range as $r): ?>
      <div class="range-card" onclick="window.location='<?= $r['link'] ?>'">
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
      <div class="why-card">
        <div class="why-icon">🏭</div>
        <h3>MODERN FACILITY</h3>
        <p>State-of-the-art manufacturing plant with automated filling, mixing and packaging lines.</p>
      </div>
      <div class="why-card">
        <div class="why-icon">🔬</div>
        <h3>R&D LAB</h3>
        <p>In-house research and development team constantly innovating superior formulations.</p>
      </div>
      <div class="why-card">
        <div class="why-icon">✅</div>
        <h3>QUALITY ASSURED</h3>
        <p>Every batch tested for safety, efficacy and stability before leaving our facility.</p>
      </div>
      <div class="why-card">
        <div class="why-icon">🏷️</div>
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
