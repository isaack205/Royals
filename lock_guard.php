<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Nairobi');

$lockPageName  = 'lock.php';
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$requestedUri  = $_SERVER['REQUEST_URI'] ?? '/';

// Pages that are never blocked by the lock (admin + lock page itself)
$whitelistedPages = [
    'lock.php', 'adminlogin.php', 'adminlogout.php',
    'admin_dashboard.php', 'admin_mailing_list.php', 'admin_products.php',
    'add_product.php', 'featured_products.php', 'orders_made.php',
    'registeredusers.php', 'upload_ads.php', 'admin_user_profiles.php',
    'home.php',
];
// Also whitelist any file starting with "admin" or in the ajax folder
$isWhitelistedPage = in_array($currentScript, $whitelistedPages, true)
                  || strpos($currentScript, 'admin') === 0
                  || strpos($requestedUri, '/ajax/') !== false;

if (!$isWhitelistedPage) {
    // If an admin is logged in, automatically bypass the lockscreen
    if (isset($_SESSION['admin_id'])) {
        $_SESSION['site_unlocked'] = true;
        return; // Proceed to the page
    }

    // Always check the DB lock state — this ensures locking from admin
    // takes immediate effect for ALL visitors, not just new sessions.

    $lockCheckHost = '127.0.0.1';
    $lockCheckDb   = 'brandx';
    $lockCheckUser = 'root';
    $lockCheckPass = 'Isaac.254';

    // $lockCheckHost = 'localhost';
    // $lockCheckDb   = 'royalsco_brandx';
    // $lockCheckUser = 'royalsco_dbuser';
    // $lockCheckPass = 'Admin@royals';

    $siteLocked = false;
    try {
        $lockPdo  = new PDO(
            "mysql:host=$lockCheckHost;dbname=$lockCheckDb;charset=utf8mb4",
            $lockCheckUser,
            $lockCheckPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Fetch settings
        $stmt = $lockPdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $siteLocked = (isset($settings['site_locked']) && $settings['site_locked'] == '1');
        $unlockAt   = $settings['unlock_at'] ?? '';
        
        // Check if scheduled unlock time has passed
        if ($siteLocked && !empty($unlockAt)) {
            if (time() >= strtotime($unlockAt)) {
                // Auto-unlock the site in the database
                $lockPdo->prepare("UPDATE site_settings SET setting_value = '0' WHERE setting_key = 'site_locked'")->execute();
                $lockPdo->prepare("UPDATE site_settings SET setting_value = '' WHERE setting_key = 'unlock_at'")->execute();
                $siteLocked = false;
            }
        }
    } catch (PDOException $e) {
        // If DB unreachable, fail open — don't block the site
        $siteLocked = false;
    }

    if ($siteLocked) {
        // DB says locked — block everyone (clear any stale session unlock)
        $_SESSION['site_unlocked'] = false;

        $acceptHeader   = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xRequestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $isAjax = stripos($xRequestedWith, 'xmlhttprequest') !== false
               || stripos($acceptHeader, 'application/json') !== false;

        if ($isAjax) {
            http_response_code(423);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'locked'  => true,
                'message' => 'Site is locked. Please unlock first.',
            ]);
            exit;
        }

        $redirect = urlencode($requestedUri);
        header('Location: ' . $lockPageName . '?redirect=' . $redirect);
        exit;
    }

    // DB says unlocked — grant access
    $_SESSION['site_unlocked'] = true;
}