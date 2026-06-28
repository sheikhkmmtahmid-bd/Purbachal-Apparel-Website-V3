<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'sustainability.php';
$d               = jsonRead(DATA_DIR . 'pages/sustainability.json');
$pageTitle       = $d['title']    ?? 'Sustainability | Purbachal Apparel Limited';
$pageDescription = $d['meta_desc'] ?? '';

function pal_pillar_icon(string $name): string {
    $icons = [
        'leaf'     => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
        'users'    => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'trending' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
    ];
    return $icons[$name] ?? $icons['leaf'];
}

function pal_initiative_icon(string $name): string {
    $icons = [
        'tree'         => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3L4 16h16L12 3z"/><line x1="12" y1="16" x2="12" y2="22"/><line x1="9" y1="22" x2="15" y2="22"/></svg>',
        'sun'          => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>',
        'droplet'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>',
        'plant'        => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22V12"/><path d="M12 12C12 7 18 3 22 3C22 8 17 12 12 12"/><path d="M12 12C12 8 6 4 2 4C2 9 7 12 12 12"/></svg>',
        'shield-check' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
        'refresh'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10"/><path d="M3.51 15a9 9 0 0 0 14.85 3.36L23 14"/></svg>',
    ];
    return $icons[$name] ?? $icons['tree'];
}

require_once __DIR__ . '/includes/site-header.php';
?>
<main>

<!-- PAGE HERO -->
<section class="page-hero"<?php if (!empty($d['hero_bg_image'])): ?> style="background-image:url('<?php echo UPLOAD_URL . 'pages/' . e($d['hero_bg_image']); ?>');background-size:cover;background-position:center"<?php endif; ?>>
  <?php if (!empty($d['hero_bg_image'])): ?>
  <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(15,26,46,.88),rgba(14,126,135,.6))"></div>
  <?php endif; ?>
  <div class="container">
    <div class="page-hero-content">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="index.php">Home</a>
        <span>&rsaquo;</span>
        <span>Sustainability</span>
      </nav>
      <h1><?php echo e($d['hero_title'] ?? ''); ?></h1>
      <p><?php echo e($d['hero_desc'] ?? ''); ?></p>
    </div>
  </div>
</section>

<!-- THREE PILLARS -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['pillars_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['pillars_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['pillars_desc'] ?? ''); ?></p>
    </div>
    <div class="pillars-grid">
      <?php $delays = ['', ' delay-1', ' delay-2']; $pi = 0;
      foreach ($d['pillar_items'] ?? [] as $pillar): ?>
      <div class="pillar-card <?php echo e($pillar['mod'] ?? ''); ?> reveal<?php echo $delays[$pi % 3]; ?>">
        <div class="pillar-icon"><?php echo pal_pillar_icon($pillar['icon'] ?? 'leaf'); ?></div>
        <h3><?php echo e($pillar['title'] ?? ''); ?></h3>
        <p><?php echo e($pillar['text'] ?? ''); ?></p>
      </div>
      <?php $pi++; endforeach; ?>
    </div>
  </div>
</section>

<!-- INITIATIVES -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['initiatives_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['initiatives_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['initiatives_desc'] ?? ''); ?></p>
    </div>
    <div class="initiative-grid">
      <?php $delays = ['', ' delay-1', ' delay-2']; $ii = 0;
      foreach ($d['initiative_items'] ?? [] as $item): ?>
      <div class="initiative-card reveal<?php echo $delays[$ii % 3]; ?>">
        <div class="initiative-icon <?php echo e($item['color'] ?? ''); ?>"><?php echo pal_initiative_icon($item['icon'] ?? 'tree'); ?></div>
        <h4><?php echo e($item['title'] ?? ''); ?></h4>
        <p><?php echo e($item['text'] ?? ''); ?></p>
      </div>
      <?php $ii++; endforeach; ?>
    </div>
  </div>
</section>

<!-- CSR SECTION -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['csr_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['csr_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['csr_desc'] ?? ''); ?></p>
    </div>
    <div class="csr-grid">
      <?php $delays = ['', ' delay-1', ' delay-2', ' delay-3']; $ci = 0;
      foreach ($d['csr_items'] ?? [] as $card): ?>
      <div class="csr-card reveal<?php echo $delays[$ci % 4]; ?>">
        <img src="<?php echo UPLOAD_URL . 'pages/' . e($card['image'] ?? ''); ?>" alt="<?php echo e($card['alt'] ?? ''); ?>" loading="lazy">
        <div class="csr-card-body">
          <h4><?php echo e($card['title'] ?? ''); ?></h4>
          <p><?php echo e($card['desc'] ?? ''); ?></p>
        </div>
      </div>
      <?php $ci++; endforeach; ?>
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
      <a href="<?php echo e($d['cta_btn2_link'] ?? 'certificates.php'); ?>" class="btn btn-outline-white btn-lg"><?php echo e($d['cta_btn2_text'] ?? 'Our Certifications'); ?></a>
    </div>
  </div>
</section>

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
