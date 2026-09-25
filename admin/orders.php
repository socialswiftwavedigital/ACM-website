<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle   = 'Orders';
$currentPage = 'orders';

/* ── CSV Export ─────────────────────────────────────────────── */
if (($_GET['export'] ?? '') === 'csv') {
    $all = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="acm-orders-' . date('Y-m-d') . '.csv"');
    echo "\xEF\xBB\xBF";
    echo "Order #,Date,Name,Company,Phone,Email,City,Product,Qty,Status,Tracking,Notes\n";
    foreach ($all as $o) {
        $items  = json_decode($o['items'] ?? '[]', true) ?: [];
        $prod   = implode(', ', array_map(fn($i) => ($i['name'] ?? '') . ' x' . ($i['qty'] ?? 1), $items));
        $qty    = array_sum(array_column($items, 'qty'));
        $notes  = str_replace(["\r", "\n"], ' ', $o['notes'] ?? '');
        echo '"' . implode('","', [
            $o['order_number'] ?? '', date('d M Y', strtotime($o['created_at'])),
            $o['customer_name'] ?? '', '', $o['customer_phone'] ?? '',
            $o['customer_email'] ?? '', $o['customer_address'] ?? '',
            $prod, $qty, $o['status'] ?? '', $o['tracking_number'] ?? '', $notes
        ]) . '"' . "\n";
    }
    exit;
}

/* ── POST actions ───────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $pdo->prepare("UPDATE orders SET status=?, tracking_number=?, notes=?, updated_at=NOW() WHERE id=?")
            ->execute([$_POST['status'], $_POST['tracking'] ?? '', $_POST['notes'] ?? '', (int)$_POST['order_id']]);
        if (!empty($_POST['notify']) && !empty($_POST['customer_email'])) {
            $subj = "Your ACM Quote #{$_POST['order_number']} — " . ucfirst($_POST['status']);
            $body = "Dear {$_POST['customer_name']},\n\nYour quote {$_POST['order_number']} is now " . strtoupper($_POST['status']) . ".\n";
            if (!empty($_POST['tracking'])) $body .= "Tracking: {$_POST['tracking']}\n";
            $body .= "\nThank you for choosing ACM Asia Cosmetics.\nwww.acmpvtltd.com";
            @mail($_POST['customer_email'], $subj, $body, "From: " . MAIL_FROM);
        }
        header('Location: /admin/orders?updated=1'); exit;
    }

    if ($action === 'inline_tracking') {
        $pdo->prepare("UPDATE orders SET tracking_number=?, updated_at=NOW() WHERE id=?")
            ->execute([trim($_POST['tracking'] ?? ''), (int)$_POST['order_id']]);
        header('Location: /admin/orders?updated=1'); exit;
    }

    if ($action === 'inline_note') {
        $pdo->prepare("UPDATE orders SET notes=?, updated_at=NOW() WHERE id=?")
            ->execute([trim($_POST['note'] ?? ''), (int)$_POST['order_id']]);
        header('Location: /admin/orders?updated=1'); exit;
    }

    if ($action === 'inline_status') {
        $pdo->prepare("UPDATE orders SET status=?, updated_at=NOW() WHERE id=?")
            ->execute([trim($_POST['status'] ?? 'new'), (int)$_POST['order_id']]);
        header('Location: /admin/orders'); exit;
    }

    if ($action === 'bulk_status') {
        $ids = array_map('intval', (array)($_POST['ids'] ?? []));
        $st  = trim($_POST['bulk_st'] ?? 'new');
        if ($ids) {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare("UPDATE orders SET status=?, updated_at=NOW() WHERE id IN ($ph)")
                ->execute(array_merge([$st], $ids));
        }
        header('Location: /admin/orders'); exit;
    }
}

/* ── Fetch orders ───────────────────────────────────────────── */
$status = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$orderId = (int)($_GET['id'] ?? 0);

