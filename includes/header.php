<?php $current = basename($_SERVER['PHP_SELF'], '.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="<?= $metaDesc ?? 'ACM Asia Cosmetics & Manufactures — Pakistan\'s Premier Cosmetics Manufacturer.' ?>">
<title><?= $pageTitle ?? 'ACM Asia Cosmetics & Manufactures' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="/assets/style.css?v=10">
</head>
<body>

<!-- Ticker -->
<div class="ticker-wrap">
  <div class="ticker-track">
    <?php $tick = 'PAKISTAN\'S PREMIER COSMETICS MANUFACTURER ◆ PREMIUM PRIVATE LABEL SOLUTIONS ◆ OEM MANUFACTURING ◆ 10 CATEGORIES ◆ 100+ PRODUCTS ◆ ISO QUALITY CERTIFIED ◆ TRUSTED BEAUTY PARTNER ◆ CONTACT: +92 325 5129241 ◆ &nbsp;&nbsp;'; ?>
    <span><?= str_repeat($tick, 4) ?></span>
  </div>
</div>

<!-- Header -->
<header class="site-header" id="site-header">
  <div class="hdr-inner">
    <a href="/" class="hdr-logo">
      <img src="/logo.png" alt="ACM" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
      <span class="logo-text" style="display:none">ACM</span>
    </a>

    <nav class="hdr-nav" id="hdr-nav">
      <a href="/" class="<?= $current==='index'?'active':'' ?>">Home</a>
      <a href="/about" class="<?= $current==='about'?'active':'' ?>">About</a>
      <div class="nav-dropdown">
        <a href="/products" class="<?= in_array($current,['products','product'])?'active':'' ?>">Products <span class="arrow">▾</span></a>
        <div class="dropdown-menu">
          <a href="/products?cat=Creams">Creams</a>
          <a href="/products?cat=Serums">Serums</a>
          <a href="/products?cat=Face+Wash">Face Wash</a>
          <a href="/products?cat=Petroleum+Jelly">Petroleum Jelly</a>
          <a href="/products?cat=Lotions">Lotions</a>
          <a href="/products?cat=Shampoo+%26+Conditioner">Shampoo & Conditioner</a>
          <a href="/products?cat=Baby+%26+Kids">Baby & Kids</a>
        </div>
      </div>
      <a href="/contact#faqs">FAQs</a>
      <a href="/contact" class="<?= $current==='contact'?'active':'' ?>">Contact</a>
    </nav>

    <div class="hdr-actions">
      <a href="https://wa.me/923255129241" class="btn-wa" target="_blank">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        WhatsApp
      </a>
      <button class="hdr-burger" onclick="document.getElementById('hdr-nav').classList.toggle('open')">☰</button>
    </div>
  </div>
</header>
