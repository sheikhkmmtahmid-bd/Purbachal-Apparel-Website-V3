<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
sendSecurityHeaders();
requireLogin();

$nav     = jsonRead(DATA_DIR . 'nav.json');
$allPages = $nav['pages'] ?? [];

// Separate system vs custom pages
$systemPages = [];
$customPages  = [];
foreach ($allPages as $p) {
    if ($p['builtin'] ?? false) $systemPages[] = $p;
    else                         $customPages[]  = $p;
}

// Active slug from query string (only custom pages)
$slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['slug'] ?? ''));
$activeCustom = null;
foreach ($customPages as $cp) {
    if ($cp['slug'] === $slug) { $activeCustom = $cp; break; }
}

$pageData = [];
$sections  = [];
if ($activeCustom) {
    $pageData = jsonRead(DATA_DIR . 'pages/' . $slug . '.json');
    $sections  = $pageData['sections'] ?? [];
}

// Parent pages list for dropdown placement (system + other custom top-level pages)
$parentOptions = [];
foreach ($allPages as $p) {
    if (($p['slug'] ?? '') === 'index') continue;
    $parentOptions[] = ['slug' => $p['slug'], 'label' => $p['label']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Page Builder | PAL CMS</title>
<link rel="stylesheet" href="assets/admin.css">
<?php echo csrfMeta(); ?>
<style>
/* ── Layout ─────────────────────────────────────────────────────────── */
.pb-wrap{display:flex;gap:0;min-height:calc(100vh - 60px)}
.pb-sidebar{width:240px;min-width:240px;border-right:1px solid var(--border);background:#fff;display:flex;flex-direction:column;flex-shrink:0}
.pb-sidebar-head{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px}
.pb-sidebar-head span{flex:1;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--gray-500)}
.pb-main{flex:1;padding:28px 32px;overflow-y:auto;min-width:0}
/* ── Page list ──────────────────────────────────────────────────────── */
.pb-group-label{padding:10px 16px 4px;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400)}
.pb-page-item{display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:.825rem;color:var(--gray-700);cursor:pointer;text-decoration:none;border-left:3px solid transparent;transition:background .12s;border-bottom:1px solid var(--gray-100)}
.pb-page-item:hover{background:var(--gray-50);color:var(--navy)}
.pb-page-item.active{background:#eef8f8;color:var(--teal);border-left-color:var(--teal);font-weight:600}
.pb-page-item.system{color:var(--gray-400);font-style:italic}
.pb-page-item.system:hover{color:var(--gray-600);background:var(--gray-50)}
.pb-status-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.pb-status-dot.active{background:#22c55e}
.pb-status-dot.suspended{background:#f59e0b}
/* ── Toolbar ─────────────────────────────────────────────────────────── */
.pb-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap}
.pb-breadcrumb{font-size:.875rem;color:var(--gray-500);flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pb-breadcrumb strong{color:var(--navy)}
.pb-status-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;flex-shrink:0}
.pb-status-badge.active{background:#dcfce7;color:#15803d}
.pb-status-badge.suspended{background:#fef3c7;color:#b45309}
/* ── Settings card ────────────────────────────────────────────────────── */
.pb-meta-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);padding:16px 20px;margin-bottom:20px}
.pb-meta-card h4{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--gray-500);margin-bottom:12px}
.pb-meta-row{display:flex;gap:12px;flex-wrap:wrap}
.pb-meta-field{flex:1;min-width:180px}
.pb-meta-field label{display:block;font-size:.72rem;font-weight:600;color:var(--gray-600);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em}
.pb-meta-field input,.pb-meta-field textarea,.pb-meta-field select{width:100%;border:1px solid var(--border);border-radius:6px;padding:7px 10px;font-size:.875rem;color:var(--gray-800);background:#fff}
.pb-meta-field textarea{resize:vertical;min-height:56px}
.pb-meta-field input:focus,.pb-meta-field textarea:focus,.pb-meta-field select:focus{outline:none;border-color:var(--teal);box-shadow:0 0 0 3px rgba(14,126,135,.1)}
/* ── Nav placement inline ──────────────────────────────────────────── */
.pb-nav-placement{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:10px 14px;background:var(--gray-50);border:1px solid var(--border);border-radius:8px;margin-bottom:20px;font-size:.8125rem}
.pb-nav-placement label{display:flex;align-items:center;gap:6px;color:var(--gray-700);font-weight:500;cursor:pointer}
.pb-nav-placement input[type=checkbox]{width:15px;height:15px}
/* ── Section list ─────────────────────────────────────────────────────── */
.pb-sections{display:flex;flex-direction:column;gap:10px}
.pb-sec-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;transition:box-shadow .15s}
.pb-sec-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.08)}
.pb-sec-card.sortable-ghost{opacity:.4}
.pb-sec-header{display:flex;align-items:center;gap:10px;padding:12px 14px}
.pb-sec-drag{cursor:grab;color:var(--gray-300);padding:2px 4px;flex-shrink:0}
.pb-sec-drag:active{cursor:grabbing}
.pb-sec-type-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;flex-shrink:0}
.pb-sec-info{flex:1;min-width:0}
.pb-sec-title{font-size:.875rem;font-weight:600;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pb-sec-sub{font-size:.72rem;color:var(--gray-400);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pb-sec-actions{display:flex;gap:6px;flex-shrink:0}
.pb-sec-btn{display:flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;color:var(--gray-500);transition:all .15s}
.pb-sec-btn:hover{border-color:var(--teal);color:var(--teal);background:#eef8f8}
.pb-sec-btn.del:hover{border-color:#e53e3e;color:#e53e3e;background:#fff5f5}
.pb-sec-preview{border-top:1px solid var(--gray-100);padding:12px 14px;display:none}
.pb-sec-card.expanded .pb-sec-preview{display:block}
.pb-preview-hero{background:linear-gradient(135deg,#0f1a2e,#0a3042);border-radius:6px;padding:14px 18px;color:#fff}
.pb-preview-hero h4{font-size:.875rem;font-weight:700;margin:0 0 4px}
.pb-preview-hero p{font-size:.72rem;opacity:.7;margin:0}
.pb-preview-2col{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.pb-preview-img{background:var(--gray-100);border-radius:6px;min-height:60px;display:flex;align-items:center;justify-content:center;color:var(--gray-300);font-size:.7rem;overflow:hidden}
.pb-preview-img img{width:100%;height:100%;object-fit:cover;border-radius:6px}
.pb-preview-text{padding:6px}
.pb-preview-text h5{font-size:.8rem;font-weight:600;color:var(--navy);margin:0 0 4px}
.pb-preview-text p{font-size:.7rem;color:var(--gray-500);margin:0}
.pb-preview-cards{display:grid;gap:6px}
.pb-preview-card{background:var(--gray-50);border:1px solid var(--border);border-radius:6px;padding:7px 10px}
.pb-preview-card h5{font-size:.72rem;font-weight:600;color:var(--navy);margin:0 0 3px}
.pb-preview-card p{font-size:.68rem;color:var(--gray-500);margin:0}
.pb-preview-stats{display:flex;gap:6px;background:#0f1a2e;border-radius:6px;padding:10px 14px}
.pb-preview-stat{flex:1;text-align:center}
.pb-preview-stat strong{display:block;font-size:.9rem;font-weight:700;color:#41c6c6}
.pb-preview-stat span{font-size:.65rem;color:rgba(255,255,255,.6)}
.pb-preview-cta{background:linear-gradient(135deg,#0E7E87,#0a3042);border-radius:6px;padding:12px 16px;text-align:center;color:#fff}
.pb-preview-cta h5{font-size:.875rem;font-weight:700;margin:0 0 5px}
.pb-preview-cta p{font-size:.7rem;opacity:.8;margin:0}
.pb-preview-logos{display:flex;flex-wrap:wrap;gap:6px}
.pb-preview-logo{background:var(--gray-50);border:1px solid var(--border);border-radius:4px;width:48px;height:34px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.pb-preview-logo img{max-width:40px;max-height:26px;object-fit:contain}
.pb-preview-gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:6px}
.pb-preview-gallery-item{border-radius:6px;overflow:hidden;height:56px;background:var(--gray-100)}
.pb-preview-gallery-item img{width:100%;height:100%;object-fit:cover}
.pb-add-btn{margin-top:12px;border:2px dashed var(--border);border-radius:var(--radius-md);padding:14px;text-align:center;cursor:pointer;color:var(--gray-500);transition:all .15s;background:#fff;width:100%;font-size:.875rem;font-weight:500}
.pb-add-btn:hover{border-color:var(--teal);color:var(--teal);background:#eef8f8}
/* ── Empty / system state ─────────────────────────────────────────────── */
.pb-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:360px;text-align:center;gap:12px;color:var(--gray-400)}
.pb-empty svg{opacity:.35}
.pb-empty h3{font-size:1.1rem;font-weight:700;color:var(--gray-600);margin:0}
.pb-empty p{font-size:.875rem;margin:0;max-width:340px}
.pb-system-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);padding:28px;text-align:center;max-width:460px;margin:60px auto}
.pb-system-box h3{color:var(--navy);margin-bottom:8px}
.pb-system-box p{color:var(--gray-500);font-size:.9rem;margin-bottom:20px}
/* ── Slide panel (section edit) ───────────────────────────────────────── */
.pb-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:none;align-items:flex-start;justify-content:flex-end}
.pb-overlay.open{display:flex}
.pb-modal{background:#fff;width:min(680px,100vw);height:100vh;overflow-y:auto;box-shadow:-4px 0 24px rgba(0,0,0,.15);display:flex;flex-direction:column}
.pb-modal-header{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid var(--border);position:sticky;top:0;background:#fff;z-index:10}
.pb-modal-title{flex:1;font-size:1rem;font-weight:700;color:var(--navy)}
.pb-modal-body{padding:20px;flex:1}
.pb-modal-footer{padding:14px 20px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;position:sticky;bottom:0;background:#fff}
/* ── Section type chooser ─────────────────────────────────────────────── */
.pb-chooser-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1100;display:none;align-items:center;justify-content:center;padding:16px}
.pb-chooser-overlay.open{display:flex}
.pb-chooser{background:#fff;border-radius:var(--radius-lg);width:min(820px,100%);max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.pb-chooser-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px}
.pb-chooser-title{flex:1;font-size:1rem;font-weight:700;color:var(--navy)}
.pb-chooser-body{padding:18px 22px}
.pb-chooser-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:10px}
.pb-type-card{border:2px solid var(--border);border-radius:var(--radius-md);padding:14px;cursor:pointer;transition:all .15s;display:flex;flex-direction:column;gap:7px}
.pb-type-card:hover{border-color:var(--teal);background:#eef8f8}
.pt-icon{width:34px;height:34px;border-radius:7px;display:flex;align-items:center;justify-content:center}
.pb-type-card h5{font-size:.78rem;font-weight:700;color:var(--navy);margin:0}
.pb-type-card p{font-size:.68rem;color:var(--gray-500);margin:0}
/* ── Fields ──────────────────────────────────────────────────────────── */
.pb-field{margin-bottom:16px}
.pb-field label{display:block;font-size:.72rem;font-weight:600;color:var(--gray-600);margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em}
.pb-field input[type=text],.pb-field input[type=url],.pb-field select{width:100%;border:1px solid var(--border);border-radius:6px;padding:8px 11px;font-size:.875rem;color:var(--gray-800);background:#fff}
.pb-field textarea{width:100%;border:1px solid var(--border);border-radius:6px;padding:8px 11px;font-size:.875rem;color:var(--gray-800);background:#fff;resize:vertical;min-height:70px}
.pb-field input:focus,.pb-field textarea:focus,.pb-field select:focus{outline:none;border-color:var(--teal);box-shadow:0 0 0 3px rgba(14,126,135,.1)}
.pb-img-wrap{border:1px solid var(--border);border-radius:8px;padding:10px;display:flex;align-items:center;gap:12px}
.pb-img-preview{width:80px;height:56px;border-radius:6px;object-fit:cover;flex-shrink:0;display:block}
.pb-img-placeholder{width:80px;height:56px;border-radius:6px;background:var(--gray-100);display:flex;align-items:center;justify-content:center;color:var(--gray-300);flex-shrink:0}
.pb-img-info{flex:1;min-width:0}
.pb-img-name{font-size:.72rem;color:var(--gray-500);word-break:break-all;margin-bottom:6px}
/* ── Icon picker field ────────────────────────────────────────────────── */
.pb-icon-field{display:flex;align-items:center;gap:10px;border:1px solid var(--border);border-radius:8px;padding:8px 12px}
.pb-icon-preview{width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:var(--gray-50);border-radius:6px;color:var(--teal);flex-shrink:0}
.pb-icon-name{flex:1;font-size:.8125rem;color:var(--gray-600)}
/* ── Icon picker modal ────────────────────────────────────────────────── */
.pb-icon-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1200;display:none;align-items:center;justify-content:center;padding:20px}
.pb-icon-overlay.open{display:flex}
.pb-icon-modal{background:#fff;border-radius:var(--radius-lg);width:min(600px,100%);max-height:80vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.pb-icon-modal-head{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.pb-icon-modal-head span{flex:1;font-weight:700;color:var(--navy)}
.pb-icon-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(48px,1fr));gap:6px;padding:16px 20px;overflow-y:auto}
.pb-icon-btn{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;padding:8px 4px;border:1px solid var(--border);border-radius:8px;background:#fff;cursor:pointer;transition:all .15s;color:var(--gray-600)}
.pb-icon-btn:hover{border-color:var(--teal);background:#eef8f8;color:var(--teal)}
.pb-icon-btn.selected{border-color:var(--teal);background:#eef8f8;color:var(--teal)}
.pb-icon-btn span{font-size:.55rem;color:var(--gray-400);text-align:center;line-height:1.2}
/* ── Items list ──────────────────────────────────────────────────────── */
.pb-section-divider{margin:20px 0 8px;border-bottom:2px solid var(--border);padding-bottom:4px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--teal)}
.pb-items-list{display:flex;flex-direction:column;gap:8px}
.pb-item-block{background:var(--gray-50);border:1px solid var(--border);border-radius:8px;overflow:hidden}
.pb-item-header{display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;user-select:none}
.pb-item-drag{cursor:grab;color:var(--gray-300);flex-shrink:0}
.pb-item-num{font-size:.8rem;font-weight:600;color:var(--gray-700);flex:1}
.pb-item-del{width:26px;height:26px;border:1px solid var(--border);border-radius:4px;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gray-400);flex-shrink:0}
.pb-item-del:hover{border-color:#e53e3e;color:#e53e3e}
.pb-item-body{padding:10px 12px;border-top:1px solid var(--border);display:none}
.pb-item-block.expanded .pb-item-body{display:block}
.pb-add-item{font-size:.8rem;color:var(--teal);background:none;border:1px dashed var(--teal);border-radius:6px;padding:7px 14px;cursor:pointer;margin-top:8px;display:inline-block}
.pb-add-item:hover{background:#eef8f8}
.pb-checklist-list{display:flex;flex-direction:column;gap:6px;margin-top:6px}
.pb-checklist-item{display:flex;gap:8px;align-items:center}
.pb-checklist-item input{flex:1;border:1px solid var(--border);border-radius:6px;padding:6px 10px;font-size:.875rem}
.pb-checklist-del{width:28px;height:28px;border:1px solid var(--border);border-radius:4px;background:#fff;cursor:pointer;color:var(--gray-400);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pb-checklist-del:hover{border-color:#e53e3e;color:#e53e3e}
/* ── Create page modal ────────────────────────────────────────────────── */
.pb-create-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1100;display:none;align-items:center;justify-content:center;padding:20px}
.pb-create-overlay.open{display:flex}
.pb-create-modal{background:#fff;border-radius:var(--radius-lg);width:min(520px,100%);box-shadow:0 20px 60px rgba(0,0,0,.25);padding:28px}
.pb-create-modal h3{font-size:1.1rem;font-weight:800;color:var(--navy);margin:0 0 20px}
/* ── Action buttons bar ───────────────────────────────────────────────── */
.pb-actions-bar{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap}
/* ── Delete confirm modal ─────────────────────────────────────────────── */
.pb-delete-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1300;display:none;align-items:center;justify-content:center;padding:20px}
.pb-delete-overlay.open{display:flex}
.pb-delete-modal{background:#fff;border-radius:var(--radius-lg);width:min(440px,100%);padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.pb-delete-modal h3{color:#c53030;margin:0 0 12px}
.pb-delete-modal p{color:var(--gray-600);font-size:.9rem;margin-bottom:20px}
.pb-delete-modal .del-actions{display:flex;gap:10px;justify-content:flex-end}
/* ── Rename modal ─────────────────────────────────────────────────────── */
.pb-rename-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1300;display:none;align-items:center;justify-content:center;padding:20px}
.pb-rename-overlay.open{display:flex}
.pb-rename-modal{background:#fff;border-radius:var(--radius-lg);width:min(440px,100%);padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.pb-rename-modal h3{color:var(--navy);margin:0 0 16px}
</style>
</head>
<body class="admin-body">
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>
<div class="admin-main">
<?php require_once __DIR__ . '/partials/topbar.php'; ?>
<div class="admin-content" style="padding:0">

<div class="pb-wrap">
  <!-- Sidebar: Page List -->
  <div class="pb-sidebar">
    <div class="pb-sidebar-head">
      <span>Pages</span>
      <button class="btn btn-sm btn-primary" id="createPageBtn" style="padding:4px 10px;font-size:.75rem">+ New</button>
    </div>

    <?php if (!empty($customPages)): ?>
    <div class="pb-group-label">Your Pages</div>
    <?php foreach ($customPages as $cp):
      $cStatus = $cp['status'] ?? 'active';
      $cActive = ($cp['slug'] === $slug) ? ' active' : '';
    ?>
    <a href="?slug=<?php echo e($cp['slug']); ?>" class="pb-page-item<?php echo $cActive; ?>">
      <span class="pb-status-dot <?php echo $cStatus === 'active' ? 'active' : 'suspended'; ?>"></span>
      <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo e($cp['label']); ?></span>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="pb-group-label" style="margin-top:8px">System Pages</div>
    <?php foreach ($systemPages as $sp):
      if (($sp['slug'] ?? '') === 'index') continue;
    ?>
    <a href="../admin/page-editor.php?page=<?php echo e($sp['slug']); ?>" class="pb-page-item system" target="_blank" title="Edit in Page Editor">
      <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo e($sp['label']); ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Right: Content Area -->
  <div class="pb-main">
    <?php if ($activeCustom): ?>
    <!-- ── Custom page editor ── -->
    <?php
      $cStatus = $activeCustom['status'] ?? 'active';
      $isSuspended = $cStatus === 'suspended';
    ?>
    <div class="pb-toolbar">
      <div class="pb-breadcrumb">Page Builder &rsaquo; <strong><?php echo e($activeCustom['label']); ?></strong></div>
      <span class="pb-status-badge <?php echo $isSuspended ? 'suspended' : 'active'; ?>">
        <?php echo $isSuspended ? 'Suspended' : 'Active'; ?>
      </span>
      <?php if (!$isSuspended): ?>
      <a href="../<?php echo e($slug . '.php'); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:-2px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Preview
      </a>
      <?php endif; ?>
      <button class="btn btn-primary btn-sm" id="saveBtn">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:-2px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
        Save
      </button>
    </div>

    <!-- Actions bar -->
    <div class="pb-actions-bar">
      <button class="btn btn-sm btn-outline-secondary" id="renameBtn">Rename</button>
      <?php if (!$isSuspended): ?>
      <button class="btn btn-sm" id="suspendBtn" style="border:1px solid #f59e0b;color:#b45309;background:#fff">Suspend Page</button>
      <?php else: ?>
      <button class="btn btn-sm btn-outline-secondary" id="activateBtn">Activate Page</button>
      <button class="btn btn-sm" id="deleteBtn" style="border:1px solid #e53e3e;color:#e53e3e;background:#fff">Delete Page</button>
      <?php endif; ?>
    </div>

    <!-- Page Settings -->
    <div class="pb-meta-card">
      <h4>Page Settings</h4>
      <div class="pb-meta-row">
        <div class="pb-meta-field">
          <label>Browser Tab Title</label>
          <input type="text" id="metaTitle" value="<?php echo e($pageData['title'] ?? ''); ?>">
        </div>
        <div class="pb-meta-field">
          <label>Meta Description (SEO)</label>
          <textarea id="metaDesc" rows="2"><?php echo e($pageData['meta_desc'] ?? ''); ?></textarea>
        </div>
      </div>
    </div>

    <!-- Nav placement -->
    <div class="pb-nav-placement">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--teal)" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
      <label>
        <input type="checkbox" id="navShowCheck" <?php echo ($activeCustom['show_in_nav'] ?? true) ? 'checked' : ''; ?>>
        Show in navigation
      </label>
      <button class="btn btn-sm btn-outline-secondary" id="saveNavBtn" style="margin-left:auto">Save Nav Settings</button>
    </div>

    <!-- Section list -->
    <div class="pb-sections" id="sectionList"></div>
    <button class="pb-add-btn" id="addSectionBtn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px;margin-right:6px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add New Section
    </button>

    <?php elseif ($slug && !$activeCustom): ?>
    <!-- Slug in URL but not found as custom page (may be system page) -->
    <div class="pb-system-box">
      <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--teal)" style="margin-bottom:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <h3>System Page</h3>
      <p>System pages use the Page Editor for content changes. The Page Builder is for new pages you create.</p>
      <a href="../admin/page-editor.php?page=<?php echo e($slug); ?>" target="_blank" class="btn btn-primary">Open in Page Editor</a>
    </div>

    <?php else: ?>
    <!-- No page selected -->
    <div class="pb-empty">
      <svg width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="var(--gray-300)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <h3><?php echo empty($customPages) ? 'No pages yet' : 'Select a page'; ?></h3>
      <p><?php echo empty($customPages) ? 'Create your first page to get started. Add sections, set nav placement, and publish.' : 'Select a page from the sidebar to edit its sections, or create a new one.'; ?></p>
      <button class="btn btn-primary" id="createPageBtn2">Create a New Page</button>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ── Section Edit Panel ── -->
<div class="pb-overlay" id="editOverlay">
  <div class="pb-modal" id="editModal">
    <div class="pb-modal-header">
      <div class="pb-modal-title" id="editModalTitle">Edit Section</div>
      <button class="btn btn-sm btn-outline-secondary" id="closeEditBtn">Close</button>
    </div>
    <div class="pb-modal-body" id="editModalBody"></div>
    <div class="pb-modal-footer">
      <button class="btn btn-sm btn-outline-secondary" id="cancelEditBtn">Cancel</button>
      <button class="btn btn-primary btn-sm" id="applyEditBtn">Apply Changes</button>
    </div>
  </div>
</div>

<!-- ── Section Type Chooser ── -->
<div class="pb-chooser-overlay" id="chooserOverlay">
  <div class="pb-chooser">
    <div class="pb-chooser-header">
      <div class="pb-chooser-title">Choose Section Type</div>
      <button class="btn btn-sm btn-outline-secondary" id="closeChooserBtn">Cancel</button>
    </div>
    <div class="pb-chooser-body">
      <div class="pb-chooser-grid" id="chooserGrid"></div>
    </div>
  </div>
</div>

<!-- ── Icon Picker Modal ── -->
<div class="pb-icon-overlay" id="iconOverlay">
  <div class="pb-icon-modal">
    <div class="pb-icon-modal-head">
      <span>Choose an Icon</span>
      <button class="btn btn-sm btn-outline-secondary" id="closeIconBtn">Cancel</button>
    </div>
    <div class="pb-icon-grid" id="iconPickerGrid"></div>
  </div>
</div>

<!-- ── Create Page Modal ── -->
<div class="pb-create-overlay" id="createOverlay">
  <div class="pb-create-modal">
    <h3>Create a New Page</h3>
    <div class="pb-field">
      <label>Page Name <span style="color:#e53e3e">*</span></label>
      <input type="text" id="cpLabel" placeholder="e.g. Careers" autocomplete="off">
    </div>
    <div class="pb-field">
      <label>URL Slug</label>
      <input type="text" id="cpSlug" placeholder="auto-generated" autocomplete="off">
      <div style="font-size:.72rem;color:var(--gray-400);margin-top:4px">Will be: <code id="cpSlugPreview" style="color:var(--teal)">yoursite.com/...</code></div>
    </div>
    <div class="pb-field">
      <label>Navigation Label</label>
      <input type="text" id="cpNavLabel" placeholder="Same as Page Name">
    </div>
    <div class="pb-field" style="margin-bottom:6px">
      <label style="display:flex;align-items:center;gap:8px;text-transform:none;font-size:.875rem;font-weight:500;letter-spacing:0;color:var(--gray-700)">
        <input type="checkbox" id="cpShowNav" checked style="width:15px;height:15px">
        Show in main navigation
      </label>
    </div>
    <div class="pb-field" id="cpParentRow" style="display:none">
      <label>Place Under (dropdown)</label>
      <select id="cpParent">
        <option value="">Top-level (no parent)</option>
        <?php foreach ($parentOptions as $po): ?>
        <option value="<?php echo e($po['slug']); ?>"><?php echo e($po['label']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div id="cpError" style="color:#e53e3e;font-size:.8125rem;margin-bottom:12px;display:none"></div>
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
      <button class="btn btn-outline-secondary" id="cancelCreateBtn">Cancel</button>
      <button class="btn btn-primary" id="submitCreateBtn">Create Page</button>
    </div>
  </div>
</div>

<!-- ── Rename Modal ── -->
<div class="pb-rename-overlay" id="renameOverlay">
  <div class="pb-rename-modal">
    <h3>Rename Page</h3>
    <div class="pb-field">
      <label>New Navigation Label</label>
      <input type="text" id="renameLabel" placeholder="Page label in nav">
    </div>
    <div id="renameError" style="color:#e53e3e;font-size:.8125rem;margin-bottom:12px;display:none"></div>
    <div style="display:flex;gap:10px;justify-content:flex-end">
      <button class="btn btn-outline-secondary" id="cancelRenameBtn">Cancel</button>
      <button class="btn btn-primary" id="submitRenameBtn">Save Label</button>
    </div>
  </div>
</div>

<!-- ── Delete Confirm Modal ── -->
<div class="pb-delete-overlay" id="deleteOverlay">
  <div class="pb-delete-modal">
    <h3>Delete Page?</h3>
    <p>This will permanently remove the page, all its sections, and the PHP file. <strong>This cannot be undone.</strong></p>
    <div class="del-actions">
      <button class="btn btn-outline-secondary" id="cancelDeleteBtn">Cancel</button>
      <button class="btn" id="confirmDeleteBtn" style="background:#e53e3e;color:#fff;border-color:#e53e3e">Delete Permanently</button>
    </div>
  </div>
</div>

</div>
</div>
<div id="toast-container"></div>
<script src="assets/admin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
(function(){
var CSRF = (document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var SLUG = <?php echo json_encode($slug); ?>;
var sections = <?php echo json_encode($sections, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
var editingIdx = -1;
var sortable = null;

// ── Section type definitions ──────────────────────────────────────────
var TYPES = {
  'hero':{label:'Page Hero',bg:'#0f1a2e',col:'#fff',desc:'Dark banner at the top of the page.',fields:[
    {k:'title',l:'Title',t:'text'},{k:'desc',l:'Description',t:'textarea'},{k:'bg_image',l:'Background Image (optional)',t:'image'}]},
  'text':{label:'Text Block',bg:'#eef8f8',col:'#0f1a2e',desc:'Heading and body text section.',fields:[
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},{k:'subheading',l:'Subheading',t:'textarea'},
    {k:'body',l:'Body Text',t:'textarea'},{k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'centered',l:'Center Aligned',t:'checkbox'}]},
  'text-image':{label:'Text + Image',bg:'#fff3e0',col:'#0f1a2e',desc:'Side-by-side or stacked text with an image.',fields:[
    {k:'layout',l:'Image Position',t:'select',options:[['right','Image on Right'],['left','Image on Left'],['top','Image on Top'],['bottom','Image on Bottom']]},
    {k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},{k:'subheading',l:'Subheading',t:'textarea'},
    {k:'body',l:'Body Text',t:'textarea'},{k:'checklist',l:'Checklist Items',t:'checklist'},
    {k:'cta_text',l:'Button Text',t:'text'},{k:'cta_link',l:'Button Link',t:'text'},
    {k:'image',l:'Image',t:'image'},{k:'image_alt',l:'Image Alt Text',t:'text'}]},
  'cards':{label:'Card Grid',bg:'#e8f4fd',col:'#0f1a2e',desc:'Grid of cards for services, features, pillars, etc.',fields:[
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},
    {k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'style',l:'Card Style',t:'select',options:[['default','Default (with optional icon)'],['mission','Mission Cards (teal icon circle)'],['process','Process Steps (numbered)'],['pillars','Pillar Cards (colored bg)'],['initiative','Initiative Cards (colored icon)']]},
    {k:'columns',l:'Columns',t:'select',options:[['2','2 Columns'],['3','3 Columns'],['4','4 Columns']]},
    {k:'items',l:'Cards',t:'items',schema:[
      {k:'icon',l:'Icon',t:'icon'},{k:'number',l:'Number (01, 02 - process only)',t:'text'},
      {k:'title',l:'Title',t:'text'},{k:'text',l:'Text',t:'textarea'},
      {k:'mod',l:'Pillar Card Color (pillars style only)',t:'select',options:[['','None'],['pillar-card-env','Environmental (Green)'],['pillar-card-soc','Social (Blue)'],['pillar-card-eco','Economic (Amber)']]},
      {k:'color',l:'Icon Color (initiative style only)',t:'select',options:[['','None'],['icon-green','Green'],['icon-amber','Amber'],['icon-blue','Blue'],['icon-teal','Teal']]}]}]},
  'steps':{label:'Numbered Steps',bg:'#f0fdf4',col:'#0f1a2e',desc:'Process or how-it-works numbered steps.',fields:[
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},
    {k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'items',l:'Steps',t:'items',schema:[{k:'number',l:'Step Number',t:'text'},{k:'title',l:'Title',t:'text'},{k:'desc',l:'Description',t:'textarea'}]}]},
  'stats':{label:'Statistics Band',bg:'#0f1a2e',col:'#fff',desc:'Dark band with large stat numbers.',fields:[
    {k:'items',l:'Stats',t:'items',schema:[{k:'value',l:'Value (e.g. 2,000+)',t:'text'},{k:'label',l:'Label',t:'text'}]}]},
  'profiles':{label:'People Profiles',bg:'#fdf2f8',col:'#0f1a2e',desc:'Director or team member cards with photos.',fields:[
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},
    {k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'style',l:'Card Style',t:'select',options:[['director','Director (horizontal)'],['team','Team (vertical)']]},
    {k:'items',l:'People',t:'items',schema:[{k:'name',l:'Name',t:'text'},{k:'role',l:'Role / Title',t:'text'},{k:'photo',l:'Photo',t:'image'},{k:'photo_position',l:'Photo Crop (e.g. center 25%)',t:'text'},{k:'quote',l:'Quote',t:'textarea'},{k:'phone',l:'Phone',t:'text'},{k:'email',l:'Email',t:'text'}]}]},
  'table':{label:'Info Table',bg:'#fffbeb',col:'#0f1a2e',desc:'Two-column key-value table.',fields:[
    {k:'heading',l:'Table Heading',t:'text'},{k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'rows',l:'Rows',t:'items',schema:[{k:'label',l:'Label',t:'text'},{k:'value',l:'Value',t:'text'}]}]},
  'logo-grid':{label:'Logo Grid',bg:'#f5f3ff',col:'#0f1a2e',desc:'Grid of logos (certifications or client brands).',fields:[
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},
    {k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'style',l:'Grid Style',t:'select',options:[['cert','Certification Logos'],['client','Client Logos (with country)']]},
    {k:'items',l:'Logos',t:'items',schema:[{k:'name',l:'Name',t:'text'},{k:'logo',l:'Logo Image',t:'image'},{k:'country',l:'Country (clients)',t:'text'}]}]},
  'cta':{label:'CTA Banner',bg:'#0E7E87',col:'#fff',desc:'Teal gradient call-to-action strip.',fields:[
    {k:'title',l:'Title',t:'text'},{k:'desc',l:'Description',t:'textarea'},
    {k:'btn1_text',l:'Button 1 Text',t:'text'},{k:'btn1_link',l:'Button 1 Link',t:'text'},
    {k:'btn2_text',l:'Button 2 Text (optional)',t:'text'},{k:'btn2_link',l:'Button 2 Link',t:'text'}]},
  'gallery':{label:'Gallery / CSR',bg:'#fef3c7',col:'#0f1a2e',desc:'Image cards with titles and descriptions.',fields:[
    {k:'eyebrow',l:'Eyebrow Label',t:'text'},{k:'heading',l:'Heading',t:'text'},
    {k:'bg',l:'Background',t:'select',options:[['','White'],['alt','Light Gray'],['dark','Dark']]},
    {k:'style',l:'Style',t:'select',options:[['csr','CSR Cards (image + title + text)'],['photo-grid','Photo Grid']]},
    {k:'columns',l:'Columns (photo-grid)',t:'select',options:[['2','2'],['3','3'],['4','4']]},
    {k:'items',l:'Items',t:'items',schema:[{k:'image',l:'Image',t:'image'},{k:'title',l:'Title',t:'text'},{k:'desc',l:'Description',t:'textarea'}]}]},
  'contact-form':{label:'Contact Form',bg:'#e8f4fd',col:'#0f1a2e',desc:'Contact info panel with EmailJS form.',fields:[
    {k:'emailjs_public_key',l:'EmailJS Public Key',t:'text'},{k:'emailjs_service_id',l:'EmailJS Service ID',t:'text'},{k:'emailjs_template_id',l:'EmailJS Template ID',t:'text'}]},
};

// ── Icon library (matches PHP _pal_icon()) ────────────────────────────
var ICON_NAMES = ['award','shield','shield-check','user-check','leaf','tree','sun','droplet','plant','refresh',
  'users','heart','trending','star','briefcase','check-circle','target','globe','package','truck','mail','phone',
  'settings','clock','lock','key','zap','layers','dollar','recycle','bar-chart','map-pin','user','flag',
  'clipboard-check','database','wind','cpu','anchor','user-plus','search','calendar'];

var ICON_PATHS = {
  'award':'<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>',
  'shield':'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
  'shield-check':'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
  'user-check':'<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
  'leaf':'<path d="M11 20A7 7 0 019.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
  'tree':'<path d="M12 3L4 16h16L12 3z"/><line x1="12" y1="16" x2="12" y2="22"/><line x1="9" y1="22" x2="15" y2="22"/>',
  'sun':'<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
  'droplet':'<path d="M12 22a7 7 0 007-7c0-2-1-3.9-3-5.5S12.5 5 12 2.5C11.5 5 10 7.4 8 9c-2 1.6-3 3.5-3 5a7 7 0 007 7z"/>',
  'plant':'<path d="M12 22V12"/><path d="M12 12C12 7 18 3 22 3C22 8 17 12 12 12"/><path d="M12 12C12 8 6 4 2 4C2 9 7 12 12 12"/>',
  'refresh':'<polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/><path d="M20.49 9A9 9 0 005.64 5.64L1 10"/><path d="M3.51 15a9 9 0 0014.85 3.36L23 14"/>',
  'users':'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
  'heart':'<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
  'trending':'<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
  'star':'<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
  'briefcase':'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>',
  'check-circle':'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
  'target':'<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
  'globe':'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>',
  'package':'<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
  'truck':'<rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
  'mail':'<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/>',
  'phone':'<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92v2z"/>',
  'settings':'<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
  'clock':'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
  'lock':'<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
  'key':'<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>',
  'zap':'<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
  'layers':'<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
  'dollar':'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
  'recycle':'<polyline points="7.6 14.6 2 12 7.6 9.4"/><path d="M22 12A10 10 0 007.6 2.6L2 5"/><polyline points="16.4 9.4 22 12 16.4 14.6"/><path d="M2 12a10 10 0 0014.4 9.4l5.6-2.4"/>',
  'bar-chart':'<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
  'map-pin':'<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
  'user':'<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
  'flag':'<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
  'clipboard-check':'<path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/>',
  'database':'<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
  'wind':'<path d="M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1114 16H2m15.73-8.27A2.5 2.5 0 1119.5 12H2"/>',
  'cpu':'<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/>',
  'anchor':'<circle cx="12" cy="5" r="3"/><line x1="12" y1="22" x2="12" y2="8"/><path d="M5 12H2a10 10 0 0020 0h-3"/>',
  'user-plus':'<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>',
  'search':'<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
  'calendar':'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
};

function iconSvg(name, size, stroke) {
  size = size||24; stroke = stroke||'currentColor';
  var p = ICON_PATHS[name]||ICON_PATHS['star'];
  return '<svg width="'+size+'" height="'+size+'" viewBox="0 0 24 24" fill="none" stroke="'+stroke+'" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">'+p+'</svg>';
}

function iUrl(f){return f?'../uploads/pages/'+f:'';}
function escH(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');}
function secTitle(s){return s.heading||s.title||s.badge||(TYPES[s.type]?TYPES[s.type].label:s.type)||'Section';}
function secSub(s){var p=[];if(s.layout)p.push('Image '+s.layout);if(s.style)p.push(s.style);var arr=s.items||s.rows||s.stats||[];if(arr.length)p.push(arr.length+' item'+(arr.length>1?'s':''));return p.join(' · ');}
function badgeStyle(type){var t=TYPES[type];return 'background:'+(t?t.bg:'#888')+';color:'+(t?t.col:'#fff');}

// ── Render section cards ─────────────────────────────────────────────
function renderCards(){
  var list=document.getElementById('sectionList');
  if(!list)return;
  if(!sections.length){
    list.innerHTML='<div style="padding:36px;text-align:center;color:var(--gray-400);background:#fff;border:1px dashed var(--border);border-radius:var(--radius-md)">No sections yet. Click "Add New Section" to get started.</div>';
    return;
  }
  list.innerHTML='';
  sections.forEach(function(s,i){
    var card=document.createElement('div');
    card.className='pb-sec-card';card.dataset.idx=i;
    var tl=TYPES[s.type]?TYPES[s.type].label:s.type;
    card.innerHTML=
      '<div class="pb-sec-header">'+
        '<span class="pb-sec-drag" title="Drag to reorder"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg></span>'+
        '<span class="pb-sec-type-badge" style="'+badgeStyle(s.type)+'">'+escH(tl)+'</span>'+
        '<div class="pb-sec-info"><div class="pb-sec-title">'+escH(secTitle(s))+'</div>'+(secSub(s)?'<div class="pb-sec-sub">'+escH(secSub(s))+'</div>':'')+'</div>'+
        '<div class="pb-sec-actions">'+
          '<button class="pb-sec-btn" title="Preview" onclick="togglePrev('+i+')"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>'+
          '<button class="pb-sec-btn" title="Edit" onclick="openEdit('+i+')"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>'+
          '<button class="pb-sec-btn del" title="Delete section" onclick="delSection('+i+')"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>'+
        '</div>'+
      '</div>'+
      '<div class="pb-sec-preview" id="prev-'+i+'">'+buildPreview(s)+'</div>';
    list.appendChild(card);
  });
  initSort();
}

function togglePrev(i){
  var p=document.getElementById('prev-'+i);if(!p)return;
  var open=p.style.display==='block';
  p.style.display=open?'none':'block';
  p.parentElement.classList.toggle('expanded',!open);
}

function buildPreview(s){
  var t=s.type;
  if(t==='hero')return '<div class="pb-preview-hero"><h4>'+escH(s.title||'Hero')+'</h4><p>'+escH(s.desc||'')+'</p></div>';
  if(t==='text')return '<div class="pb-preview-text"><h5>'+escH(s.heading||'')+'</h5><p>'+escH((s.subheading||s.body||'').substring(0,120))+'</p></div>';
  if(t==='text-image'){var lay=s.layout||'right';var img=s.image?'<div class="pb-preview-img"><img src="'+escH(iUrl(s.image))+'" alt=""></div>':'<div class="pb-preview-img"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';var txt='<div class="pb-preview-text"><h5>'+escH(s.heading||'')+'</h5><p>'+escH((s.body||'').substring(0,80))+'</p></div>';if(lay==='top'||lay==='bottom')return lay==='top'?img+txt:txt+img;return '<div class="pb-preview-2col">'+(lay==='left'?img+txt:txt+img)+'</div>';}
  if(t==='cards'||t==='steps'){var items=(s.items||[]).slice(0,3),cols=Math.min(parseInt(s.columns)||3,3);var h='<div class="pb-preview-cards" style="grid-template-columns:repeat('+cols+',1fr)">';items.forEach(function(it){h+='<div class="pb-preview-card"><h5>'+escH(it.title||it.number||'')+'</h5><p>'+escH((it.text||it.desc||'').substring(0,50))+'</p></div>';});return h+'</div>';}
  if(t==='stats'){var h='<div class="pb-preview-stats">';(s.items||[]).slice(0,4).forEach(function(it){h+='<div class="pb-preview-stat"><strong>'+escH(it.value||'')+'</strong><span>'+escH(it.label||'')+'</span></div>';});return h+'</div>';}
  if(t==='cta')return '<div class="pb-preview-cta"><h5>'+escH(s.title||'')+'</h5><p>'+escH(s.desc||'')+'</p></div>';
  if(t==='logo-grid'){var h='<div class="pb-preview-logos">';(s.items||[]).slice(0,6).forEach(function(it){h+='<div class="pb-preview-logo">'+(it.logo?'<img src="'+escH(iUrl(it.logo))+'" alt="'+escH(it.name||'')+'">':'<span style="font-size:.55rem;color:var(--gray-400)">'+escH(it.name||'')+'</span>')+'</div>';});return h+'</div>';}
  if(t==='gallery'){var h='<div class="pb-preview-gallery">';(s.items||[]).slice(0,4).forEach(function(it){h+='<div class="pb-preview-gallery-item">'+(it.image?'<img src="'+escH(iUrl(it.image))+'" alt="">':'')+'</div>';});return h+'</div>';}
  if(t==='profiles'){var h='<div class="pb-preview-2col">';(s.items||[]).slice(0,2).forEach(function(it){h+='<div class="pb-preview-card" style="display:flex;gap:8px;align-items:center">'+(it.photo?'<img src="'+escH(iUrl(it.photo))+'" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">':'<div style="width:34px;height:34px;border-radius:50%;background:var(--teal);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.9rem;flex-shrink:0">'+escH((it.name||'?')[0])+'</div>')+'<div><h5 style="font-size:.72rem">'+escH(it.name||'')+'</h5><p>'+escH(it.role||'')+'</p></div></div>';});return h+'</div>';}
  if(t==='table'){var h='<table style="width:100%;font-size:.72rem;border-collapse:collapse">';(s.rows||[]).slice(0,3).forEach(function(r){h+='<tr style="border-bottom:1px solid var(--border)"><td style="padding:3px 6px;font-weight:600;color:var(--navy);width:40%">'+escH(r.label||'')+'</td><td style="padding:3px 6px;color:var(--gray-600)">'+escH(r.value||'')+'</td></tr>';});return h+'</table>';}
  if(t==='contact-form')return '<div style="font-size:.8rem;color:var(--gray-500);display:flex;align-items:center;gap:8px">'+iconSvg('mail',18,'var(--teal)')+' Contact information + EmailJS form</div>';
  return '<div style="font-size:.72rem;color:var(--gray-400)">'+escH(t)+'</div>';
}

// ── Sortable ───────────────────────────────────────────────────────────
function initSort(){
  var list=document.getElementById('sectionList');if(!list)return;
  if(sortable)sortable.destroy();
  sortable=Sortable.create(list,{handle:'.pb-sec-drag',animation:150,ghostClass:'sortable-ghost',
    onEnd:function(e){var m=sections.splice(e.oldIndex,1)[0];sections.splice(e.newIndex,0,m);}});
}

// ── Edit panel ─────────────────────────────────────────────────────────
function openEdit(i){
  editingIdx=i;
  var s=sections[i],ti=TYPES[s.type];
  document.getElementById('editModalTitle').textContent='Edit: '+(ti?ti.label:s.type);
  document.getElementById('editModalBody').innerHTML=buildForm(s.type,s);
  document.getElementById('editOverlay').classList.add('open');
  bindImgUploads(document.getElementById('editModalBody'));
  bindItemSortables(document.getElementById('editModalBody'));
}
function closeEdit(){document.getElementById('editOverlay').classList.remove('open');editingIdx=-1;}
function applyEdit(){
  if(editingIdx<0)return;
  sections[editingIdx]=collectForm(document.getElementById('editModalBody'),sections[editingIdx].type);
  renderCards();closeEdit();
}

// ── Chooser ────────────────────────────────────────────────────────────
function openChooser(){
  var g=document.getElementById('chooserGrid');g.innerHTML='';
  Object.keys(TYPES).forEach(function(key){
    var t=TYPES[key];
    var c=document.createElement('div');c.className='pb-type-card';
    c.innerHTML='<div class="pt-icon" style="background:'+t.bg+'"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="'+t.col+'" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg></div><h5>'+escH(t.label)+'</h5><p>'+escH(t.desc)+'</p>';
    c.onclick=function(){addSection(key);};
    g.appendChild(c);
  });
  document.getElementById('chooserOverlay').classList.add('open');
}
function closeChooser(){document.getElementById('chooserOverlay').classList.remove('open');}
function addSection(type){sections.push({type:type});renderCards();closeChooser();openEdit(sections.length-1);}

// ── Delete section ──────────────────────────────────────────────────────
function delSection(i){
  if(!confirm('Remove this section? Click Save to make it permanent.'))return;
  sections.splice(i,1);renderCards();
}

// ── Icon picker ──────────────────────────────────────────────────────────
var iconPickerCallback=null;
function openIconPicker(currentVal,callback){
  iconPickerCallback=callback;
  var grid=document.getElementById('iconPickerGrid');grid.innerHTML='';
  ICON_NAMES.forEach(function(name){
    var btn=document.createElement('button');
    btn.type='button';
    btn.className='pb-icon-btn'+(name===currentVal?' selected':'');
    btn.title=name;
    btn.innerHTML=iconSvg(name,22,'currentColor')+'<span>'+escH(name)+'</span>';
    btn.onclick=function(){
      grid.querySelectorAll('.pb-icon-btn').forEach(function(b){b.classList.remove('selected');});
      btn.classList.add('selected');
      if(iconPickerCallback)iconPickerCallback(name);
      closeIconPicker();
    };
    grid.appendChild(btn);
  });
  document.getElementById('iconOverlay').classList.add('open');
}
function closeIconPicker(){document.getElementById('iconOverlay').classList.remove('open');iconPickerCallback=null;}

// ── Build form ─────────────────────────────────────────────────────────
function buildForm(type,data){
  var ti=TYPES[type];
  if(!ti)return '<p style="color:red">Unknown section type: '+escH(type)+'</p>';
  var h='<input type="hidden" name="__type__" value="'+escH(type)+'">';
  ti.fields.forEach(function(f){h+=buildField(f,data[f.k]);});
  return h;
}

function buildField(f,val){
  if(f.t==='items')return buildItemsField(f,val||[]);
  if(f.t==='checklist')return buildChecklistField(f,val||[]);
  var id='f_'+f.k;
  var h='<div class="pb-field">';
  if(f.t==='checkbox'){
    h+='<label style="display:flex;align-items:center;gap:8px;font-size:.875rem;text-transform:none;letter-spacing:0;font-weight:400;color:var(--gray-700)"><input type="checkbox" name="'+escH(f.k)+'"'+(val?' checked':'')+' style="width:auto"> '+escH(f.l)+'</label>';
  } else if(f.t==='image'){
    var prev=val?'<img src="'+escH(iUrl(val))+'" class="pb-img-preview" alt="">':'<div class="pb-img-placeholder"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
    h+='<label>'+escH(f.l)+'</label><div class="pb-img-wrap">'+prev+
      '<div class="pb-img-info"><div class="pb-img-name">'+escH(val||'No image selected')+'</div>'+
      '<button type="button" class="btn btn-sm btn-outline-secondary pb-img-change" data-field="'+escH(f.k)+'">Choose Image</button></div></div>'+
      '<input type="hidden" name="'+escH(f.k)+'" value="'+escH(val||'')+'">'+
      '<input type="file" class="pb-img-file" style="display:none" accept="image/*" data-field="'+escH(f.k)+'">';
  } else if(f.t==='icon'){
    var iconPreview=val?iconSvg(val,22,'var(--teal)'):'<svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--gray-300)" stroke-width="1.75"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>';
    h+='<label>'+escH(f.l)+'</label>'+
      '<div class="pb-icon-field">'+
        '<div class="pb-icon-preview" id="ipr_'+escH(f.k)+'" data-field="'+escH(f.k)+'">'+iconPreview+'</div>'+
        '<span class="pb-icon-name" id="inm_'+escH(f.k)+'">'+escH(val||'None selected')+'</span>'+
        '<input type="hidden" name="'+escH(f.k)+'" value="'+escH(val||'')+'">'+
        '<button type="button" class="btn btn-sm btn-outline-secondary pb-icon-btn-pick" data-field="'+escH(f.k)+'">Choose Icon</button>'+
      '</div>';
  } else {
    h+='<label for="'+id+'">'+escH(f.l)+'</label>';
    if(f.t==='select'){
      h+='<select id="'+id+'" name="'+escH(f.k)+'">';
      (f.options||[]).forEach(function(o){h+='<option value="'+escH(o[0])+'"'+(String(val||'')==String(o[0])?' selected':'')+'>'+escH(o[1])+'</option>';});
      h+='</select>';
    } else if(f.t==='textarea'){
      h+='<textarea id="'+id+'" name="'+escH(f.k)+'">'+escH(val||'')+'</textarea>';
    } else {
      h+='<input type="text" id="'+id+'" name="'+escH(f.k)+'" value="'+escH(val||'')+'">';
    }
  }
  return h+'</div>';
}

function normalizeItem(item,schema){
  if(typeof item!=='object'||item===null){var obj={};if(schema&&schema.length)obj[schema[0].k]=String(item);return obj;}
  return item;
}

function buildItemsField(f,items){
  var h='<div class="pb-section-divider">'+escH(f.l)+'</div>';
  h+='<div class="pb-items-list" id="items_'+f.k+'" data-key="'+escH(f.k)+'" data-schema=\''+escH(JSON.stringify(f.schema))+'\'>';
  items.forEach(function(it,i){h+=buildItemBlock(f.schema,normalizeItem(it,f.schema),i,f.k);});
  h+='</div><button type="button" class="pb-add-item" data-list="items_'+f.k+'" data-key="'+escH(f.k)+'">+ Add Item</button>';
  return h;
}

function buildItemBlock(schema,item,i,key){
  var lbl=item.title||item.name||item.label||item.value||item.image||('Item '+(i+1));
  var h='<div class="pb-item-block" data-idx="'+i+'">';
  h+='<div class="pb-item-header" onclick="togItem(this)"><span class="pb-item-drag"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg></span><span class="pb-item-num">'+escH(lbl)+'</span><button type="button" class="pb-item-del" onclick="event.stopPropagation();this.closest(\'.pb-item-block\').remove()"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>';
  h+='<div class="pb-item-body">';
  schema.forEach(function(sf){h+=buildField(sf,item[sf.k]||'');});
  return h+'</div></div>';
}

function buildChecklistField(f,items){
  var h='<div class="pb-field"><label>'+escH(f.l)+'</label><div class="pb-checklist-list" id="cl_'+f.k+'" data-key="'+escH(f.k)+'">';
  (items||[]).forEach(function(v){h+=clItem(v);});
  return h+'</div><button type="button" class="pb-add-item" onclick="addCl(\''+f.k+'\')">+ Add Item</button></div>';
}
function clItem(v){return '<div class="pb-checklist-item"><input type="text" value="'+escH(v||'')+'" placeholder="List item..."><button type="button" class="pb-checklist-del" onclick="this.closest(\'.pb-checklist-item\').remove()"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>';}
function addCl(key){var l=document.getElementById('cl_'+key);if(l){var d=document.createElement('div');d.innerHTML=clItem('');l.appendChild(d.firstChild);}}
function togItem(h){h.parentElement.classList.toggle('expanded');var b=h.nextElementSibling;if(b)b.style.display=h.parentElement.classList.contains('expanded')?'block':'none';}

// ── Add item button ─────────────────────────────────────────────────────
document.addEventListener('click',function(e){
  var btn=e.target.closest('.pb-add-item[data-list]');
  if(!btn)return;
  var list=document.getElementById(btn.dataset.list);if(!list)return;
  var sc=[];try{sc=JSON.parse(list.dataset.schema||'[]');}catch(ex){}
  var i=list.querySelectorAll('.pb-item-block').length;
  var tmp=document.createElement('div');tmp.innerHTML=buildItemBlock(sc,{},i,btn.dataset.key||'');
  var nb=tmp.firstChild;list.appendChild(nb);
  bindImgUploads(nb);
  nb.classList.add('expanded');
  var body=nb.querySelector('.pb-item-body');if(body)body.style.display='block';
});

// ── Icon pick button delegate ──────────────────────────────────────────
document.addEventListener('click',function(e){
  var btn=e.target.closest('.pb-icon-btn-pick');
  if(!btn)return;
  var fld=btn.dataset.field;
  var wrap=btn.closest('.pb-icon-field');
  var currentVal=wrap?wrap.querySelector('input[type=hidden][name="'+fld+'"]')?.value:'';
  openIconPicker(currentVal,function(iconName){
    if(!wrap)return;
    var hidden=wrap.querySelector('input[type=hidden][name="'+fld+'"]');
    if(hidden)hidden.value=iconName;
    var prev=wrap.querySelector('.pb-icon-preview');
    if(prev)prev.innerHTML=iconSvg(iconName,22,'var(--teal)');
    var nm=wrap.querySelector('.pb-icon-name');
    if(nm)nm.textContent=iconName;
  });
});

// ── Item sortable ────────────────────────────────────────────────────────
function bindItemSortables(c){
  c.querySelectorAll('.pb-items-list').forEach(function(l){Sortable.create(l,{handle:'.pb-item-drag',animation:120});});
}

// ── Image upload ─────────────────────────────────────────────────────────
function bindImgUploads(c){
  c.querySelectorAll('.pb-img-change').forEach(function(btn){
    btn.addEventListener('click',function(){
      var fld=btn.dataset.field;
      var scope=btn.closest('.pb-field,.pb-item-body');
      var fi=scope?scope.querySelector('.pb-img-file[data-field="'+fld+'"]'):c.querySelector('.pb-img-file[data-field="'+fld+'"]');
      if(fi)fi.click();
    });
  });
  c.querySelectorAll('.pb-img-file').forEach(function(fi){
    fi.addEventListener('change',function(){
      if(!fi.files||!fi.files[0])return;
      var fld=fi.dataset.field;
      var fd=new FormData();fd.append('file',fi.files[0]);fd.append('dest','pages');fd.append('csrf_token',CSRF);
      var wrap=fi.closest('.pb-field,.pb-item-body').querySelector('.pb-img-wrap');
      if(wrap){var nm=wrap.querySelector('.pb-img-name');if(nm)nm.textContent='Uploading...';}
      fetch('api/upload-image.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(res){
        if(res.ok){
          var scope=fi.closest('.pb-field,.pb-item-body');
          var h=scope.querySelector('input[type=hidden][name="'+fld+'"]');if(h)h.value=res.filename;
          if(wrap){
            var img=wrap.querySelector('img.pb-img-preview'),ph=wrap.querySelector('.pb-img-placeholder');
            if(!img){img=document.createElement('img');img.className='pb-img-preview';img.alt='';if(ph)ph.replaceWith(img);else wrap.prepend(img);}
            img.src=res.url;
            var nm=wrap.querySelector('.pb-img-name');if(nm)nm.textContent=res.filename;
          }
        }else{alert('Upload failed: '+(res.message||'Error'));if(wrap){var nm=wrap.querySelector('.pb-img-name');if(nm)nm.textContent=fld;}}
      }).catch(function(){alert('Upload error.');});
    });
  });
}

// ── Collect form ──────────────────────────────────────────────────────────
function collectForm(c,type){
  var result={type:type};var ti=TYPES[type];if(!ti)return result;
  ti.fields.forEach(function(f){
    if(f.t==='items'){result[f.k]=collectItems(c,f.k,f.schema);}
    else if(f.t==='checklist'){result[f.k]=collectCl(c,f.k);}
    else if(f.t==='checkbox'){var el=c.querySelector('[name="'+f.k+'"]');result[f.k]=el?el.checked:false;}
    else{var el=c.querySelector('[name="'+f.k+'"]');if(el)result[f.k]=el.value;}
  });
  return result;
}
function collectItems(c,key,schema){
  var list=c.querySelector('#items_'+key);if(!list)return[];
  return Array.from(list.querySelectorAll(':scope > .pb-item-block')).map(function(b){
    var item={};
    schema.forEach(function(sf){
      if(sf.t==='image'||sf.t==='icon'){var h=b.querySelector('input[type=hidden][name="'+sf.k+'"]');if(h)item[sf.k]=h.value;}
      else if(sf.t==='checkbox'){var el=b.querySelector('[name="'+sf.k+'"]');item[sf.k]=el?el.checked:false;}
      else{var el=b.querySelector('[name="'+sf.k+'"]');if(el)item[sf.k]=el.value;}
    });
    return item;
  });
}
function collectCl(c,key){
  var list=c.querySelector('#cl_'+key);if(!list)return[];
  return Array.from(list.querySelectorAll('input[type=text]')).map(function(i){return i.value;}).filter(Boolean);
}

// ── Save sections ──────────────────────────────────────────────────────────
function saveAll(){
  if(!SLUG)return;
  var btn=document.getElementById('saveBtn');if(!btn)return;
  btn.disabled=true;btn.textContent='Saving...';
  fetch('api/save-sections.php',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({slug:SLUG,title:document.getElementById('metaTitle').value,meta_desc:document.getElementById('metaDesc').value,sections:sections})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok&&typeof showToast==='function')showToast('Page saved.','success');
    else if(!res.ok)alert('Error: '+(res.message||'Unknown'));
    btn.disabled=false;btn.textContent='Save';
  }).catch(function(){alert('Network error.');btn.disabled=false;btn.textContent='Save';});
}

// ── Save nav placement ─────────────────────────────────────────────────────
function saveNav(){
  if(!SLUG)return;
  var showInNav=document.getElementById('navShowCheck')?.checked??true;
  fetch('api/save-nav-visibility.php',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({slug:SLUG,show_in_nav:showInNav})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok&&typeof showToast==='function')showToast('Navigation settings saved.','success');
    else if(!res.ok)alert(res.error||'Failed to save nav.');
  }).catch(function(){alert('Network error.');});
}

// ── Suspend / Activate ─────────────────────────────────────────────────────
function doSuspend(){
  if(!confirm('Suspend this page? It will return a 404 to visitors until reactivated.'))return;
  fetch('api/suspend-page.php',{
    method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({slug:SLUG,action:'suspend'})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok)location.reload();else alert(res.error||'Failed.');
  });
}
function doActivate(){
  fetch('api/suspend-page.php',{
    method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({slug:SLUG,action:'activate'})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok)location.reload();else alert(res.error||'Failed.');
  });
}

// ── Delete ─────────────────────────────────────────────────────────────────
function confirmDelete(){
  fetch('api/delete-page.php',{
    method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({slug:SLUG})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok)location.href='page-builder.php';else alert(res.error||'Failed.');
  });
}

// ── Rename ─────────────────────────────────────────────────────────────────
function doRename(){
  var lbl=document.getElementById('renameLabel').value.trim();
  var err=document.getElementById('renameError');
  if(!lbl){err.textContent='Label cannot be empty.';err.style.display='block';return;}
  err.style.display='none';
  document.getElementById('submitRenameBtn').disabled=true;
  fetch('api/rename-page.php',{
    method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({slug:SLUG,label:lbl})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok)location.reload();else{err.textContent=res.error||'Failed.';err.style.display='block';document.getElementById('submitRenameBtn').disabled=false;}
  });
}

// ── Create page ────────────────────────────────────────────────────────────
function slugify(s){return s.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');}

document.getElementById('cpLabel')?.addEventListener('input',function(){
  var sl=slugify(this.value);
  document.getElementById('cpSlug').placeholder=sl;
  document.getElementById('cpSlugPreview').textContent=sl?sl+'.php':'...';
});
document.getElementById('cpShowNav')?.addEventListener('change',function(){
  document.getElementById('cpParentRow').style.display=this.checked?'block':'none';
});

function doCreate(){
  var label=document.getElementById('cpLabel').value.trim();
  var slug=document.getElementById('cpSlug').value.trim()||slugify(label);
  var navLabel=document.getElementById('cpNavLabel').value.trim()||label;
  var showNav=document.getElementById('cpShowNav').checked;
  var parentSlug=document.getElementById('cpParent')?.value||'';
  var err=document.getElementById('cpError');
  if(!label){err.textContent='Page name is required.';err.style.display='block';return;}
  err.style.display='none';
  document.getElementById('submitCreateBtn').disabled=true;
  fetch('api/create-page.php',{
    method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
    body:JSON.stringify({label:label,slug:slug,nav_label:navLabel,show_in_nav:showNav,parent_slug:parentSlug})
  }).then(function(r){return r.json();}).then(function(res){
    if(res.ok)location.href='page-builder.php?slug='+encodeURIComponent(res.slug);
    else{err.textContent=res.error||'Failed.';err.style.display='block';document.getElementById('submitCreateBtn').disabled=false;}
  }).catch(function(){err.textContent='Network error.';err.style.display='block';document.getElementById('submitCreateBtn').disabled=false;});
}

// ── Event wiring ───────────────────────────────────────────────────────────
function openCreateModal(){document.getElementById('createOverlay').classList.add('open');document.getElementById('cpLabel').focus();}
function closeCreateModal(){document.getElementById('createOverlay').classList.remove('open');}

var saveBtn=document.getElementById('saveBtn');
if(saveBtn)saveBtn.addEventListener('click',saveAll);
var addSectionBtn=document.getElementById('addSectionBtn');
if(addSectionBtn)addSectionBtn.addEventListener('click',openChooser);
var saveNavBtn=document.getElementById('saveNavBtn');
if(saveNavBtn)saveNavBtn.addEventListener('click',saveNav);
var suspendBtn=document.getElementById('suspendBtn');
if(suspendBtn)suspendBtn.addEventListener('click',doSuspend);
var activateBtn=document.getElementById('activateBtn');
if(activateBtn)activateBtn.addEventListener('click',doActivate);
var deleteBtn=document.getElementById('deleteBtn');
if(deleteBtn)deleteBtn.addEventListener('click',function(){document.getElementById('deleteOverlay').classList.add('open');});
var renameBtn=document.getElementById('renameBtn');
if(renameBtn)renameBtn.addEventListener('click',function(){
  document.getElementById('renameLabel').value=<?php echo json_encode($activeCustom ? $activeCustom['label'] : ''); ?>;
  document.getElementById('renameOverlay').classList.add('open');
  document.getElementById('renameLabel').focus();
});

document.getElementById('closeEditBtn').addEventListener('click',closeEdit);
document.getElementById('cancelEditBtn').addEventListener('click',closeEdit);
document.getElementById('applyEditBtn').addEventListener('click',applyEdit);
document.getElementById('closeChooserBtn').addEventListener('click',closeChooser);
document.getElementById('closeIconBtn').addEventListener('click',closeIconPicker);
document.getElementById('cancelCreateBtn').addEventListener('click',closeCreateModal);
document.getElementById('submitCreateBtn').addEventListener('click',doCreate);
document.getElementById('cancelRenameBtn').addEventListener('click',function(){document.getElementById('renameOverlay').classList.remove('open');});
document.getElementById('submitRenameBtn').addEventListener('click',doRename);
document.getElementById('cancelDeleteBtn').addEventListener('click',function(){document.getElementById('deleteOverlay').classList.remove('open');});
document.getElementById('confirmDeleteBtn').addEventListener('click',confirmDelete);

// Overlay backdrop clicks
document.getElementById('editOverlay').addEventListener('click',function(e){if(e.target===this)closeEdit();});
document.getElementById('chooserOverlay').addEventListener('click',function(e){if(e.target===this)closeChooser();});
document.getElementById('iconOverlay').addEventListener('click',function(e){if(e.target===this)closeIconPicker();});
document.getElementById('createOverlay').addEventListener('click',function(e){if(e.target===this)closeCreateModal();});

// Create page buttons
document.querySelectorAll('#createPageBtn,#createPageBtn2').forEach(function(b){if(b)b.addEventListener('click',openCreateModal);});

// Init
renderCards();

// Expose handlers to global scope for inline onclick attributes
window.openEdit   = openEdit;
window.togglePrev = togglePrev;
window.delSection = delSection;
window.togItem    = togItem;
window.addCl      = addCl;

})();
</script>
</body>
</html>
