<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();

$listingId = filter_var($_POST['listing_id'] ?? $_GET['listing_id'] ?? null, FILTER_VALIDATE_INT);
if (!$listingId) {
    hmd_redirect('my-listings.php', 'Choose a valid listing.');
}
$listing = hmd_get_seller_listing($conn, $listingId, hmd_current_user_id());
if (!$listing) {
    hmd_redirect('my-listings.php', 'You can manage bids only on your own listing.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('accept-bid.php?listing_id=' . $listingId, 'Your form session expired. Please try again.');
    }
    $bidId = filter_var($_POST['bid_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$bidId) {
        hmd_redirect('accept-bid.php?listing_id=' . $listingId, 'Choose a valid bid.');
    }
    try {
        hmd_accept_bid($conn, $listingId, $bidId, hmd_current_user_id());
    } catch (mysqli_sql_exception $error) {
        error_log('HandMeDown bid acceptance database error: ' . $error->getMessage());
        hmd_redirect('accept-bid.php?listing_id=' . $listingId, 'A database error prevented the bid from being accepted.');
    } catch (RuntimeException $error) {
        hmd_redirect('accept-bid.php?listing_id=' . $listingId, $error->getMessage());
    }
    hmd_redirect('my-listings.php', 'Bid accepted and order created.', 'success');
}

$stmt = $conn->prepare(
    'SELECT id, bidder_id, amount, created_at FROM hmd_bids
     WHERE listing_id = ? AND status = \'pending\' ORDER BY amount DESC, created_at ASC'
);
$stmt->bind_param('i', $listingId);
$stmt->execute();
$result = $stmt->get_result();
$bids = [];
while ($row = $result->fetch_assoc()) {
    $bids[] = $row;
}
$stmt->close();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage bids | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
  <header class="intro"><span class="eyebrow">Your listing</span><h1>Bids for <?= hmd_escape($listing['title']) ?></h1></header>
  <p><a href="my-listings.php">Back to my listings</a></p>
  <?php if ($error !== ''): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
  <?php if (!$bids): ?>
    <p class="empty-state">No pending bids yet.</p>
  <?php else: ?>
    <ul class="hmd-list">
      <?php foreach ($bids as $bid): ?>
        <li>
          <strong>₹<?= hmd_escape(number_format((float) $bid['amount'], 2)) ?></strong>
          <form class="hmd-form" method="post" style="display:inline-flex;margin-left:12px">
            <input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>">
            <input type="hidden" name="listing_id" value="<?= (int) $listingId ?>">
            <input type="hidden" name="bid_id" value="<?= (int) $bid['id'] ?>">
            <button type="submit">Accept bid</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  </main>
</body>
</html>
