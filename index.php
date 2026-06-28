<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'index.php';
$d               = jsonRead(DATA_DIR . 'pages/index.json');
$pageTitle       = $d['title']    ?? 'Home | Purbachal Apparel Limited';
$pageDescription = $d['meta_desc'] ?? '';
$site            = jsonRead(DATA_DIR . 'site.json');

function pal_service_icon(string $name): string {
    $icons = [
        'scissors' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>',
        'gear'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
        'globe'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>',
    ];
    return $icons[$name] ?? $icons['scissors'];
}

require_once __DIR__ . '/includes/site-header.php';
?>
<main>

<!-- HERO -->
<section class="hero" id="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-left">
        <div class="hero-badge reveal">
          <span class="hero-badge-dot"></span>
          <?php echo e($d['hero_badge'] ?? ''); ?>
        </div>
        <h1 class="hero-title reveal delay-1"><?php echo $d['hero_title'] ?? ''; ?></h1>
        <p class="hero-desc reveal delay-2"><?php echo e($d['hero_desc'] ?? ''); ?></p>
        <div class="hero-actions reveal delay-3">
          <a href="<?php echo e($d['hero_cta1_link'] ?? 'contact.php'); ?>" class="btn btn-primary btn-lg"><?php echo e($d['hero_cta1_text'] ?? 'Contact Us'); ?></a>
          <a href="<?php echo e($d['hero_cta2_link'] ?? 'products.php'); ?>" class="btn btn-outline-white btn-lg"><?php echo e($d['hero_cta2_text'] ?? 'View Our Products'); ?></a>
        </div>
      </div>
      <div class="hero-right">
        <div class="hero-card hero-card-teal float-anim">
          <div class="hero-card-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--teal-light)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
          </div>
          <div class="hero-card-body">
            <h4><?php echo e($d['hero_card1_title'] ?? ''); ?></h4>
            <p><?php echo e($d['hero_card1_desc'] ?? ''); ?></p>
          </div>
        </div>
        <div class="hero-card hero-card-magenta float-anim" style="animation-delay:.8s">
          <div class="hero-card-icon hero-card-icon-magenta">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--teal-light)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div class="hero-card-body">
            <h4><?php echo e($d['hero_card2_title'] ?? ''); ?></h4>
            <p><?php echo e($d['hero_card2_desc'] ?? ''); ?></p>
          </div>
        </div>
        <div class="hero-float-badges float-anim" style="animation-delay:1.2s">
          <div class="hero-float-badge">
            <div class="hero-float-badge-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--teal-light)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></div>
            <span>100% Certified</span>
          </div>
          <div class="hero-float-badge">
            <div class="hero-float-badge-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--teal-light)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            <span>Global Exporter</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CLIENT MARQUEE -->
<?php $marqueeItems = $d['marquee_items'] ?? []; ?>
<div class="marquee-section">
  <div class="marquee-label"><?php echo e($d['marquee_label'] ?? 'Trusted by Leading Global Retailers'); ?></div>
  <div class="marquee-track">
    <?php foreach ($marqueeItems as $logo): ?>
    <div class="marquee-logo"><img src="<?php echo UPLOAD_URL . 'pages/' . e($logo['image'] ?? ''); ?>" alt="<?php echo e($logo['alt'] ?? ''); ?>" loading="lazy"></div>
    <?php endforeach; ?>
    <?php foreach ($marqueeItems as $logo): ?>
    <div class="marquee-logo"><img src="<?php echo UPLOAD_URL . 'pages/' . e($logo['image'] ?? ''); ?>" alt="<?php echo e($logo['alt'] ?? ''); ?>" loading="lazy"></div>
    <?php endforeach; ?>
  </div>
</div>

<!-- SERVICES -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['cards_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['cards_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['cards_desc'] ?? ''); ?></p>
    </div>
    <div class="services-grid">
      <?php $delays = ['', ' delay-1', ' delay-2']; $ci = 0;
      foreach ($d['cards_items'] ?? [] as $card): ?>
      <div class="service-card reveal<?php echo $delays[$ci % 3]; ?>">
        <div class="service-number"><?php echo e($card['number'] ?? ''); ?></div>
        <div class="service-icon"><?php echo pal_service_icon($card['icon'] ?? 'scissors'); ?></div>
        <h3><?php echo e($card['title'] ?? ''); ?></h3>
        <p><?php echo e($card['text'] ?? ''); ?></p>
      </div>
      <?php $ci++; endforeach; ?>
    </div>
  </div>
</section>

