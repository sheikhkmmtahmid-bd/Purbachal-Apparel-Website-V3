<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

$pageFile        = 'about.php';
$d               = jsonRead(DATA_DIR . 'pages/about.json');
$pageTitle       = $d['title']    ?? 'About Us | Purbachal Apparel Limited';
$pageDescription = $d['meta_desc'] ?? '';

function pal_mission_icon(string $name): string {
    $icons = [
        'compass' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
        'eye'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
        'star'    => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    ];
    return $icons[$name] ?? $icons['compass'];
}

function pal_culture_icon(string $name): string {
    $icons = [
        'map'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>',
        'users'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
        'values'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"/><line x1="12" y1="22" x2="12" y2="8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/></svg>',
        'trending' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
    ];
    return $icons[$name] ?? $icons['map'];
}

require_once __DIR__ . '/includes/site-header.php';
?>
<main>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="page-hero-content">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="index.php">Home</a>
        <span>&rsaquo;</span>
        <span>About Us</span>
      </nav>
      <h1><?php echo e($d['hero_title'] ?? ''); ?></h1>
      <p><?php echo e($d['hero_desc'] ?? ''); ?></p>
    </div>
  </div>
</section>

<!-- COMPANY OVERVIEW -->
<section class="section">
  <div class="container">
    <div class="company-overview-grid">
      <div class="reveal-left">
        <span class="eyebrow"><?php echo e($d['overview_left_eyebrow'] ?? ''); ?></span>
        <h2 class="section-title"><?php echo e($d['overview_left_heading'] ?? ''); ?></h2>
        <table class="info-table">
          <?php foreach ($d['overview_table_rows'] ?? [] as $row): ?>
          <tr><td><?php echo e($row['label'] ?? ''); ?></td><td><?php echo e($row['value'] ?? ''); ?></td></tr>
          <?php endforeach; ?>
        </table>
      </div>
      <div class="reveal-right">
        <span class="eyebrow"><?php echo e($d['overview_right_eyebrow'] ?? ''); ?></span>
        <h2 class="section-title"><?php echo e($d['overview_right_heading'] ?? ''); ?></h2>
        <?php
        $rightParas = array_values(array_filter(array_map('trim', preg_split('/\n{2,}/', $d['overview_right_body'] ?? ''))));
        $lastIdx    = count($rightParas) - 1;
        foreach ($rightParas as $ri => $rp): ?>
        <p<?php echo $ri < $lastIdx ? ' style="margin-bottom:16px"' : ''; ?>><?php echo e($rp); ?></p>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- BOARD OF DIRECTORS -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['directors_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['directors_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['directors_desc'] ?? ''); ?></p>
    </div>
    <div class="director-quote-grid">
      <?php $delays = ['', ' delay-1']; $di = 0;
      foreach ($d['directors_items'] ?? [] as $dir):
        $telLink = 'tel:' . preg_replace('/[^+\d]/', '', $dir['phone'] ?? '');
      ?>
      <div class="director-quote-card reveal<?php echo $delays[$di % 2]; ?>">
        <div class="director-photo">
          <img src="<?php echo UPLOAD_URL . 'pages/' . e($dir['photo'] ?? ''); ?>" alt="<?php echo e($dir['name'] ?? ''); ?>, <?php echo e($dir['role'] ?? ''); ?>" loading="lazy"<?php echo ($dir['photo_position'] ?? '') ? ' style="object-position:' . e($dir['photo_position']) . '"' : ''; ?>>
        </div>
        <div class="director-body">
          <h3 class="director-name"><?php echo e($dir['name'] ?? ''); ?></h3>
          <p class="director-title"><?php echo e($dir['role'] ?? ''); ?></p>
          <div class="director-divider"></div>
          <p class="director-quote">"<?php echo e($dir['quote'] ?? ''); ?>"</p>
          <div class="director-contact" style="margin-top:20px;display:flex;flex-direction:column;gap:6px;">
            <?php if (!empty($dir['phone'])): ?>
            <a href="<?php echo e($telLink); ?>" style="display:flex;align-items:center;gap:8px;font-size:.875rem;color:var(--teal);font-weight:500;"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><?php echo e($dir['phone']); ?></a>
            <?php endif; ?>
            <?php if (!empty($dir['email'])): ?>
            <a href="mailto:<?php echo e($dir['email']); ?>" style="display:flex;align-items:center;gap:8px;font-size:.875rem;color:var(--teal);font-weight:500;"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><?php echo e($dir['email']); ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php $di++; endforeach; ?>
    </div>
  </div>
