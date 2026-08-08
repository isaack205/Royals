<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

// ─── EMAIL CREDENTIALS ────────────────────────────────────────────────
define('MAIL_HOST',       'sv13.byethost13.org');
define('MAIL_PORT',        465);
define('MAIL_USERNAME',   'info@royals.co.ke');
define('MAIL_PASSWORD',   'Royals@2026');
define('MAIL_FROM_EMAIL', 'info@royals.co.ke');
define('MAIL_FROM_NAME',  'Royals - Footware');
// ──────────────────────────────────────────────────────────────────────

/**
 * Create and return a configured PHPMailer instance.
 */
function createMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = MAIL_PORT;
    $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    return $mail;
}
