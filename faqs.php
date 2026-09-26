<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$moqOptions  = [100, 200, 300, 500, 1000];
$allProducts = $pdo->query("SELECT id, name, category FROM products ORDER BY category, name")->fetchAll();

// Handle modal form POST
$modalSuccess = false;
$modalError   = '';
$modalRef     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'quote') {
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
        $modalError = 'Please fill in all required fields (Name, Phone, Product).';
    } else {
        $productName = '';
        foreach ($allProducts as $ap) {
            if ($ap['id'] == $pid) { $productName = $ap['name']; break; }
        }
        $productName = preg_replace('/^ACM\s+/i', '', $productName);
        $quoteNum = 'ACM-Q-' . strtoupper(substr(uniqid(), -6));
        $items    = [['name' => $productName, 'qty' => $finalQty, 'price' => 0]];
        $addr     = $city ?: 'Not specified';
        $notes    = "Company: $company\nQty: $finalQty pcs\nMessage: $message";

        $pdo->prepare("INSERT INTO orders (order_number,customer_name,customer_email,customer_phone,customer_address,items,total,source,notes) VALUES (?,?,?,?,?,?,0,'quote',?)")
            ->execute([$quoteNum, $name, $email, $phone, $addr, json_encode($items), $notes]);

        $adminBody = "New Quote Request: $quoteNum\n\nName: $name\nCompany: $company\nPhone: $phone\nEmail: $email\nCity: $city\nProduct: $productName\nQuantity: $finalQty pcs\nMessage:\n$message\n\nView: " . ADMIN_URL;
        $headers   = "From: " . MAIL_FROM . "\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        @mail(ADMIN_EMAIL, "Quote Request $quoteNum — ACM", $adminBody, $headers);

        if ($email) {
            $custBody = "Dear $name,\n\nThank you for your quote request!\n\nReference: $quoteNum\nProduct: $productName\nQuantity: $finalQty pcs\n\nOur team will get back to you within 24 hours.\n\nACM Asia Cosmetics & Manufactures Pvt. Ltd.\nPhone: +92 325 5129241";
            @mail($email, "Quote Request Received — $quoteNum", $custBody, "From: ACM Asia Cosmetics <" . MAIL_FROM . ">");
        }

        $modalSuccess = true;
        $modalRef = $quoteNum;
    }
}

$pageTitle = 'FAQs — ACM Asia Cosmetics Pakistan';
$metaDesc  = 'Frequently asked questions about ACM Asia Cosmetics — MOQ, private label, production time, samples, certifications and delivery.';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / FAQs</div>
    <h1>Frequently Asked Questions</h1>
    <p>Everything you need to know about working with ACM</p>
  </div>
</div>

