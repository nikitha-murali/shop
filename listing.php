<?php
require_once __DIR__ . '/functions/browse-functions.php';

$listingId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$listing = $listingId ? hmd_get_listing($conn, $listingId) : null;
if (!$listing) {
    http_response_code(404);
    exit('Listing not found.');
}
$bidsStmt = $conn->prepare(
    "SELECT MAX(amount) AS highest_bid, COUNT(*) AS bid_count FROM hmd_bids WHERE listing_id = ? AND status IN ('pending', 'accepted')"
);
$bidsStmt->bind_param('i', $listingId);
$bidsStmt->execute();
$bidSummary = $bidsStmt->get_result()->fetch_assoc();
$bidsStmt->close();
$recommendations = hmd_get_recommendations($conn, $listing);
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= hmd_escape($listing['title']) ?> | HandMeDown</title><link rel="stylesheet" href="css/hmd-marketplace.css"></head>
<body><?php include __DIR__ . '/includes/hmd-header.php'; ?><main class="hmd-content">
<p><a href="browse.php">← Back to browse</a></p>
<?php if ($error !== ''): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
<section class="item">
  <img src="admin/upload/<?= rawurlencode($listing['image']) ?>" alt="<?= hmd_escape($listing['title']) ?>">
  <div><h1><?= hmd_escape($listing['title']) ?></h1>
    <p><?= hmd_escape($listing['category']) ?> · <?= hmd_escape($listing['course']) ?> · <?= hmd_escape($listing['semester']) ?></p>
    <p><?= nl2br(hmd_escape($listing['description'])) ?></p><p>Pickup: <?= hmd_escape($listing['pickup_location']) ?></p>
    <p>Condition: <?= hmd_escape($listing['condition_score']) ?>/5 · Age: <?= (int) $listing['age_months'] ?> months</p>
    <p>Original price: ₹<?= hmd_escape(number_format((float) $listing['original_price'], 2)) ?></p>
    <p class="box">Asking ₹<?= hmd_escape(number_format((float) $listing['asking_price'], 2)) ?> · Indicative price estimate: <?= $listing['predicted_price'] === null ? 'not available' : '₹' . hmd_escape(number_format((float) $listing['predicted_price'], 2)) ?> · <?= hmd_escape($listing['deal_badge'] ?? '') ?><br><small>Estimate only, not a guaranteed sale price.</small></p>
    <?php if ($listing['story']): ?><blockquote><?= nl2br(hmd_escape($listing['story'])) ?></blockquote><?php endif; ?>
    <p>Seller: <?= hmd_escape($listing['seller_name']) ?></p>
    <a class="btn" href="buy.php?id=<?= (int) $listing['id'] ?>">Buy now</a>
    <?php if ((int) $listing['allow_bids'] === 1): ?>
      <div class="box"><p>Highest bid: <?= $bidSummary['highest_bid'] === null ? 'No bids yet' : '₹' . hmd_escape(number_format((float) $bidSummary['highest_bid'], 2)) ?> · <?= (int) $bidSummary['bid_count'] ?> bids</p>
        <?php if (!empty($_SESSION['id']) && (int) $_SESSION['id'] !== (int) $listing['seller_id']): ?>
        <form action="place-bid.php" method="post"><input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>"><input type="hidden" name="listing_id" value="<?= (int) $listing['id'] ?>"><label>Bid (minimum ₹<?= hmd_escape(number_format((float) $listing['price_min'], 2)) ?>)<input name="amount" type="number" min="<?= hmd_escape($listing['price_min']) ?>" step="0.01" required></label><button class="btn">Place bid</button></form>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php if ($recommendations): ?><h2>Similar items</h2><section class="cards"><?php foreach ($recommendations as $similar): ?><article><a href="listing.php?id=<?= (int) $similar['id'] ?>"><img src="admin/upload/<?= rawurlencode($similar['image']) ?>" alt="<?= hmd_escape($similar['title']) ?>"><h3><?= hmd_escape($similar['title']) ?></h3></a><p>₹<?= hmd_escape(number_format((float) $similar['asking_price'], 2)) ?></p></article><?php endforeach; ?></section><?php endif; ?>
</main></body></html>