</section>

<!-- MISSION / VISION / MOTIVE -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['mission_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['mission_heading'] ?? ''); ?></h2>
    </div>
    <div class="mission-grid">
      <?php $delays = ['', ' delay-1', ' delay-2']; $mi = 0;
      foreach ($d['mission_items'] ?? [] as $mc): ?>
      <div class="mission-card reveal<?php echo $delays[$mi % 3]; ?>">
        <div class="mission-icon"><?php echo pal_mission_icon($mc['icon'] ?? 'compass'); ?></div>
        <h4><?php echo e($mc['title'] ?? ''); ?></h4>
        <p><?php echo e($mc['text'] ?? ''); ?></p>
      </div>
      <?php $mi++; endforeach; ?>
    </div>
  </div>
</section>

<!-- WORKER WELFARE & SUSTAINABILITY -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['welfare_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['welfare_heading'] ?? ''); ?></h2>
      <p class="section-desc reveal delay-2"><?php echo e($d['welfare_desc'] ?? ''); ?></p>
    </div>
    <div class="welfare-stats">
      <?php $delays = ['', ' delay-1', ' delay-2']; $wi = 0;
      foreach ($d['welfare_items'] ?? [] as $ws): ?>
      <div class="welfare-stat reveal<?php echo $delays[$wi % 3]; ?>">
        <span class="welfare-stat-val"><?php echo e($ws['value'] ?? ''); ?></span>
        <span class="welfare-stat-lbl"><?php echo e($ws['label'] ?? ''); ?></span>
      </div>
      <?php $wi++; endforeach; ?>
    </div>
  </div>
</section>

<!-- CULTURE CARDS -->
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['culture_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['culture_heading'] ?? ''); ?></h2>
    </div>
    <div class="culture-grid">
      <?php $delays = ['', ' delay-1', ' delay-2', ' delay-3']; $ci = 0;
      foreach ($d['culture_items'] ?? [] as $cc): ?>
      <div class="culture-card reveal<?php echo $delays[$ci % 4]; ?>">
        <div class="culture-icon"><?php echo pal_culture_icon($cc['icon'] ?? 'map'); ?></div>
        <h4><?php echo e($cc['title'] ?? ''); ?></h4>
        <p><?php echo e($cc['text'] ?? ''); ?></p>
      </div>
      <?php $ci++; endforeach; ?>
    </div>
  </div>
</section>

<!-- PERFORMANCE PROGRESS BARS -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header center">
      <span class="eyebrow reveal"><?php echo e($d['perf_eyebrow'] ?? ''); ?></span>
      <h2 class="section-title reveal delay-1"><?php echo e($d['perf_heading'] ?? ''); ?></h2>
    </div>
    <div style="max-width:700px;margin:0 auto">
      <?php $delays = ['', ' delay-1', ' delay-2']; $pi = 0;
      foreach ($d['perf_items'] ?? [] as $pb): ?>
      <div class="progress-item reveal<?php echo $delays[$pi % 3]; ?>">
        <div class="progress-header"><span><?php echo e($pb['label'] ?? ''); ?></span><em><?php echo e($pb['percent'] ?? ''); ?>%</em></div>
        <div class="progress-bar-wrap"><div class="progress-bar" data-width="<?php echo e($pb['percent'] ?? '0'); ?>"></div></div>
      </div>
      <?php $pi++; endforeach; ?>
    </div>
  </div>
</section>

</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