<!-- FAQs — 2 columns 5×5 -->
<section class="section faq-section">
  <div class="container">
    <div class="faq-top">
      <p class="faq-eyebrow">FAQ</p>
      <h2 class="faq-main-title">Common Questions Answered</h2>
    </div>

    <div class="faq-grid-2col">

      <!-- Column 1 -->
      <div class="faq-col">
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            What is the minimum order quantity (MOQ)? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Our standard MOQ is 100 pieces per product variant. For bulk or custom orders, we offer flexible MOQ options — contact us on WhatsApp to discuss your requirements.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Do you offer private label and custom branding? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Yes! We specialize in private label manufacturing. You can use your own brand name, logo, and packaging design on any of our products. Our team will guide you through the complete branding process from design to delivery.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            How long does production take? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Standard production takes 7–14 working days after order confirmation and advance payment. Custom formulations or new packaging designs may take 3–4 weeks. Rush orders are available on request.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Can I get samples before placing a bulk order? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Yes, samples are available for most products. Sample charges apply and are adjusted against your bulk order. Contact us on WhatsApp to request samples and pricing — we typically dispatch samples within 3–5 working days.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Are your products ISO certified and quality tested? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">All ACM products are manufactured in our ISO-certified facility and undergo strict quality control testing at every stage — from raw material sourcing to final packaging — to ensure consistency and safety.</div>
        </div>
      </div>

      <!-- Column 2 -->
      <div class="faq-col">
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Do you deliver across Pakistan and internationally? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Yes, we deliver nationwide across Pakistan via TCS, Leopards, and other courier services. We also export to international markets. Shipping costs and timelines vary by location — reach out for a custom shipping quote.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Do you offer OEM and contract manufacturing? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Yes, we provide full OEM and contract manufacturing services. Whether you need an existing formula with your branding or a completely new formulation developed from scratch, our R&D team handles everything end-to-end.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Can I customize the product formulation? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Absolutely. Our in-house R&D team can customize any formula — adjusting ingredients, concentration levels, fragrance, texture, and packaging to meet your exact requirements and target market needs.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            What payment terms do you offer? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">We typically require 50% advance payment to start production and 50% before dispatch. For established clients with a track record, flexible payment terms may be available. Bank transfer and online payments are accepted.</div>
        </div>
        <div class="faq-item">
          <button class="faq-q" onclick="toggleFaq(this)">
            Do you provide product safety certificates and test reports? <span class="faq-icon">+</span>
          </button>
          <div class="faq-a">Yes, we provide Certificates of Analysis (COA), safety data sheets, and batch testing reports for all products. These are available on request and are especially useful for export orders requiring documentation.</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="faq-cta-section">
  <div class="container">
    <div class="faq-cta-inner">
      <div class="faq-cta-tag">STILL HAVE QUESTIONS?</div>
      <h2 class="faq-cta-title">Let's Talk About Your Requirements</h2>
      <p class="faq-cta-sub">Chat with us directly or request a price quote — we respond within 24 hours.</p>
      <div class="faq-cta-btns">
        <a href="https://wa.me/923255129241" target="_blank" class="faq-btn-wa">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.104.549 4.076 1.508 5.793L0 24l6.399-1.489A11.946 11.946 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.5-5.2-1.373l-.374-.22-3.8.885.928-3.694-.243-.38A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
          WhatsApp Now
        </a>
        <button class="faq-btn-quote" onclick="openQuoteModal()">Get A Quote →</button>
      </div>
    </div>
  </div>
</section>

