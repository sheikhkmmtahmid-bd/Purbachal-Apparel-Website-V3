<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile = 'contact.php';
$site     = jsonRead(DATA_DIR . 'site.json');
$ctact    = jsonRead(DATA_DIR . 'pages/contact.json');
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>Contact Us</h1><p>Get in touch for a quote or any enquiries.</p></div>
  </section>
  <section class="section">
    <div class="container">
      <div class="contact-layout">
        <div class="contact-info">
          <h2>Get in Touch</h2>
          <div class="contact-item">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span><?php echo e($site['address']); ?></span>
          </div>
          <div class="contact-item">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <div><a href="tel:<?php echo e($site['phone1_tel']); ?>"><?php echo e($site['phone1']); ?></a><br><a href="tel:<?php echo e($site['phone2_tel']); ?>"><?php echo e($site['phone2']); ?></a></div>
          </div>
          <div class="contact-item">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a>
          </div>
          <?php if (!empty($site['map_embed'])): ?>
          <div class="map-wrap mt-4">
            <iframe src="<?php echo e($site['map_embed']); ?>" width="100%" height="280" style="border:0;border-radius:8px;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map"></iframe>
          </div>
          <?php endif; ?>
        </div>
        <div class="contact-form-wrap">
          <h2>Send a Message</h2>
          <div id="formMsg" class="form-msg" role="alert" aria-live="polite"></div>
          <form id="contactForm" novalidate>
            <div class="form-row">
              <div class="form-group"><label for="cf_name">Full Name <span aria-hidden="true">*</span></label><input type="text" id="cf_name" name="from_name" class="form-control" required></div>
              <div class="form-group"><label for="cf_email">Email <span aria-hidden="true">*</span></label><input type="email" id="cf_email" name="reply_to" class="form-control" required></div>
            </div>
            <div class="form-group"><label for="cf_subject">Subject</label><input type="text" id="cf_subject" name="subject" class="form-control"></div>
            <div class="form-group"><label for="cf_msg">Message <span aria-hidden="true">*</span></label><textarea id="cf_msg" name="message" class="form-control" rows="6" required></textarea></div>
            <button type="submit" class="btn btn-primary" id="cfSubmit">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script>
(function(){
  var cfg = {
    pub: <?php echo json_encode($ctact['public_key'] ?? ''); ?>,
    svc: <?php echo json_encode($ctact['service_id'] ?? ''); ?>,
    tpl: <?php echo json_encode($ctact['template_id'] ?? ''); ?>
  };
  if (cfg.pub) emailjs.init({ publicKey: cfg.pub });
  var form = document.getElementById('contactForm');
  var msg  = document.getElementById('formMsg');
  var btn  = document.getElementById('cfSubmit');
  if (!form) return;
  form.addEventListener('submit', function(e){
    e.preventDefault();
    if (!cfg.pub || !cfg.svc || !cfg.tpl) { msg.textContent = 'Email service not configured.'; msg.className = 'form-msg error'; return; }
    btn.disabled = true; btn.textContent = 'Sending...';
    emailjs.sendForm(cfg.svc, cfg.tpl, form)
      .then(function(){ msg.textContent = 'Message sent successfully!'; msg.className = 'form-msg success'; form.reset(); })
      .catch(function(err){ msg.textContent = 'Failed to send: ' + (err.text || 'unknown error'); msg.className = 'form-msg error'; })
      .finally(function(){ btn.disabled = false; btn.textContent = 'Send Message'; });
  });
})();
</script>
