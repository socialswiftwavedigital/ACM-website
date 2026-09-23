<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$success = false;
$error   = '';

// Pre-selected product
$preProduct = (int)($_GET['product'] ?? 0);
$preQty     = max(1, (int)($_GET['qty'] ?? 1));

// Fetch all products for the dropdown
$allProducts = $pdo->query("SELECT id, name, price, category FROM products WHERE stock > 0 ORDER BY category, name")->fetchAll();
$preProductData = null;
if ($preProduct) {
    foreach ($allProducts as $ap) {
        if ($ap['id'] == $preProduct) { $preProductData = $ap; break; }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');
    $pids    = $_POST['product_id'] ?? [];
    $qtys    = $_POST['qty'] ?? [];

    if (!$name || !$phone || !$address) {
        $error = 'Please fill in all required fields.';
    } elseif (empty($pids)) {
        $error = 'Please select at least one product.';
    } else {
        // Build items
        $items = [];
        $total = 0;
        foreach ($pids as $i => $pid) {
            $pid = (int)$pid;
            $qty = max(1, (int)($qtys[$i] ?? 1));
            foreach ($allProducts as $ap) {
                if ($ap['id'] == $pid) {
                    $items[] = ['name' => $ap['name'], 'qty' => $qty, 'price' => (float)$ap['price']];
                    $total  += $ap['price'] * $qty;
                    break;
                }
            }
        }

        if (empty($items)) { $error = 'Invalid product selection.'; }
        else {
            $orderNum = 'ACM-' . strtoupper(substr(uniqid(), -6));

            // Save customer
            if ($email) {
                $c = $pdo->prepare("SELECT id FROM customers WHERE email=?");
                $c->execute([$email]);
                if (!$c->fetch()) {
                    $pdo->prepare("INSERT INTO customers (name,email,phone,city,total_orders,total_spent) VALUES (?,?,?,?,1,?)")
                        ->execute([$name, $email, $phone, $city, $total]);
                } else {
                    $pdo->prepare("UPDATE customers SET total_orders=total_orders+1, total_spent=total_spent+? WHERE email=?")
                        ->execute([$total, $email]);
                }
            }

            // Save order
            $pdo->prepare("INSERT INTO orders (order_number,customer_name,customer_email,customer_phone,customer_address,items,total,source,notes) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$orderNum, $name, $email, $phone, $address . ($city ? ', ' . $city : ''), json_encode($items), $total, 'website', $notes]);

            $orderId = $pdo->lastInsertId();

            // Email notifications
            $adminBody = "New Order: $orderNum\n\nCustomer: $name\nPhone: $phone\nCity: $city\nAddress: $address\nTotal: Rs. " . number_format($total) . "\n\nItems:\n";
            foreach ($items as $it) $adminBody .= "- {$it['name']} x{$it['qty']} = Rs." . number_format($it['price']*$it['qty']) . "\n";
            @mail(ADMIN_EMAIL, "New Order $orderNum — ACM", $adminBody, "From: " . MAIL_FROM);

            if ($email) {
                $custBody = "Dear $name,\n\nThank you for your order!\n\nOrder Number: $orderNum\nTotal: Rs. " . number_format($total) . "\n\nWe will contact you shortly to confirm delivery.\n\nACM Asia Cosmetics\nwww.acmpvtltd.com";
                @mail($email, "Order Confirmed — $orderNum", $custBody, "From: ACM Asia Cosmetics <" . MAIL_FROM . ">");
            }

            $_SESSION['order_success'] = ['num' => $orderNum, 'total' => $total, 'name' => $name];
            header('Location: /order?success=1');
            exit;
        }
    }
}

// Show success
if (isset($_GET['success']) && isset($_SESSION['order_success'])) {
    $ord = $_SESSION['order_success'];
    unset($_SESSION['order_success']);
    $pageTitle = 'Order Confirmed — ACM Asia Cosmetics';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section style="background:var(--bg);min-height:80vh;display:flex;align-items:center">
      <div class="container">
        <div class="success-card">
          <div class="success-icon">🎉</div>
          <div class="success-title">Order Placed!</div>
          <div class="success-sub">
            Thank you <strong><?= htmlspecialchars($ord['name']) ?></strong>! Your order <strong><?= $ord['num'] ?></strong> for <strong>Rs. <?= number_format($ord['total']) ?></strong> has been received. We will call you shortly to confirm.
          </div>
          <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
            <a href="/" class="btn btn-navy">Continue Shopping</a>
            <a href="https://wa.me/923000000000?text=I+placed+order+<?= $ord['num'] ?>" target="_blank" class="btn btn-whatsapp">💬 Track on WhatsApp</a>
          </div>
        </div>
      </div>
    </section>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php exit;
}

$pageTitle = 'Place Order — ACM Asia Cosmetics';
$metaDesc  = 'Order ACM skincare products online. Fast delivery across Pakistan. Cash on delivery available.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="order-section">
  <div class="container order-grid">
    <!-- Left Info -->
    <div class="order-info">
      <div class="section-badge" style="margin-bottom:16px">Order Online</div>
      <h1>Get Your Products<br>Delivered Home</h1>
      <p>Fill the form and we'll deliver your order within 2-3 days. Cash on delivery available across Pakistan.</p>
      <div class="order-perks">
        <div class="order-perk"><div class="order-perk-icon">🚚</div> Free delivery on orders above Rs. 2,000</div>
        <div class="order-perk"><div class="order-perk-icon">💵</div> Cash on delivery available</div>
        <div class="order-perk"><div class="order-perk-icon">📞</div> We call to confirm before dispatch</div>
        <div class="order-perk"><div class="order-perk-icon">✅</div> 100% authentic products</div>
        <div class="order-perk"><div class="order-perk-icon">💬</div> <a href="https://wa.me/923000000000" style="color:rgba(255,255,255,.8)" target="_blank">Or order via WhatsApp</a></div>
      </div>
    </div>

    <!-- Order Form -->
    <div class="order-form-card">
      <div class="form-title">Place Your Order</div>
      <div class="form-sub">Fill in your details — we'll confirm via call</div>

      <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" id="orderForm">
        <div class="form-grid">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" required placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Phone Number *</label>
            <input type="tel" name="phone" required placeholder="03XX-XXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
          </div>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label>Email (Optional)</label>
            <input type="email" name="email" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>City *</label>
            <input type="text" name="city" required placeholder="Lahore, Karachi..." value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label>Delivery Address *</label>
          <textarea name="address" required rows="2" placeholder="House #, Street, Area..."><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>

        <!-- Products -->
        <div class="form-group">
          <label>Products *</label>
          <div class="order-items" id="itemsContainer">
            <div class="order-item-row" data-row="0">
              <select name="product_id[]" required>
                <option value="">Select product...</option>
                <?php foreach ($allProducts as $ap): ?>
                <option value="<?= $ap['id'] ?>" data-price="<?= $ap['price'] ?>"
                  <?= ($preProduct == $ap['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ap['name']) ?> — Rs. <?= number_format($ap['price']) ?>
                </option>
                <?php endforeach; ?>
              </select>
              <input type="number" name="qty[]" value="<?= $preQty ?>" min="1" max="100" placeholder="Qty">
              <button type="button" onclick="removeItem(this)" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);padding:4px">×</button>
            </div>
          </div>
          <button type="button" class="add-item-btn" onclick="addItem()">+ Add Another Product</button>
        </div>

        <!-- Total -->
        <div class="order-total">
          <div class="order-total-row"><span>Subtotal</span><span id="subtotalDisplay">—</span></div>
          <div class="order-total-row"><span>Delivery</span><span>Free (above Rs.2,000) / Rs.200</span></div>
          <div class="order-total-final"><span>Total</span><span id="totalDisplay">—</span></div>
        </div>

        <div class="form-group">
          <label>Special Notes</label>
          <textarea name="notes" rows="2" placeholder="Any special instructions..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">
          Place Order →
        </button>
        <div style="text-align:center;margin-top:12px;font-size:12px;color:var(--muted)">
          By placing order you agree to our <a href="/contact" style="color:var(--navy)">terms</a>. We'll call to confirm.
        </div>
      </form>
    </div>
  </div>
</section>

<script>
const products = <?= json_encode(array_column($allProducts, null, 'id')) ?>;

function calcTotal() {
  let total = 0;
  document.querySelectorAll('.order-item-row').forEach(row => {
    const sel = row.querySelector('select');
    const qty = parseInt(row.querySelector('input[type=number]').value) || 0;
    if (sel.value) {
      const price = parseFloat(sel.options[sel.selectedIndex].dataset.price || 0);
      total += price * qty;
    }
  });
  document.getElementById('subtotalDisplay').textContent = total ? 'Rs. ' + total.toLocaleString() : '—';
  document.getElementById('totalDisplay').textContent = total ? 'Rs. ' + (total < 2000 ? total+200 : total).toLocaleString() : '—';
}

function addItem() {
  const container = document.getElementById('itemsContainer');
  const first = container.querySelector('.order-item-row');
  const clone = first.cloneNode(true);
  clone.querySelector('select').value = '';
  clone.querySelector('input[type=number]').value = 1;
  container.appendChild(clone);
  clone.querySelector('select').addEventListener('change', calcTotal);
  clone.querySelector('input[type=number]').addEventListener('input', calcTotal);
}

function removeItem(btn) {
  const rows = document.querySelectorAll('.order-item-row');
  if (rows.length > 1) { btn.closest('.order-item-row').remove(); calcTotal(); }
}

document.querySelectorAll('select, input[type=number]').forEach(el => el.addEventListener('change', calcTotal));
calcTotal();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
