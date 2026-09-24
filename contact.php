<?php
session_start();
$sent  = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if (!$name || !$email || !$message) {
        $error = 'Please fill in all required fields.';
    } else {
        require_once __DIR__ . '/config.php';
        @mail(ADMIN_EMAIL, "Contact: $subject — ACM Website", "From: $name <$email>\n\n$message", "From: $name <" . MAIL_FROM . ">");
        $sent = true;
    }
}

$pageTitle = 'Contact Us — ACM Asia Cosmetics';
$metaDesc  = 'Get in touch with ACM Asia Cosmetics. Call, WhatsApp, or email us. We are here to help you with your skincare needs.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Contact</div>
    <h1>Get in Touch</h1>
    <p>We'd love to hear from you — call, WhatsApp, or send a message</p>
  </div>
</div>

<section class="section">
  <div class="container contact-grid">
    <!-- Contact Info -->
    <div class="contact-info-card">
      <h2>Contact Information</h2>
      <p>Reach out to us anytime. We usually respond within a few hours.</p>
      <div class="contact-item">
        <div class="contact-item-icon">📞</div>
        <div>
          <div class="contact-item-label">Phone</div>
          <div class="contact-item-value"><a href="tel:+923255129241" style="color:#fff">+92 325 5129241</a></div>
        </div>
      </div>
      <div class="contact-item">
        <div class="contact-item-icon">💬</div>
        <div>
          <div class="contact-item-label">WhatsApp</div>
          <div class="contact-item-value"><a href="https://wa.me/923255129241" target="_blank" style="color:#25D366">Chat on WhatsApp</a></div>
        </div>
      </div>
      <div class="contact-item">
        <div class="contact-item-icon">📧</div>
        <div>
          <div class="contact-item-label">Email</div>
          <div class="contact-item-value"><a href="mailto:info@acmpvtltd.com" style="color:#fff">info@acmpvtltd.com</a></div>
        </div>
      </div>
      <div class="contact-item">
        <div class="contact-item-icon">📍</div>
        <div>
          <div class="contact-item-label">Location</div>
          <div class="contact-item-value">Lahore, Pakistan</div>
        </div>
      </div>
      <div style="margin-top:32px">
        <a href="https://wa.me/923255129241" target="_blank" class="btn btn-whatsapp" style="width:100%;justify-content:center">
          💬 Chat Now on WhatsApp
        </a>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form-card">
      <h2 style="font-size:22px;font-weight:800;color:var(--navy);margin-bottom:4px">Send a Message</h2>
      <p style="color:var(--muted);font-size:13px;margin-bottom:24px">We'll get back to you within 24 hours.</p>

      <?php if ($sent): ?>
      <div class="alert alert-success">✅ Message sent! We'll get back to you soon.</div>
      <?php elseif ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label>Your Name *</label>
            <input type="text" name="name" required placeholder="Full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label>Subject</label>
          <input type="text" name="subject" placeholder="What is this about?" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Message *</label>
          <textarea name="message" required rows="5" placeholder="Write your message here..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-navy btn-lg" style="width:100%;justify-content:center">Send Message →</button>
      </form>
    </div>
  </div>
</section>

<!-- FAQs -->
<section class="section" id="faqs" style="background:var(--offwhite)">
  <div class="container" style="max-width:800px">
    <div class="section-eyebrow" style="text-align:center">FAQS</div>
    <h2 style="text-align:center;font-size:32px;font-weight:800;color:var(--navy);margin-bottom:40px">Frequently Asked Questions</h2>

    <div class="faq-list">

      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          What is the minimum order quantity (MOQ)?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Our standard MOQ is 100 pieces per product variant. For bulk or custom orders, we offer flexible MOQ options — contact us on WhatsApp to discuss your requirements.</div>
      </div>

      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          Do you offer private label and custom branding?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Yes! We specialize in private label manufacturing. You can use your own brand name, logo, and packaging design on any of our products. Our team will guide you through the complete branding process.</div>
      </div>

      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          How long does production take?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Standard production takes 7–14 working days after order confirmation and advance payment. Custom formulations or new packaging designs may take 3–4 weeks. Rush orders are available on request.</div>
      </div>

      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          Can I get samples before placing a bulk order?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Yes, samples are available for most products. Sample charges apply and are adjusted against your bulk order. Contact us on WhatsApp to request samples and pricing.</div>
      </div>

      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          Are your products ISO certified and quality tested?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">All ACM products are manufactured in our ISO-certified facility and undergo strict quality control testing at every stage — from raw material sourcing to final packaging — to ensure consistency and safety.</div>
      </div>

      <div class="faq-item">
        <button class="faq-q" onclick="this.parentElement.classList.toggle('open')">
          Do you deliver across Pakistan and internationally?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Yes, we deliver nationwide across Pakistan. We also export to international markets. Shipping costs and timelines vary by location — reach out to us for a custom shipping quote.</div>
      </div>

    </div>
  </div>
</section>

<style>
.faq-list { display: flex; flex-direction: column; gap: 12px; }
.faq-item { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(13,34,88,.07); }
.faq-q { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 20px 24px; background: none; border: none; font-family: 'Poppins',sans-serif; font-size: 15px; font-weight: 700; color: var(--navy); cursor: pointer; text-align: left; }
.faq-icon { font-size: 22px; font-weight: 400; flex-shrink: 0; transition: transform .25s; }
.faq-a { max-height: 0; overflow: hidden; padding: 0 24px; font-size: 14px; color: #555; line-height: 1.75; transition: max-height .3s ease, padding .3s ease; }
.faq-item.open .faq-a { max-height: 200px; padding: 0 24px 20px; }
.faq-item.open .faq-icon { transform: rotate(45deg); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
