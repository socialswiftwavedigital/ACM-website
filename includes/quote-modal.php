<?php
$_qmProducts = $pdo->query("SELECT id, name, category FROM products ORDER BY category, name")->fetchAll();
?>
<!-- ── Global Quote Modal ── -->
<div id="quoteModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(9,27,71,.72);z-index:9999;align-items:center;justify-content:center;padding:16px" onclick="if(event.target===this)closeQuoteModal()">
  <div style="background:#fff;border-radius:20px;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative;box-shadow:0 24px 64px rgba(0,0,0,.35)">

    <!-- Close -->
    <button onclick="closeQuoteModal()" style="position:absolute;top:14px;right:16px;background:rgba(13,34,88,.08);border:none;width:34px;height:34px;border-radius:50%;font-size:16px;cursor:pointer;color:#0D2258;z-index:10;display:flex;align-items:center;justify-content:center">✕</button>

    <!-- Header -->
    <div style="padding:28px 32px 0">
      <div style="font-size:20px;font-weight:900;color:#0D2258;margin-bottom:4px">Get A Price Quote</div>
      <div style="font-size:13px;color:#8898AA">We'll respond within 24 hours with pricing &amp; details</div>
    </div>

    <!-- Error -->
    <div id="qmError" style="display:none;margin:12px 32px 0;background:#FEF2F2;border:1.5px solid #FCA5A5;color:#991B1B;padding:12px 16px;border-radius:8px;font-size:13px"></div>

    <!-- Form -->
    <form id="qmForm" style="padding:20px 32px 28px;display:flex;flex-direction:column;gap:14px">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:12px;font-weight:700;color:#0D2258">Full Name <span style="color:#DC2626">*</span></label>
          <input name="name" type="text" required placeholder="Your name" style="border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:12px;font-weight:700;color:#0D2258">Company / Brand</label>
          <input name="company" type="text" placeholder="Company name" style="border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:12px;font-weight:700;color:#0D2258">Phone <span style="color:#DC2626">*</span></label>
          <input name="phone" type="tel" required placeholder="03XX-XXXXXXX" style="border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:12px;font-weight:700;color:#0D2258">Email</label>
          <input name="email" type="email" placeholder="you@company.com" style="border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%">
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:5px">
        <label style="font-size:12px;font-weight:700;color:#0D2258">Product <span style="color:#DC2626">*</span></label>
        <select id="qmProduct" name="product_id" required style="border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%;cursor:pointer;background:#fff">
          <option value="">— Select a product —</option>
          <?php
          $lastCat = '';
          foreach ($_qmProducts as $ap):
            if ($ap['category'] !== $lastCat) {
                if ($lastCat) echo '</optgroup>';
                echo '<optgroup label="' . htmlspecialchars($ap['category']) . '">';
                $lastCat = $ap['category'];
            }
          ?>
          <option value="<?= $ap['id'] ?>"><?= htmlspecialchars(preg_replace('/^ACM\s+/i', '', $ap['name'])) ?></option>
          <?php endforeach; if ($lastCat) echo '</optgroup>'; ?>
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:7px">
        <label style="font-size:12px;font-weight:700;color:#0D2258">Quantity (pcs) <span style="color:#DC2626">*</span></label>
        <div style="display:flex;gap:7px;flex-wrap:wrap" id="qmQtyRow">
          <?php foreach ([100,200,300,500,1000] as $i=>$opt): ?>
          <label class="qm-qty-btn <?= $i===0?'qm-sel':'' ?>" onclick="qmSelectQty(this,<?= $opt ?>,false)" style="display:flex;flex-direction:column;align-items:center;border:1.5px solid <?= $i===0?'#0D2258':'#D1D9E6' ?>;border-radius:8px;padding:9px 14px;cursor:pointer;min-width:66px;background:<?= $i===0?'#0D2258':'#fff' ?>">
            <span style="font-size:12px;font-weight:700;color:<?= $i===0?'#fff':'#0D2258' ?>"><?= number_format($opt) ?></span>
            <?php if($opt>=500): ?><span style="font-size:10px;color:<?= $i===0?'rgba(255,255,255,.7)':'#8898AA' ?>">Bulk</span><?php endif; ?>
          </label>
          <?php endforeach; ?>
          <label class="qm-qty-btn" id="qmCustomLabel" onclick="qmSelectQty(this,0,true)" style="display:flex;flex-direction:column;align-items:center;border:1.5px solid #D1D9E6;border-radius:8px;padding:9px 14px;cursor:pointer;min-width:66px;background:#fff">
            <span style="font-size:12px;font-weight:700;color:#0D2258">Custom</span>
          </label>
        </div>
        <input type="hidden" name="qty" id="qmQtyVal" value="100">
        <input type="number" name="custom_qty" id="qmCustomInput" min="100" placeholder="Enter quantity (min 100)" style="display:none;border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%">
      </div>

      <div style="display:flex;flex-direction:column;gap:5px">
        <label style="font-size:12px;font-weight:700;color:#0D2258">Message</label>
        <textarea name="message" rows="2" placeholder="Private label, custom packaging, fragrance..." style="border:1.5px solid #D1D9E6;border-radius:8px;padding:10px 13px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;width:100%;resize:vertical;min-height:72px"></textarea>
      </div>

      <button type="submit" id="qmSubmitBtn" style="background:#DC2626;color:#fff;font-family:'Poppins',sans-serif;font-size:14px;font-weight:700;padding:14px 28px;border-radius:8px;border:none;cursor:pointer;width:100%">Send Quote Request →</button>
      <p style="font-size:11px;color:#8898AA;text-align:center;margin-top:-6px">Goes to <strong>info@acmpvtltd.com</strong> — reply within 24 hours</p>
    </form>

    <!-- Success (hidden initially) -->
    <div id="qmSuccess" style="display:none;padding:48px 32px;text-align:center">
      <div style="width:56px;height:56px;background:#22C55E;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;margin:0 auto 16px;font-weight:700">✓</div>
      <h3 style="font-size:20px;font-weight:800;color:#166534;margin-bottom:8px">Quote Request Sent!</h3>
      <p style="color:#166534">Reference: <strong id="qmSuccessRef"></strong></p>
      <p style="color:#8898AA;font-size:13px;margin-top:6px">We'll get back to you within 24 hours.</p>
      <button onclick="closeQuoteModal()" style="margin-top:20px;background:#DC2626;color:#fff;font-family:'Poppins',sans-serif;font-size:14px;font-weight:700;padding:12px 28px;border-radius:8px;border:none;cursor:pointer">Close</button>
    </div>

  </div>
