<?php
require_once __DIR__ . '/browse-functions.php';

function hmd_razorpay_credentials() {
    $keyId = getenv('HMD_RAZORPAY_KEY_ID');
    $keySecret = getenv('HMD_RAZORPAY_KEY_SECRET');
    if (!$keyId || !$keySecret) {
        throw new RuntimeException('Razorpay test keys are not configured on the server.');
    }
    if (strpos($keyId, 'rzp_test_') !== 0) {
        throw new RuntimeException('Razorpay checkout is restricted to test keys. Use a key ID beginning with rzp_test_.');
    }
    return [$keyId, $keySecret];
}

function hmd_razorpay_request($method, $path, ?array $payload = null) {
    list($keyId, $keySecret) = hmd_razorpay_credentials();
    $url = 'https://api.razorpay.com/v1/' . ltrim($path, '/');
    $json = null;
    if ($payload !== null) {
        $json = json_encode($payload);
        if ($json === false) {
            throw new RuntimeException('Unable to prepare the Razorpay request.');
        }
    }

    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERPWD => $keyId . ':' . $keySecret,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ];
        if ($json !== null) {
            $options[CURLOPT_POSTFIELDS] = $json;
        }
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            error_log('Razorpay connection failed: ' . $error);
            throw new RuntimeException('Could not connect to Razorpay. Please try again.');
        }
        curl_close($curl);
    } else {
        if (!filter_var((string) ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            throw new RuntimeException('Enable PHP cURL or allow_url_fopen for Razorpay checkout.');
        }
        $headers = "Content-Type: application/json\r\nAuthorization: Basic "
            . base64_encode($keyId . ':' . $keySecret) . "\r\n";
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => $headers,
                'content' => $json ?? '',
                'timeout' => 20,
                'ignore_errors' => true,
            ],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $warning = '';
        set_error_handler(static function ($severity, $message) use (&$warning) {
            $warning = $message;
            return true;
        });
        try {
            $response = file_get_contents($url, false, $context);
        } finally {
            restore_error_handler();
        }
        if ($response === false) {
            error_log('Razorpay connection failed: ' . $warning);
            throw new RuntimeException('Could not connect to Razorpay. Please try again.');
        }
        $status = 0;
        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $match)) {
                $status = (int) $match[1];
                break;
            }
        }
    }

    $decoded = json_decode($response, true);
    if ($status < 200 || $status >= 300 || !is_array($decoded)) {
        error_log('Razorpay API returned HTTP ' . $status . '.');
        throw new RuntimeException('Razorpay could not process checkout. Check the test account configuration and try again.');
    }
    return $decoded;
}

function hmd_release_expired_razorpay_holds(mysqli $conn) {
    $result = $conn->query(
        "SELECT id FROM hmd_payment_attempts WHERE status = 'created' AND expires_at <= NOW()"
    );
    $attemptIds = [];
    while ($row = $result->fetch_assoc()) {
        $attemptIds[] = (int) $row['id'];
    }
    $result->close();

    foreach ($attemptIds as $attemptId) {
        $conn->begin_transaction();
        try {
            $attemptStmt = $conn->prepare(
                "SELECT status, expires_at <= NOW() AS is_expired
                 FROM hmd_payment_attempts WHERE id = ? FOR UPDATE"
            );
            $attemptStmt->bind_param('i', $attemptId);
            $attemptStmt->execute();
            $attempt = $attemptStmt->get_result()->fetch_assoc();
            $attemptStmt->close();
            if (!$attempt || $attempt['status'] !== 'created' || !(int) $attempt['is_expired']) {
                $conn->commit();
                continue;
            }

            $items = $conn->prepare(
                'SELECT listing_id FROM hmd_payment_attempt_items WHERE attempt_id = ?'
            );
            $items->bind_param('i', $attemptId);
            $items->execute();
            $itemResult = $items->get_result();
            $listingIds = [];
            while ($item = $itemResult->fetch_assoc()) {
                $listingIds[] = (int) $item['listing_id'];
            }
            $items->close();

            $release = $conn->prepare(
                "UPDATE hmd_listings SET status = 'active' WHERE id = ? AND status = 'pending_payment'"
            );
            foreach ($listingIds as $listingId) {
                $release->bind_param('i', $listingId);
                $release->execute();
            }
            $release->close();

            $expire = $conn->prepare(
                "UPDATE hmd_payment_attempts SET status = 'expired' WHERE id = ? AND status = 'created'"
            );
            $expire->bind_param('i', $attemptId);
            $expire->execute();
            $expire->close();
            $conn->commit();
        } catch (Throwable $error) {
            $conn->rollback();
            throw $error;
        }
    }
}

