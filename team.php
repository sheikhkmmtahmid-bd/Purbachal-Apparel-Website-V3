<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile  = 'team.php';
$page      = jsonRead(DATA_DIR . 'pages/team.json');
$directors = $page['directors'] ?? [];
$stats     = $page['stats'] ?? [];
$gallery   = $page['gallery'] ?? [];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>Our Team</h1><p>Meet the leadership and people behind Purbachal Apparel Limited.</p></div>
  </section>
  <?php if (!empty($directors)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">Board of Directors</h2>
      <div class="directors-grid">
        <?php foreach ($directors as $d): ?>
        <div class="director-card">
          <div class="director-avatar"><?php echo strtoupper(substr($d['name']??'?',0,1)); ?></div>
          <h3><?php echo e($d['name'] ?? ''); ?></h3>
          <span class="director-role"><?php echo e($d['role'] ?? ''); ?></span>
          <?php if (!empty($d['bio'])): ?><p class="director-bio"><?php echo e($d['bio']); ?></p><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if (!empty($stats)): ?>
  <section class="section section-alt">
    <div class="container">
      <div class="stats-grid">
        <?php foreach ($stats as $s): ?>
        <div class="stat-card"><strong><?php echo e($s['value'] ?? ''); ?></strong><span><?php echo e($s['label'] ?? ''); ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if (!empty($gallery)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">Gallery</h2>
      <div class="team-gallery">
        <?php foreach ($gallery as $img): ?>
        <div class="team-gallery-item"><img src="uploads/gallery/<?php echo e($img); ?>" alt="Team photo" loading="lazy"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
