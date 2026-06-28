<?php
if (!defined('_PAL_CMS_')) die('Direct access not permitted.');

function palMail(string $to, string $subject, string $htmlBody): bool {
    $host    = $_SERVER['HTTP_HOST'] ?? 'purbachalapparel.com';
    $domain  = preg_replace('/^www\./i', '', $host);
    $from    = 'PAL CMS <noreply@' . $domain . '>';
    $headers = implode("\r\n", [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from,
        'X-Mailer: PAL-CMS/1.0',
    ]);
    return @mail($to, $subject, $htmlBody, $headers);
}

function palMailTemplate(string $heading, string $bodyHtml): string {
    return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<style>
body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background:#f0f2f5;color:#1e293b;}
.wrap{max-width:520px;margin:36px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1);}
.head{background:#0F1A2E;padding:22px 32px;}
.head-logo{display:flex;align-items:center;gap:12px;}
.head h1{color:#fff;font-size:1rem;font-weight:700;margin:0;}
.head p{color:rgba(255,255,255,.6);font-size:.78rem;margin:4px 0 0;}
.body{padding:28px 32px;line-height:1.65;}
.body p{margin:0 0 16px;}
.btn{display:inline-block;background:#0E7E87;color:#fff !important;padding:12px 26px;border-radius:6px;text-decoration:none;font-weight:700;font-size:.9rem;}
.foot{padding:14px 32px;font-size:.76rem;color:#64748b;border-top:1px solid #e2e8f0;}
</style></head><body>
<div class="wrap">
  <div class="head">
    <div class="head-logo">
      <div>
        <h1>Purbachal Apparel Limited</h1>
        <p>Website CMS Notification</p>
      </div>
    </div>
  </div>
  <div class="body">
    <p style="font-size:1.05rem;font-weight:700;color:#0F1A2E;margin-bottom:20px;">' . $heading . '</p>
    ' . $bodyHtml . '
  </div>
  <div class="foot">This is an automated message from the PAL website CMS. Do not reply to this email.</div>
</div>
</body></html>';
}
