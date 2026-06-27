<?php if (!defined('_PAL_CMS_')) die('Direct access not permitted.'); ?>
<header class="admin-topbar">
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
  </button>
  <h1 class="topbar-title"><?php echo e($pageTitle ?? 'Dashboard'); ?></h1>
  <div class="topbar-right">
    <a href="../index.php" class="btn btn-sm btn-outline" target="_blank">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      View Site
    </a>
    <div class="topbar-user">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <?php echo e($_SESSION['pal_admin_user'] ?? 'Admin'); ?>
    </div>
  </div>
</header>
