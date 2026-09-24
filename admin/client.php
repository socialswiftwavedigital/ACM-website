<?php
// ── ACM Client Portal ─────────────────────────────────────────
// Single-password read-mostly dashboard (separate from admin session)
// SECURITY: config.php must NEVER be committed — excluded via .gitignore
// ──────────────────────────────────────────────────────────────

session_name('acm_client_sess');
session_start();

define('SESSION_TIMEOUT', 1800); // 30 minutes

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/config.php';

$CLIENT_PASS = defined('CLIENT_PASS') ? CLIENT_PASS : 'ACM@Client2026';

// ── Session timeout ───────────────────────────────────────────
if (isset($_SESSION['acm_client']) && isset($_SESSION['client_last_activity'])) {
    if (time() - $_SESSION['client_last_activity'] > SESSION_TIMEOUT) {
        session_unset(); session_destroy();
        header('Location: /admin/client'); exit;
    }
    $_SESSION['client_last_activity'] = time();
}

// ── Logout ────────────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_unset(); session_destroy();
    header('Location: /admin/client'); exit;
}

// ── Login ─────────────────────────────────────────────────────
$loginError = '';
if (!isset($_SESSION['acm_client'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
        if ($_POST['pass'] === $CLIENT_PASS) {
            $_SESSION['acm_client'] = true;
            $_SESSION['client_last_activity'] = time();
            header('Location: /admin/client'); exit;
        } else {
            $loginError = 'Password غلط ہے۔ دوبارہ کوشش کریں۔';
        }
    }
    // Show login page
    ?>
<!doctype html>
<html lang="ur" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ACM Client Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',sans-serif;background:#f0f2f8;display:flex;align-items:center;justify-content:center;min-height:100vh}
.login-card{background:#fff;border-radius:16px;padding:48px 40px;width:380px;max-width:94vw;box-shadow:0 8px 40px rgba(13,34,88,.12)}
.login-logo{display:flex;align-items:center;gap:14px;justify-content:center;margin-bottom:32px}
.login-logo img{width:48px;height:48px;object-fit:contain}
.login-logo-text{font-size:18px;font-weight:700;color:#0D2258}
.login-logo-sub{font-size:11px;color:#64748b;font-weight:500}
h2{font-size:20px;font-weight:600;color:#0D2258;text-align:center;margin-bottom:6px}
.login-sub{font-size:12px;color:#94a3b8;text-align:center;margin-bottom:28px}
label{display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:6px}
input[type=password]{width:100%;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Poppins',sans-serif;font-size:14px;color:#1e293b;outline:none;transition:.15s}
input[type=password]:focus{border-color:#0D2258}
.error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:10px 14px;font-size:13px;margin-top:12px}
button[type=submit]{width:100%;margin-top:20px;padding:13px;background:#0D2258;color:#fff;border:none;border-radius:10px;font-family:'Poppins',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:.15s}
button[type=submit]:hover{background:#16337a}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-logo">
    <img src="https://acmpvtltd.com/logo.png" alt="ACM">
    <div>
      <div class="login-logo-text">ACM</div>
      <div class="login-logo-sub">Asia Cosmetics Mfg.</div>
    </div>
  </div>
  <h2>Client Portal</h2>
  <p class="login-sub">Apna password enter karein apka dashboard dekhne ke liye</p>
  <form method="post">
    <label>Password</label>
    <input type="password" name="pass" placeholder="••••••••" autofocus required>
    <?php if ($loginError): ?><div class="error"><?= htmlspecialchars($loginError) ?></div><?php endif; ?>
    <button type="submit">Dashboard Kholein →</button>
  </form>
</div>
</body>
</html>
<?php exit; }

// ── Status / Tracking update (client can ONLY change these two) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)($_POST['order_id'] ?? 0);
    if ($id > 0) {
        if ($_POST['action'] === 'update_status' && !empty($_POST['status'])) {
            $allowed = ['new','processing','shipped','delivered','cancelled'];
            $st = in_array($_POST['status'], $allowed) ? $_POST['status'] : 'new';
            $stmt = $pdo->prepare("UPDATE orders SET status=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$st, $id]);
        }
        if ($_POST['action'] === 'update_tracking' && isset($_POST['tracking_number'])) {
            $tr = trim($_POST['tracking_number']);
            $stmt = $pdo->prepare("UPDATE orders SET tracking_number=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$tr, $id]);
        }
    }
    header('Location: /admin/client'); exit;
}

// ── Data queries ──────────────────────────────────────────────
$totalOrders   = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$newOrders     = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn();
$processingOrd = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='processing'")->fetchColumn();
$deliveredOrd  = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn();
$totalRevenue  = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status!='cancelled'")->fetchColumn();
$uniqueClients = (int)$pdo->query("SELECT COUNT(DISTINCT customer_phone) FROM orders")->fetchColumn();

// Revenue chart builder
function buildChart($pdo, $days) {
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
$todayRev   = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status!='cancelled'")->fetchColumn();
$chartToday = ['labels' => ['Today'], 'data' => [$todayRev]];
$chart7     = buildChart($pdo, 7);
$chart14    = buildChart($pdo, 14);
$chart30    = buildChart($pdo, 30);

// Recent orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Products / stock
$products = $pdo->query("SELECT name, category, stock, low_stock_threshold FROM products ORDER BY stock ASC LIMIT 20")->fetchAll();

// All orders for main table
$allOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html>
<html lang="ur" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ACM Client Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --navy:#0D2258; --red:#DC2626; --green:#16a34a; --orange:#d97706;
  --bg:#f0f2f8; --surface:#fff; --border:#e2e8f0; --muted:#94a3b8;
  --text:#1e293b; --text2:#475569;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}

/* ── Top nav ───────────────────────────────────────────── */
.topnav{background:var(--navy);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(0,0,0,.2)}
.nav-left{display:flex;align-items:center;gap:14px}
.nav-left img{width:34px;height:34px;object-fit:contain;filter:brightness(0) invert(1)}
.nav-brand{color:#fff;font-weight:700;font-size:15px}
.nav-brand-sub{color:rgba(255,255,255,.5);font-size:10px;font-weight:500}
.badge-readonly{background:rgba(220,38,38,.15);border:1px solid rgba(220,38,38,.5);color:#f87171;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;letter-spacing:.5px}
.nav-right{display:flex;align-items:center;gap:16px;color:rgba(255,255,255,.6);font-size:12px}
.nav-date{display:none}
@media(min-width:600px){.nav-date{display:block}}
.logout-btn{color:rgba(255,255,255,.5);text-decoration:none;font-size:12px;padding:6px 12px;border:1px solid rgba(255,255,255,.15);border-radius:8px;transition:.15s}
.logout-btn:hover{color:#fff;border-color:rgba(255,255,255,.4)}

/* ── Hero ──────────────────────────────────────────────── */
.hero{background:linear-gradient(135deg, #0D2258 0%, #1a3575 60%, #0f2b6b 100%);padding:36px 24px;color:#fff;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")}
.hero-inner{position:relative;max-width:900px}
.hero h1{font-size:22px;font-weight:700;margin-bottom:6px}
.hero-sub{color:rgba(255,255,255,.65);font-size:13px;margin-bottom:20px}
.hero-stats{display:flex;gap:24px;flex-wrap:wrap}
.hero-stat{text-align:center}
.hero-stat-val{font-size:24px;font-weight:700;display:block}
.hero-stat-label{font-size:10px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.5px}
.hero-pending{margin-top:16px;background:rgba(220,38,38,.15);border:1px solid rgba(220,38,38,.3);border-radius:10px;padding:10px 16px;display:inline-flex;align-items:center;gap:10px;font-size:13px}
.hero-pending strong{color:#f87171}

/* ── Info note ─────────────────────────────────────────── */
.info-note{background:#eff6ff;border-left:3px solid #3b82f6;padding:10px 16px;margin:20px 24px 0;border-radius:0 8px 8px 0;font-size:12px;color:#1e40af}

/* ── Wrapper ───────────────────────────────────────────── */
.wrap{max-width:1100px;margin:0 auto;padding:20px 20px 40px}

/* ── Stats grid ─────────────────────────────────────────── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px}
.stat-card{background:var(--surface);border-radius:12px;padding:16px 18px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.stat-label{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;display:flex;justify-content:space-between;align-items:center}
.stat-val{font-size:26px;font-weight:700;color:var(--navy);font-variant-numeric:tabular-nums}
.stat-sub{font-size:11px;color:var(--muted);margin-top:4px}
.stat-icon{font-size:18px}

/* ── Grid 2-col ─────────────────────────────────────────── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
@media(max-width:700px){.grid-2{grid-template-columns:1fr}}

/* ── Card ───────────────────────────────────────────────── */
.card{background:var(--surface);border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);margin-bottom:16px}
.card-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px}
.card-title{font-size:13px;font-weight:700;color:var(--navy)}
.card-body{padding:16px 18px}

/* ── Period buttons ─────────────────────────────────────── */
.period-btns{display:flex;gap:6px;flex-wrap:wrap}
.period-btn{padding:4px 12px;border-radius:20px;border:1.5px solid var(--border);background:#fff;color:var(--muted);font-family:'Poppins',sans-serif;font-size:11px;font-weight:600;cursor:pointer;transition:.15s}
.period-btn:hover{border-color:var(--navy);color:var(--navy)}
.period-active{background:var(--navy)!important;color:#fff!important;border-color:var(--navy)!important}

/* ── Table ───────────────────────────────────────────────── */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);border-bottom:1px solid var(--border);white-space:nowrap}
td{padding:10px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#f8fafc}

/* ── Badges ──────────────────────────────────────────────── */
.badge{padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;white-space:nowrap}
.badge-new{background:#dbeafe;color:#1d4ed8}
.badge-processing{background:#fef3c7;color:#92400e}
.badge-shipped{background:#e0e7ff;color:#3730a3}
.badge-delivered{background:#dcfce7;color:#166534}
.badge-cancelled{background:#fee2e2;color:#991b1b}

/* ── Status select ────────────────────────────────────────── */
.status-form select{padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;font-family:'Poppins',sans-serif;color:var(--text);cursor:pointer;background:#fff}
.status-form select:focus{outline:none;border-color:var(--navy)}

/* ── Tracking form ────────────────────────────────────────── */
.tracking-form{display:flex;gap:6px;align-items:center}
.tracking-form input{padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;font-family:'Poppins',sans-serif;width:110px}
.tracking-form input:focus{outline:none;border-color:var(--navy)}
.tracking-form button{padding:4px 10px;background:var(--navy);color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif}

/* ── Stock bar ────────────────────────────────────────────── */
.stock-bar{height:5px;background:var(--border);border-radius:4px;margin-top:4px;overflow:hidden}
.stock-fill{height:100%;border-radius:4px;transition:.3s}

/* ── Responsive ────────────────────────────────────────────── */
@media(max-width:500px){
  .hero h1{font-size:17px}
  .hero-stats{gap:14px}
  .hero-stat-val{font-size:18px}
  .stat-val{font-size:20px}
  .topnav{padding:0 14px}
}
</style>
</head>
<body>

<!-- ── Top Nav ─────────────────────────────────────────────── -->
<nav class="topnav">
  <div class="nav-left">
    <img src="https://acmpvtltd.com/logo.png" alt="ACM">
    <div>
      <div class="nav-brand">ACM Client Portal</div>
      <div class="nav-brand-sub">Asia Cosmetics Manufacturing</div>
    </div>
    <span class="badge-readonly">READ ONLY</span>
  </div>
  <div class="nav-right">
    <span class="nav-date">📅 <?= date('d M Y') ?></span>
    <a href="/admin/client?logout=1" class="logout-btn">Logout ⎋</a>
  </div>
</nav>

<!-- ── Hero ─────────────────────────────────────────────────── -->
<div class="hero">
  <div class="hero-inner">
    <h1>ACM Business Dashboard</h1>
    <p class="hero-sub">Asia Cosmetics Manufacturing Pvt. Ltd. — Client Overview</p>
    <div class="hero-stats">
      <div class="hero-stat">
        <span class="hero-stat-val"><?= $totalOrders ?></span>
        <span class="hero-stat-label">Total Orders</span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-val"><?= $deliveredOrd ?></span>
        <span class="hero-stat-label">Delivered</span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-val"><?= $uniqueClients ?></span>
        <span class="hero-stat-label">Clients</span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-val">Rs.<?= number_format($totalRevenue/1000, 0) ?>k</span>
        <span class="hero-stat-label">Revenue</span>
      </div>
    </div>
    <?php if ($newOrders > 0 || $processingOrd > 0): ?>
    <div class="hero-pending">
      🔔 <strong><?= $newOrders ?></strong> naye orders pending &nbsp;·&nbsp; <strong><?= $processingOrd ?></strong> production mein hain
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ── Info note ─────────────────────────────────────────────── -->
<div class="info-note">
  ℹ️ Aap sirf <strong>status</strong> aur <strong>tracking number</strong> update kar sakte hain — baqi tamam fields read only hain.
</div>

<div class="wrap">

  <!-- ── Stats ─────────────────────────────────────────────── -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Quotes <span class="stat-icon">📋</span></div>
      <div class="stat-val"><?= $totalOrders ?></div>
      <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Pending <span class="stat-icon">🆕</span></div>
      <div class="stat-val" style="color:var(--red)"><?= $newOrders ?></div>
      <div class="stat-sub"><?= $newOrders > 0 ? 'Action needed' : 'All handled' ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Processing <span class="stat-icon">⚙️</span></div>
      <div class="stat-val" style="color:var(--orange)"><?= $processingOrd ?></div>
      <div class="stat-sub">In production</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Delivered <span class="stat-icon">✅</span></div>
      <div class="stat-val" style="color:var(--green)"><?= $deliveredOrd ?></div>
      <div class="stat-sub">Completed</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Revenue <span class="stat-icon">💰</span></div>
      <div class="stat-val">Rs.<?= number_format($totalRevenue) ?></div>
      <div class="stat-sub">Excl. cancelled</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Unique Clients <span class="stat-icon">👥</span></div>
      <div class="stat-val"><?= $uniqueClients ?></div>
      <div class="stat-sub">By phone</div>
    </div>
  </div>

  <!-- ── Revenue Chart ──────────────────────────────────────── -->
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Revenue Overview (Rs.)</div>
        <div style="font-size:11px;color:var(--muted)">Total: <strong id="chartTotal" style="color:var(--navy)">Rs. <?= number_format(array_sum($chart14['data'])) ?></strong> · <span id="chartPeriod">Last 14 Days</span></div>
      </div>
      <div class="period-btns">
        <button onclick="switchPeriod('today')" id="pbtn-today" class="period-btn">Today</button>
        <button onclick="switchPeriod('7')"     id="pbtn-7"     class="period-btn">7 Days</button>
        <button onclick="switchPeriod('14')"    id="pbtn-14"    class="period-btn period-active">14 Days</button>
        <button onclick="switchPeriod('30')"    id="pbtn-30"    class="period-btn">30 Days</button>
      </div>
    </div>
    <div class="card-body">
      <canvas id="revenueChart" height="160"></canvas>
    </div>
  </div>

  <!-- ── Recent + Stock ─────────────────────────────────────── -->
  <div class="grid-2">
    <!-- Recent Orders -->
    <div class="card" style="margin-bottom:0">
      <div class="card-header">
        <div class="card-title">Recent Orders</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
          <?php if (empty($recentOrders)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:30px">Koi orders nahi hain abhi.</td></tr>
          <?php else: foreach ($recentOrders as $o): ?>
            <tr>
              <td style="font-weight:600;font-size:12px"><?= htmlspecialchars($o['order_number']) ?></td>
              <td style="font-size:12px"><?= htmlspecialchars($o['customer_name']) ?></td>
              <td style="font-size:12px;font-weight:600">Rs.<?= number_format($o['total']) ?></td>
              <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Products / Stock -->
    <div class="card" style="margin-bottom:0">
      <div class="card-header">
        <div class="card-title">Products & Stock</div>
      </div>
      <div class="card-body" style="padding:0">
        <?php if (empty($products)): ?>
          <p style="padding:20px;color:var(--muted);font-size:13px">Products nahi hain.</p>
        <?php else: ?>
        <div style="max-height:300px;overflow-y:auto">
          <?php foreach ($products as $p):
            $pct = ($p['low_stock_threshold'] > 0)
                ? min(100, round($p['stock'] / max($p['stock'], $p['low_stock_threshold'] * 2) * 100))
                : ($p['stock'] > 0 ? 100 : 0);
            $color = $p['stock'] == 0 ? '#dc2626' : ($p['stock'] <= $p['low_stock_threshold'] ? '#d97706' : '#16a34a');
          ?>
          <div style="padding:10px 18px;border-bottom:1px solid var(--border)">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <div style="font-size:12px;font-weight:600;color:var(--text)"><?= htmlspecialchars($p['name']) ?></div>
              <div style="font-size:11px;font-weight:700;color:<?= $color ?>"><?= $p['stock'] ?> units</div>
            </div>
            <div style="font-size:10px;color:var(--muted);margin-top:1px"><?= htmlspecialchars($p['category']) ?></div>
            <div class="stock-bar"><div class="stock-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ── All Orders Table ────────────────────────────────────── -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Tamam Orders</div>
      <span style="font-size:11px;color:var(--muted)"><?= count($allOrders) ?> orders total</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Tracking</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($allOrders)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">Koi orders nahi hain abhi.</td></tr>
        <?php else: foreach ($allOrders as $o): ?>
          <tr>
            <td style="font-weight:600;white-space:nowrap"><?= htmlspecialchars($o['order_number']) ?></td>
            <td style="white-space:nowrap"><?= htmlspecialchars($o['customer_name']) ?></td>
            <td style="white-space:nowrap;font-size:12px;color:var(--muted)"><?= htmlspecialchars($o['customer_phone']) ?></td>
            <td style="font-weight:600;white-space:nowrap">Rs.<?= number_format($o['total']) ?></td>
            <td style="font-size:12px;white-space:nowrap"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td>
              <form class="status-form" method="post" onchange="this.submit()">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status">
                  <?php foreach (['new','processing','shipped','delivered','cancelled'] as $st): ?>
                  <option value="<?= $st ?>" <?= $o['status']===$st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td>
              <form class="tracking-form" method="post">
                <input type="hidden" name="action" value="update_tracking">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <input type="text" name="tracking_number" value="<?= htmlspecialchars($o['tracking_number'] ?? '') ?>" placeholder="Add tracking...">
                <button type="submit">✓</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- /wrap -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
var _periods = {
  'today': {labels:<?= json_encode($chartToday['labels']) ?>, data:<?= json_encode($chartToday['data']) ?>, label:'Aaj'},
  '7':     {labels:<?= json_encode($chart7['labels'])     ?>, data:<?= json_encode($chart7['data'])     ?>, label:'Aakhri 7 Din'},
  '14':    {labels:<?= json_encode($chart14['labels'])    ?>, data:<?= json_encode($chart14['data'])    ?>, label:'Aakhri 14 Din'},
  '30':    {labels:<?= json_encode($chart30['labels'])    ?>, data:<?= json_encode($chart30['data'])    ?>, label:'Aakhri 30 Din'},
};
var _chart = new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: _periods['14'].labels,
    datasets: [{
      label: 'Revenue',
      data: _periods['14'].data,
      backgroundColor: 'rgba(13,34,88,.15)',
      borderColor: '#0D2258',
      borderWidth: 2,
      borderRadius: 6,
      hoverBackgroundColor: 'rgba(13,34,88,.3)'
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: true,
    plugins: {legend: {display: false}},
    scales: {
      x: {grid: {display: false}, ticks: {font: {family: 'Poppins', size: 10}}},
      y: {
        beginAtZero: true,
        grid: {color: 'rgba(214,224,245,.6)'},
        ticks: {font: {family: 'Poppins', size: 10}, callback: v => 'Rs ' + (v/1000).toFixed(0) + 'k'}
      }
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
  document.querySelectorAll('.period-btn').forEach(function(b){ b.classList.remove('period-active'); });
  document.getElementById('pbtn-' + p).classList.add('period-active');
}
</script>
</body>
</html>