</div>

<script>
var _qmSelectedQty = 100;

function openQuoteModal(preProductId) {
  var m = document.getElementById('quoteModal');
  m.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  // Reset to form state (hide success if re-opening)
  document.getElementById('qmForm').style.display = 'flex';
  document.getElementById('qmSuccess').style.display = 'none';
  document.getElementById('qmError').style.display = 'none';
  // Pre-select product if provided
  if (preProductId) {
    var sel = document.getElementById('qmProduct');
    if (sel) sel.value = preProductId;
  }
}

function closeQuoteModal() {
  document.getElementById('quoteModal').style.display = 'none';
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeQuoteModal(); });

function qmSelectQty(el, val, isCustom) {
  document.querySelectorAll('.qm-qty-btn').forEach(function(b){
    b.style.borderColor = '#D1D9E6';
    b.style.background  = '#fff';
    b.querySelectorAll('span').forEach(function(s){ s.style.color = s.classList.contains('bulk-label') ? '#8898AA' : '#0D2258'; });
  });
  el.style.borderColor = '#0D2258';
  el.style.background  = '#0D2258';
  el.querySelectorAll('span').forEach(function(s){ s.style.color = '#fff'; });

  var ci = document.getElementById('qmCustomInput');
  var qv = document.getElementById('qmQtyVal');
  if (isCustom) {
    ci.style.display = 'block';
    ci.focus();
    qv.value = '0';
  } else {
    ci.style.display = 'none';
    qv.value = val;
    _qmSelectedQty = val;
  }
}

document.getElementById('qmForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var btn = document.getElementById('qmSubmitBtn');
  btn.disabled = true;
  btn.textContent = 'Sending...';

  var fd = new FormData(this);

  fetch('/api/quote.php', { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) {
        document.getElementById('qmForm').style.display = 'none';
        document.getElementById('qmSuccess').style.display = 'block';
        document.getElementById('qmSuccessRef').textContent = d.ref;
        document.getElementById('qmError').style.display = 'none';
      } else {
        var err = document.getElementById('qmError');
        err.textContent = d.error || 'Something went wrong. Please try again.';
        err.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Send Quote Request →';
      }
    })
    .catch(function(){
      var err = document.getElementById('qmError');
      err.textContent = 'Network error. Please try again or contact us on WhatsApp.';
      err.style.display = 'block';
      btn.disabled = false;
      btn.textContent = 'Send Quote Request →';
    });
});
</script>
