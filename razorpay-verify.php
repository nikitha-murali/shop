<?php
require_once __DIR__ . '/functions/razorpay-functions.php';
header('Content-Type: application/json; charset=utf-8');

function hmd_razorpay_verify_error($message, $status) {
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    hmd_razorpay_verify_error('Method not allowed.', 405);
}
if (empty($_SESSION['id'])) {
    hmd_razorpay_verify_error('Please sign in again to confirm your order.', 401);
}
if (!hmd_verify_csrf_token($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    hmd_razorpay_verify_error('Your form session expired. Refresh the page and try again.', 403);
}

$orderId = $_POST['razorpay_order_id'] ?? '';
$paymentId = $_POST['razorpay_payment_id'] ?? '';
$signature = $_POST['razorpay_signature'] ?? '';
if (!is_string($orderId) || !is_string($paymentId) || !is_string($signature)) {
    hmd_razorpay_verify_error('Razorpay returned invalid payment details.', 400);
}

try {
    list(, $keySecret) = hmd_razorpay_credentials();
    $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $keySecret);
    if (!hash_equals($expectedSignature, $signature)) {
        hmd_razorpay_verify_error('Payment signature verification failed.', 400);
    }

    hmd_finalize_razorpay_payment($conn, $orderId, $paymentId, hmd_current_user_id());
    $_SESSION['hmd_cart'] = [];
    echo json_encode(['success' => true, 'redirect' => 'my-orders.php?success=' . urlencode('Razorpay test payment confirmed. Your order is placed.')]);
} catch (mysqli_sql_exception $exception) {
    error_log('Razorpay payment verification database error: ' . $exception->getMessage());
    hmd_razorpay_verify_error('Payment was received, but the order could not be confirmed. Contact support with payment ID ' . $paymentId . '.', 500);
} catch (RuntimeException $exception) {
    hmd_razorpay_verify_error($exception->getMessage(), 400);
}
