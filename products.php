<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$pageFile = 'products.php';
$page     = jsonRead(DATA_DIR . 'pages/products.json');
$cats = [
  'kids' => ['label' => 'Kid\'s Wear', 'desc' => 'Quality children\'s garments designed for comfort and durability.'],
  'mens' => ['label' => 'Men\'s Wear', 'desc' => 'Premium woven menswear with superior craftsmanship.'],
  'womens' => ['label' => 'Women\'s Wear', 'desc' => 'Elegant and modern styles for today\'s women.'],
];
require_once __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="page-hero">
    <div class="container"><h1>Our Products</h1><p>Woven garments across three categories, manufactured with precision.</p></div>
  </section>
  <?php foreach ($cats as $cat => $info): $imgs = $page[$cat] ?? []; ?>
  <section class="section <?php echo ($cat === 'mens') ? 'section-alt' : ''; ?>" id="<?php echo e($cat); ?>">
    <div class="container">
      <h2 class="section-title"><?php echo e($info['label']); ?></h2>
      <p class="section-desc"><?php echo e($info['desc']); ?></p>
      <?php if (!empty($imgs)): ?>
      <div class="products-grid">
        <?php foreach ($imgs as $img): ?>
        <div class="product-card">
          <img src="uploads/products/<?php echo e($cat); ?>/<?php echo e($img); ?>" alt="<?php echo e($info['label']); ?>" loading="lazy">
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p class="text-muted">Images coming soon.</p>
      <?php endif; ?>
    </div>
  </section>
  <?php endforeach; ?>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
