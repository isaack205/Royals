<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once dirname(__DIR__) . '/db.php';

$action = $_POST['action'] ?? '';

if (!in_array($action, ['lock', 'unlock', 'schedule', 'cancel_schedule'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    if ($action === 'lock') {
        // Lock immediately, clear schedule
        $pdo->prepare("UPDATE site_settings SET setting_value = '1' WHERE setting_key = 'site_locked'")->execute();
        $pdo->prepare("UPDATE site_settings SET setting_value = '' WHERE setting_key = 'unlock_at'")->execute();
        $resMsg = 'Site is now LOCKED';
    } elseif ($action === 'unlock') {
        // Unlock immediately, clear schedule
        $pdo->prepare("UPDATE site_settings SET setting_value = '0' WHERE setting_key = 'site_locked'")->execute();
        $pdo->prepare("UPDATE site_settings SET setting_value = '' WHERE setting_key = 'unlock_at'")->execute();
        $resMsg = 'Site is now OPEN';
    } elseif ($action === 'schedule') {
        $datetime = trim($_POST['datetime'] ?? '');
        if (empty($datetime)) {
            echo json_encode(['success' => false, 'message' => 'Please select a valid date and time']);
            exit;
        }
        $timestamp = strtotime($datetime);
        if ($timestamp <= time()) {
            echo json_encode(['success' => false, 'message' => 'Scheduled time must be in the future']);
            exit;
        }

        // Format to standard MySQL DATETIME
        $mysqlDatetime = date('Y-m-d H:i:s', $timestamp);
        
        // Lock the site and set the unlock target
        $pdo->prepare("UPDATE site_settings SET setting_value = '1' WHERE setting_key = 'site_locked'")->execute();
        $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'unlock_at'")->execute([$mysqlDatetime]);
        $resMsg = 'Site scheduled to unlock at ' . date('M j, Y g:i A', $timestamp);
    } elseif ($action === 'cancel_schedule') {
        // Keep site locked, just clear the schedule date
        $pdo->prepare("UPDATE site_settings SET setting_value = '' WHERE setting_key = 'unlock_at'")->execute();
        $resMsg = 'Scheduled unlock cancelled. Site remains LOCKED.';
    }

    // Get final values to return
    $st1 = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_locked'")->fetchColumn();
    $st2 = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'unlock_at'")->fetchColumn();

    echo json_encode([
        'success'   => true,
        'locked'    => ($st1 === '1'),
        'unlock_at' => $st2,
        'message'   => $resMsg,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
