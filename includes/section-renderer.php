<?php
// Section renderer — renders page sections from JSON data.
// Usage: pal_render_sections($page['sections'] ?? []);
if (!defined('_PAL_CMS_')) exit;

function pal_render_sections(array $sections): void {
    foreach ($sections as $s) {
        if (!empty($s['type'])) pal_render_section($s);
    }
}

function pal_render_section(array $s): void {
    switch ($s['type']) {
        case 'hero':         _pal_sec_hero($s);         break;
        case 'text':         _pal_sec_text($s);          break;
        case 'text-image':   _pal_sec_text_image($s);    break;
        case 'cards':        _pal_sec_cards($s);         break;
        case 'steps':        _pal_sec_steps($s);         break;
        case 'stats':        _pal_sec_stats($s);         break;
        case 'profiles':     _pal_sec_profiles($s);      break;
        case 'table':        _pal_sec_table($s);         break;
        case 'logo-grid':    _pal_sec_logo_grid($s);     break;
        case 'cta':          _pal_sec_cta($s);           break;
        case 'gallery':      _pal_sec_gallery($s);       break;
        case 'contact-form': _pal_sec_contact_form($s);  break;
        case 'home-hero':    _pal_sec_home_hero($s);     break;
    }
}

// ── Icon library ───────────────────────────────────────────────────────────