$where = []; $params = [];
if ($status !== 'all') { $where[] = 'status=?'; $params[] = $status; }
if ($search) {
    $where[] = '(customer_name LIKE ? OR order_number LIKE ? OR customer_phone LIKE ?)';
    $params  = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
$sql    = 'SELECT * FROM orders' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
$stmt   = $pdo->prepare($sql); $stmt->execute($params);
$orders = $stmt->fetchAll();

$counts = $pdo->query("SELECT status, COUNT(*) FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$totalC = array_sum($counts);

$selectedOrder = null;
if ($orderId) {
    $s = $pdo->prepare("SELECT * FROM orders WHERE id=?"); $s->execute([$orderId]);
    $selectedOrder = $s->fetch();
}

include __DIR__ . '/includes/header.php';
?>
<div style="display:flex;min-height:100vh">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main">
  <div class="topbar">
    <div>
      <div class="page-title">Orders</div>
      <div class="page-sub"><?= count($orders) ?> of <?= $totalC ?> total quote requests</div>
    </div>
    <a href="/admin/orders?export=csv" class="btn btn-outline btn-sm">⬇ Export CSV</a>
  </div>

  <div class="content-section" style="padding-top:20px">
    <?php if (!empty($_GET['updated'])): ?>
    <div class="alert alert-success" style="margin-bottom:14px">✓ Order updated.</div>
    <?php endif; ?>

    <!-- Status Tabs -->
    <div class="filter-tabs">
      <a href="/admin/orders"                  class="filter-tab <?= $status==='all'        ?'active':'' ?>">All (<?= $totalC ?>)</a>
      <a href="/admin/orders?status=new"       class="filter-tab <?= $status==='new'        ?'active':'' ?>">New (<?= $counts['new']        ?? 0 ?>)</a>
      <a href="/admin/orders?status=processing"class="filter-tab <?= $status==='processing' ?'active':'' ?>">Processing (<?= $counts['processing'] ?? 0 ?>)</a>
      <a href="/admin/orders?status=shipped"   class="filter-tab <?= $status==='shipped'    ?'active':'' ?>">Shipped (<?= $counts['shipped']    ?? 0 ?>)</a>
      <a href="/admin/orders?status=delivered" class="filter-tab <?= $status==='delivered'  ?'active':'' ?>">Delivered (<?= $counts['delivered']  ?? 0 ?>)</a>
      <a href="/admin/orders?status=cancelled" class="filter-tab <?= $status==='cancelled'  ?'active':'' ?>">Cancelled (<?= $counts['cancelled']  ?? 0 ?>)</a>
    </div>

    <!-- Search -->
    <div class="search-bar" style="margin-bottom:12px">
      <form method="GET" style="display:flex;gap:8px;align-items:center">
        <input type="hidden" name="status" value="<?= clean($status) ?>">
        <input type="text" name="q" placeholder="Search name, order #, phone..." value="<?= clean($search) ?>" style="max-width:300px">
        <button type="submit" class="btn btn-outline btn-sm">Search</button>
        <?php if ($search): ?><a href="/admin/orders?status=<?= $status ?>" class="btn btn-ghost btn-sm">✕ Clear</a><?php endif; ?>
      </form>
    </div>

    <!-- Bulk action bar -->
    <div id="bulkBar" style="display:none;background:var(--navy);color:#fff;padding:12px 18px;border-radius:10px;margin-bottom:12px;align-items:center;gap:12px;flex-wrap:wrap">
      <span id="bulkCount" style="font-size:13px;font-weight:600">0 selected</span>
      <form method="POST" action="/admin/orders" id="bulkForm" style="display:flex;gap:8px;align-items:center;margin:0">
        <input type="hidden" name="action" value="bulk_status">
        <div id="bulkIdsWrap"></div>
        <select name="bulk_st" style="padding:6px 10px;border-radius:6px;font-size:12px;border:none;outline:none;font-family:'Poppins',sans-serif">
          <option value="processing">⚙️ Processing</option>
          <option value="shipped">🚚 Shipped</option>
          <option value="delivered">✅ Delivered</option>
          <option value="new">🆕 New</option>
          <option value="cancelled">❌ Cancelled</option>
        </select>
        <button type="submit" class="btn btn-sm" style="background:#fff;color:var(--navy);font-weight:700">Apply</button>
      </form>
      <button onclick="clearSel()" style="background:transparent;border:1px solid rgba(255,255,255,.4);color:#fff;padding:5px 12px;border-radius:6px;font-size:12px;cursor:pointer;font-family:'Poppins',sans-serif">Clear</button>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th><input type="checkbox" id="selAll" onchange="toggleAll(this)" style="cursor:pointer"></th>
              <th>Order #</th><th>Date</th><th>Customer</th><th>Phone</th><th>Product / Qty</th>
              <th>Tracking</th><th>Status</th><th>Note</th><th>WhatsApp</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="10" style="text-align:center;color:var(--muted);padding:50px">No orders found.</td></tr>
          <?php else: ?>
          <?php foreach ($orders as $o):
            $items   = json_decode($o['items'] ?? '[]', true) ?: [];
            $prod    = implode(', ', array_map(fn($i) => ($i['name'] ?? ''), $items));
            $qty     = array_sum(array_column($items, 'qty'));
            $wa      = waNumber($o['customer_phone'] ?? '');
            $nm      = clean($o['customer_name'] ?? '');
            $qn      = clean($o['order_number'] ?? '');
            $pr      = clean($prod);
            $waConfirm  = $wa ? 'https://wa.me/'.$wa.'?text='.urlencode("Hello $nm! ✅ Your quote request *$qn* for *$pr* ({$qty} pcs) has been received. We will send you pricing within 24 hours. — ACM Asia Cosmetics") : '';
            $waProd     = $wa ? 'https://wa.me/'.$wa.'?text='.urlencode("Hello $nm! ⚙️ Your order *$qn* (*$pr*, {$qty} pcs) is now in production. We will update you once it is completed. — ACM Asia Cosmetics") : '';
            $waShip     = $wa ? 'https://wa.me/'.$wa.'?text='.urlencode("Assalam o Alaikum $nm! 🚚 Aapka order *$qn* dispatch ho gaya hai. Tracking: ".clean($o['tracking_number'] ?? 'Coming soon')." — ACM Asia Cosmetics") : '';
            $waDone     = $wa ? 'https://wa.me/'.$wa.'?text='.urlencode("Assalam o Alaikum $nm! ✅ Aapka order *$qn* deliver ho gaya. Feedback zaroor dein! — ACM Asia Cosmetics") : '';
          ?>
          <tr>
            <td><input type="checkbox" class="ord-chk" value="<?= $o['id'] ?>" onchange="updateBulk()" style="cursor:pointer"></td>
            <td>
              <a href="/admin/orders?id=<?= $o['id'] ?>" class="order-id" style="text-decoration:none;color:var(--navy)"><?= $qn ?></a>
              <div style="font-size:10px;color:var(--muted);margin-top:2px"><?= ucfirst($o['source'] ?? 'quote') ?></div>
            </td>
            <td style="font-size:11px;color:var(--muted);white-space:nowrap"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td>
              <div style="font-weight:600;font-size:13px"><?= $nm ?></div>
              <div style="font-size:11px;color:var(--muted)"><?= clean($o['customer_address'] ?? '') ?></div>
            </td>
            <td style="font-size:12px;white-space:nowrap">📞 <?= clean($o['customer_phone'] ?? '') ?></td>
            <td>
              <div style="font-size:12px;font-weight:600"><?= $pr ?: '—' ?></div>
              <?php if ($qty > 0): ?><div style="font-size:11px;color:var(--muted)"><?= number_format($qty) ?> pcs</div><?php endif; ?>
            </td>
            <td>
              <form method="POST" action="/admin/orders" style="display:flex;gap:4px;min-width:140px">
                <input type="hidden" name="action" value="inline_tracking">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <input type="text" name="tracking" value="<?= clean($o['tracking_number'] ?? '') ?>" placeholder="TCS#..." style="flex:1;min-width:0;padding:4px 8px;border:1.5px solid var(--border);border-radius:6px;font-size:11px;font-family:'Poppins',sans-serif;outline:none">
                <button type="submit" class="btn btn-outline btn-sm" style="padding:4px 8px;font-size:11px">✓</button>
              </form>
            </td>
            <td>
              <form method="POST" action="/admin/orders" style="display:inline">
                <input type="hidden" name="action" value="inline_status">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" onchange="this.form.submit()" class="status-sel">
                  <?php foreach (['new','processing','shipped','delivered','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td>
              <form method="POST" action="/admin/orders" style="display:flex;gap:4px;min-width:140px">
                <input type="hidden" name="action" value="inline_note">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <input type="text" name="note" value="<?= clean($o['notes'] ?? '') ?>" placeholder="Note..." style="flex:1;min-width:0;padding:4px 8px;border:1.5px solid var(--border);border-radius:6px;font-size:11px;font-family:'Poppins',sans-serif;outline:none">
                <button type="submit" class="btn btn-outline btn-sm" style="padding:4px 8px;font-size:11px">✓</button>
              </form>
            </td>
            <td>
              <?php if ($wa): ?>
              <div style="display:flex;flex-direction:column;gap:3px;min-width:110px">
                <a href="<?= $waConfirm ?>" target="_blank" class="wa-tpl-btn" style="background:#25D366">✅ Quote OK</a>
                <a href="<?= $waProd ?>"    target="_blank" class="wa-tpl-btn" style="background:#1da851">⚙️ Production</a>
                <a href="<?= $waShip ?>"    target="_blank" class="wa-tpl-btn" style="background:#128C7E">🚚 Shipped</a>
                <a href="<?= $waDone ?>"    target="_blank" class="wa-tpl-btn" style="background:#075E54">📦 Delivered</a>
              </div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</div>

<!-- Order Detail Side Panel -->
<?php if ($selectedOrder):
  $items = json_decode($selectedOrder['items'] ?? '[]', true) ?: [];
?>
<div class="detail-overlay open" onclick="closePanel()"></div>
<div class="detail-panel open">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <div style="font-size:16px;font-weight:700"><?= clean($selectedOrder['order_number']) ?></div>
    <a href="/admin/orders" class="btn btn-ghost btn-sm" onclick="closePanel();return false">✕</a>
  </div>
  <div style="margin-bottom:16px">
    <span class="badge badge-<?= $selectedOrder['status'] ?>"><?= ucfirst($selectedOrder['status']) ?></span>
    <span style="font-size:11px;color:var(--muted);margin-left:8px"><?= date('d M Y, h:i A', strtotime($selectedOrder['created_at'])) ?></span>
  </div>
  <div class="card" style="margin-bottom:12px;padding:14px">
    <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:8px;font-weight:600">Customer</div>
    <div style="font-weight:700"><?= clean($selectedOrder['customer_name']) ?></div>
    <div style="font-size:12px;color:var(--muted)"><?= clean($selectedOrder['customer_phone']) ?></div>
    <div style="font-size:12px;color:var(--muted)"><?= clean($selectedOrder['customer_email']) ?></div>
    <div style="font-size:12px;color:var(--muted);margin-top:4px"><?= nl2br(clean($selectedOrder['customer_address'])) ?></div>
  </div>
  <?php if ($items): ?>
  <div class="card" style="margin-bottom:12px;padding:14px">
    <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:8px;font-weight:600">Items</div>
    <?php foreach ($items as $item): ?>
    <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border);font-size:12px">
      <span><?= clean($item['name'] ?? '') ?> × <?= number_format((int)($item['qty'] ?? 1)) ?> pcs</span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php if (!empty($selectedOrder['notes'])): ?>
  <div class="card" style="margin-bottom:12px;padding:14px">
    <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px;font-weight:600">Notes</div>
    <div style="font-size:12px;white-space:pre-wrap"><?= nl2br(clean($selectedOrder['notes'])) ?></div>
  </div>
  <?php endif; ?>
  <form method="POST" style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px">
    <input type="hidden" name="action"         value="update_status">
    <input type="hidden" name="order_id"       value="<?= $selectedOrder['id'] ?>">
    <input type="hidden" name="order_number"   value="<?= clean($selectedOrder['order_number']) ?>">
    <input type="hidden" name="customer_email" value="<?= clean($selectedOrder['customer_email']) ?>">
    <input type="hidden" name="customer_name"  value="<?= clean($selectedOrder['customer_name']) ?>">
    <div style="font-size:13px;font-weight:600;margin-bottom:10px">Update Order</div>
    <div class="form-group">
      <label>Status</label>
      <select name="status">
        <?php foreach (['new','processing','shipped','delivered','cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $selectedOrder['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Tracking Number</label>
      <input type="text" name="tracking" value="<?= clean($selectedOrder['tracking_number'] ?? '') ?>" placeholder="TCS-12345...">
    </div>
    <div class="form-group">
      <label>Notes</label>
      <textarea name="notes" rows="2" style="resize:none"><?= clean($selectedOrder['notes'] ?? '') ?></textarea>
    </div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
      <input type="checkbox" name="notify" id="notify" value="1" <?= empty($selectedOrder['customer_email'])?'disabled':'' ?>>
      <label for="notify" style="font-size:11px;color:var(--muted);cursor:pointer;text-transform:none;letter-spacing:0">Email customer about status change</label>
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center">Save Changes</button>
  </form>
</div>
<script>function closePanel(){history.back();}</script>
<?php endif; ?>

<style>
.wa-tpl-btn{display:block;color:#fff;text-decoration:none;font-family:'Poppins',sans-serif;font-size:10px;font-weight:700;padding:3px 8px;border-radius:5px;text-align:center}
select.status-sel{padding:5px 8px;border:1.5px solid var(--border);border-radius:6px;font-size:11px;font-family:'Poppins',sans-serif;outline:none;cursor:pointer;background:#fff;color:var(--navy);font-weight:600}
</style>
<script>
function updateBulk() {
  var chks = document.querySelectorAll('.ord-chk:checked');
  var bar  = document.getElementById('bulkBar');
  var cnt  = document.getElementById('bulkCount');
  var wrap = document.getElementById('bulkIdsWrap');
  if (!bar) return;
  if (chks.length > 0) {
    bar.style.display = 'flex';
    cnt.textContent = chks.length + ' selected';
    wrap.innerHTML = '';
    chks.forEach(function(c) {
      var inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = c.value;
      wrap.appendChild(inp);
    });
  } else { bar.style.display = 'none'; }
}
function toggleAll(el) {
  document.querySelectorAll('.ord-chk').forEach(function(c){ c.checked = el.checked; });
  updateBulk();
}
function clearSel() {
  document.querySelectorAll('.ord-chk, #selAll').forEach(function(c){ c.checked = false; });
  updateBulk();
}
</script>
</body></html>
