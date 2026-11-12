<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/config.php';
if (empty($_SESSION['id']) || ($_SESSION['customer_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

function hmd_admin_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function hmd_admin_csrf_token() {
    if (empty($_SESSION['hmd_admin_csrf'])) {
        $_SESSION['hmd_admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['hmd_admin_csrf'];
}

function hmd_admin_verify_csrf($token) {
    return is_string($token) && isset($_SESSION['hmd_admin_csrf'])
        && hash_equals($_SESSION['hmd_admin_csrf'], $token);
}
