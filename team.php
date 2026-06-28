<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'team.php';
$d               = jsonRead(DATA_DIR . 'pages/team.json');
$pageTitle       = $d['title']    ?? 'Our Team | Purbachal Apparel Limited';
$pageDescription = $d['meta_desc'] ?? '';

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
        <a href="about.php">About Us</a>
        <span>&rsaquo;</span>
        <span>Our Team</span>
      </nav>
      <h1><?php echo e($d['hero_title'] ?? ''); ?></h1>
      <p><?php echo e($d['hero_desc'] ?? ''); ?></p>
    </div>
  </div>
</section>

<!-- TEAM CARDS + WORKFORCE STATS -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['directors_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['directors_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['directors_desc'] ?? ''); ?></p>
    </div>
    <div class="team-grid">
      <?php $delays = ['', ' delay-1']; $di = 0;
      foreach ($d['directors_items'] ?? [] as $dir): ?>
      <div class="team-card reveal<?php echo $delays[$di % 2]; ?>">
        <div class="team-card-photo">
          <img src="<?php echo UPLOAD_URL . 'pages/' . e($dir['photo'] ?? ''); ?>" alt="<?php echo e($dir['name'] ?? ''); ?>, <?php echo e($dir['role'] ?? ''); ?>" loading="lazy"<?php echo ($dir['photo_position'] ?? '') ? ' style="object-position:' . e($dir['photo_position']) . '"' : ''; ?>>
        </div>
        <div class="team-card-body">
          <h3 class="team-card-name"><?php echo e($dir['name'] ?? ''); ?></h3>
          <p class="team-card-title"><?php echo e($dir['role'] ?? ''); ?></p>
          <div class="team-card-divider"></div>
          <p class="team-card-quote">"<?php echo e($dir['quote'] ?? ''); ?>"</p>
        </div>
      </div>
      <?php $di++; endforeach; ?>
    </div>

    <!-- WORKFORCE STATS -->
    <div class="workforce-grid">
      <?php $delays = ['', ' delay-1', ' delay-2', ' delay-3']; $si = 0;
      foreach ($d['stats_items'] ?? [] as $stat): ?>
      <div class="workforce-stat reveal<?php echo $delays[$si % 4]; ?>">
        <span class="workforce-stat-value"><?php echo e($stat['value'] ?? ''); ?></span>
        <span class="workforce-stat-label"><?php echo e($stat['label'] ?? ''); ?></span>
      </div>
      <?php $si++; endforeach; ?>
    </div>
  </div>
</section>

<!-- FACILITY GALLERY -->
<section class="facility-gallery-section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal" style="color:var(--teal-light);"><?php echo e($d['gallery_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1" style="color:#fff;"><?php echo e($d['gallery_heading'] ?? ''); ?></h2>
    </div>
    <div class="img-gallery reveal delay-2">
      <div class="gallery-wrap">
        <button class="gallery-arrow gallery-prev" aria-label="Previous">
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="gallery-viewport">
          <div class="gallery-track">
            <?php foreach ($d['gallery_items'] ?? [] as $slide): ?>
            <div class="gallery-slide"><img src="<?php echo UPLOAD_URL . 'gallery/' . e($slide['filename'] ?? ''); ?>" alt="<?php echo e($slide['alt'] ?? ''); ?>" loading="lazy"></div>
            <?php endforeach; ?>
          </div>
        </div>
        <button class="gallery-arrow gallery-next" aria-label="Next">
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
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

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
