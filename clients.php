<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'clients.php';
$d               = jsonRead(DATA_DIR . 'pages/clients.json');
$pageTitle       = $d['title']    ?? 'Our Clients | Purbachal Apparel Limited';
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
        <span>Our Clients</span>
      </nav>
      <h1><?php echo e($d['hero_title'] ?? ''); ?></h1>
      <p><?php echo e($d['hero_desc'] ?? ''); ?></p>
    </div>
  </div>
</section>

<!-- CLIENTS GRID -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['logos_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['logos_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['logos_desc'] ?? ''); ?></p>
    </div>
    <div class="client-grid">
      <?php $delays = ['', ' delay-1', ' delay-2', ' delay-3']; $ci = 0;
      foreach ($d['client_items'] ?? [] as $client): ?>
      <div class="client-card reveal<?php echo $delays[$ci % 4]; ?>">
        <img src="<?php echo UPLOAD_URL . 'pages/' . e($client['logo'] ?? ''); ?>" alt="<?php echo e($client['name'] ?? ''); ?>" loading="lazy">
        <span class="client-card-name"><?php echo e($client['name'] ?? ''); ?></span>
        <span class="client-card-country"><?php echo e($client['country'] ?? ''); ?></span>
      </div>
      <?php $ci++; endforeach; ?>
    </div>
  </div>
</section>

<!-- DIRECTOR QUOTES -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['quotes_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['quotes_heading'] ?? ''); ?></h2>
    </div>
    <div class="director-quote-grid">
      <?php $delays = ['', ' delay-1']; $di = 0;
      foreach ($d['director_items'] ?? [] as $dir): ?>
      <div class="director-quote-card reveal<?php echo $delays[$di % 2]; ?>">
        <div class="director-photo">
          <img src="<?php echo UPLOAD_URL . 'pages/' . e($dir['photo'] ?? ''); ?>" alt="<?php echo e($dir['name'] ?? ''); ?>, <?php echo e($dir['role'] ?? ''); ?>" loading="lazy"<?php echo ($dir['photo_position'] ?? '') ? ' style="object-position:' . e($dir['photo_position']) . '"' : ''; ?>>
        </div>
        <div class="director-body">
          <h3 class="director-name"><?php echo e($dir['name'] ?? ''); ?></h3>
          <p class="director-title"><?php echo e($dir['role'] ?? ''); ?></p>
          <div class="director-divider"></div>
          <p class="director-quote">"<?php echo e($dir['quote'] ?? ''); ?>"</p>
        </div>
      </div>
      <?php $di++; endforeach; ?>
    </div>
  </div>
</section>

<!-- PARTNERSHIP IN ACTION -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['partnership_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['partnership_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['partnership_desc'] ?? ''); ?></p>
    </div>
    <div class="reveal" style="border-radius:16px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,.10);">
      <img src="<?php echo UPLOAD_URL . 'pages/' . e($d['partnership_image'] ?? ''); ?>" alt="<?php echo e($d['partnership_image_alt'] ?? ''); ?>" loading="lazy" style="width:100%; display:block; max-height:500px; object-fit:cover; object-position:center top;">
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
      <a href="<?php echo e($d['cta_btn2_link'] ?? 'contact.php'); ?>" class="btn btn-outline-white btn-lg"><?php echo e($d['cta_btn2_text'] ?? 'Request a Quote'); ?></a>
    </div>
  </div>
</section>

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
