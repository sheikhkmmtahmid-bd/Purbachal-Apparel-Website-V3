<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/section-renderer.php';
sendSecurityHeaders();

$pageFile        = 'products.php';
$page            = jsonRead(DATA_DIR . 'pages/products.json');
$pageTitle       = $page['title']     ?? 'Our Products | Purbachal Apparel Limited';
$pageDescription = $page['meta_desc'] ?? '';

$sections = $page['sections'] ?? [];
$catImgs  = $page['categories'] ?? [];
$cats = [
    'kids'   => ['label' => "Kid's Wear",   'desc' => "Quality children's garments designed for comfort and durability."],
    'mens'   => ['label' => "Men's Wear",   'desc' => "Premium woven menswear with superior craftsmanship."],
    'womens' => ['label' => "Women's Wear", 'desc' => "Elegant and modern styles for today's women."],
];

// Split sections: before carousel (up to first non-hero section that isn't cta) and after
$beforeSecs = [];
$afterSecs  = [];
$foundNonHero = false;
foreach ($sections as $s) {
    if (!$foundNonHero && in_array($s['type'], ['hero', 'home-hero'], true)) {
        $beforeSecs[] = $s;
    } else {
        $foundNonHero = true;
        $afterSecs[] = $s;
    }
}

require_once __DIR__ . '/includes/site-header.php';
?>
<main>
<?php pal_render_sections($beforeSecs); ?>

<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow reveal">What We Make</span>
      <h2 class="section-title reveal delay-1">Browse Our Collections</h2>
      <p class="section-desc reveal delay-2">With a monthly capacity of 750,000 pieces across 20+ production lines, we manufacture a wide range of high-quality woven garments for men, women, and children.</p>
    </div>
    <div class="filter-tabs reveal">
      <?php $first = true; foreach ($cats as $catKey => $info): ?>
      <button class="filter-btn<?php echo $first ? ' active' : ''; ?>" data-filter="<?php echo $catKey; ?>"><?php echo e($info['label']); ?></button>
      <?php $first = false; endforeach; ?>
    </div>
    <div class="carousel-panels">
      <?php $first = true; foreach ($cats as $catKey => $info):
        $imgs = $catImgs[$catKey] ?? [];
      ?>
      <div class="carousel-panel<?php echo $first ? ' active' : ''; ?>" data-panel="<?php echo $catKey; ?>">
        <div class="carousel-wrap">
          <button class="carousel-arrow carousel-prev" aria-label="Previous"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
          <div class="carousel-viewport">
            <div class="carousel-track">
              <?php if (!empty($imgs)): foreach ($imgs as $img): ?>
              <div class="carousel-item"><img src="<?php echo e(UPLOAD_URL . 'products/' . $img); ?>" alt="<?php echo e($info['label']); ?> - Purbachal Apparel" loading="lazy"></div>
              <?php endforeach; else: ?>
              <div style="padding:40px;color:var(--gray-400);font-size:.9rem;">No images uploaded yet.</div>
              <?php endif; ?>
            </div>
          </div>
          <button class="carousel-arrow carousel-next" aria-label="Next"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
        </div>
      </div>
      <?php $first = false; endforeach; ?>
    </div>
  </div>
</section>

<?php pal_render_sections($afterSecs); ?>
</main>
<div class="lb-overlay" id="lb-overlay">
  <button class="lb-close" id="lb-close" aria-label="Close">&times;</button>
  <div class="lb-img-wrap"><img id="lb-img" src="" alt=""></div>
</div>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
