<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle   = 'Overview';
$currentPage = 'overview';

// Stats
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$newOrders    = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn();
$totalOrders  = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$lowStock     = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= low_stock_threshold AND stock > 0")->fetchColumn();
$outOfStock   = $pdo->query("SELECT COUNT(*) FROM products WHERE stock = 0")->fetchColumn();

// Build chart data for multiple periods
function buildAdminChart($pdo, $days) {
    $labels = []; $data = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $labels[] = date('j M', strtotime("-$i days"));
        $data[]   = 0;
    }
    $rows = $pdo->query("
        SELECT DATE(created_at) as d, COALESCE(SUM(total),0) as rev
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL " . ($days - 1) . " DAY)
          AND status != 'cancelled'
        GROUP BY DATE(created_at)
    ")->fetchAll();
    foreach ($rows as $r) {
        $key = date('j M', strtotime($r['d']));
        $idx = array_search($key, $labels);
        if ($idx !== false) $data[$idx] = (float)$r['rev'];
    }
    return ['labels' => $labels, 'data' => $data];
}
$chart7  = buildAdminChart($pdo, 7);
$chart14 = buildAdminChart($pdo, 14);
$chart30 = buildAdminChart($pdo, 30);
// Today
$todayRev = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status!='cancelled'")->fetchColumn();
$chartToday = ['labels'=>['Today'], 'data'=>[$todayRev]];
// Week labels / weekData still for backward compat display
$weekLabels = $chart7['labels'];
$weekData   = $chart7['data'];

// Recent orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 7")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div style="display:flex;min-height:100vh">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main">
  <div class="topbar">
    <div>
      <div class="page-title">Overview</div>
      <div class="page-sub">ACM Asia Cosmetics — Admin Dashboard</div>
    </div>
    <div class="topbar-date">📅 <?= date('d M Y') ?></div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Revenue <div class="stat-icon blue">💰</div></div>
      <div class="stat-value">Rs. <?= number_format($totalRevenue) ?></div>
      <div class="stat-change">All time</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">New Orders <div class="stat-icon green">🛒</div></div>
      <div class="stat-value"><?= $newOrders ?></div>
      <div class="stat-change <?= $newOrders > 0 ? 'down' : '' ?>"><?= $newOrders > 0 ? 'Pending action' : 'All handled' ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Orders <div class="stat-icon orange">📋</div></div>
      <div class="stat-value"><?= $totalOrders ?></div>
      <div class="stat-change neutral">All time</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Low / Out of Stock <div class="stat-icon red">⚠️</div></div>
      <div class="stat-value"><?= (int)$lowStock + (int)$outOfStock ?></div>
      <div class="stat-change down"><?= $outOfStock > 0 ? "$outOfStock out of stock" : 'Needs restock' ?></div>
    </div>
  </div>

  <div class="content-section">
    <div class="content-grid">
      <!-- Revenue Chart -->
      <div class="card">
        <div class="card-header" style="flex-wrap:wrap;gap:8px">
          <div>
            <div class="card-title">Revenue (Rs.)</div>
            <div style="font-size:11px;color:var(--muted)">Total: <strong id="chartTotal" style="color:var(--navy)">Rs. <?= number_format(array_sum($chart14['data'])) ?></strong> · <span id="chartPeriod">Last 14 Days</span></div>
          </div>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <button onclick="switchPeriod('today')" id="pbtn-today" class="period-btn">Today</button>
            <button onclick="switchPeriod('7')"     id="pbtn-7"     class="period-btn">7 Days</button>
            <button onclick="switchPeriod('14')"    id="pbtn-14"    class="period-btn period-active">14 Days</button>
            <button onclick="switchPeriod('30')"    id="pbtn-30"    class="period-btn">30 Days</button>
          </div>
        </div>
        <div class="card-body">
          <canvas id="revenueChart" height="180"></canvas>
        </div>
      </div>

      <!-- Ad Spend (mock until API connected) -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">Ad Spend Split</div>
          <a href="/admin/ads" class="card-link">Details →</a>
        </div>
        <div class="card-body">
          <?php if (!META_ACCESS_TOKEN && !GOOGLE_DEVELOPER_TOKEN): ?>
          <div class="alert alert-warning" style="margin-bottom:16px">⚠️ Connect Meta & Google API keys in config.php to see live data.</div>
          <?php endif; ?>
          <div class="ad-row">
            <div class="ad-row-top">
              <span class="ad-name">🔵 Meta Ads</span>
              <span class="ad-amount">—</span>
            </div>
            <div class="ad-bar-bg"><div class="ad-bar-fill" style="width:0%;background:#1877F2"></div></div>
            <div class="ad-sub"><a href="/admin/ads" style="color:var(--red);font-size:12px">Connect Meta API →</a></div>
          </div>
          <div class="ad-row">
            <div class="ad-row-top">
              <span class="ad-name">🔴 Google Ads</span>
              <span class="ad-amount">—</span>
            </div>
            <div class="ad-bar-bg"><div class="ad-bar-fill" style="width:0%;background:#EA4335"></div></div>
            <div class="ad-sub"><a href="/admin/ads" style="color:var(--red);font-size:12px">Connect Google API →</a></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">Recent Orders</div>
        <a href="/admin/orders" class="card-link">View all →</a>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Order #</th><th>Customer</th><th>Amount</th><th>Items</th><th>Status</th><th>Date</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($recentOrders)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">No orders yet. Orders from the website will appear here.</td></tr>
          <?php else: ?>
          <?php foreach ($recentOrders as $o):
            $items = json_decode($o['items'] ?? '[]', true);
            $itemCount = count($items);
          ?>
            <tr onclick="location.href='/admin/orders?id=<?= $o['id'] ?>'">
              <td><span class="order-id"><?= htmlspecialchars($o['order_number']) ?></span></td>
              <td><?= htmlspecialchars($o['customer_name']) ?></td>
              <td><span class="amount">Rs. <?= number_format($o['total']) ?></span></td>
              <td><?= $itemCount ?> item<?= $itemCount != 1 ? 's' : '' ?></td>
              <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
              <td><?= date('d M', strtotime($o['created_at'])) ?></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
