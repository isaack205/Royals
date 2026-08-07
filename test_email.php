<?php
// ⚠️ DELETE THIS FILE after testing — it's for SMTP verification only
require_once __DIR__ . '/mailer_config.php';
use PHPMailer\PHPMailer\Exception;

try {
    $mail = createMailer();
    $mail->addAddress('info@royals.co.ke'); // ← sends to yourself as a test
    $mail->Subject = 'Royals SMTP Test ✅';
    $mail->Body    = '<h2 style="color:#00d2ff;">SMTP is working!</h2><p>PHPMailer connected successfully to sv13.byethost13.org.</p>';
    $mail->AltBody = 'SMTP is working! PHPMailer connected successfully.';
    $mail->send();
    echo '✅ Email sent successfully! Check your inbox at info@royals.co.ke';
} catch (Exception $e) {
    echo '❌ SMTP Error: ' . $e->getMessage();
}
