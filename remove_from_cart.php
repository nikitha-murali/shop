<?php
session_start();

if (isset($_POST['remove_key'])) {
    $key = $_POST['remove_key'];
    
    if (isset($_SESSION['mycart'][$key])) {
        unset($_SESSION['mycart'][$key]);
        // بازآرایی ایندکس‌ها برای جلوگیری از مشکلات احتمالی
        $_SESSION['mycart'] = array_values($_SESSION['mycart']);
    }
}

header("Location: card.php");
exit();
