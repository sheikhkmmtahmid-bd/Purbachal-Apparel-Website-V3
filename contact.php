<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'contact.php';
$pageTitle       = 'Contact Us | Purbachal Apparel Limited';
$pageDescription = 'Contact Purbachal Apparel Limited in Gazipur, Bangladesh. Request a quote, arrange a factory visit, or discuss your garment sourcing needs.';

$site = jsonRead(DATA_DIR . 'site.json');

require_once __DIR__ . '/includes/site-header.php';
?>
<main>
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="page-hero-content">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="index.php">Home</a>
        <span>&rsaquo;</span>
        <span>Contact Us</span>
      </nav>
      <h1>Get In Touch</h1>
      <p>We would love to hear from you. Reach out to discuss your sourcing needs, request a quote, or arrange a factory visit.</p>
    </div>
  </div>
</section>

<!-- CONTACT SECTION -->
<section class="section">
  <div class="container">
    <div class="contact-grid">
      <!-- INFO CARD -->
      <div class="contact-info-card reveal-left">
        <h3>Contact Information</h3>
        <p>Our team is ready to assist you Monday through Saturday, 9 AM to 6 PM (Bangladesh Standard Time).</p>

        <div class="contact-detail">
          <div class="contact-detail-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <div class="contact-detail-body">
            <span class="contact-detail-label">Address</span>
            <span class="contact-detail-value">South Panjora, Ward No. 05, Nagori, Kaliganj, Gazipur-1720, Bangladesh</span>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          </div>
          <div class="contact-detail-body">
            <span class="contact-detail-label">Phone</span>
            <span class="contact-detail-value">
              <a href="tel:+8801713001008">+880 1713 001008</a><br>
              <a href="tel:+85264369811">+852 6436 9811</a>
            </span>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div class="contact-detail-body">
            <span class="contact-detail-label">Email</span>
            <span class="contact-detail-value"><a href="mailto:info@purbachalapparel.com">info@purbachalapparel.com</a></span>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
          </div>
          <div class="contact-detail-body">
            <span class="contact-detail-label">Website</span>
            <span class="contact-detail-value"><a href="index.php">www.purbachalapparel.com</a></span>
          </div>
        </div>

        <div class="contact-social-row">
          <a href="https://www.facebook.com/share/1CqNTHAErC/" class="contact-social" aria-label="Facebook" target="_blank" rel="noopener">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
          </a>
          <a href="https://www.linkedin.com/in/purbachal-apparel-limited-42a279381" class="contact-social" aria-label="LinkedIn" target="_blank" rel="noopener">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
          </a>
          <a href="https://youtu.be/bsH9ow0jLE0" class="contact-social" aria-label="YouTube" target="_blank" rel="noopener">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>
          </a>
          <a href="https://wa.me/+8801713001008" class="contact-social" aria-label="WhatsApp" target="_blank" rel="noopener">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          </a>
        </div>

        <div class="contact-map">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3647.0!2d90.4125!3d23.9505!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjPCsDU3JzAxLjgiTiA5MMKwMjQnNDUuMCJF!5e0!3m2!1sen!2sbd!4v1700000000000!5m2!1sen!2sbd"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            title="Purbachal Apparel Limited location map"></iframe>
        </div>
      </div>

      <!-- CONTACT FORM -->
      <div class="contact-form-card reveal-right">
        <h3>Send Us a Message</h3>
        <p>Fill in the form below and one of our team members will get back to you within one business day.</p>
        <form id="contact-form">
          <!--
            CONTACT FORM powered by EmailJS (emailjs.com, free tier 200/month).
            Submissions go directly to info@purbachalapparel.com.
            To change destination email: login at dashboard.emailjs.com
              with sheikh.k.m.m.tahmid@gmail.com > Email Templates > Contact Us.
            Keys (public, safe in client-side code):
              Public Key  : mt429GxgF_853gm9a
              Service ID  : service_l20zm0b
              Template ID : template_tjydd46
          -->
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="contact-name">Full Name <span>*</span></label>
              <input type="text" id="contact-name" name="name" class="form-input" placeholder="Your full name" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="contact-email">Email Address <span>*</span></label>
              <input type="email" id="contact-email" name="email" class="form-input" placeholder="your@email.com" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="contact-phone">Phone Number</label>
              <input type="tel" id="contact-phone" name="phone" class="form-input" placeholder="+1 234 567 8900">
            </div>
            <div class="form-group">
              <label class="form-label" for="contact-subject">Inquiry Type</label>
              <select id="contact-subject" name="inquiry_type" class="form-select">
                <option value="">Select a subject</option>
                <option value="Request a Quote">Request a Quote</option>
                <option value="Sample Request">Sample Request</option>
                <option value="Factory Visit">Factory Visit</option>
                <option value="Partnership Inquiry">Partnership Inquiry</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="contact-message">Message <span>*</span></label>
            <textarea id="contact-message" name="message" class="form-textarea" placeholder="Tell us about your requirements, product categories, quantities, and target delivery timelines..." required></textarea>
          </div>
          <div id="form-result" class="form-result" role="alert" aria-live="polite" style="display:none"></div>
          <button type="submit" class="btn btn-primary form-submit btn-lg">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Send Message
          </button>
        </form>
        <script>
        (function () {
          emailjs.init('mt429GxgF_853gm9a');
          var form    = document.getElementById('contact-form');
          if (!form) return;
          var btn     = form.querySelector('.form-submit');
          var result  = document.getElementById('form-result');
          var btnHTML = btn.innerHTML;
          form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!form.checkValidity()) { form.reportValidity(); return; }
            btn.disabled  = true;
            btn.innerHTML = 'Sending...';
            result.style.display = 'none';
            var params = {
              name:         form.querySelector('[name="name"]').value,
              email:        form.querySelector('[name="email"]').value,
              phone:        form.querySelector('[name="phone"]').value || 'Not provided',
              inquiry_type: form.querySelector('[name="inquiry_type"]').value || 'Not specified',
              message:      form.querySelector('[name="message"]').value
            };
            emailjs.send('service_l20zm0b', 'template_tjydd46', params)
              .then(function () {
                result.innerHTML = 'Thank you! Your message has been sent. We will reply within one business day.';
                result.className = 'form-result form-result-success';
                form.reset();
              })
              .catch(function () {
                result.innerHTML = 'Something went wrong. Please email us directly at <a href="mailto:info@purbachalapparel.com">info@purbachalapparel.com<\/a>.';
                result.className = 'form-result form-result-error';
              })
              .finally(function () {
                btn.disabled  = false;
                btn.innerHTML = btnHTML;
                result.style.display = 'block';
              });
          });
        })();
        </script>
      </div>
    </div>
  </div>
</section>

<!-- CRAFT BAND -->
<section class="craft-band">
  <div class="craft-band-left">
    <h2>Crafting Quality,<br>Delivering Trust</h2>
    <div class="craft-band-contact">
      <div class="craft-band-contact-item">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        <a href="tel:+8801713001008">+880 1713 001008</a>
      </div>
      <div class="craft-band-contact-item">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <a href="mailto:info@purbachalapparel.com">info@purbachalapparel.com</a>
      </div>
    </div>
    <a href="contact.php" class="btn btn-white">Contact Us Today</a>
  </div>
  <div class="craft-band-right">
    <img src="uploads/pages/banner-worker.jpg" alt="Skilled worker at sewing machine at Purbachal Apparel" loading="lazy">
  </div>
</section>

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