.period-btn{padding:4px 12px;border-radius:20px;border:1.5px solid var(--border);background:#fff;color:var(--muted);font-family:'Poppins',sans-serif;font-size:11px;font-weight:600;cursor:pointer;transition:.15s}
.period-btn:hover{border-color:var(--navy);color:var(--navy)}
.period-active{background:var(--navy);color:#fff!important;border-color:var(--navy)}
</style>
<script>
var _periods = {
  'today': {labels:<?= json_encode($chartToday['labels']) ?>, data:<?= json_encode($chartToday['data']) ?>, label:'Today'},
  '7':     {labels:<?= json_encode($chart7['labels'])     ?>, data:<?= json_encode($chart7['data'])     ?>, label:'Last 7 Days'},
  '14':    {labels:<?= json_encode($chart14['labels'])    ?>, data:<?= json_encode($chart14['data'])    ?>, label:'Last 14 Days'},
  '30':    {labels:<?= json_encode($chart30['labels'])    ?>, data:<?= json_encode($chart30['data'])    ?>, label:'Last 30 Days'},
};
var _chart = new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: _periods['14'].labels,
    datasets: [{label:'Revenue',data:_periods['14'].data,backgroundColor:'rgba(13,33,88,.18)',borderColor:'#0D2258',borderWidth:2,borderRadius:6,hoverBackgroundColor:'rgba(13,33,88,.35)'}]
  },
  options: {
    responsive:true, maintainAspectRatio:true,
    plugins:{legend:{display:false}},
    scales:{
      x:{grid:{display:false},ticks:{font:{family:'Poppins',size:10}}},
      y:{beginAtZero:true,grid:{color:'rgba(214,224,245,.6)'},ticks:{font:{family:'Poppins',size:10},callback:v=>'Rs '+(v/1000).toFixed(0)+'k'}}
    }
  }
});
function switchPeriod(p) {
  var d = _periods[p];
  _chart.data.labels = d.labels;
  _chart.data.datasets[0].data = d.data;
  _chart.update();
  var tot = d.data.reduce(function(a,b){return a+b;},0);
  document.getElementById('chartTotal').textContent = 'Rs. ' + tot.toLocaleString('en-PK');
  document.getElementById('chartPeriod').textContent = d.label;
  document.querySelectorAll('.period-btn').forEach(function(b){b.classList.remove('period-active');});
  document.getElementById('pbtn-' + p).classList.add('period-active');
}
</script>
</body></html>
