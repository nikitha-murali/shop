<?php
require_once __DIR__ . '/sell-functions.php';

function hmd_is_academic_category($category) {
    $category = strtolower(trim((string) $category));
    foreach (['book', 'textbook', 'note', 'study material', 'course material', 'academic'] as $keyword) {
        if (strpos($category, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

function hmd_condition_label($score) {
    $score = (float) $score;
    if ($score < 1.5) {
        return 'Poor';
    }
    if ($score < 2.5) {
        return 'Fair';
    }
    if ($score < 3.5) {
        return 'Good';
    }
    if ($score < 4.5) {
        return 'Very good';
    }
    return 'Like new';
}

function hmd_render_condition_rating($score) {
    $score = max(1, min(5, (float) $score));
    $filledStars = (int) round($score);
    $rating = number_format($score, 1);
    $label = hmd_condition_label($score);
    echo '<span class="condition-rating" aria-label="Condition rated '
        . hmd_escape($rating) . ' out of 5: ' . hmd_escape($label) . '">'
        . '<span class="condition-stars" aria-hidden="true">'
        . str_repeat('★', $filledStars) . str_repeat('☆', 5 - $filledStars)
        . '</span><span class="condition-label">' . hmd_escape($label)
        . ' · ' . hmd_escape($rating) . '/5</span></span>';
}

function hmd_fetch_listings(mysqli $conn, array $filters = []) {
    $where = ["l.status = 'active'"];
    $values = [];
    $types = '';
    $sortOptions = [
        'newest' => 'l.created_at DESC, l.id DESC',
        'price_low' => 'l.asking_price ASC, l.id DESC',
        'price_high' => 'l.asking_price DESC, l.id DESC',
    ];
    $orderBy = $sortOptions[$filters['sort'] ?? 'newest'] ?? $sortOptions['newest'];

    $query = trim($filters['q'] ?? '');
    if ($query !== '') {
        $where[] = '(l.title LIKE ? OR l.description LIKE ? OR l.category LIKE ?)';
        $pattern = '%' . $query . '%';
        array_push($values, $pattern, $pattern, $pattern);
        $types .= 'sss';
    }

    $category = trim($filters['category'] ?? '');
    if ($category !== '') {
        $where[] = 'l.category = ?';
        $values[] = $category;
        $types .= 's';
    }

    if (hmd_is_academic_category($category)) {
        foreach (['course' => 'l.course', 'semester' => 'l.semester'] as $key => $column) {
            $value = trim($filters[$key] ?? '');
            if ($value !== '') {
                $where[] = "{$column} = ?";
                $values[] = $value;
                $types .= 's';
            }
        }
    }

    if (isset($filters['max_price']) && $filters['max_price'] !== '' && is_numeric($filters['max_price'])) {
        $where[] = 'l.asking_price <= ?';
        $values[] = (float) $filters['max_price'];
        $types .= 'd';
    }
    if (isset($filters['condition']) && filter_var($filters['condition'], FILTER_VALIDATE_INT) !== false) {
        $minimumCondition = (int) $filters['condition'];
        if ($minimumCondition >= 1 && $minimumCondition <= 5) {
            $where[] = 'l.condition_score >= ?';
            $values[] = $minimumCondition;
            $types .= 'i';
        }
    }
    if (!empty($filters['bids_open'])) {
        $where[] = 'l.allow_bids = 1';
    }

    $sql = 'SELECT l.* FROM hmd_listings l WHERE ' . implode(' AND ', $where)
        . ' ORDER BY ' . $orderBy . ' LIMIT 100';
    $stmt = $conn->prepare($sql);
    if ($values) {
        $parameters = [$types];
        foreach ($values as $index => &$value) {
            $parameters[] = &$value;
        }
        call_user_func_array([$stmt, 'bind_param'], $parameters);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $listings = [];
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
    $stmt->close();
    return $listings;
}

function hmd_get_listing(mysqli $conn, $listingId) {
    $stmt = $conn->prepare(
        'SELECT l.*, c.customer_fname AS seller_name
         FROM hmd_listings l
         JOIN customer c ON c.customer_id = l.seller_id
         WHERE l.id = ?'
    );
    $stmt->bind_param('i', $listingId);
    $stmt->execute();
    $listing = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $listing ?: null;
}

function hmd_get_listing_categories(mysqli $conn) {
    $result = $conn->query("SELECT DISTINCT category FROM hmd_listings WHERE status = 'active' ORDER BY category");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    if (!in_array('Books', $categories, true)) {
        $categories[] = 'Books';
        sort($categories, SORT_NATURAL | SORT_FLAG_CASE);
    }
    return $categories;
}

function hmd_get_recommendations(mysqli $conn, array $listing, $limit = 4) {
    $listingId = (int) $listing['id'];
    $course = $listing['course'];
    $category = $listing['category'];
    $stmt = $conn->prepare(
        "SELECT l.* FROM hmd_recommendations r
         JOIN hmd_listings l ON l.id = r.similar_id
         WHERE r.listing_id = ? AND l.status = 'active'
         ORDER BY r.score DESC LIMIT ?"
    );
    $stmt->bind_param('ii', $listingId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    if ($rows) {
        return $rows;
    }

    $fallback = $conn->prepare(
        "SELECT * FROM hmd_listings
         WHERE status = 'active' AND id <> ? AND (course = ? OR category = ?)
         ORDER BY created_at DESC LIMIT ?"
    );
    $fallback->bind_param('issi', $listingId, $course, $category, $limit);
    $fallback->execute();
    $result = $fallback->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $fallback->close();
    return $rows;
}

function hmd_place_bid(mysqli $conn, $listingId, $bidderId, $amount) {
    $conn->begin_transaction();
    try {
        $listingStmt = $conn->prepare(
            "SELECT seller_id, price_min, status, allow_bids FROM hmd_listings WHERE id = ? FOR UPDATE"
        );
        $listingStmt->bind_param('i', $listingId);
        $listingStmt->execute();
        $listing = $listingStmt->get_result()->fetch_assoc();
        $listingStmt->close();
        if (!$listing || $listing['status'] !== 'active' || (int) $listing['allow_bids'] !== 1
            || (int) $listing['seller_id'] === (int) $bidderId) {
            throw new RuntimeException('Bidding is not available for this item.');
        }

        $highestStmt = $conn->prepare(
            "SELECT MAX(amount) AS highest_bid FROM hmd_bids WHERE listing_id = ? AND status IN ('pending', 'accepted')"
        );
        $highestStmt->bind_param('i', $listingId);
        $highestStmt->execute();
        $highestBid = $highestStmt->get_result()->fetch_assoc()['highest_bid'];
        $highestStmt->close();

        $minimum = max((float) $listing['price_min'], $highestBid === null ? 0 : (float) $highestBid + 0.01);
        if ($amount < $minimum) {
            throw new RuntimeException('Your bid must be at least ₹' . number_format($minimum, 2) . '.');
        }

        $insert = $conn->prepare(
            "INSERT INTO hmd_bids (listing_id, bidder_id, amount, status) VALUES (?, ?, ?, 'pending')"
        );
        $insert->bind_param('iid', $listingId, $bidderId, $amount);
        $insert->execute();
        $insert->close();
        $conn->commit();
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }
}

function hmd_buy_listing(mysqli $conn, $listingId, $buyerId, $paymentMethod) {
    if (!in_array($paymentMethod, ['cash_on_pickup', 'upi_demo'], true)) {
        throw new RuntimeException('Choose cash on pickup or UPI demo.');
    }
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare(
            "SELECT seller_id, asking_price, status FROM hmd_listings WHERE id = ? FOR UPDATE"
        );
        $stmt->bind_param('i', $listingId);
        $stmt->execute();
        $listing = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$listing || $listing['status'] !== 'active' || (int) $listing['seller_id'] === (int) $buyerId) {
            throw new RuntimeException('This item is no longer available to buy.');
        }

        $sold = $conn->prepare(
            "UPDATE hmd_listings SET status = 'sold' WHERE id = ? AND status = 'active'"
        );
        $sold->bind_param('i', $listingId);
        $sold->execute();
        $changed = $sold->affected_rows === 1;
        $sold->close();
        if (!$changed) {
            throw new RuntimeException('This item is no longer available.');
        }

        $reject = $conn->prepare(
            "UPDATE hmd_bids SET status = 'rejected' WHERE listing_id = ? AND status = 'pending'"
        );
        $reject->bind_param('i', $listingId);
        $reject->execute();
        $reject->close();

        $price = (float) $listing['asking_price'];
        $order = $conn->prepare(
            "INSERT INTO hmd_orders (listing_id, buyer_id, price, source, payment_method, payment_status)
             VALUES (?, ?, ?, 'buy', ?, 'pending')"
        );
        $order->bind_param('iids', $listingId, $buyerId, $price, $paymentMethod);
        $order->execute();
        $order->close();
        $conn->commit();
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }
}

function hmd_buy_cart(mysqli $conn, array $listingIds, $buyerId, $paymentMethod) {
    if (!in_array($paymentMethod, ['cash_on_pickup', 'upi_demo'], true)) {
        throw new RuntimeException('Choose cash on pickup or UPI demo.');
    }

    $listingIds = array_values(array_unique(array_filter(
        array_map('intval', $listingIds),
        static function ($id) { return $id > 0; }
    )));
    sort($listingIds, SORT_NUMERIC);
    if (!$listingIds) {
        throw new RuntimeException('Your cart is empty.');
    }

    $conn->begin_transaction();
    try {
        $select = $conn->prepare(
            'SELECT id, seller_id, asking_price, status
             FROM hmd_listings WHERE id = ? FOR UPDATE'
        );
        $markSold = $conn->prepare(
            "UPDATE hmd_listings SET status = 'sold' WHERE id = ? AND status = 'active'"
        );
        $rejectBids = $conn->prepare(
            "UPDATE hmd_bids SET status = 'rejected' WHERE listing_id = ? AND status = 'pending'"
        );
        $insertOrder = $conn->prepare(
            "INSERT INTO hmd_orders
                (listing_id, buyer_id, price, source, payment_method, payment_status)
             VALUES (?, ?, ?, 'buy', ?, 'pending')"
        );

        foreach ($listingIds as $listingId) {
            $select->bind_param('i', $listingId);
            $select->execute();
            $listing = $select->get_result()->fetch_assoc();
            if (!$listing || $listing['status'] !== 'active'
                || (int) $listing['seller_id'] === (int) $buyerId) {
                throw new RuntimeException('An item in your cart is no longer available. Review your cart and try again.');
            }

            $markSold->bind_param('i', $listingId);
            $markSold->execute();
            if ($markSold->affected_rows !== 1) {
                throw new RuntimeException('An item in your cart was just purchased. Review your cart and try again.');
            }

            $rejectBids->bind_param('i', $listingId);
            $rejectBids->execute();

            $price = (float) $listing['asking_price'];
            $insertOrder->bind_param('iids', $listingId, $buyerId, $price, $paymentMethod);
            $insertOrder->execute();
        }

        $select->close();
        $markSold->close();
        $rejectBids->close();
        $insertOrder->close();
        $conn->commit();
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }
}
