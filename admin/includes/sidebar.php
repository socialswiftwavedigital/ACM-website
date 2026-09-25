<?php
$current = $currentPage ?? '';

// New orders count for badge (safe — db is already included by the calling page)
$_newOrders = 0;
try { $_newOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn(); } catch(Exception $e){}
?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <img src="https://acmpvtltd.com/logo.png" alt="ACM" style="width:80px;height:37px;object-fit:contain;border-radius:6px;flex-shrink:0">
    <div>
      <div class="brand-name">ACM Admin</div>
      <div class="brand-sub">Asia Cosmetics</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-label">Main Menu</div>
    <a href="/admin"          class="nav-link <?= $current==='overview'  ?'active':'' ?>"><span class="nav-icon">📊</span> Overview</a>
    <a href="/admin/orders"    class="nav-link <?= $current==='orders'    ?'active':'' ?>">
      <span class="nav-icon">🛒</span> Orders
      <?php if ($_newOrders > 0): ?>
      <span class="nav-badge"><?= $_newOrders ?></span>
      <?php endif; ?>
    </a>
    <a href="/admin/products"  class="nav-link <?= $current==='products'  ?'active':'' ?>"><span class="nav-icon">📦</span> Products</a>
    <a href="/admin/customers" class="nav-link <?= $current==='customers' ?'active':'' ?>"><span class="nav-icon">👥</span> Customers</a>
    <a href="/admin/cities"    class="nav-link <?= $current==='cities'    ?'active':'' ?>"><span class="nav-icon">📍</span> Cities</a>
    <a href="/admin/ads"       class="nav-link <?= $current==='ads'       ?'active':'' ?>"><span class="nav-icon">📣</span> Ads Dashboard</a>

    <div class="sidebar-label" style="margin-top:16px">Access</div>
    <a href="/admin/client" target="_blank" class="nav-link"><span class="nav-icon">🔗</span> Client Portal</a>
  </nav>

  <div class="sidebar-footer">
    <a href="https://acmpvtltd.com" target="_blank" style="display:block;font-size:11px;color:rgba(255,255,255,.35);text-decoration:none;margin-bottom:6px">🌐 Live Site</a>
    <a href="/admin/logout" style="display:block;font-size:11px;color:rgba(255,255,255,.4);text-decoration:none">⎋ Logout</a>
  </div>
</aside>
<style>
.nav-badge{margin-left:auto;background:#DC2626;color:#fff;border-radius:20px;padding:1px 7px;font-size:10px;font-weight:700;}
</style>