<!-- Quote Modal -->
<div id="quoteModal" class="qm-overlay" onclick="if(event.target===this)closeQuoteModal()">
  <div class="qm-box">
    <button class="qm-close" onclick="closeQuoteModal()">✕</button>

    <?php if ($modalSuccess): ?>
    <div class="qm-success">
      <div class="qm-success-icon">✓</div>
      <h3>Quote Request Sent!</h3>
      <p>Reference: <strong><?= htmlspecialchars($modalRef) ?></strong></p>
      <p style="color:var(--muted);font-size:13px;margin-top:6px">We'll get back to you within 24 hours.</p>
      <button class="faq-btn-quote" style="margin-top:20px" onclick="closeQuoteModal()">Close</button>
    </div>
    <?php else: ?>

    <div class="qm-header">
      <div class="qm-title">Get A Price Quote</div>
      <div class="qm-sub">We'll respond within 24 hours with pricing &amp; details</div>
    </div>

    <?php if ($modalError): ?>
    <div class="qm-error"><?= htmlspecialchars($modalError) ?></div>
    <?php endif; ?>

    <form method="POST" class="qm-form">
      <input type="hidden" name="form_type" value="quote">

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
          <label class="qf-label">Phone <span class="req">*</span></label>
          <input class="qf-input" type="tel" name="phone" required placeholder="03XX-XXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="qf-group">
          <label class="qf-label">Email</label>
          <input class="qf-input" type="email" name="email" placeholder="you@company.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
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
          <option value="<?= $ap['id'] ?>" <?= (isset($_POST['product_id']) && $_POST['product_id'] == $ap['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars(preg_replace('/^ACM\s+/i', '', $ap['name'])) ?>
          </option>
          <?php endforeach; if ($lastCat) echo '</optgroup>'; ?>
        </select>
      </div>

      <div class="qf-group">
        <label class="qf-label">Quantity (pcs) <span class="req">*</span></label>
        <div class="qf-moq-row">
          <?php foreach ($moqOptions as $opt): ?>
          <label class="qf-moq-btn <?= $opt == 100 ? 'selected' : '' ?>">
            <input type="radio" name="qty" value="<?= $opt ?>" <?= $opt == 100 ? 'checked' : '' ?> onchange="toggleCustomModal(false)">
            <span><?= number_format($opt) ?></span>
            <?php if ($opt >= 500): ?><small>Bulk</small><?php endif; ?>
          </label>
          <?php endforeach; ?>
          <label class="qf-moq-btn" id="modalCustomLabel">
            <input type="radio" name="qty" value="0" onchange="toggleCustomModal(true)">
            <span>Custom</span>
          </label>
        </div>
        <input class="qf-input" type="number" name="custom_qty" id="modalCustomQty" min="100" placeholder="Enter custom quantity (min 100)" style="display:none;margin-top:8px">
      </div>

      <div class="qf-group">
        <label class="qf-label">Message</label>
        <textarea class="qf-input qf-textarea" name="message" rows="2" placeholder="Private label, custom packaging, fragrance..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="qf-submit" style="width:100%;justify-content:center">Send Quote Request →</button>
      <p class="qf-note" style="text-align:center">Goes to <strong>info@acmpvtltd.com</strong> — reply within 24 hours</p>
    </form>
    <?php endif; ?>
  </div>
</div>

<style>
/* ── FAQ Section ───────────────────────────── */
.faq-section { background: var(--offwhite); }
.faq-eyebrow { font-size: 11px; font-weight: 800; letter-spacing: 2.5px; color: var(--red); text-transform: uppercase; margin-bottom: 8px; }
.faq-main-title { font-size: clamp(24px,3.5vw,34px); font-weight: 900; color: var(--navy); margin-bottom: 40px; }
.faq-top { text-align: center; margin-bottom: 20px; }
.faq-grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 28px; }
@media(max-width:700px) { .faq-grid-2col { grid-template-columns: 1fr; } }
.faq-col { display: flex; flex-direction: column; gap: 12px; }
.faq-item { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(13,34,88,.07); border: 1px solid #E8ECF5; }
.faq-q { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 22px; background: none; border: none; font-family: 'Poppins',sans-serif; font-size: 14px; font-weight: 700; color: var(--navy); cursor: pointer; text-align: left; line-height: 1.4; transition: background .15s; }
.faq-q:hover { background: rgba(13,34,88,.03); }
.faq-icon { font-size: 22px; font-weight: 400; flex-shrink: 0; transition: transform .25s; color: var(--red); }
.faq-a { max-height: 0; overflow: hidden; padding: 0 22px; font-size: 13.5px; color: #555; line-height: 1.8; transition: max-height .3s ease, padding .3s ease; }
.faq-item.open .faq-a { max-height: 250px; padding: 2px 22px 18px; }
.faq-item.open .faq-icon { transform: rotate(45deg); }
.faq-item.open .faq-q { color: var(--red); }

/* ── CTA ───────────────────────────────────── */
.faq-cta-section { background: linear-gradient(135deg,#091B47 0%,#0D2258 60%,#1a1060 100%); padding: 72px 0; position: relative; overflow: hidden; }
.faq-cta-section::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--red),#ff6b6b,var(--red)); }
.faq-cta-inner { text-align:center; position:relative; z-index:1; max-width:560px; margin:0 auto; }
.faq-cta-tag { font-size:11px; font-weight:800; letter-spacing:3px; color:var(--red); text-transform:uppercase; margin-bottom:14px; }
.faq-cta-title { font-size:clamp(22px,3.5vw,34px); font-weight:900; color:#fff; line-height:1.2; margin-bottom:12px; }
.faq-cta-sub { font-size:15px; color:rgba(255,255,255,.7); margin-bottom:32px; line-height:1.6; }
.faq-cta-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }
.faq-btn-wa { display:inline-flex; align-items:center; gap:10px; background:#25D366; color:#fff; font-family:'Poppins',sans-serif; font-size:15px; font-weight:700; padding:14px 30px; border-radius:8px; transition:background .2s,box-shadow .2s; box-shadow:0 4px 20px rgba(37,211,102,.35); border:none; cursor:pointer; text-decoration:none; }
.faq-btn-wa:hover { background:#1ebe5a; }
.faq-btn-quote { display:inline-flex; align-items:center; background:var(--red); color:#fff; font-family:'Poppins',sans-serif; font-size:15px; font-weight:700; padding:14px 30px; border-radius:8px; transition:background .2s,box-shadow .2s; box-shadow:0 4px 20px rgba(220,38,38,.3); border:none; cursor:pointer; text-decoration:none; }
.faq-btn-quote:hover { background:var(--red2); }

/* ── Modal ─────────────────────────────────── */
.qm-overlay { display:none; position:fixed; inset:0; background:rgba(9,27,71,.7); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px; }
.qm-overlay.open { display:flex; }
.qm-box { background:#fff; border-radius:20px; width:100%; max-width:580px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 24px 64px rgba(0,0,0,.3); }
.qm-close { position:absolute; top:16px; right:18px; background:rgba(13,34,88,.08); border:none; width:34px; height:34px; border-radius:50%; font-size:16px; cursor:pointer; color:var(--navy); z-index:10; transition:background .15s; display:flex; align-items:center; justify-content:center; }
.qm-close:hover { background:rgba(13,34,88,.16); }
.qm-header { padding:32px 36px 0; }
.qm-title { font-size:20px; font-weight:900; color:var(--navy); margin-bottom:4px; }
.qm-sub { font-size:13px; color:var(--muted); }
.qm-form { padding:24px 36px 32px; display:flex; flex-direction:column; gap:16px; }
@media(max-width:500px) { .qm-header { padding:28px 20px 0; } .qm-form { padding:20px 20px 28px; } }
.qm-error { margin:12px 36px 0; background:#FEF2F2; border:1.5px solid #FCA5A5; color:#991B1B; padding:12px 16px; border-radius:8px; font-size:13px; }
.qm-success { padding:48px 36px; text-align:center; }
.qm-success-icon { width:56px; height:56px; background:#22C55E; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:26px; color:#fff; margin:0 auto 16px; font-weight:700; }
.qm-success h3 { font-size:20px; font-weight:800; color:#166534; margin-bottom:8px; }
.qm-success p { color:#166534; }

/* Form shared styles */
.qf-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media(max-width:480px) { .qf-grid-2 { grid-template-columns:1fr; } }
.qf-group { display:flex; flex-direction:column; gap:5px; }
.qf-label { font-size:12px; font-weight:700; color:var(--navy); }
.req { color:var(--red); }
.qf-input { border:1.5px solid #D1D9E6; border-radius:8px; padding:10px 13px; font-family:'Poppins',sans-serif; font-size:13.5px; color:var(--text); background:#fff; outline:none; transition:border-color .2s; width:100%; }
.qf-input:focus { border-color:var(--navy); }
.qf-select { cursor:pointer; }
.qf-textarea { resize:vertical; min-height:76px; }
.qf-moq-row { display:flex; gap:7px; flex-wrap:wrap; }
.qf-moq-btn { display:flex; flex-direction:column; align-items:center; justify-content:center; border:1.5px solid #D1D9E6; border-radius:8px; padding:9px 14px; cursor:pointer; transition:all .15s; min-width:66px; text-align:center; }
.qf-moq-btn input { display:none; }
.qf-moq-btn span { font-size:12px; font-weight:700; color:var(--navy); }
.qf-moq-btn small { font-size:10px; color:var(--muted); margin-top:2px; }
.qf-moq-btn.selected { border-color:var(--navy); background:var(--navy); }
.qf-moq-btn.selected span { color:#fff; }
.qf-moq-btn.selected small { color:rgba(255,255,255,.7); }
.qf-submit { background:var(--red); color:#fff; font-family:'Poppins',sans-serif; font-size:14px; font-weight:700; padding:14px 28px; border-radius:8px; border:none; cursor:pointer; transition:background .2s; display:inline-flex; align-items:center; }
.qf-submit:hover { background:var(--red2); }
.qf-note { font-size:11px; color:var(--muted); }
</style>

<script>
function openQuoteModal() {
  document.getElementById('quoteModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeQuoteModal() {
  document.getElementById('quoteModal').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeQuoteModal(); });

function toggleFaq(btn) {
  var item = btn.parentElement;
  var wasOpen = item.classList.contains('open');
  document.querySelectorAll('.faq-item.open').forEach(function(el){ el.classList.remove('open'); });
  if (!wasOpen) item.classList.add('open');
}

function toggleCustomModal(show) {
  var inp = document.getElementById('modalCustomQty');
  var lbl = document.getElementById('modalCustomLabel');
  inp.style.display = show ? 'block' : 'none';
  if (show) inp.focus();
  document.querySelectorAll('.qf-moq-btn').forEach(function(b){ b.classList.remove('selected'); });
  if (show) lbl.classList.add('selected');
}
document.querySelectorAll('.qf-moq-btn input[type=radio]').forEach(function(r){
  r.addEventListener('change', function(){
    document.querySelectorAll('.qf-moq-btn').forEach(function(b){ b.classList.remove('selected'); });
    this.closest('.qf-moq-btn').classList.add('selected');
  });
});

<?php if ($modalSuccess || $modalError): ?>
window.addEventListener('load', function(){ openQuoteModal(); });
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
