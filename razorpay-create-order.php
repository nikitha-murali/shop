<?php
require_once __DIR__ . '/functions/razorpay-functions.php';
header('Content-Type: application/json; charset=utf-8');

function hmd_razorpay_json_error($message, $status) {
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    hmd_razorpay_json_error('Method not allowed.', 405);
}
if (empty($_SESSION['id'])) {
    hmd_razorpay_json_error('Please sign in before checkout.', 401);
}
if (!hmd_verify_csrf_token($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    hmd_razorpay_json_error('Your form session expired. Refresh the page and try again.', 403);
}

try {
    $order = hmd_create_razorpay_cart_order(
        $conn,
        array_keys($_SESSION['hmd_cart'] ?? []),
        hmd_current_user_id()
    );
    echo json_encode($order);
} catch (mysqli_sql_exception $exception) {
    error_log('Razorpay order creation database error: ' . $exception->getMessage());
    hmd_razorpay_json_error('Checkout could not be started. Please try again.', 500);
} catch (RuntimeException $exception) {
    hmd_razorpay_json_error($exception->getMessage(), 400);
}
