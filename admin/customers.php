<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle   = 'Customers';
$currentPage = 'customers';

$q = trim($_GET['q'] ?? '');
$selectedId = (int)($_GET['id'] ?? 0);

$where = $q ? 'WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?' : '';
$params = $q ? ["%$q%","%$q%","%$q%"] : [];
$stmt = $pdo->prepare("SELECT * FROM customers $where ORDER BY total_orders DESC, created_at DESC");
$stmt->execute($params);
$customers = $stmt->fetchAll();

$total = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalRevFromCustomers = $pdo->query("SELECT COALESCE(SUM(total_spent),0) FROM customers")->fetchColumn();

$selected = null;
$selectedOrders = [];
if ($selectedId) {
    $s = $pdo->prepare("SELECT * FROM customers WHERE id=?");
    $s->execute([$selectedId]);
    $selected = $s->fetch();
    if ($selected) {
        $so = $pdo->prepare("SELECT * FROM orders WHERE customer_email=? ORDER BY created_at DESC");
        $so->execute([$selected['email']]);
        $selectedOrders = $so->fetchAll();
    }
}

include __DIR__ . '/includes/header.php';
?>
<div style="display:flex;min-height:100vh">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main">
  <div class="topbar">
    <div>
      <div class="page-title">Customers</div>
      <div class="page-sub"><?= $total ?> total customers · Rs. <?= number_format($totalRevFromCustomers) ?> lifetime value</div>
    </div>
  </div>

  <div class="content-section" style="padding-top:20px">
    <div class="search-bar">
      <form method="GET" style="display:flex;gap:8px">
        <input type="text" name="q" placeholder="Search by name, email, phone..." value="<?= htmlspecialchars($q) ?>" style="max-width:320px">
        <button type="submit" class="btn btn-outline btn-sm">Search</button>
        <?php if ($q): ?><a href="/admin/customers" class="btn btn-ghost btn-sm">✕</a><?php endif; ?>
      </form>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Customer</th><th>Phone</th><th>City</th><th>Orders</th><th>Total Spent</th><th>Joined</th><th></th></tr>
          </thead>
          <tbody>
          <?php if (empty($customers)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">No customers yet. They appear here when orders are placed.</td></tr>
          <?php else: ?>
          <?php foreach ($customers as $c): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:34px;height:34px;border-radius:50%;background:var(--navy);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                    <?= strtoupper(substr($c['name'],0,1)) ?>
                  </div>
                  <div>
                    <div style="font-weight:600"><?= htmlspecialchars($c['name']) ?></div>
                    <div style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($c['email']) ?></div>
                  </div>
                </div>
              </td>
              <td style="color:var(--muted)"><?= htmlspecialchars($c['phone']) ?></td>
              <td><?= htmlspecialchars($c['city']) ?></td>
              <td style="font-weight:700"><?= $c['total_orders'] ?></td>
              <td><span class="amount">Rs. <?= number_format($c['total_spent']) ?></span></td>
              <td style="color:var(--muted)"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
              <td><a href="/admin/customers?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</div>

<?php if ($selected): ?>
<div class="detail-overlay open" onclick="location.href='/customers'"></div>
<div class="detail-panel open">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <div style="font-size:16px;font-weight:700">Customer Profile</div>
    <a href="/admin/customers" class="btn btn-ghost btn-sm">✕</a>
  </div>

  <div style="text-align:center;margin-bottom:20px">
    <div style="width:60px;height:60px;border-radius:50%;background:var(--navy);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;margin:0 auto 10px">
      <?= strtoupper(substr($selected['name'],0,1)) ?>
    </div>
    <div style="font-size:17px;font-weight:700"><?= htmlspecialchars($selected['name']) ?></div>
    <div style="font-size:13px;color:var(--muted)"><?= htmlspecialchars($selected['email']) ?></div>
    <div style="font-size:13px;color:var(--muted)"><?= htmlspecialchars($selected['phone']) ?></div>
    <?php if ($selected['city']): ?>
    <div style="font-size:13px;color:var(--muted)">📍 <?= htmlspecialchars($selected['city']) ?></div>
    <?php endif; ?>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
    <div style="background:var(--bg);border-radius:10px;padding:14px;text-align:center">
      <div style="font-size:22px;font-weight:800"><?= $selected['total_orders'] ?></div>
      <div style="font-size:11px;color:var(--muted)">Total Orders</div>
    </div>
    <div style="background:var(--bg);border-radius:10px;padding:14px;text-align:center">
      <div style="font-size:16px;font-weight:800;color:var(--green)">Rs. <?= number_format($selected['total_spent']) ?></div>
      <div style="font-size:11px;color:var(--muted)">Total Spent</div>
    </div>
  </div>

  <div style="font-size:13px;font-weight:600;margin-bottom:10px">Order History</div>
  <?php if (empty($selectedOrders)): ?>
    <p style="color:var(--muted);font-size:13px">No orders found.</p>
  <?php else: ?>
  <?php foreach ($selectedOrders as $o): ?>
  <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px">
    <div>
      <div style="font-weight:600"><?= htmlspecialchars($o['order_number']) ?></div>
      <div style="font-size:11px;color:var(--muted)"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
    </div>
    <div style="text-align:right">
      <div class="amount">Rs. <?= number_format($o['total']) ?></div>
      <span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>
<?php endif; ?>
</body></html>