function hmd_create_razorpay_cart_order(mysqli $conn, array $listingIds, $buyerId) {
    hmd_release_expired_razorpay_holds($conn);
    $listingIds = array_values(array_unique(array_filter(
        array_map('intval', $listingIds),
        static function ($id) { return $id > 0; }
    )));
    sort($listingIds, SORT_NUMERIC);
    if (!$listingIds) {
        throw new RuntimeException('Your cart is empty.');
    }

    $select = $conn->prepare(
        'SELECT id, seller_id, asking_price, title
         FROM hmd_listings WHERE status = \'active\' AND id = ?'
    );
    $items = [];
    $totalPaise = 0;
    foreach ($listingIds as $listingId) {
        $select->bind_param('i', $listingId);
        $select->execute();
        $item = $select->get_result()->fetch_assoc();
        if (!$item || (int) $item['seller_id'] === (int) $buyerId) {
            $select->close();
            throw new RuntimeException('An item in your cart is no longer available. Review your cart and try again.');
        }
        $item['amount_paise'] = (int) round((float) $item['asking_price'] * 100);
        $totalPaise += $item['amount_paise'];
        $items[] = $item;
    }
    $select->close();
    if ($totalPaise < 100 || $totalPaise > 50000000) {
        throw new RuntimeException('The cart total is outside Razorpay’s supported range.');
    }

    $gatewayOrder = hmd_razorpay_request('POST', 'orders', [
        'amount' => $totalPaise,
        'currency' => 'INR',
        'receipt' => 'hmd-' . bin2hex(random_bytes(8)),
        'notes' => ['buyer_id' => (string) $buyerId, 'item_count' => (string) count($items)],
    ]);
    $gatewayOrderId = $gatewayOrder['id'] ?? '';
    if (!is_string($gatewayOrderId) || !preg_match('/^order_[A-Za-z0-9]+$/', $gatewayOrderId)
        || (int) ($gatewayOrder['amount'] ?? 0) !== $totalPaise
        || ($gatewayOrder['currency'] ?? '') !== 'INR') {
        throw new RuntimeException('Razorpay returned an invalid checkout order.');
    }

    $conn->begin_transaction();
    try {
        $lock = $conn->prepare(
            'SELECT seller_id, status FROM hmd_listings WHERE id = ? FOR UPDATE'
        );
        foreach ($items as $item) {
            $listingId = (int) $item['id'];
            $lock->bind_param('i', $listingId);
            $lock->execute();
            $current = $lock->get_result()->fetch_assoc();
            if (!$current || $current['status'] !== 'active'
                || (int) $current['seller_id'] === (int) $buyerId) {
                throw new RuntimeException('An item in your cart was just reserved or purchased. Review your cart and try again.');
            }
        }
        $lock->close();

        $attemptStmt = $conn->prepare(
            "INSERT INTO hmd_payment_attempts
                (listing_id, buyer_id, amount_paise, gateway_order_id, status, expires_at)
             VALUES (?, ?, ?, ?, 'created', DATE_ADD(NOW(), INTERVAL 15 MINUTE))"
        );
        $firstListingId = (int) $items[0]['id'];
        $attemptStmt->bind_param('iiis', $firstListingId, $buyerId, $totalPaise, $gatewayOrderId);
        $attemptStmt->execute();
        $attemptId = (int) $conn->insert_id;
        $attemptStmt->close();

        $reserve = $conn->prepare(
            "UPDATE hmd_listings SET status = 'pending_payment' WHERE id = ? AND status = 'active'"
        );
        $attemptItem = $conn->prepare(
            'INSERT INTO hmd_payment_attempt_items (attempt_id, listing_id, amount_paise) VALUES (?, ?, ?)'
        );
        foreach ($items as $item) {
            $listingId = (int) $item['id'];
            $itemPaise = (int) $item['amount_paise'];
            $reserve->bind_param('i', $listingId);
            $reserve->execute();
            if ($reserve->affected_rows !== 1) {
                throw new RuntimeException('An item in your cart was just reserved. Please try again.');
            }
            $attemptItem->bind_param('iii', $attemptId, $listingId, $itemPaise);
            $attemptItem->execute();
        }
        $reserve->close();
        $attemptItem->close();
        $conn->commit();
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }

    list($keyId) = hmd_razorpay_credentials();
    return [
        'key_id' => $keyId,
        'order_id' => $gatewayOrderId,
        'amount' => $totalPaise,
        'currency' => 'INR',
        'name' => 'HandMeDown',
        'description' => count($items) . ' campus marketplace item' . (count($items) === 1 ? '' : 's'),
    ];
}

