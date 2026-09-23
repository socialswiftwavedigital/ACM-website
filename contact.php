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
          <div class="contact-item-value"><a href="tel:+92300000000" style="color:#fff">+92-300-000-0000</a></div>
        </div>
      </div>
      <div class="contact-item">
        <div class="contact-item-icon">💬</div>
        <div>
          <div class="contact-item-label">WhatsApp</div>
          <div class="contact-item-value"><a href="https://wa.me/923000000000" target="_blank" style="color:#25D366">Chat on WhatsApp</a></div>
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
        <a href="https://wa.me/923000000000" target="_blank" class="btn btn-whatsapp" style="width:100%;justify-content:center">
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
