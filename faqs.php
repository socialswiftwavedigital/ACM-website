<?php
$pageTitle = 'FAQs — ACM Asia Cosmetics';
$metaDesc  = 'Frequently asked questions about ACM Asia Cosmetics — MOQ, private label, production time, samples, certifications and delivery.';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / FAQs</div>
    <h1>Frequently Asked Questions</h1>
    <p>Everything you need to know about working with ACM</p>
  </div>
</div>

<section class="section" style="background:var(--offwhite)">
  <div class="container" style="max-width:800px">

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

    <div style="text-align:center;margin-top:48px">
      <p style="color:var(--muted);margin-bottom:16px">Still have questions? We're happy to help.</p>
      <a href="https://wa.me/923255129241" target="_blank" class="btn btn-whatsapp btn-lg">💬 Ask on WhatsApp</a>
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
