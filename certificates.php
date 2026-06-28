<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'certificates.php';
$d               = jsonRead(DATA_DIR . 'pages/certificates.json');
$pageTitle       = $d['title']    ?? 'Our Certifications | Purbachal Apparel Limited';
$pageDescription = $d['meta_desc'] ?? '';

function pal_pillar_icon(string $name): string {
    $icons = [
        'award'      => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>',
        'shield'     => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
        'user-check' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>',
    ];
    return $icons[$name] ?? $icons['award'];
}

function pal_csr_icon(string $name): string {
    $icons = [
        'leaf'  => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 019.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
        'heart' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
        'users' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
    ];
    return $icons[$name] ?? $icons['leaf'];
}

require_once __DIR__ . '/includes/site-header.php';
?>
<main>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="page-hero-content">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="index.php">Home</a>
        <span>&rsaquo;</span>
        <span>Our Certifications</span>
      </nav>
      <h1><?php echo e($d['hero_title'] ?? ''); ?></h1>
      <p><?php echo e($d['hero_desc'] ?? ''); ?></p>
    </div>
  </div>
</section>

<!-- CERTS GRID -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['certs_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['certs_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['certs_desc'] ?? ''); ?></p>
    </div>
    <div class="cert-grid">
      <?php $delays = ['', ' delay-1', ' delay-2', ' delay-3']; $ci = 0;
      foreach ($d['cert_items'] ?? [] as $cert): ?>
      <div class="cert-card reveal<?php echo $delays[$ci % 4]; ?>">
        <img src="<?php echo UPLOAD_URL . 'pages/' . e($cert['logo'] ?? ''); ?>" alt="<?php echo e($cert['name'] ?? ''); ?>" loading="lazy">
        <span class="cert-card-name"><?php echo e($cert['name'] ?? ''); ?></span>
      </div>
      <?php $ci++; endforeach; ?>
    </div>
  </div>
</section>

<!-- THREE PILLARS -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['pillars_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['pillars_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['pillars_desc'] ?? ''); ?></p>
    </div>
    <div class="cert-pillar-grid">
      <?php $delays = ['', ' delay-1', ' delay-2']; $pi = 0;
      foreach ($d['pillar_items'] ?? [] as $pillar): ?>
      <div class="cert-pillar reveal<?php echo $delays[$pi % 3]; ?>">
        <div class="cert-pillar-icon"><?php echo pal_pillar_icon($pillar['icon'] ?? 'award'); ?></div>
        <h4><?php echo e($pillar['title'] ?? ''); ?></h4>
        <p><?php echo e($pillar['text'] ?? ''); ?></p>
      </div>
      <?php $pi++; endforeach; ?>
    </div>
  </div>
</section>

<!-- CSR PREVIEW -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow reveal"><?php echo e($d['csr_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['csr_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['csr_body'] ?? ''); ?><?php if (!empty($d['csr_link_text']) && !empty($d['csr_link'])): ?> <a href="<?php echo e($d['csr_link']); ?>" class="text-teal" style="font-weight:600"><?php echo e($d['csr_link_text']); ?></a><?php endif; ?><?php if (!empty($d['csr_link_suffix'])): ?> <?php echo e($d['csr_link_suffix']); ?><?php endif; ?></p>
    </div>
    <div class="grid-3 mt-4">
      <?php $delays = ['', ' delay-1', ' delay-2']; $si = 0;
      foreach ($d['csr_items'] ?? [] as $item): ?>
      <div class="mission-card reveal<?php echo $delays[$si % 3]; ?>">
        <div class="mission-icon"><?php echo pal_csr_icon($item['icon'] ?? 'leaf'); ?></div>
        <h4><?php echo e($item['title'] ?? ''); ?></h4>
        <p><?php echo e($item['text'] ?? ''); ?></p>
      </div>
      <?php $si++; endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-band">
  <div class="container">
    <h2 class="reveal"><?php echo e($d['cta_title'] ?? ''); ?></h2>
    <p class="reveal delay-1"><?php echo e($d['cta_desc'] ?? ''); ?></p>
    <div class="cta-band-actions reveal delay-2">
      <a href="<?php echo e($d['cta_btn1_link'] ?? 'contact.php'); ?>" class="btn btn-white btn-lg"><?php echo e($d['cta_btn1_text'] ?? 'Contact Us'); ?></a>
      <a href="<?php echo e($d['cta_btn2_link'] ?? 'sustainability.php'); ?>" class="btn btn-outline-white btn-lg"><?php echo e($d['cta_btn2_text'] ?? 'Our Sustainability'); ?></a>
    </div>
  </div>
</section>

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
