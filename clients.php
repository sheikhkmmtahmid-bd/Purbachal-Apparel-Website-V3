<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile = 'clients.php';
$page     = jsonRead(DATA_DIR . 'pages/clients.json');
$clients  = $page['clients'] ?? [];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>Our Clients</h1><p>Trusted by leading global fashion brands.</p></div>
  </section>
  <section class="section">
    <div class="container">
      <div class="clients-grid">
        <?php foreach ($clients as $c): ?>
        <div class="client-card">
          <?php if (!empty($c['logo'])): ?>
          <img src="<?php echo e(UPLOAD_URL . 'pages/' . $c['logo']); ?>" alt="<?php echo e($c['name'] ?? ''); ?>" loading="lazy">
          <?php else: ?>
          <span class="client-name-text"><?php echo e($c['name'] ?? ''); ?></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
