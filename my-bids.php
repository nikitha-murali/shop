<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();
$stmt = $conn->prepare(
    'SELECT b.amount, b.status, b.created_at, l.id AS listing_id, l.title,
      (SELECT MAX(b2.amount) FROM hmd_bids b2 WHERE b2.listing_id = b.listing_id AND b2.status IN (\'pending\', \'accepted\')) AS highest_bid
     FROM hmd_bids b JOIN hmd_listings l ON l.id = b.listing_id
     WHERE b.bidder_id = ? ORDER BY b.created_at DESC'
);
$userId = hmd_current_user_id();
$stmt->bind_param('i', $userId);
$stmt->execute();
$bids = $stmt->get_result();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>My bids | HandMeDown</title><link rel="stylesheet" href="css/hmd-marketplace.css"></head>
<body><?php include __DIR__ . '/includes/hmd-header.php'; ?><main class="hmd-content"><header class="intro"><span class="eyebrow">Your activity</span><h1>My bids</h1><p>See which items you are leading on and follow the outcome.</p></header>
<?php if ($bids->num_rows === 0): ?><p class="empty-state">You have not placed any bids yet. <a class="details-link" href="browse.php">Explore listings →</a></p><?php else: ?><ul class="hmd-list"><?php while ($bid = $bids->fetch_assoc()): $state = $bid['status'] === 'accepted' ? 'Accepted' : ($bid['status'] === 'rejected' ? 'Outbid / closed' : ((float) $bid['amount'] >= (float) $bid['highest_bid'] ? 'Leading' : 'Outbid')); ?><li><strong><a href="listing.php?id=<?= (int) $bid['listing_id'] ?>"><?= hmd_escape($bid['title']) ?></a></strong><p>₹<?= hmd_escape(number_format((float) $bid['amount'], 2)) ?> · <?= hmd_escape($state) ?></p></li><?php endwhile; ?></ul><?php endif; ?>
</main></body></html><?php $stmt->close(); ?>
