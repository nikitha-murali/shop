<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remove') {
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('my-listings.php', 'Your form session expired. Please try again.');
    }
    $listingId = filter_var($_POST['listing_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$listingId || !hmd_remove_listing($conn, $listingId, hmd_current_user_id())) {
        hmd_redirect('my-listings.php', 'The listing could not be removed.');
    }
    hmd_redirect('my-listings.php', 'Listing removed.', 'success');
}

$listings = hmd_get_seller_listings($conn, hmd_current_user_id());
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My listings | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
  <header class="intro"><span class="eyebrow">Your activity</span><h1>My listings</h1><p>Manage the items you have shared with campus.</p></header>
  <?php if ($error !== ''): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
  <?php if ($success !== ''): ?><p class="success"><?= hmd_escape($success) ?></p><?php endif; ?>
  <?php if (!$listings): ?>
    <p class="empty-state">You have not posted any active listings. <a class="details-link" href="sell.php">List your first item →</a></p>
  <?php else: ?>
    <ul class="hmd-list">
      <?php foreach ($listings as $listing): ?>
        <li>
          <strong><?= hmd_escape($listing['title']) ?></strong>
          — ₹<?= hmd_escape(number_format((float) $listing['asking_price'], 2)) ?>
          — <?= hmd_escape($listing['status']) ?>
          <?php if ($listing['status'] === 'active'): ?>
            <a href="edit-listing.php?id=<?= (int) $listing['id'] ?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Remove this listing?')">
              <input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>">
              <input type="hidden" name="listing_id" value="<?= (int) $listing['id'] ?>">
              <input type="hidden" name="action" value="remove">
              <button type="submit">Remove</button>
            </form>
          <?php endif; ?>
          <?php if ($listing['status'] === 'active'): ?>
            <a href="accept-bid.php?listing_id=<?= (int) $listing['id'] ?>">Manage bids</a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  </main>
</body>
</html>