function hmd_finalize_razorpay_payment(mysqli $conn, $gatewayOrderId, $paymentId, $buyerId = null) {
    if (!is_string($gatewayOrderId) || !preg_match('/^order_[A-Za-z0-9]+$/', $gatewayOrderId)
        || !is_string($paymentId) || !preg_match('/^pay_[A-Za-z0-9]+$/', $paymentId)) {
        throw new RuntimeException('Razorpay returned invalid payment details.');
    }

    function hmd_cancel_razorpay_attempt(mysqli $conn, $gatewayOrderId, $buyerId) {
        if (!is_string($gatewayOrderId) || !preg_match('/^order_[A-Za-z0-9]+$/', $gatewayOrderId)) {
            throw new RuntimeException('Invalid Razorpay order.');
        }

        $payments = hmd_razorpay_request(
            'GET',
            'orders/' . rawurlencode($gatewayOrderId) . '/payments'
        );
        foreach ($payments['items'] ?? [] as $payment) {
            if (($payment['status'] ?? '') === 'captured' && !empty($payment['id'])) {
                hmd_finalize_razorpay_payment($conn, $gatewayOrderId, $payment['id'], $buyerId);
                return 'paid';
            }
            if (($payment['status'] ?? '') === 'authorized') {
                return 'pending';
            }
        }

        $conn->begin_transaction();
        try {
            $attemptStmt = $conn->prepare(
                "SELECT id, status FROM hmd_payment_attempts
                 WHERE gateway_order_id = ? AND buyer_id = ? FOR UPDATE"
            );
            $attemptStmt->bind_param('si', $gatewayOrderId, $buyerId);
            $attemptStmt->execute();
            $attempt = $attemptStmt->get_result()->fetch_assoc();
            $attemptStmt->close();
            if (!$attempt || $attempt['status'] !== 'created') {
                $conn->commit();
                return $attempt && $attempt['status'] === 'paid' ? 'paid' : 'pending';
            }

            $attemptId = (int) $attempt['id'];
            $items = $conn->prepare(
                'SELECT listing_id FROM hmd_payment_attempt_items WHERE attempt_id = ?'
            );
            $items->bind_param('i', $attemptId);
            $items->execute();
            $result = $items->get_result();
            $listingIds = [];
            while ($item = $result->fetch_assoc()) {
                $listingIds[] = (int) $item['listing_id'];
            }
            $items->close();

            $release = $conn->prepare(
                "UPDATE hmd_listings SET status = 'active' WHERE id = ? AND status = 'pending_payment'"
            );
            foreach ($listingIds as $listingId) {
                $release->bind_param('i', $listingId);
                $release->execute();
            }
            $release->close();
            $fail = $conn->prepare(
                "UPDATE hmd_payment_attempts SET status = 'failed' WHERE id = ? AND status = 'created'"
            );
            $fail->bind_param('i', $attemptId);
            $fail->execute();
            $fail->close();
            $conn->commit();
            return 'cancelled';
        } catch (Throwable $error) {
            $conn->rollback();
            throw $error;
        }
    }
    $payment = hmd_razorpay_request('GET', 'payments/' . rawurlencode($paymentId));
    if (($payment['order_id'] ?? '') !== $gatewayOrderId
        || ($payment['status'] ?? '') !== 'captured'
        || ($payment['currency'] ?? '') !== 'INR') {
        throw new RuntimeException('Razorpay has not confirmed this payment as captured.');
    }

    $conn->begin_transaction();
    try {
        $attemptStmt = $conn->prepare(
            'SELECT id, buyer_id, amount_paise, status, expires_at > NOW() AS is_valid
             FROM hmd_payment_attempts WHERE gateway_order_id = ? FOR UPDATE'
        );
        $attemptStmt->bind_param('s', $gatewayOrderId);
        $attemptStmt->execute();
        $attempt = $attemptStmt->get_result()->fetch_assoc();
        $attemptStmt->close();
        if ($attempt && $attempt['status'] === 'paid'
            && hash_equals((string) $attempt['gateway_payment_id'], $paymentId)) {
            $alreadyPaidBuyerId = (int) $attempt['buyer_id'];
            $conn->commit();
            return $alreadyPaidBuyerId;
        }
        if (!$attempt || $attempt['status'] !== 'created'
            || !(int) $attempt['is_valid']
            || ($buyerId !== null && (int) $attempt['buyer_id'] !== (int) $buyerId)
            || (int) $payment['amount'] !== (int) $attempt['amount_paise']) {
            throw new RuntimeException('This payment session is expired or does not match your account.');
        }

        $itemsStmt = $conn->prepare(
            'SELECT listing_id, amount_paise FROM hmd_payment_attempt_items
             WHERE attempt_id = ? ORDER BY listing_id FOR UPDATE'
        );
        $attemptId = (int) $attempt['id'];
        $itemsStmt->bind_param('i', $attemptId);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();
        $items = [];
        while ($item = $itemsResult->fetch_assoc()) {
            $items[] = $item;
        }
        $itemsStmt->close();
        if (!$items || array_sum(array_column($items, 'amount_paise')) !== (int) $attempt['amount_paise']) {
            throw new RuntimeException('The saved cart total does not match the payment.');
        }

        $markSold = $conn->prepare(
            "UPDATE hmd_listings SET status = 'sold' WHERE id = ? AND status = 'pending_payment'"
        );
        $rejectBids = $conn->prepare(
            "UPDATE hmd_bids SET status = 'rejected' WHERE listing_id = ? AND status = 'pending'"
        );
        $order = $conn->prepare(
            "INSERT INTO hmd_orders
                (listing_id, buyer_id, price, source, payment_method, payment_status)
             VALUES (?, ?, ?, 'buy', 'razorpay', 'paid')"
        );
        $attemptBuyerId = (int) $attempt['buyer_id'];
        foreach ($items as $item) {
            $listingId = (int) $item['listing_id'];
            $markSold->bind_param('i', $listingId);
            $markSold->execute();
            if ($markSold->affected_rows !== 1) {
                throw new RuntimeException('A reserved listing is no longer available. Contact support with your Razorpay payment ID.');
            }
            $rejectBids->bind_param('i', $listingId);
            $rejectBids->execute();
            $price = (int) $item['amount_paise'] / 100;
            $order->bind_param('iid', $listingId, $attemptBuyerId, $price);
            $order->execute();
        }
        $markSold->close();
        $rejectBids->close();
        $order->close();

        $paid = $conn->prepare(
            "UPDATE hmd_payment_attempts
             SET status = 'paid', gateway_payment_id = ?
             WHERE id = ? AND status = 'created'"
        );
        $paid->bind_param('si', $paymentId, $attemptId);
        $paid->execute();
        if ($paid->affected_rows !== 1) {
            throw new RuntimeException('This payment was already processed.');
        }
        $paid->close();
        $conn->commit();
        return $attemptBuyerId;
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }
}
