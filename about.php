<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile = 'about.php';
$page     = jsonRead(DATA_DIR . 'pages/about.json');
$company  = $page['company_table'] ?? [];
$directors= $page['directors'] ?? [];
$mission  = $page['mission'] ?? [];
$culture  = $page['culture'] ?? [];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>About Us</h1><p>A trusted partner in high-quality woven garment manufacturing.</p></div>
  </section>

  <!-- Company Overview Table -->
  <?php if (!empty($company)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">Company Overview</h2>
      <div class="table-wrap">
        <table class="info-table">
          <tbody>
            <?php foreach ($company as $row): ?>
            <tr><th><?php echo e($row['label'] ?? ''); ?></th><td><?php echo e($row['value'] ?? ''); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Directors -->
  <?php if (!empty($directors)): ?>
  <section class="section section-alt">
    <div class="container">
      <h2 class="section-title">Board of Directors</h2>
      <div class="directors-grid">
        <?php foreach ($directors as $d): ?>
        <div class="director-card">
          <div class="director-avatar"><?php echo strtoupper(substr($d['name']??'?',0,1)); ?></div>
          <h3><?php echo e($d['name'] ?? ''); ?></h3>
          <span class="director-role"><?php echo e($d['role'] ?? ''); ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Mission / Values -->
  <?php if (!empty($mission)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">Mission &amp; Values</h2>
      <div class="pillars-grid">
        <?php foreach ($mission as $m): ?>
        <div class="pillar-card">
          <h3><?php echo e($m['title'] ?? ''); ?></h3>
          <p><?php echo e($m['desc'] ?? ''); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Culture -->
  <?php if (!empty($culture)): ?>
  <section class="section section-alt">
    <div class="container">
      <h2 class="section-title">Our Culture</h2>
      <div class="culture-grid">
        <?php foreach ($culture as $c): ?>
        <div class="culture-card">
          <h3><?php echo e($c['title'] ?? ''); ?></h3>
          <p><?php echo e($c['desc'] ?? ''); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
