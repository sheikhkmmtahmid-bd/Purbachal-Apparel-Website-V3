<?php
if (!defined('_PAL_CMS_')) die('Direct access not permitted.');

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_name('PALCMS');
        session_start();
    }
}

function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $t): bool {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $t);
}

function requireCsrf(): void {
    $t = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!validateCsrfToken($t)) {
        http_response_code(403);
        header('Content-Type: application/json');
        die(json_encode(['ok' => false, 'error' => 'Invalid CSRF token']));
    }
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(generateCsrfToken(), ENT_QUOTES | ENT_HTML5, 'UTF-8') . '">';
}

function csrfMeta(): string {
    return '<meta name="csrf-token" content="'
        . htmlspecialchars(generateCsrfToken(), ENT_QUOTES | ENT_HTML5, 'UTF-8') . '">';
}

define('RATE_FILE',    __DIR__ . '/../data/.login_attempts.json');
define('MAX_ATTEMPTS', 5);
define('LOCKOUT_SEC',  900);

function isRateLimited(): bool {
    $ip = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    if (!file_exists(RATE_FILE)) return false;
    $d = json_decode(file_get_contents(RATE_FILE), true) ?? [];
    if (!isset($d[$ip])) return false;
    if (time() - $d[$ip]['first'] > LOCKOUT_SEC) {
        unset($d[$ip]);
        _atomicWrite(RATE_FILE, $d);
        return false;
    }
    return $d[$ip]['count'] >= MAX_ATTEMPTS;
}

function getRateLimitSeconds(): int {
    $ip = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    if (!file_exists(RATE_FILE)) return 0;
    $d = json_decode(file_get_contents(RATE_FILE), true) ?? [];
    if (!isset($d[$ip])) return 0;
    return max(0, LOCKOUT_SEC - (time() - $d[$ip]['first']));
}

function recordFailedLogin(): void {
    $ip = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    $d  = file_exists(RATE_FILE) ? (json_decode(file_get_contents(RATE_FILE), true) ?? []) : [];
    if (!isset($d[$ip]) || time() - $d[$ip]['first'] > LOCKOUT_SEC) {
        $d[$ip] = ['count' => 1, 'first' => time()];
    } else {
        $d[$ip]['count']++;
    }
    _atomicWrite(RATE_FILE, $d);
}

function clearLoginAttempts(): void {
    $ip = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    $d  = file_exists(RATE_FILE) ? (json_decode(file_get_contents(RATE_FILE), true) ?? []) : [];
    unset($d[$ip]);
    _atomicWrite(RATE_FILE, $d);
}

function _atomicWrite(string $path, mixed $data): bool {
    $tmp = $path . '.tmp.' . bin2hex(random_bytes(4));
    $enc = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($enc === false) return false;
    if (file_put_contents($tmp, $enc, LOCK_EX) === false) return false;
    return rename($tmp, $path);
}

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitize(string $s, int $max = 2000): string {
    return mb_substr(trim($s), 0, $max);
}

define('ALLOWED_MIMES', [
    'image/jpeg' => ['jpg', 'jpeg'],
    'image/png'  => ['png'],
    'image/gif'  => ['gif'],
    'image/webp' => ['webp'],
]);
define('MAX_UPLOAD', 10 * 1024 * 1024);

function validateAndProcessUpload(array $file, string $destDir): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload error'];
    }
    if ($file['size'] > MAX_UPLOAD) {
        return ['ok' => false, 'error' => 'File too large (max 10MB)'];
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'error' => 'Invalid upload'];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!array_key_exists($mime, ALLOWED_MIMES)) {
        return ['ok' => false, 'error' => 'Invalid file type'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_MIMES[$mime], true)) {
        return ['ok' => false, 'error' => 'Extension mismatch'];
    }
    $img = @imagecreatefromstring(file_get_contents($file['tmp_name']));
    if (!$img) {
        return ['ok' => false, 'error' => 'Cannot process image'];
    }
    $ext      = ($ext === 'jpeg') ? 'jpg' : $ext;
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $dest = rtrim($destDir, '/') . '/' . $filename;
    $ok   = match ($ext) {
        'jpg'  => imagejpeg($img, $dest, 90),
        'png'  => imagepng($img, $dest, 6),
        'gif'  => imagegif($img, $dest),
        'webp' => imagewebp($img, $dest, 90),
        default => false,
    };
    imagedestroy($img);
    if (!$ok) {
        return ['ok' => false, 'error' => 'Failed to save image'];
    }
    return ['ok' => true, 'filename' => $filename];
}

function sendSecurityHeaders(): void {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function jsonWrite(string $path, mixed $data): bool {
    return _atomicWrite($path, $data);
}

function jsonRead(string $path, mixed $def = []): mixed {
    if (!file_exists($path)) return $def;
    $c = file_get_contents($path);
    if ($c === false) return $def;
    return json_decode($c, true) ?? $def;
}
