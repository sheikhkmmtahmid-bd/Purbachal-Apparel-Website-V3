<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile = 'index.php';
$page     = jsonRead(DATA_DIR . 'pages/index.json');
$site     = jsonRead(DATA_DIR . 'site.json');
$hero     = $page['hero'] ?? [];
$services = $page['services'] ?? [];
$about    = $page['about'] ?? [];
$process  = $page['process'] ?? [];
$cta      = $page['cta'] ?? [];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <!-- Hero -->
  <section class="hero" id="hero">
    <div class="container">
      <div class="hero-content">
        <span class="hero-badge"><?php echo e($hero['badge'] ?? ''); ?></span>
        <h1><?php echo e($hero['headline'] ?? ''); ?></h1>
        <p><?php echo e($hero['sub'] ?? ''); ?></p>
        <div class="hero-btns">
          <a href="<?php echo e($hero['cta1_url'] ?? 'contact.php'); ?>" class="btn btn-primary"><?php echo e($hero['cta1_text'] ?? 'Get a Quote'); ?></a>
          <a href="<?php echo e($hero['cta2_url'] ?? 'about.php'); ?>" class="btn btn-outline"><?php echo e($hero['cta2_text'] ?? 'Learn More'); ?></a>
        </div>
      </div>
    </div>
  </section>

  <!-- Services -->
  <?php if (!empty($services)): ?>
  <section class="section" id="services">
    <div class="container">
      <h2 class="section-title"><?php echo e($page['services_title'] ?? 'Our Services'); ?></h2>
      <div class="services-grid">
        <?php foreach ($services as $svc): ?>
        <div class="service-card">
          <?php if (!empty($svc['icon'])): ?><div class="service-icon"><?php echo $svc['icon']; ?></div><?php endif; ?>
          <h3><?php echo e($svc['title'] ?? ''); ?></h3>
          <p><?php echo e($svc['desc'] ?? ''); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- About Teaser -->
  <?php if (!empty($about)): ?>
  <section class="section section-alt" id="about-teaser">
    <div class="container">
      <div class="about-grid">
        <div class="about-text">
          <h2><?php echo e($about['title'] ?? ''); ?></h2>
          <p><?php echo e($about['desc'] ?? ''); ?></p>
          <?php if (!empty($page['checklist'])): ?>
          <ul class="checklist">
            <?php foreach ($page['checklist'] as $item): ?>
            <li><?php echo e($item); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <a href="about.php" class="btn btn-primary mt-4"><?php echo e($about['cta_text'] ?? 'Learn About Us'); ?></a>
        </div>
        <div class="about-stats">
          <?php foreach ($site['stats'] ?? [] as $stat): ?>
          <div class="stat-card"><strong><?php echo e($stat['value'] ?? ''); ?></strong><span><?php echo e($stat['label'] ?? ''); ?></span></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Process -->
  <?php if (!empty($process)): ?>
  <section class="section" id="process">
    <div class="container">
      <h2 class="section-title"><?php echo e($page['process_title'] ?? 'Our Process'); ?></h2>
      <div class="process-steps">
        <?php foreach ($process as $i => $step): ?>
        <div class="process-step">
          <div class="step-num"><?php echo $i+1; ?></div>
          <h3><?php echo e($step['title'] ?? ''); ?></h3>
          <p><?php echo e($step['desc'] ?? ''); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- CTA Banner -->
  <?php if (!empty($cta)): ?>
  <section class="cta-banner">
    <div class="container">
      <h2><?php echo e($cta['title'] ?? ''); ?></h2>
      <p><?php echo e($cta['desc'] ?? ''); ?></p>
      <a href="<?php echo e($cta['url'] ?? 'contact.php'); ?>" class="btn btn-primary"><?php echo e($cta['text'] ?? 'Contact Us'); ?></a>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
