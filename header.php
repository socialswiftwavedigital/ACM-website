<?php
$current = basename($_SERVER['PHP_SELF'], '.php');
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="<?= $metaDesc ?? 'ACM Asia Cosmetics — Premium skincare & beauty products made in Pakistan.' ?>">
<title><?= $pageTitle ?? 'ACM Asia Cosmetics & Manufactures' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<!-- Top bar -->
<div class="topbar">
  <div class="container topbar-inner">
    <span>📞 <a href="tel:+92300000000">+92-300-000-0000</a></span>
    <span>🚚 Free delivery on orders above Rs. 2,000</span>
    <span><a href="https://wa.me/923000000000" target="_blank">💬 WhatsApp Us</a></span>
  </div>
</div>

<!-- Header -->
<header class="site-header">
  <div class="container header-inner">
    <a href="/" class="site-logo">
      <img src="/assets/logo.png" alt="ACM" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="logo-fallback" style="display:none">
        <div class="logo-icon">ACM</div>
        <div>
          <div class="logo-name">ACM</div>
          <div class="logo-sub">Asia Cosmetics</div>
        </div>
      </div>
    </a>
    <nav class="site-nav">
      <a href="/" class="<?= $current==='index'?'active':'' ?>">Home</a>
      <a href="/products" class="<?= $current==='products'?'active':'' ?>">Products</a>
      <a href="/about" class="<?= $current==='about'?'active':'' ?>">About</a>
      <a href="/contact" class="<?= $current==='contact'?'active':'' ?>">Contact</a>
    </nav>
    <div class="header-actions">
      <a href="https://wa.me/923000000000" class="btn-whatsapp" target="_blank">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        WhatsApp
      </a>
      <button class="mobile-menu-btn" onclick="document.querySelector('.site-nav').classList.toggle('open')">☰</button>
    </div>
  </div>
</header>