<!-- WHO WE ARE -->
<section class="section">
  <div class="container">
    <div class="about-grid">
      <div class="about-img-wrap reveal-left">
        <div class="about-img-main">
          <img src="<?php echo UPLOAD_URL . 'pages/' . e($d['about_image'] ?? ''); ?>" alt="<?php echo e($d['about_image_alt'] ?? ''); ?>" loading="lazy">
        </div>
        <div class="about-img-inset">
          <img src="<?php echo UPLOAD_URL . 'pages/' . e($d['about_inset_image'] ?? ''); ?>" alt="<?php echo e($d['about_inset_alt'] ?? ''); ?>" loading="lazy">
        </div>
        <div class="about-badge">
          <span class="about-badge-value"><?php echo e($d['about_badge_value'] ?? ''); ?></span>
          <span class="about-badge-label"><?php echo e($d['about_badge_label'] ?? ''); ?></span>
        </div>
      </div>
      <div class="reveal-right">
        <span class="eyebrow"><?php echo e($d['about_eyebrow'] ?? ''); ?></span>
        <h2 class="section-title"><?php echo e($d['about_heading'] ?? ''); ?></h2>
        <p><?php echo e($d['about_body'] ?? ''); ?></p>
        <ul class="about-list">
          <?php foreach ($d['about_checklist'] ?? [] as $item): ?>
          <li class="about-list-item">
            <div class="about-list-icon"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
            <?php echo e($item); ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <div class="about-actions">
          <a href="<?php echo e($d['about_cta_link'] ?? 'about.php'); ?>" class="btn btn-primary"><?php echo e($d['about_cta_text'] ?? 'Learn More'); ?></a>
          <a href="<?php echo e($d['about_cta2_link'] ?? 'contact.php'); ?>" class="btn btn-outline"><?php echo e($d['about_cta2_text'] ?? 'Get a Quote'); ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WORKING PROCESS -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['process_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['process_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['process_desc'] ?? ''); ?></p>
    </div>
    <div class="process-grid">
      <?php $delays = ['', ' delay-1', ' delay-2']; $pi = 0;
      foreach ($d['process_items'] ?? [] as $step): ?>
      <div class="process-card reveal<?php echo $delays[$pi % 3]; ?>">
        <div class="process-step-num"><?php echo e($step['number'] ?? ''); ?></div>
        <h3><?php echo e($step['title'] ?? ''); ?></h3>
        <p><?php echo e($step['desc'] ?? ''); ?></p>
      </div>
      <?php $pi++; endforeach; ?>
    </div>
  </div>
</section>

<!-- CERTIFICATIONS PREVIEW -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['certs_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['certs_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['certs_desc'] ?? ''); ?></p>
    </div>
    <div class="cert-strip-wrap reveal">
      <div class="cert-strip">
        <?php foreach ($d['certs_items'] ?? [] as $cert): ?>
        <div class="cert-strip-item"><img src="<?php echo UPLOAD_URL . 'pages/' . e($cert['image'] ?? ''); ?>" alt="<?php echo e($cert['label'] ?? ''); ?>" loading="lazy"><span><?php echo e($cert['label'] ?? ''); ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="text-center mt-4 reveal">
      <a href="<?php echo e($d['certs_link'] ?? 'certificates.php'); ?>" class="btn btn-outline"><?php echo e($d['certs_link_text'] ?? 'View All Certifications'); ?></a>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
  <div class="container">
    <h2 class="reveal"><?php echo e($d['cta_title'] ?? ''); ?></h2>
    <p class="reveal delay-1"><?php echo e($d['cta_desc'] ?? ''); ?></p>
    <div class="cta-band-actions reveal delay-2">
      <a href="<?php echo e($d['cta_btn1_link'] ?? 'contact.php'); ?>" class="btn btn-white btn-lg"><?php echo e($d['cta_btn1_text'] ?? 'Contact Us'); ?></a>
      <a href="<?php echo e($d['cta_btn2_link'] ?? 'contact.php'); ?>" class="btn btn-outline-white btn-lg"><?php echo e($d['cta_btn2_text'] ?? 'Get a Quote'); ?></a>
    </div>
  </div>
</section>

<!-- CRAFT BAND -->
<section class="craft-band">
  <div class="craft-band-left">
    <h2><?php echo $d['craft_heading'] ?? ''; ?></h2>
    <div class="craft-band-contact">
      <div class="craft-band-contact-item">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        <a href="tel:<?php echo e($site['phone1_tel'] ?? ''); ?>"><?php echo e($site['phone1'] ?? ''); ?></a>
      </div>
      <div class="craft-band-contact-item">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <a href="mailto:<?php echo e($site['email'] ?? ''); ?>"><?php echo e($site['email'] ?? ''); ?></a>
      </div>
    </div>
    <a href="<?php echo e($d['craft_cta_link'] ?? 'contact.php'); ?>" class="btn btn-white"><?php echo e($d['craft_cta_text'] ?? 'Contact Us Today'); ?></a>
  </div>
  <div class="craft-band-right">
    <img src="<?php echo UPLOAD_URL . 'pages/' . e($d['craft_image'] ?? ''); ?>" alt="<?php echo e($d['craft_image_alt'] ?? ''); ?>" loading="lazy">
  </div>
</section>

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
