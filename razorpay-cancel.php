<?php
require_once __DIR__ . '/functions/razorpay-functions.php';
header('Content-Type: application/json; charset=utf-8');

function hmd_razorpay_cancel_error($message, $status) {
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    hmd_razorpay_cancel_error('Method not allowed.', 405);
}
if (empty($_SESSION['id'])) {
    hmd_razorpay_cancel_error('Please sign in again.', 401);
}
if (!hmd_verify_csrf_token($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    hmd_razorpay_cancel_error('Your form session expired. Refresh the page and try again.', 403);
}

$orderId = $_POST['razorpay_order_id'] ?? '';
try {
    $status = hmd_cancel_razorpay_attempt($conn, $orderId, hmd_current_user_id());
    if ($status === 'paid') {
        $_SESSION['hmd_cart'] = [];
        echo json_encode([
            'status' => 'paid',
            'redirect' => 'my-orders.php?success=' . urlencode('Razorpay confirmed payment. Your order is placed.'),
        ]);
        exit();
    }
    if ($status === 'pending') {
        echo json_encode(['status' => 'pending']);
        exit();
    }
    echo json_encode(['status' => 'cancelled']);
} catch (mysqli_sql_exception $exception) {
    error_log('Razorpay cancel database error: ' . $exception->getMessage());
    hmd_razorpay_cancel_error('The payment attempt could not be updated. It will expire automatically.', 500);
} catch (RuntimeException $exception) {
    hmd_razorpay_cancel_error($exception->getMessage(), 400);
}
