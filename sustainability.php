<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile  = 'sustainability.php';
$page      = jsonRead(DATA_DIR . 'pages/sustainability.json');
$pillars   = $page['pillars'] ?? [];
$initiatives = $page['initiatives'] ?? [];
$csr       = $page['csr'] ?? [];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>Sustainability</h1><p>Our commitment to people, planet, and responsible manufacturing.</p></div>
  </section>
  <?php if (!empty($pillars)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">Our Pillars</h2>
      <div class="pillars-grid">
        <?php foreach ($pillars as $p): ?>
        <div class="pillar-card"><h3><?php echo e($p['title'] ?? ''); ?></h3><p><?php echo e($p['desc'] ?? ''); ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if (!empty($initiatives)): ?>
  <section class="section section-alt">
    <div class="container">
      <h2 class="section-title">Our Initiatives</h2>
      <div class="initiatives-grid">
        <?php foreach ($initiatives as $item): ?>
        <div class="initiative-card">
          <h3><?php echo e($item['title'] ?? ''); ?></h3>
          <p><?php echo e($item['desc'] ?? ''); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if (!empty($csr)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">CSR</h2>
      <div class="csr-grid">
        <?php foreach ($csr as $c): ?>
        <div class="csr-card"><h3><?php echo e($c['title'] ?? ''); ?></h3><p><?php echo e($c['desc'] ?? ''); ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
