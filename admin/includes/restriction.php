<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['id']) || ($_SESSION['customer_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}
?>