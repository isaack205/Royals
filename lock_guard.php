<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lockPageName = 'lock.php';
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$requestedUri = $_SERVER['REQUEST_URI'] ?? '/';

if (!isset($_SESSION['site_unlocked'])) {
    $_SESSION['site_unlocked'] = false;
}

$isWhitelistedPage = ($currentScript === $lockPageName);

if (!$_SESSION['site_unlocked'] && !$isWhitelistedPage) {
    // For AJAX/API requests, return JSON status instead of redirecting HTML.
    $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xRequestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $isAjaxRequest = stripos($xRequestedWith, 'xmlhttprequest') !== false || stripos($acceptHeader, 'application/json') !== false;

    if ($isAjaxRequest) {
        http_response_code(423);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'locked' => true,
            'message' => 'Site is locked. Please unlock first.'
        ]);
        exit;
    }

    $redirect = urlencode($requestedUri);
    header('Location: ' . $lockPageName . '?redirect=' . $redirect);
    exit;
}
?>