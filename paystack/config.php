<?php
/**
 * Paystack Configuration - Kenya (KES) - PRODUCTION
 */

// ⚠️ IMPORTANT: Replace these with your LIVE Paystack keys from dashboard.paystack.com
// API Keys (LIVE Mode for Production)
// define('PAYSTACK_PUBLIC_KEY', 'pk_live_YOUR_LIVE_PUBLIC_KEY_HERE');
// define('PAYSTACK_SECRET_KEY', 'sk_live_YOUR_LIVE_SECRET_KEY_HERE');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_91b41dd1e3939838d98a3ad6b8255dcffda32188');
define('PAYSTACK_SECRET_KEY', 'sk_test_9f38c53877f21386af582a85433c404d94a2c86d');

// API Endpoints
define('PAYSTACK_INIT_URL', 'https://api.paystack.co/transaction/initialize');
define('PAYSTACK_VERIFY_URL', 'https://api.paystack.co/transaction/verify/');

// Settings for Kenya
define('PAYSTACK_CURRENCY', 'KES'); // Kenyan Shillings

// ⚠️ IMPORTANT: Update this to your actual domain
// For production use: 'https://YOUR-DOMAIN.com/paystack/verify.php'

// For local development use: 'http://localhost/Royals/paystack/verify.php'
define('PAYSTACK_CALLBACK_URL', 'http://localhost/Royals/paystack/verify.php');

// Payment Channels for Kenya
define('PAYSTACK_CHANNELS', ['card', 'mobile_money']); // M-PESA + Cards
?>