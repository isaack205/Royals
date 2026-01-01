<?php
session_start();
header('Content-Type: application/json');

if (isset($_GET['stage'])) {
    $stage = (int)$_GET['stage'];
    if ($stage >= 1 && $stage <= 3) {
        $_SESSION['checkout_stage'] = $stage;
        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false]);
?>