<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile = 'certificates.php';
$page     = jsonRead(DATA_DIR . 'pages/certificates.json');
$certs    = $page['certificates'] ?? [];
$pillars  = $page['pillars'] ?? [];
$csr      = $page['csr_cards'] ?? [];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>Certifications &amp; Compliance</h1><p>Our commitment to quality, ethics, and sustainability.</p></div>
  </section>
  <?php if (!empty($pillars)): ?>
  <section class="section">
    <div class="container">
      <div class="pillars-grid">
        <?php foreach ($pillars as $p): ?>
        <div class="pillar-card"><h3><?php echo e($p['title'] ?? ''); ?></h3><p><?php echo e($p['desc'] ?? ''); ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if (!empty($certs)): ?>
  <section class="section section-alt">
    <div class="container">
      <h2 class="section-title">Our Certifications</h2>
      <div class="certs-grid">
        <?php foreach ($certs as $c): ?>
        <div class="cert-card">
          <?php if (!empty($c['logo'])): ?>
          <img src="<?php echo e(UPLOAD_URL . 'pages/' . $c['logo']); ?>" alt="<?php echo e($c['name'] ?? ''); ?>" loading="lazy">
          <?php else: ?><div class="cert-badge"><?php echo e($c['short'] ?? substr($c['name']??'',0,4)); ?></div><?php endif; ?>
          <h4><?php echo e($c['name'] ?? ''); ?></h4>
          <?php if (!empty($c['desc'])): ?><p><?php echo e($c['desc']); ?></p><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php if (!empty($csr)): ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title">CSR Commitments</h2>
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
