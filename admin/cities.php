<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle   = 'Cities';
$currentPage = 'cities';

$all = $pdo->query("SELECT customer_address, status FROM orders")->fetchAll();

$cityMap = [];
foreach ($all as $o) {
    $raw  = trim($o['customer_address'] ?? '');
    // Extract city — stored as city name only, or "city, address"
    $city = $raw ?: 'Unknown';
    // If it looks like a multi-part address, take first segment
    $parts = preg_split('/[\n,]/', $city);
    $city  = trim($parts[0]) ?: 'Unknown';
    if (!$city) $city = 'Unknown';

    if (!isset($cityMap[$city])) $cityMap[$city] = ['orders'=>0,'new'=>0,'processing'=>0,'shipped'=>0,'delivered'=>0,'cancelled'=>0];
    $cityMap[$city]['orders']++;
    $st = $o['status'] ?? 'new';
    $cityMap[$city][$st] = ($cityMap[$city][$st] ?? 0) + 1;
}
uasort($cityMap, fn($a,$b) => $b['orders'] - $a['orders']);
$total = array_sum(array_column($cityMap, 'orders'));

include __DIR__ . '/includes/header.php';
?>
<div style="display:flex;min-height:100vh">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main">
  <div class="topbar">
    <div>
      <div class="page-title">Cities</div>
      <div class="page-sub"><?= count($cityMap) ?> cities · <?= $total ?> total orders</div>
    </div>
  </div>

  <div class="content-section" style="padding-top:20px">

    <!-- Top 4 tiles -->
    <div class="stats-grid" style="margin-bottom:20px">
    <?php foreach (array_slice($cityMap, 0, 4, true) as $city => $cd): ?>
    <div class="stat-card">
      <div class="stat-label">📍 <?= htmlspecialchars($city) ?> <div class="stat-icon blue">🏙️</div></div>
      <div class="stat-value"><?= $cd['orders'] ?></div>
      <div class="stat-change neutral">orders from this city</div>
    </div>
    <?php endforeach; ?>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>City</th><th>Orders</th><th>New</th><th>Processing</th><th>Delivered</th><th>Cancelled</th><th>Share %</th></tr>
          </thead>
          <tbody>
          <?php if (empty($cityMap)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:50px">No city data yet.</td></tr>
          <?php else: ?>
          <?php foreach ($cityMap as $city => $cd):
            $share = $total > 0 ? round($cd['orders'] / $total * 100) : 0;
          ?>
          <tr>
            <td style="font-weight:600">📍 <?= htmlspecialchars($city) ?></td>
            <td style="font-weight:700"><?= $cd['orders'] ?></td>
            <td><span class="badge badge-new"><?= $cd['new'] ?></span></td>
            <td><span class="badge badge-processing"><?= $cd['processing'] ?></span></td>
            <td><span class="badge badge-delivered"><?= $cd['delivered'] ?></span></td>
            <td><span class="badge badge-cancelled"><?= $cd['cancelled'] ?></span></td>
            <td style="min-width:120px">
              <div style="display:flex;align-items:center;gap:8px">
                <div style="flex:1;height:5px;background:var(--border);border-radius:4px;overflow:hidden">
                  <div style="width:<?= $share ?>%;height:100%;background:var(--navy);border-radius:4px"></div>
                </div>
                <span style="font-size:12px;color:var(--muted);min-width:28px"><?= $share ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</div>
</body></html>
