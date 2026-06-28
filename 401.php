<?php
define('_PAL_CMS_', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
sendSecurityHeaders();

http_response_code(401);

$pageFile        = '401.php';
$pageTitle       = 'Authentication Required | Purbachal Apparel Limited';
$pageDescription = '';

require_once __DIR__ . '/includes/site-header.php';
?>
<main>
<section class="page-hero" style="min-height:60vh;display:flex;align-items:center">
  <div class="container" style="text-align:center;padding-top:4rem;padding-bottom:4rem">
    <p class="eyebrow" style="margin-bottom:1rem">401 Error</p>
    <h1 style="font-size:clamp(2rem,5vw,3.25rem);margin-bottom:1.25rem">Authentication Required</h1>
    <p style="font-size:1.125rem;color:var(--text-muted);max-width:520px;margin:0 auto 2.5rem">
      Access to this resource requires authentication. If you are a registered user, please sign in and try again.
    </p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="index.php" class="btn btn-primary">Back to Home</a>
      <a href="contact.php" class="btn btn-outline">Contact Us</a>
    </div>
  </div>
</section>
</main>
<?php require_once __DIR__ . '/includes/site-footer.php'; ?>
