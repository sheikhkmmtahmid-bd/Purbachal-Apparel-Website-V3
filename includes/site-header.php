<?php
if (!defined('_PAL_CMS_')) die('Direct access not permitted.');
$pageFile  = $pageFile  ?? basename($_SERVER['PHP_SELF']);
$site      = jsonRead(DATA_DIR . 'site.json');
$navData   = jsonRead(DATA_DIR . 'nav.json');
$logoSrc   = getLogoSrc($site);
$social    = $site['social'] ?? [];
$_docTitle = isset($pageTitle)
    ? e($pageTitle)
    : e($site['company_name'] . ' | Woven Garment Manufacturer, Bangladesh');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $_docTitle; ?></title>
<?php if (!empty($pageDescription)): ?>
<meta name="description" content="<?php echo e($pageDescription); ?>">
<?php endif; ?>
<link rel="icon" type="image/png" sizes="64x64" href="favicon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32.png">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="topbar">
  <div class="container">
    <div class="topbar-inner">
      <div class="topbar-left">
        <div class="topbar-item">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <?php echo e($site['address']); ?>
        </div>
        <div class="topbar-item">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a>
        </div>
        <div class="topbar-item">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          <a href="tel:<?php echo e($site['phone1_tel']); ?>"><?php echo e($site['phone1']); ?></a> &nbsp;|&nbsp; <a href="tel:<?php echo e($site['phone2_tel']); ?>"><?php echo e($site['phone2']); ?></a>
        </div>
      </div>
      <div class="topbar-right">
        <?php if (!empty($social['facebook'])): ?>
        <a href="<?php echo e($social['facebook']); ?>" class="topbar-social" aria-label="Facebook" target="_blank" rel="noopener"><?php echo getSocialIconSvg('facebook', 14); ?></a>
        <?php endif; ?>
        <?php if (!empty($social['linkedin'])): ?>
        <a href="<?php echo e($social['linkedin']); ?>" class="topbar-social" aria-label="LinkedIn" target="_blank" rel="noopener"><?php echo getSocialIconSvg('linkedin', 14); ?></a>
        <?php endif; ?>
        <?php if (!empty($social['youtube'])): ?>
        <a href="<?php echo e($social['youtube']); ?>" class="topbar-social" aria-label="YouTube" target="_blank" rel="noopener"><?php echo getSocialIconSvg('youtube', 14); ?></a>
        <?php endif; ?>
        <?php if (!empty($social['whatsapp'])): ?>
        <a href="<?php echo e($social['whatsapp']); ?>" class="topbar-social" aria-label="WhatsApp" target="_blank" rel="noopener"><?php echo getSocialIconSvg('whatsapp', 14); ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<nav class="site-nav" id="site-nav">
  <div class="container">
    <div class="nav-inner">
      <a href="index.php" class="nav-logo">
        <div class="nav-logo-icon"><img src="<?php echo $logoSrc; ?>" alt="<?php echo e($site['company_name']); ?>" loading="lazy"></div>
        <span class="nav-logo-name">Purbachal<br>Apparel Limited</span>
      </a>
      <ul class="nav-links">
        <li class="nav-item"><a href="index.php" class="nav-link<?php echo ($pageFile === 'index.php') ? ' active' : ''; ?>">Home</a></li>
        <?php foreach ($navData['pages'] as $page):
            if (($page['slug'] ?? '') === 'index') continue;
            $pFile   = $page['file'];
            $pLabel  = e($page['label']);
            $pActive = ($pFile === $pageFile) ? ' active' : '';
            if (!empty($page['dropdown'])): ?>
        <li class="nav-item">
          <a href="<?php echo e($pFile); ?>" class="nav-link<?php echo $pActive; ?>"><?php echo $pLabel; ?> <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg></a>
          <div class="dropdown">
            <a href="<?php echo e($pFile); ?>" class="dropdown-link"><?php echo $pLabel; ?></a>
            <?php foreach ($page['dropdown'] as $child): ?>
            <a href="<?php echo e($child['file']); ?>" class="dropdown-link"><?php echo e($child['label']); ?></a>
            <?php endforeach; ?>
          </div>
        </li>
            <?php else: ?>
        <li class="nav-item"><a href="<?php echo e($pFile); ?>" class="nav-link<?php echo $pActive; ?>"><?php echo $pLabel; ?></a></li>
            <?php endif;
        endforeach; ?>
      </ul>
      <a href="<?php echo e($navData['cta']['file'] ?? 'contact.php'); ?>" class="btn btn-primary btn-sm nav-cta"><?php echo e($navData['cta']['label'] ?? 'Get a Quote'); ?></a>
      <button class="hamburger" aria-label="Open menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</nav>
<div class="mobile-nav">
  <button class="mobile-nav-close" aria-label="Close menu">&times;</button>
  <ul class="mobile-nav-links">
    <li><a href="index.php" class="mobile-nav-link">Home</a></li>
    <?php foreach ($navData['pages'] as $page):
        if (($page['slug'] ?? '') === 'index') continue;
        $pFile  = $page['file'];
        $pLabel = e($page['label']);
        if (!empty($page['dropdown'])): ?>
    <li class="mobile-has-dropdown">
      <a href="<?php echo e($pFile); ?>" class="mobile-nav-link"><?php echo $pLabel; ?></a>
      <div class="mobile-dropdown">
        <a href="<?php echo e($pFile); ?>" class="mobile-dropdown-link"><?php echo $pLabel; ?></a>
        <?php foreach ($page['dropdown'] as $child): ?>
        <a href="<?php echo e($child['file']); ?>" class="mobile-dropdown-link"><?php echo e($child['label']); ?></a>
        <?php endforeach; ?>
      </div>
    </li>
        <?php else: ?>
    <li><a href="<?php echo e($pFile); ?>" class="mobile-nav-link"><?php echo $pLabel; ?></a></li>
        <?php endif;
    endforeach; ?>
    <li style="margin-top:16px"><a href="<?php echo e($navData['cta']['file'] ?? 'contact.php'); ?>" class="btn btn-primary" style="display:block;text-align:center"><?php echo e($navData['cta']['label'] ?? 'Get a Quote'); ?></a></li>
  </ul>
</div>
<div class="overlay"></div>
