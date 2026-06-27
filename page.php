<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
sendSecurityHeaders();

$slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['p'] ?? ''));
if (!$slug) { http_response_code(404); exit('Page not found.'); }

$path = DATA_DIR . 'custom/' . $slug . '.json';
if (!is_file($path)) { http_response_code(404); exit('Page not found.'); }

$page      = jsonRead($path);
$site      = jsonRead(DATA_DIR . 'site.json');
$pageTitle = e($page['title'] ?? '') . ' | ' . e($site['company_name']);
$pageFile  = 'page.php';

require_once __DIR__ . '/includes/site-header.php';
?>
<main>
<?php foreach ($page['sections'] ?? [] as $block):
    $type = $block['type'] ?? '';
    $d    = $block['data'] ?? [];
?>

<?php if ($type === 'hero'): ?>
<section class="hero" id="hero"<?php if (!empty($d['bg_image'])): ?> style="background-image:linear-gradient(rgba(15,26,46,.72),rgba(15,26,46,.72)),url('<?php echo e($d['bg_image']); ?>');background-size:cover;background-position:center"<?php endif; ?>>
  <div class="container">
    <div class="hero-content" style="max-width:680px">
      <?php if (!empty($d['headline'])): ?><h1><?php echo e($d['headline']); ?></h1><?php endif; ?>
      <?php if (!empty($d['subheadline'])): ?><p><?php echo e($d['subheadline']); ?></p><?php endif; ?>
      <?php if (!empty($d['cta_text'])): ?>
      <div class="hero-btns">
        <a href="<?php echo e($d['cta_url'] ?? 'contact.php'); ?>" class="btn btn-primary"><?php echo e($d['cta_text']); ?></a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php elseif ($type === 'text_block'): ?>
<section class="section">
  <div class="container">
    <?php $hasImg = !empty($d['image']); $side = ($d['image_side'] ?? 'right') === 'left'; ?>
    <?php if ($hasImg): ?>
    <div style="display:flex;gap:3rem;align-items:center;flex-wrap:wrap;<?php echo $side ? 'flex-direction:row-reverse' : ''; ?>">
      <div style="flex:1;min-width:260px">
        <?php if (!empty($d['heading'])): ?><h2 class="section-title" style="text-align:left;margin-bottom:1rem"><?php echo e($d['heading']); ?></h2><?php endif; ?>
        <?php if (!empty($d['body'])): ?><p style="line-height:1.8"><?php echo nl2br(e($d['body'])); ?></p><?php endif; ?>
      </div>
      <div style="flex:1;min-width:260px">
        <img src="<?php echo e($d['image']); ?>" alt="" style="width:100%;border-radius:10px;display:block" loading="lazy">
      </div>
    </div>
    <?php else: ?>
    <?php if (!empty($d['heading'])): ?><h2 class="section-title"><?php echo e($d['heading']); ?></h2><?php endif; ?>
    <?php if (!empty($d['body'])): ?><p style="max-width:780px;margin:0 auto;line-height:1.8;text-align:center"><?php echo nl2br(e($d['body'])); ?></p><?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($type === 'cta_banner'): ?>
<section class="cta-banner">
  <div class="container">
    <?php if (!empty($d['heading'])): ?><h2><?php echo e($d['heading']); ?></h2><?php endif; ?>
    <?php if (!empty($d['text'])): ?><p><?php echo e($d['text']); ?></p><?php endif; ?>
    <?php if (!empty($d['btn_text'])): ?>
    <a href="<?php echo e($d['btn_url'] ?? 'contact.php'); ?>" class="btn btn-primary"><?php echo e($d['btn_text']); ?></a>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($type === 'image_grid'): ?>
<section class="section section-alt">
  <div class="container">
    <?php if (!empty($d['heading'])): ?><h2 class="section-title"><?php echo e($d['heading']); ?></h2><?php endif; ?>
    <?php if (!empty($d['images'])): ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:2rem">
      <?php foreach ($d['images'] as $img): ?>
      <div style="border-radius:8px;overflow:hidden;aspect-ratio:4/3">
        <img src="<?php echo e($img); ?>" alt="" style="width:100%;height:100%;object-fit:cover" loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php endif; ?>
<?php endforeach; ?>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
