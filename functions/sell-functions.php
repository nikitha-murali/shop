<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/hmd-config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function hmd_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function hmd_require_login() {
    if (empty($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
}

function hmd_current_user_id() {
    return (int) $_SESSION['id'];
}

function hmd_csrf_token() {
    if (empty($_SESSION['hmd_csrf_token'])) {
        $_SESSION['hmd_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['hmd_csrf_token'];
}

function hmd_verify_csrf_token($token) {
    return is_string($token)
        && isset($_SESSION['hmd_csrf_token'])
        && hash_equals($_SESSION['hmd_csrf_token'], $token);
}

function hmd_redirect($page, $message, $kind = 'error') {
    header('Location: ' . $page . '?' . urlencode($kind) . '=' . urlencode($message));
    exit();
}

function hmd_get_seller_listings(mysqli $conn, $sellerId) {
    $stmt = $conn->prepare(
        'SELECT id, title, asking_price, status, image, created_at
         FROM hmd_listings
         WHERE seller_id = ? AND status <> \'removed\'
         ORDER BY created_at DESC'
    );
    $stmt->bind_param('i', $sellerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $listings = [];
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
    $stmt->close();
    return $listings;
}

function hmd_get_seller_listing(mysqli $conn, $listingId, $sellerId) {
    $stmt = $conn->prepare('SELECT * FROM hmd_listings WHERE id = ? AND seller_id = ?');
    $stmt->bind_param('ii', $listingId, $sellerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $listing = $result->fetch_assoc();
    $stmt->close();
    return $listing ?: null;
}

function hmd_validate_listing_input(array $input) {
    $data = [
        'title' => trim($input['title'] ?? ''),
        'category' => trim($input['category'] ?? ''),
        'course' => trim($input['course'] ?? ''),
        'semester' => trim($input['semester'] ?? ''),
        'description' => trim($input['description'] ?? ''),
        'story' => trim($input['story'] ?? ''),
        'pickup_location' => trim($input['pickup_location'] ?? ''),
    ];

    foreach (['title', 'category', 'course', 'semester', 'description', 'pickup_location'] as $required) {
        if ($data[$required] === '') {
            throw new InvalidArgumentException('Complete all required listing fields.');
        }
    }
    if (strlen($data['title']) > 200 || strlen($data['category']) > 100
        || strlen($data['course']) > 100 || strlen($data['semester']) > 50
        || strlen($data['pickup_location']) > 255) {
        throw new InvalidArgumentException('A listing field is too long.');
    }

    foreach (['condition_score', 'age_months', 'original_price', 'price_min', 'price_max', 'asking_price'] as $number) {
        if (!isset($input[$number]) || !is_numeric($input[$number])) {
            throw new InvalidArgumentException('Enter valid condition, age, and price values.');
        }
    }

    $data['condition_score'] = (float) $input['condition_score'];
    $data['age_months'] = filter_var($input['age_months'], FILTER_VALIDATE_INT);
    $data['original_price'] = (float) $input['original_price'];
    $data['price_min'] = (float) $input['price_min'];
    $data['price_max'] = (float) $input['price_max'];
    $data['asking_price'] = (float) $input['asking_price'];
    $data['allow_bids'] = isset($input['allow_bids']) && (string) $input['allow_bids'] === '1' ? 1 : 0;

    if ($data['age_months'] === false || $data['age_months'] < 0
        || $data['condition_score'] < 1 || $data['condition_score'] > 5
        || $data['original_price'] <= 0 || $data['price_min'] <= 0
        || $data['price_max'] < $data['price_min']
        || $data['asking_price'] < $data['price_min']
        || $data['asking_price'] > $data['price_max']) {
        throw new InvalidArgumentException('Check the condition and price range; asking price must be within your range.');
    }

    return $data;
}

function hmd_predict_price(array $listing) {
    $modelFile = __DIR__ . '/ml-functions.php';
    if (!is_file($modelFile)) {
        throw new RuntimeException('Price prediction is not installed yet. Ask Person 3 to add functions/ml-functions.php.');
    }

    require_once $modelFile;
    if (!function_exists('predict_price')) {
        throw new RuntimeException('functions/ml-functions.php must provide predict_price().');
    }

    $prediction = predict_price($listing);
    if (!is_array($prediction)
        || !isset($prediction['predicted_price'], $prediction['deal_badge'])
        || !is_numeric($prediction['predicted_price'])
        || !is_string($prediction['deal_badge'])
        || $prediction['deal_badge'] === ''
        || strlen($prediction['deal_badge']) > 50) {
        throw new RuntimeException('predict_price() must return predicted_price and deal_badge.');
    }

    return [
        'predicted_price' => (float) $prediction['predicted_price'],
        'deal_badge' => $prediction['deal_badge'],
    ];
}

function hmd_create_listing(mysqli $conn, $sellerId, array $data, array $prediction, $image) {
    $stmt = $conn->prepare(
        'INSERT INTO hmd_listings
            (seller_id, title, category, course, semester, description, story, pickup_location,
             condition_score, age_months, original_price, price_min, price_max, asking_price,
             predicted_price, deal_badge, image, allow_bids)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'i' . str_repeat('s', 7) . 'd' . 'i' . str_repeat('d', 5) . 'ssi',
        $sellerId,
        $data['title'],
        $data['category'],
        $data['course'],
        $data['semester'],
        $data['description'],
        $data['story'],
        $data['pickup_location'],
        $data['condition_score'],
        $data['age_months'],
        $data['original_price'],
        $data['price_min'],
        $data['price_max'],
        $data['asking_price'],
        $prediction['predicted_price'],
        $prediction['deal_badge'],
        $image,
        $data['allow_bids']
    );
    $created = $stmt->execute();
    $listingId = $created ? $stmt->insert_id : 0;
    $stmt->close();
    return $listingId;
}

function hmd_update_listing(mysqli $conn, $listingId, $sellerId, array $data, array $prediction) {
    $stmt = $conn->prepare(
        'UPDATE hmd_listings
         SET title = ?, category = ?, course = ?, semester = ?, description = ?, story = ?,
             pickup_location = ?, condition_score = ?, age_months = ?, original_price = ?,
             price_min = ?, price_max = ?, asking_price = ?, predicted_price = ?, deal_badge = ?,
             allow_bids = ?
         WHERE id = ? AND seller_id = ? AND status = \'active\''
    );
    $stmt->bind_param(
        str_repeat('s', 7) . 'd' . 'i' . str_repeat('d', 5) . 'siii',
        $data['title'],
        $data['category'],
        $data['course'],
        $data['semester'],
        $data['description'],
        $data['story'],
        $data['pickup_location'],
        $data['condition_score'],
        $data['age_months'],
        $data['original_price'],
        $data['price_min'],
        $data['price_max'],
        $data['asking_price'],
        $prediction['predicted_price'],
        $prediction['deal_badge'],
        $data['allow_bids'],
        $listingId,
        $sellerId
    );
    $updated = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    if (!$updated || $affectedRows === 1) {
        return $updated;
    }

    $currentListing = hmd_get_seller_listing($conn, $listingId, $sellerId);
    return $currentListing !== null && $currentListing['status'] === 'active';
}

function hmd_remove_listing(mysqli $conn, $listingId, $sellerId) {
    $stmt = $conn->prepare(
        'UPDATE hmd_listings SET status = \'removed\' WHERE id = ? AND seller_id = ? AND status = \'active\''
    );
    $stmt->bind_param('ii', $listingId, $sellerId);
    $removed = $stmt->execute() && $stmt->affected_rows === 1;
    $stmt->close();
    return $removed;
}

function hmd_accept_bid(mysqli $conn, $listingId, $bidId, $sellerId) {
    $conn->begin_transaction();
    try {
        $listingStmt = $conn->prepare(
            'SELECT seller_id, status, allow_bids, price_min FROM hmd_listings WHERE id = ? FOR UPDATE'
        );
        $listingStmt->bind_param('i', $listingId);
        $listingStmt->execute();
        $listing = $listingStmt->get_result()->fetch_assoc();
        $listingStmt->close();
        if (!$listing || (int) $listing['seller_id'] !== (int) $sellerId
            || $listing['status'] !== 'active' || (int) $listing['allow_bids'] !== 1) {
            throw new RuntimeException('This listing cannot accept a bid.');
        }

        $bidStmt = $conn->prepare(
            'SELECT bidder_id, amount, status FROM hmd_bids WHERE id = ? AND listing_id = ? FOR UPDATE'
        );
        $bidStmt->bind_param('ii', $bidId, $listingId);
        $bidStmt->execute();
        $bid = $bidStmt->get_result()->fetch_assoc();
        $bidStmt->close();
        if (!$bid || $bid['status'] !== 'pending'
            || (int) $bid['bidder_id'] === (int) $sellerId
            || (float) $bid['amount'] < (float) $listing['price_min']) {
            throw new RuntimeException('That bid is no longer available.');
        }

        $acceptStmt = $conn->prepare(
            'UPDATE hmd_bids SET status = \'accepted\' WHERE id = ? AND listing_id = ? AND status = \'pending\''
        );
        $acceptStmt->bind_param('ii', $bidId, $listingId);
        $acceptStmt->execute();
        $accepted = $acceptStmt->affected_rows === 1;
        $acceptStmt->close();
        if (!$accepted) {
            throw new RuntimeException('The bid changed; refresh and try again.');
        }

        $rejectStmt = $conn->prepare(
            'UPDATE hmd_bids SET status = \'rejected\' WHERE listing_id = ? AND id <> ? AND status = \'pending\''
        );
        $rejectStmt->bind_param('ii', $listingId, $bidId);
        $rejectStmt->execute();
        $rejectStmt->close();

        $soldStmt = $conn->prepare(
            'UPDATE hmd_listings SET status = \'sold\' WHERE id = ? AND seller_id = ? AND status = \'active\''
        );
        $soldStmt->bind_param('ii', $listingId, $sellerId);
        $soldStmt->execute();
        $sold = $soldStmt->affected_rows === 1;
        $soldStmt->close();
        if (!$sold) {
            throw new RuntimeException('The listing is no longer available.');
        }

        $orderStmt = $conn->prepare(
            'INSERT INTO hmd_orders (listing_id, buyer_id, price, source, payment_method, payment_status)
             VALUES (?, ?, ?, \'bid\', \'cash_on_pickup\', \'pending\')'
        );
        $amount = (float) $bid['amount'];
        $bidderId = (int) $bid['bidder_id'];
        $orderStmt->bind_param('iid', $listingId, $bidderId, $amount);
        $orderStmt->execute();
        $orderStmt->close();

        $conn->commit();
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }
}