function _pal_icon(string $name, int $size = 24, string $stroke = 'currentColor', float $sw = 1.75): string {
    static $icons = [
        'award'           => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>',
        'shield'          => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'shield-check'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'user-check'      => '<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
        'leaf'            => '<path d="M11 20A7 7 0 019.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
        'tree'            => '<path d="M12 3L4 16h16L12 3z"/><line x1="12" y1="16" x2="12" y2="22"/><line x1="9" y1="22" x2="15" y2="22"/>',
        'sun'             => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
        'droplet'         => '<path d="M12 22a7 7 0 007-7c0-2-1-3.9-3-5.5S12.5 5 12 2.5C11.5 5 10 7.4 8 9c-2 1.6-3 3.5-3 5a7 7 0 007 7z"/>',
        'plant'           => '<path d="M12 22V12"/><path d="M12 12C12 7 18 3 22 3C22 8 17 12 12 12"/><path d="M12 12C12 8 6 4 2 4C2 9 7 12 12 12"/>',
        'refresh'         => '<polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/><path d="M20.49 9A9 9 0 005.64 5.64L1 10"/><path d="M3.51 15a9 9 0 0014.85 3.36L23 14"/>',
        'users'           => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
        'heart'           => '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
        'trending'        => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
        'star'            => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'briefcase'       => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>',
        'check-circle'    => '<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'target'          => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'globe'           => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>',
        'package'         => '<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'truck'           => '<rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'mail'            => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/>',
        'phone'           => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92v2z"/>',
        'settings'        => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
        'clock'           => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'lock'            => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
        'key'             => '<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>',
        'zap'             => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
        'layers'          => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
        'dollar'          => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
        'recycle'         => '<polyline points="7.6 14.6 2 12 7.6 9.4"/><path d="M22 12A10 10 0 007.6 2.6L2 5"/><polyline points="16.4 9.4 22 12 16.4 14.6"/><path d="M2 12a10 10 0 0014.4 9.4l5.6-2.4"/>',
        'bar-chart'       => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
        'map-pin'         => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
        'user'            => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'flag'            => '<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
        'clipboard-check' => '<path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/>',
        'database'        => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
        'wind'            => '<path d="M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1114 16H2m15.73-8.27A2.5 2.5 0 1119.5 12H2"/>',
        'cpu'             => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/>',
        'anchor'          => '<circle cx="12" cy="5" r="3"/><line x1="12" y1="22" x2="12" y2="8"/><path d="M5 12H2a10 10 0 0020 0h-3"/>',
        'user-plus'       => '<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>',
        'search'          => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'calendar'        => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
    ];
    $paths = $icons[$name] ?? $icons['star'];
    return sprintf(
        '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="%s" stroke-width="%.2f" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
        $size, $size, htmlspecialchars($stroke, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $sw, $paths
    );
}

// ── Utilities ──────────────────────────────────────────────────────────────

function _pal_bg_class(string $bg): string {
    if ($bg === 'alt')  return 'section section-alt';
    if ($bg === 'dark') return 'section section-dark';
    return 'section';
}

function _pal_img(string $file): string {
    return UPLOAD_URL . 'pages/' . $file;
}

function _pal_section_header(array $s, bool $center = true): string {
    $html = '<div class="section-header' . ($center ? ' center' : '') . '">';
    if (!empty($s['eyebrow']))    $html .= '<span class="eyebrow reveal">'         . e($s['eyebrow']) . '</span>';
    if (!empty($s['heading']))    $html .= '<h2 class="section-title reveal delay-1">' . e($s['heading']) . '</h2>';
    if (!empty($s['subheading'])) $html .= '<p class="section-desc reveal delay-2">'  . e($s['subheading']) . '</p>';
    return $html . '</div>';
}

// ── Section: Hero (inner pages) ────────────────────────────────────────────

function _pal_sec_hero(array $s): void {
    $style = '';
    if (!empty($s['bg_image'])) {
        $style = ' style="background-image:linear-gradient(135deg,rgba(15,26,46,.87),rgba(10,48,66,.82)),url(' . e(_pal_img($s['bg_image'])) . ');background-size:cover;background-position:center"';
    }
    echo '<section class="page-hero"' . $style . '>';
    echo '<div class="container"><div class="page-hero-content">';
    echo '<h1>' . e($s['title'] ?? '') . '</h1>';
    if (!empty($s['desc'])) echo '<p>' . e($s['desc']) . '</p>';
    echo '</div></div></section>';
}

// ── Section: Home Hero ─────────────────────────────────────────────────────

function _pal_sec_home_hero(array $s): void {
    $badge   = e($s['badge']    ?? '');
    $title   = $s['title']      ?? 'Ethical.<br><span>Efficient.</span><br>Exceptional.';
    $desc    = e($s['desc']     ?? '');
    $cta1t   = e($s['cta1_text']   ?? 'Contact Us');
    $cta1l   = e($s['cta1_link']   ?? 'contact.php');
    $cta2t   = e($s['cta2_text']   ?? 'View Our Products');
    $cta2l   = e($s['cta2_link']   ?? 'products.php');
    $card1t  = e($s['card1_title'] ?? '2,000+ Skilled Workers');
    $card1d  = e($s['card1_desc']  ?? '');
    $card2t  = e($s['card2_title'] ?? 'Global Export Reach');
    $card2d  = e($s['card2_desc']  ?? '');
    echo '<section class="hero" id="hero"><div class="container"><div class="hero-grid">';
    echo '<div class="hero-left">';
    if ($badge) echo '<div class="hero-badge reveal"><span class="hero-badge-dot"></span>' . $badge . '</div>';
    echo '<h1 class="hero-title reveal delay-1">' . $title . '</h1>';
    echo '<p class="hero-desc reveal delay-2">' . $desc . '</p>';
    echo '<div class="hero-actions reveal delay-3">';
    echo '<a href="' . $cta1l . '" class="btn btn-primary btn-lg">' . $cta1t . '</a>';
    echo '<a href="' . $cta2l . '" class="btn btn-outline-white btn-lg">' . $cta2t . '</a>';
    echo '</div>';
    echo '<div class="hero-stats">';
    $stats = $s['stats'] ?? [
        ['value'=>'750K','label'=>'Monthly Capacity'],
        ['value'=>'32M','label'=>'Annual Turnover USD'],
        ['value'=>'2,000+','label'=>'Skilled Workers'],
        ['value'=>'100%','label'=>'Export Oriented'],
    ];
    foreach ($stats as $st) {
        echo '<div class="hero-stat"><span class="hero-stat-value">' . e($st['value']) . '</span><span class="hero-stat-label">' . e($st['label']) . '</span></div>';
    }
    echo '</div></div>';
    echo '<div class="hero-right">';
    echo '<div class="hero-card hero-card-teal float-anim"><div class="hero-card-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--teal-light)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div><div class="hero-card-body"><h4>' . $card1t . '</h4><p>' . $card1d . '</p></div></div>';
    echo '<div class="hero-card hero-card-magenta float-anim" style="animation-delay:.8s"><div class="hero-card-icon hero-card-icon-magenta"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D4197A" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg></div><div class="hero-card-body"><h4>' . $card2t . '</h4><p>' . $card2d . '</p></div></div>';
    echo '</div></div></div></section>';
}

// ── Section: Text block ────────────────────────────────────────────────────

function _pal_sec_text(array $s): void {
    $cls     = _pal_bg_class($s['bg'] ?? '');
    $centered = !empty($s['centered']);
    echo '<section class="' . $cls . '"><div class="container">';
    echo _pal_section_header($s, $centered);
    if (!empty($s['body'])) {
        $paras = is_array($s['body']) ? $s['body'] : array_filter(explode("\n\n", $s['body']));
        foreach ($paras as $p) echo '<p class="reveal" style="font-size:1.0625rem;line-height:1.75;margin-bottom:16px">' . e($p) . '</p>';
    }
    echo '</div></section>';
}

// ── Section: Text + Image ──────────────────────────────────────────────────

function _pal_sec_text_image(array $s): void {
    $cls    = _pal_bg_class($s['bg'] ?? '');
    $layout = $s['layout'] ?? 'right'; // right = image on right, left = image on left, top/bottom
    $isTopBottom = in_array($layout, ['top', 'bottom'], true);
    $img    = $s['image'] ?? '';

    echo '<section class="' . $cls . '"><div class="container">';

    if ($isTopBottom) {
        // Vertical layout
        if ($layout === 'top' && $img) {
            echo '<div class="mb-4" style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-lg)">';
            echo '<img src="' . e(_pal_img($img)) . '" alt="' . e($s['image_alt'] ?? '') . '" loading="lazy" style="width:100%;max-height:420px;object-fit:cover">';
            echo '</div>';
        }
        echo '<div class="section-header ' . (!empty($s['centered']) ? 'center' : '') . '">';
        if (!empty($s['eyebrow'])) echo '<span class="eyebrow">' . e($s['eyebrow']) . '</span>';
        if (!empty($s['heading'])) echo '<h2 class="section-title">' . e($s['heading']) . '</h2>';
        if (!empty($s['subheading'])) echo '<p class="section-desc">' . e($s['subheading']) . '</p>';
        echo '</div>';
        if (!empty($s['body'])) {
            echo '<p style="font-size:.9375rem;line-height:1.75;color:var(--gray-700);margin-bottom:24px">' . e($s['body']) . '</p>';
        }
        if ($layout === 'bottom' && $img) {
            echo '<div class="mt-4" style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-lg)">';
            echo '<img src="' . e(_pal_img($img)) . '" alt="' . e($s['image_alt'] ?? '') . '" loading="lazy" style="width:100%;max-height:420px;object-fit:cover">';
            echo '</div>';
        }
    } else {
        // Side-by-side layout
        echo '<div class="about-grid">';
        $textHtml = '<div>';
        if (!empty($s['eyebrow']))    $textHtml .= '<span class="eyebrow reveal">' . e($s['eyebrow']) . '</span>';
        if (!empty($s['heading']))    $textHtml .= '<h2 class="section-title reveal delay-1">' . e($s['heading']) . '</h2>';
        if (!empty($s['subheading'])) $textHtml .= '<p class="section-desc mb-4 reveal delay-2">' . e($s['subheading']) . '</p>';
        if (!empty($s['body']))       $textHtml .= '<p class="reveal delay-2" style="font-size:.9375rem;line-height:1.75;color:var(--gray-700);margin-bottom:24px">' . e($s['body']) . '</p>';
        if (!empty($s['checklist'])) {
            $textHtml .= '<ul class="about-list">';
            foreach ((array)$s['checklist'] as $item) {
                $textHtml .= '<li class="about-list-item reveal delay-3"><span class="about-list-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span><span>' . e($item) . '</span></li>';
            }
            $textHtml .= '</ul>';
        }
        if (!empty($s['cta_text'])) {
            $textHtml .= '<a href="' . e($s['cta_link'] ?? '#') . '" class="btn btn-primary">' . e($s['cta_text']) . '</a>';
        }
        $textHtml .= '</div>';
        $rev = $layout === 'left' ? 'reveal-left' : 'reveal-right';
        $imgHtml = '<div class="about-img-wrap">';
        if ($img) {
            $imgHtml .= '<div class="about-img-main ' . $rev . '"><img src="' . e(_pal_img($img)) . '" alt="' . e($s['image_alt'] ?? '') . '" loading="lazy" style="width:100%;height:420px;object-fit:cover"></div>';
        } else {
            $imgHtml .= '<div style="background:var(--gray-100);height:420px;border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;color:var(--gray-300);font-size:1.1rem">No image</div>';
        }
        $imgHtml .= '</div>';
        if ($layout === 'left') echo $imgHtml . $textHtml;
        else                    echo $textHtml . $imgHtml;
        echo '</div>';
    }
    echo '</div></section>';
}

// ── Section: Cards grid ────────────────────────────────────────────────────

function _pal_sec_cards(array $s): void {
    $cls    = _pal_bg_class($s['bg'] ?? '');
    $style  = $s['style'] ?? 'default';
    $cols   = max(2, min(4, (int)($s['columns'] ?? 3)));
    $items  = $s['items'] ?? [];
    $gridCls = $cols === 2 ? 'grid-2' : ($cols === 4 ? 'grid-4' : 'services-grid');
    if ($style === 'process') $gridCls = 'process-grid';
    if ($style === 'pillars') $gridCls = 'pillars-grid';
    if ($style === 'initiative') $gridCls = 'initiative-grid';

    echo '<section class="' . $cls . '"><div class="container">';
    if (!empty($s['heading']) || !empty($s['eyebrow'])) {
        echo _pal_section_header($s, !isset($s['align_left']));
    }
    echo '<div class="' . $gridCls . '">';

    $pillarMods = ['pillar-card-env', 'pillar-card-soc', 'pillar-card-eco'];
    $iconColors = ['icon-green', 'icon-amber', 'icon-blue', 'icon-teal', 'icon-green', 'icon-amber'];
    $svcColors  = ['#0E7E87', '#D4197A', '#F5A800', '#3A74C0', '#2DAD5F'];

    foreach ($items as $i => $item) {
        $d = ' delay-' . min($i + 1, 5);
        $title = e($item['title'] ?? '');
        $text  = e($item['text']  ?? $item['desc'] ?? '');

        if ($style === 'process') {
            echo '<div class="process-card reveal' . $d . '">';
            if (!empty($item['number'])) echo '<div class="process-step-num">' . e($item['number']) . '</div>';
            echo '<h3>' . $title . '</h3><p>' . $text . '</p></div>';

        } elseif ($style === 'pillars') {
            $mod = !empty($item['mod']) ? e($item['mod']) : $pillarMods[$i % 3];
            echo '<div class="pillar-card ' . $mod . ' reveal' . $d . '">';
            if (!empty($item['icon'])) {
                echo '<div class="pillar-icon">' . _pal_icon($item['icon'], 32, 'currentColor', 1.75) . '</div>';
            }
            echo '<h3>' . $title . '</h3><p>' . $text . '</p></div>';

        } elseif ($style === 'initiative') {
            $ic  = !empty($item['color']) ? e($item['color']) : $iconColors[$i % count($iconColors)];
            echo '<div class="initiative-card reveal' . $d . '">';
            echo '<div class="initiative-icon ' . $ic . '">' . _pal_icon($item['icon'] ?? 'zap', 22, 'currentColor', 1.75) . '</div>';
            echo '<h4>' . $title . '</h4><p>' . $text . '</p></div>';

        } elseif ($style === 'mission') {
            $color = $svcColors[$i % count($svcColors)];
            echo '<div class="service-card reveal' . $d . '">';
            if (!empty($item['icon'])) {
                echo '<div class="service-icon" style="background:' . $color . '">' . _pal_icon($item['icon'], 24, 'white', 1.75) . '</div>';
            }
            echo '<h3>' . $title . '</h3><p>' . $text . '</p></div>';

        } else {
            echo '<div class="service-card reveal' . $d . '">';
            if (!empty($item['number'])) echo '<div class="service-number">' . e($item['number']) . '</div>';
            if (!empty($item['icon']) && empty($item['number'])) {
                echo '<div class="service-icon">' . _pal_icon($item['icon'], 24, 'white', 1.75) . '</div>';
            }
            echo '<h3>' . $title . '</h3><p>' . $text . '</p></div>';
        }
    }
    echo '</div></div></section>';
}

// ── Section: Numbered steps ────────────────────────────────────────────────

function _pal_sec_steps(array $s): void {
    $cls   = _pal_bg_class($s['bg'] ?? '');
    $items = $s['items'] ?? [];
    echo '<section class="' . $cls . '"><div class="container">';
    if (!empty($s['heading']) || !empty($s['eyebrow'])) echo _pal_section_header($s);
    echo '<div class="process-grid">';
    foreach ($items as $i => $item) {
        $d = ' delay-' . min($i + 1, 5);
        echo '<div class="process-card reveal' . $d . '">';
        echo '<div class="process-step-num">' . e($item['number'] ?? ($i + 1)) . '</div>';
        echo '<h3>' . e($item['title'] ?? '') . '</h3>';
        echo '<p>' . e($item['desc'] ?? $item['text'] ?? '') . '</p>';
        echo '</div>';
    }
    echo '</div></div></section>';
}

// ── Section: Stats band ────────────────────────────────────────────────────

function _pal_sec_stats(array $s): void {
    $items = $s['items'] ?? [];
    echo '<div class="stats-band"><div class="container"><div class="stats-band-grid">';
    foreach ($items as $item) {
        echo '<div class="stat-item">';
        echo '<span class="stat-value">' . e($item['value'] ?? '') . '</span>';
        echo '<span class="stat-label">' . e($item['label'] ?? '') . '</span>';
        echo '</div>';
    }
    echo '</div></div></div>';
}

// ── Section: Profiles (directors / team) ──────────────────────────────────

function _pal_sec_profiles(array $s): void {
    $cls   = _pal_bg_class($s['bg'] ?? 'alt');
    $items = $s['items'] ?? [];
    $style = $s['style'] ?? 'director'; // director | team
    echo '<section class="' . $cls . '"><div class="container">';
    if (!empty($s['heading']) || !empty($s['eyebrow'])) echo _pal_section_header($s);

    if ($style === 'team') {
        echo '<div class="team-grid">';
        foreach ($items as $i => $d) {
            $photo = $d['photo'] ?? '';
            $pos   = e($d['photo_position'] ?? 'center center');
            echo '<div class="team-card reveal delay-' . min($i + 1, 5) . '">';
            echo '<div class="team-card-photo">';
            if ($photo) echo '<img src="' . e(_pal_img($photo)) . '" alt="' . e($d['name'] ?? '') . '" loading="lazy" style="object-position:' . $pos . '">';
            echo '</div>';
            echo '<div class="team-card-body">';
            echo '<h3 class="team-card-name">' . e($d['name'] ?? '') . '</h3>';
            echo '<div class="team-card-title">' . e($d['role'] ?? '') . '</div>';
            echo '<div class="team-card-divider"></div>';
            if (!empty($d['quote'])) echo '<p class="team-card-quote">' . e($d['quote']) . '</p>';
            echo '<div class="team-card-contacts">';
            if (!empty($d['phone'])) echo '<a href="tel:' . e($d['phone']) . '" class="team-contact-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>' . e($d['phone']) . '</a>';
            if (!empty($d['email'])) echo '<a href="mailto:' . e($d['email']) . '" class="team-contact-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>' . e($d['email']) . '</a>';
            echo '</div></div></div>';
        }
        echo '</div>';
    } else {
        // Director quote cards
        echo '<div class="director-quote-grid">';
        foreach ($items as $i => $d) {
            $photo = $d['photo'] ?? '';
            $pos   = e($d['photo_position'] ?? 'center center');
            $delay = $i > 0 ? ' delay-1' : '';
            echo '<div class="director-quote-card reveal' . $delay . '">';
            echo '<div class="director-photo">';
            if ($photo) {
                echo '<img src="' . e(_pal_img($photo)) . '" alt="' . e($d['name'] ?? '') . '" loading="lazy" style="object-position:' . $pos . '">';
            } else {
                echo '<div style="width:100%;height:100%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:var(--teal-light)">' . strtoupper(substr($d['name'] ?? '?', 0, 1)) . '</div>';
            }
            echo '</div>';
            echo '<div class="director-body">';
            echo '<h3 class="director-name">' . e($d['name'] ?? '') . '</h3>';
            echo '<p class="director-title">' . e($d['role'] ?? '') . '</p>';
            echo '<div class="director-divider"></div>';
            if (!empty($d['quote'])) echo '<p class="director-quote">"' . e($d['quote']) . '"</p>';
            echo '<div style="margin-top:16px;display:flex;flex-direction:column;gap:6px">';
            if (!empty($d['phone'])) echo '<a href="tel:' . e($d['phone']) . '" style="display:flex;align-items:center;gap:8px;font-size:.875rem;color:var(--teal);font-weight:500;text-decoration:none"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>' . e($d['phone']) . '</a>';
            if (!empty($d['email'])) echo '<a href="mailto:' . e($d['email']) . '" style="display:flex;align-items:center;gap:8px;font-size:.875rem;color:var(--teal);font-weight:500;text-decoration:none"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>' . e($d['email']) . '</a>';
            echo '</div></div></div>';
        }
        echo '</div>';
    }
    echo '</div></section>';
}

// ── Section: Table ─────────────────────────────────────────────────────────

function _pal_sec_table(array $s): void {
    $cls  = _pal_bg_class($s['bg'] ?? '');
    $rows = $s['rows'] ?? [];
    echo '<section class="' . $cls . '"><div class="container">';
    if (!empty($s['heading'])) echo '<h2 class="section-title reveal mb-4">' . e($s['heading']) . '</h2>';
    echo '<div class="table-wrap"><table class="info-table"><tbody>';
    foreach ($rows as $r) {
        echo '<tr><td>' . e($r['label'] ?? '') . '</td><td>' . e($r['value'] ?? '') . '</td></tr>';
    }
    echo '</tbody></table></div></div></section>';
}

// ── Section: Logo grid (certs / clients) ───────────────────────────────────

function _pal_sec_logo_grid(array $s): void {
    $cls   = _pal_bg_class($s['bg'] ?? '');
    $items = $s['items'] ?? [];
    $style = $s['style'] ?? 'cert'; // cert | client
    echo '<section class="' . $cls . '"><div class="container">';
    if (!empty($s['heading']) || !empty($s['eyebrow'])) echo _pal_section_header($s);

    if ($style === 'client') {
        echo '<div class="client-grid">';
        foreach ($items as $i => $item) {
            echo '<div class="client-card reveal delay-' . min($i + 1, 5) . '">';
            if (!empty($item['logo'])) echo '<img src="' . e(_pal_img($item['logo'])) . '" alt="' . e($item['name'] ?? '') . '" loading="lazy">';
            echo '<div class="client-card-name">' . e($item['name'] ?? '') . '</div>';
            if (!empty($item['country'])) echo '<div class="client-card-country">' . e($item['country_flag'] ?? '') . ' ' . e($item['country']) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="cert-grid">';
        foreach ($items as $i => $item) {
            echo '<div class="cert-card reveal delay-' . min($i + 1, 5) . '">';
            if (!empty($item['logo'])) echo '<img src="' . e(_pal_img($item['logo'])) . '" alt="' . e($item['name'] ?? '') . '" loading="lazy">';
            echo '<div class="cert-card-name">' . e($item['name'] ?? '') . '</div>';
            echo '</div>';
        }
    }
    echo '</div></div></section>';
}

// ── Section: CTA banner ────────────────────────────────────────────────────

function _pal_sec_cta(array $s): void {
    echo '<div class="cta-band">';
    echo '<div class="container">';
    echo '<h2>' . e($s['title'] ?? '') . '</h2>';
    if (!empty($s['desc'])) echo '<p>' . e($s['desc']) . '</p>';
    echo '<div class="cta-band-actions">';
    if (!empty($s['btn1_text'])) echo '<a href="' . e($s['btn1_link'] ?? 'contact.php') . '" class="btn btn-white btn-lg">' . e($s['btn1_text']) . '</a>';
    if (!empty($s['btn2_text'])) echo '<a href="' . e($s['btn2_link'] ?? '#') . '" class="btn btn-outline-white btn-lg">' . e($s['btn2_text']) . '</a>';
    echo '</div></div></div>';
}

// ── Section: Gallery / CSR ─────────────────────────────────────────────────

function _pal_sec_gallery(array $s): void {
    $cls   = _pal_bg_class($s['bg'] ?? '');
    $style = $s['style'] ?? 'csr';
    $items = $s['items'] ?? [];
    echo '<section class="' . $cls . '"><div class="container">';
    if (!empty($s['heading']) || !empty($s['eyebrow'])) echo _pal_section_header($s);

    if ($style === 'csr') {
        echo '<div class="csr-grid">';
        foreach ($items as $i => $item) {
            echo '<div class="csr-card reveal delay-' . min($i + 1, 5) . '">';
            if (!empty($item['image'])) echo '<img src="' . e(_pal_img($item['image'])) . '" alt="' . e($item['title'] ?? '') . '" loading="lazy">';
            echo '<div class="csr-card-body"><h4>' . e($item['title'] ?? '') . '</h4><p>' . e($item['desc'] ?? '') . '</p></div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        // Simple photo grid
        $cols = (int)($s['columns'] ?? 4);
        $gcls = $cols === 2 ? 'grid-2' : ($cols === 3 ? 'grid-3' : 'grid-4');
        echo '<div class="' . $gcls . '">';
        foreach ($items as $item) {
            $img = is_array($item) ? ($item['image'] ?? '') : (string)$item;
            echo '<div style="border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-sm)">';
            echo '<img src="' . e(_pal_img($img)) . '" alt="" loading="lazy" style="width:100%;height:220px;object-fit:cover">';
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div></section>';
}

// ── Section: Contact form ──────────────────────────────────────────────────

function _pal_sec_contact_form(array $s): void {
    $site   = jsonRead(DATA_DIR . 'site.json');
    $social = $site['social'] ?? [];
    $ejsPub = $s['emailjs_public_key'] ?? '';
    $ejsSvc = $s['emailjs_service_id']  ?? '';
    $ejsTpl = $s['emailjs_template_id'] ?? '';
    echo '<section class="section"><div class="container"><div class="contact-grid">';
    // Info card
    echo '<div class="contact-info-card reveal-left">';
    echo '<h3>Contact Information</h3><p>Our team is ready to assist you Monday through Saturday, 9 AM to 6 PM (Bangladesh Standard Time).</p>';
    foreach ([
        ['key'=>'address','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>','label'=>'Address'],
        ['key'=>'email',  'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>','label'=>'Email'],
    ] as $det) {
        if (empty($site[$det['key']])) continue;
        echo '<div class="contact-detail"><div class="contact-detail-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">' . $det['icon'] . '</svg></div>';
        echo '<div class="contact-detail-body"><span class="contact-detail-label">' . $det['label'] . '</span><span class="contact-detail-value">' . e($site[$det['key']]) . '</span></div></div>';
    }
    if (!empty($site['phone1'])) {
        echo '<div class="contact-detail"><div class="contact-detail-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>';
        echo '<div class="contact-detail-body"><span class="contact-detail-label">Phone</span><span class="contact-detail-value"><a href="tel:' . e($site['phone1_tel'] ?? $site['phone1']) . '">' . e($site['phone1']) . '</a>' . (!empty($site['phone2']) ? '<br><a href="tel:' . e($site['phone2_tel'] ?? $site['phone2']) . '">' . e($site['phone2']) . '</a>' : '') . '</span></div></div>';
    }
    if (!empty($site['map_embed'])) echo '<div class="contact-map"><iframe src="' . e($site['map_embed']) . '" allowfullscreen loading="lazy"></iframe></div>';
    echo '</div>';
    // Form card
    echo '<div class="contact-form-card reveal-right">';
    echo '<h3>Send Us a Message</h3><p>Fill in the details below and our team will get back to you within one business day.</p>';
    echo '<form id="contactForm" novalidate>';
    echo '<div class="form-row"><div class="form-group"><label class="form-label">Name <span>*</span></label><input type="text" name="from_name" class="form-input" required placeholder="Your full name"></div>';
    echo '<div class="form-group"><label class="form-label">Company</label><input type="text" name="company" class="form-input" placeholder="Company name"></div></div>';
    echo '<div class="form-row"><div class="form-group"><label class="form-label">Email <span>*</span></label><input type="email" name="email" class="form-input" required placeholder="your@email.com"></div>';
    echo '<div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" placeholder="+1 234 567 8900"></div></div>';
    echo '<div class="form-group"><label class="form-label">Subject</label><select name="subject" class="form-select"><option value="">Select a subject</option><option>Sourcing Inquiry</option><option>Request a Quote</option><option>Factory Visit</option><option>Other</option></select></div>';
    echo '<div class="form-group"><label class="form-label">Message <span>*</span></label><textarea name="message" class="form-textarea" required placeholder="Tell us about your requirements..."></textarea></div>';
    echo '<button type="submit" class="btn btn-primary btn-lg form-submit">Send Message</button>';
    echo '<div id="formResult" class="form-result" style="display:none"></div>';
    echo '</form></div>';
    echo '</div></div></section>';
    if ($ejsPub && $ejsSvc && $ejsTpl) {
        echo '<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>';
        echo '<script>(function(){emailjs.init("' . e($ejsPub) . '");var f=document.getElementById("contactForm");var r=document.getElementById("formResult");if(!f)return;f.addEventListener("submit",function(e){e.preventDefault();var btn=f.querySelector("button[type=submit]");btn.disabled=true;btn.textContent="Sending...";emailjs.sendForm("' . e($ejsSvc) . '","' . e($ejsTpl) . '",f).then(function(){r.className="form-result form-result-success";r.textContent="Thank you! Your message has been sent. We will respond within one business day.";r.style.display="block";f.reset();btn.disabled=false;btn.textContent="Send Message";},function(err){r.className="form-result form-result-error";r.innerHTML="Something went wrong. Please email us directly at <a href=\'mailto:' . e($site['email'] ?? '') . '\'>' . e($site['email'] ?? '') . '</a>";r.style.display="block";btn.disabled=false;btn.textContent="Send Message";});});})();</script>';
    }
}
