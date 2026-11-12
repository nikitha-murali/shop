<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();
$stmt = $conn->prepare(
    'SELECT o.id, o.price, o.source, o.payment_method, o.payment_status, o.created_at, l.title, l.pickup_location
     FROM hmd_orders o JOIN hmd_listings l ON l.id = o.listing_id
     WHERE o.buyer_id = ? ORDER BY o.created_at DESC'
);
$userId = hmd_current_user_id();
$stmt->bind_param('i', $userId);
$stmt->execute();
$orders = $stmt->get_result();
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>My orders | HandMeDown</title><link rel="stylesheet" href="css/hmd-marketplace.css"></head>
<body><?php include __DIR__ . '/includes/hmd-header.php'; ?><main class="hmd-content"><header class="intro"><span class="eyebrow">Your activity</span><h1>My orders</h1><p>Keep track of purchases and pickup details.</p></header>
<?php if ($success): ?><p class="empty-state"><?= hmd_escape($success) ?></p><?php endif; ?>
<?php if ($orders->num_rows === 0): ?><p class="empty-state">No orders yet. <a class="details-link" href="browse.php">Browse campus finds →</a></p><?php else: ?><ul class="hmd-list"><?php while ($order = $orders->fetch_assoc()): $paymentLabel = $order['payment_method'] === 'upi_demo' ? 'UPI demo (no transfer)' : str_replace('_', ' ', $order['payment_method']); ?><li><strong><?= hmd_escape($order['title']) ?></strong><p>₹<?= hmd_escape(number_format((float) $order['price'], 2)) ?> · <?= hmd_escape($order['source']) ?> · <?= hmd_escape($paymentLabel) ?> (<?= hmd_escape(str_replace('_', ' ', $order['payment_status'])) ?>)</p><p class="meta">Pickup: <?= hmd_escape($order['pickup_location']) ?></p></li><?php endwhile; ?></ul><?php endif; ?>
</main></body></html><?php $stmt->close(); ?>
