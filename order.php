<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$success = false;
$error   = '';

$preProduct = (int)($_GET['product'] ?? 0);
$preQty     = (int)($_GET['qty'] ?? 100);
$moqOptions = [100, 200, 300, 500, 1000];

$allProducts = $pdo->query("SELECT id, name, category FROM products ORDER BY category, name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $company = trim($_POST['company'] ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $email   = trim($_POST['email']   ?? '');
    $city    = trim($_POST['city']    ?? '');
    $pid     = (int)($_POST['product_id'] ?? 0);
    $qty     = (int)($_POST['qty'] ?? 100);
    $custom  = (int)($_POST['custom_qty'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    $finalQty = $custom > 0 ? $custom : $qty;

    if (!$name || !$phone || !$pid) {
        $error = 'Please fill in all required fields.';
    } else {
        $productName = '';
        foreach ($allProducts as $ap) {
            if ($ap['id'] == $pid) { $productName = $ap['name']; break; }
        }
        $productName = preg_replace('/^ACM\s+/i', '', $productName);

        $quoteNum = 'ACM-Q-' . strtoupper(substr(uniqid(), -6));
        $items = [['name' => $productName, 'qty' => $finalQty, 'price' => 0]];

        // Save to orders table (source = quote)
        $addr = $city ?: 'Not specified';
        $notes = "Company: $company\nQty: $finalQty pcs\nMessage: $message";
        $pdo->prepare("INSERT INTO orders (order_number,customer_name,customer_email,customer_phone,customer_address,items,total,source,notes) VALUES (?,?,?,?,?,?,0,'quote',?)")
            ->execute([$quoteNum, $name, $email, $phone, $addr, json_encode($items), $notes]);

        // Email to admin
        $adminBody = "New Quote Request: $quoteNum\n\n";
        $adminBody .= "Name: $name\n";
        $adminBody .= "Company: $company\n";
        $adminBody .= "Phone: $phone\n";
        $adminBody .= "Email: $email\n";
        $adminBody .= "City: $city\n";
        $adminBody .= "Product: $productName\n";
        $adminBody .= "Quantity: $finalQty pcs\n";
        $adminBody .= "Message:\n$message\n\n";
        $adminBody .= "View in Admin Panel: " . ADMIN_URL;

        $headers  = "From: " . MAIL_FROM . "\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        @mail(ADMIN_EMAIL, "Quote Request $quoteNum — ACM", $adminBody, $headers);

        // Confirm email to customer
        if ($email) {
            $custBody  = "Dear $name,\n\n";
            $custBody .= "Thank you for your quote request!\n\n";
            $custBody .= "Reference: $quoteNum\n";
            $custBody .= "Product: $productName\n";
            $custBody .= "Quantity: $finalQty pcs\n\n";
            $custBody .= "Our team will review your request and get back to you within 24 hours.\n\n";
            $custBody .= "ACM Asia Cosmetics & Manufactures Pvt. Ltd.\n";
            $custBody .= "Phone: +92 325 5129241\n";
            $custBody .= "Website: www.acmpvtltd.com";
            @mail($email, "Quote Request Received — $quoteNum", $custBody, "From: ACM Asia Cosmetics <" . MAIL_FROM . ">");
        }

        $_SESSION['quote_success'] = ['num' => $quoteNum, 'product' => $productName, 'qty' => $finalQty, 'name' => $name];
        header('Location: /order?success=1');
        exit;
    }
}

// Success page
if (isset($_GET['success']) && isset($_SESSION['quote_success'])) {
    $q = $_SESSION['quote_success'];
    unset($_SESSION['quote_success']);
    $pageTitle = 'Quote Request Sent — ACM Asia Cosmetics';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section style="background:var(--bg);min-height:80vh;display:flex;align-items:center">
      <div class="container" style="max-width:600px;margin:0 auto;text-align:center;padding:60px 24px">
        <div style="font-size:64px;margin-bottom:20px">✅</div>
        <h1 style="font-size:28px;font-weight:800;color:var(--navy);margin-bottom:12px">Quote Request Sent!</h1>
        <p style="font-size:16px;color:var(--muted);margin-bottom:8px">
          Thank you <strong><?= htmlspecialchars($q['name']) ?></strong>! Your request for
          <strong><?= htmlspecialchars($q['qty']) ?> pcs of <?= htmlspecialchars($q['product']) ?></strong>
          has been received.
        </p>
        <p style="font-size:14px;color:var(--muted);margin-bottom:30px">Reference: <strong><?= $q['num'] ?></strong> — We'll get back to you within 24 hours.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
          <a href="/products" class="btn btn-navy">Browse More Products</a>
          <a href="https://wa.me/923255129241?text=Hi, my quote reference is <?= $q['num'] ?>" target="_blank" class="btn-wa-lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            Follow Up on WhatsApp
          </a>
        </div>
      </div>
    </section>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php exit;
}

$pageTitle = 'Get A Quote — ACM Asia Cosmetics';
$metaDesc  = 'Request a bulk manufacturing quote from ACM Asia Cosmetics. Minimum order 100 pcs. Private label available.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Get A Quote</div>
    <h1>Get A Quote</h1>
    <p>Fill in your details and we'll send you a customized price quote within 24 hours</p>
  </div>
</div>

<section class="section" style="padding-top:50px;padding-bottom:70px">
  <div class="container">
    <div class="quote-layout">

      <!-- Left: Info panel -->
      <div class="quote-info">
        <h2 class="quote-info-heading">Why Choose ACM?</h2>
        <div class="quote-perks">
          <div class="quote-perk">
            <div class="quote-perk-icon">🏭</div>
            <div>
              <div class="quote-perk-title">In-House Manufacturing</div>
              <div class="quote-perk-desc">State-of-the-art facility in Pakistan. No middlemen.</div>
            </div>
          </div>
          <div class="quote-perk">
            <div class="quote-perk-icon">🏷️</div>
            <div>
              <div class="quote-perk-title">Private Label Available</div>
              <div class="quote-perk-desc">Your brand, our formula. Custom packaging & labeling.</div>
            </div>
          </div>
          <div class="quote-perk">
            <div class="quote-perk-icon">📦</div>
            <div>
              <div class="quote-perk-title">Low MOQ — 100 Pcs</div>
              <div class="quote-perk-desc">Start small and scale up. Bulk discounts available.</div>
            </div>
          </div>
          <div class="quote-perk">
            <div class="quote-perk-icon">⚡</div>
            <div>
              <div class="quote-perk-title">Fast Turnaround</div>
              <div class="quote-perk-desc">Quote within 24 hrs. Production timeline discussed after.</div>
            </div>
          </div>
          <div class="quote-perk">
            <div class="quote-perk-icon">✅</div>
            <div>
              <div class="quote-perk-title">Quality Certified</div>
              <div class="quote-perk-desc">Every batch tested. ISO-compliant production process.</div>
            </div>
          </div>
        </div>

        <div class="quote-contact-box">
          <div class="quote-contact-label">Prefer to talk directly?</div>
          <a href="https://wa.me/923255129241" target="_blank" class="btn-wa-lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            Chat on WhatsApp
          </a>
          <div class="quote-or">or call <a href="tel:+923255129241">+92 325 5129241</a></div>
        </div>
      </div>

      <!-- Right: Quote Form -->
      <div class="quote-form-card">
        <div class="quote-form-header">
          <div class="quote-form-title">Request a Price Quote</div>
          <div class="quote-form-sub">We'll respond within 24 hours with pricing & details</div>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin:20px 20px 0"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="quote-form-body">
          <div class="qf-grid-2">
            <div class="qf-group">
              <label class="qf-label">Full Name <span class="req">*</span></label>
              <input class="qf-input" type="text" name="name" required placeholder="Your name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="qf-group">
              <label class="qf-label">Company / Brand</label>
              <input class="qf-input" type="text" name="company" placeholder="Company name" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
            </div>
          </div>
          <div class="qf-grid-2">
            <div class="qf-group">
              <label class="qf-label">Phone Number <span class="req">*</span></label>
              <input class="qf-input" type="tel" name="phone" required placeholder="03XX-XXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="qf-group">
              <label class="qf-label">Email Address</label>
              <input class="qf-input" type="email" name="email" placeholder="you@company.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
          </div>
          <div class="qf-group">
            <label class="qf-label">City</label>
            <input class="qf-input" type="text" name="city" placeholder="Lahore, Karachi..." value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
          </div>

          <div class="qf-group">
            <label class="qf-label">Product <span class="req">*</span></label>
            <select class="qf-input qf-select" name="product_id" required>
              <option value="">— Select a product —</option>
              <?php
              $lastCat = '';
              foreach ($allProducts as $ap):
                if ($ap['category'] !== $lastCat) {
                    if ($lastCat) echo '</optgroup>';
                    echo '<optgroup label="' . htmlspecialchars($ap['category']) . '">';
                    $lastCat = $ap['category'];
                }
              ?>
              <option value="<?= $ap['id'] ?>" <?= ($preProduct == $ap['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars(preg_replace('/^ACM\s+/i', '', $ap['name'])) ?>
              </option>
              <?php endforeach; if ($lastCat) echo '</optgroup>'; ?>
            </select>
          </div>

          <div class="qf-group">
            <label class="qf-label">Quantity (pieces) <span class="req">*</span></label>
            <div class="qf-moq-row">
              <?php foreach ($moqOptions as $opt): ?>
              <label class="qf-moq-btn <?= ($preQty == $opt || (!$preQty && $opt == 100)) ? 'selected' : '' ?>">
                <input type="radio" name="qty" value="<?= $opt ?>" <?= ($preQty == $opt || (!$preQty && $opt == 100)) ? 'checked' : '' ?> onchange="toggleCustom(false)">
                <span><?= number_format($opt) ?></span>
                <?php if ($opt >= 500): ?><small>Bulk</small><?php endif; ?>
              </label>
              <?php endforeach; ?>
              <label class="qf-moq-btn" id="customLabel">
                <input type="radio" name="qty" value="0" onchange="toggleCustom(true)">
                <span>Custom</span>
              </label>
            </div>
            <input class="qf-input" type="number" name="custom_qty" id="customQtyInput" min="100" placeholder="Enter custom quantity (min 100)" style="display:none;margin-top:10px">
          </div>

          <div class="qf-group">
            <label class="qf-label">Additional Requirements</label>
            <textarea class="qf-input qf-textarea" name="message" rows="3" placeholder="Private label, custom packaging, fragrance, special formulation..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="qf-submit">
            Send Quote Request →
          </button>
          <p class="qf-note">Your request goes directly to <strong>info@acmpvtltd.com</strong>. We respond within 24 hours.</p>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
function toggleCustom(show) {
  const inp = document.getElementById('customQtyInput');
  const lbl = document.getElementById('customLabel');
  inp.style.display = show ? 'block' : 'none';
  if (show) inp.focus();
  document.querySelectorAll('.qf-moq-btn').forEach(b => b.classList.remove('selected'));
  if (show) lbl.classList.add('selected');
}
document.querySelectorAll('.qf-moq-btn input[type=radio]').forEach(r => {
  r.addEventListener('change', function() {
    document.querySelectorAll('.qf-moq-btn').forEach(b => b.classList.remove('selected'));
    this.closest('.qf-moq-btn').classList.add('selected');
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
