<?php
$site       = jsonRead(DATA_DIR . 'site.json');
$footerData = jsonRead(DATA_DIR . 'footer.json');
$navData    = jsonRead(DATA_DIR . 'nav.json');
$logoSrc    = getLogoSrc($site);
$social     = $site['social'] ?? [];
$year       = date('Y');
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">
          <div class="footer-logo-icon"><img src="<?php echo $logoSrc; ?>" alt="<?php echo e($site['company_name']); ?>" loading="lazy"></div>
          <span class="footer-logo-name">Purbachal<br>Apparel Limited</span>
        </div>
        <p><?php echo e($footerData['brand_desc'] ?? $site['footer_desc'] ?? ''); ?></p>
        <div class="footer-social">
          <?php if (!empty($social['facebook'])): ?>
          <a href="<?php echo e($social['facebook']); ?>" class="footer-social-link" aria-label="Facebook" target="_blank" rel="noopener"><?php echo getSocialIconSvg('facebook', 16); ?></a>
          <?php endif; if (!empty($social['linkedin'])): ?>
          <a href="<?php echo e($social['linkedin']); ?>" class="footer-social-link" aria-label="LinkedIn" target="_blank" rel="noopener"><?php echo getSocialIconSvg('linkedin', 16); ?></a>
          <?php endif; if (!empty($social['youtube'])): ?>
          <a href="<?php echo e($social['youtube']); ?>" class="footer-social-link" aria-label="YouTube" target="_blank" rel="noopener"><?php echo getSocialIconSvg('youtube', 16); ?></a>
          <?php endif; if (!empty($social['whatsapp'])): ?>
          <a href="<?php echo e($social['whatsapp']); ?>" class="footer-social-link" aria-label="WhatsApp" target="_blank" rel="noopener"><?php echo getSocialIconSvg('whatsapp', 16); ?></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="footer-col">
        <h5>Quick Links</h5>
        <ul class="footer-links">
          <?php foreach ($navData['pages'] as $page): ?>
          <li><a href="<?php echo e($page['file']); ?>" class="footer-link"><?php echo e($page['label']); ?></a></li>
          <?php if (!empty($page['dropdown'])): foreach ($page['dropdown'] as $child): ?>
          <li><a href="<?php echo e($child['file']); ?>" class="footer-link"><?php echo e($child['label']); ?></a></li>
          <?php endforeach; endif; endforeach; ?>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Our Products</h5>
        <ul class="footer-links">
          <li><a href="products.php#kids" class="footer-link">Kid&#39;s Wear</a></li>
          <li><a href="products.php#mens" class="footer-link">Men&#39;s Wear</a></li>
          <li><a href="products.php#womens" class="footer-link">Women&#39;s Wear</a></li>
        </ul>
      </div>
      <?php if (!empty($footerData['pdf'])): $pdf = $footerData['pdf']; ?>
      <div class="footer-col">
        <div class="footer-pdf-card">
          <svg width="36" height="44" viewBox="0 0 36 44" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect width="36" height="44" rx="3" fill="#efefef"/><path d="M22 0L36 13H22V0Z" fill="#d0d0ce"/>
            <rect y="25" width="36" height="14" fill="#C0392B"/>
            <text x="18" y="36" font-family="Arial,sans-serif" font-weight="700" font-size="8.5" fill="#fff" text-anchor="middle">PDF</text>
          </svg>
          <div class="footer-pdf-info">
            <span class="footer-pdf-label"><?php echo e($pdf['label']); ?></span>
            <strong class="footer-pdf-title"><?php echo e($pdf['title']); ?></strong>
            <a href="<?php echo e($pdf['url']); ?>" class="footer-pdf-dl" target="_blank" rel="noopener">
              <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
              Download
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <div class="footer-col">
        <h5>Get in Touch</h5>
        <div class="footer-contact-item">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <span><?php echo e($site['address']); ?></span>
        </div>
        <div class="footer-contact-item">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          <span><a href="tel:<?php echo e($site['phone1_tel']); ?>"><?php echo e($site['phone1']); ?></a><br><a href="tel:<?php echo e($site['phone2_tel']); ?>"><?php echo e($site['phone2']); ?></a></span>
        </div>
        <div class="footer-contact-item">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a>
        </div>
        <a href="contact.php" class="btn btn-primary btn-sm mt-2">Get a Quote</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>Copyright &copy; <?php echo $year; ?> <?php echo e($site['company_name']); ?>. All rights reserved.</span>
      <span>Powered By <a href="http://skmmt.rootexception.com/" target="_blank" rel="noopener" style="color:var(--teal);text-decoration:none;">SKMMT</a></span>
      <span>Woven garment manufacturers &amp; exporters, Bangladesh</span>
    </div>
  </div>
</footer>
<div class="fab-group">
  <a href="<?php echo e($social['whatsapp'] ?? '#'); ?>" class="fab fab-whatsapp" aria-label="WhatsApp" target="_blank" rel="noopener">
    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </a>
  <button class="fab fab-top" aria-label="Back to top">
    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
  </button>
</div>
