<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();

$listingId = filter_var($_POST['listing_id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$listingId) {
    hmd_redirect('my-listings.php', 'Choose a valid listing.');
}
$sellerId = hmd_current_user_id();
$listing = hmd_get_seller_listing($conn, $listingId, $sellerId);
if (!$listing || $listing['status'] !== 'active') {
    hmd_redirect('my-listings.php', 'You can edit only your own active listings.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('edit-listing.php?id=' . $listingId, 'Your form session expired. Please try again.');
    }
    try {
        $data = hmd_validate_listing_input($_POST);
        $prediction = hmd_predict_price($data);
        if (!hmd_update_listing($conn, $listingId, $sellerId, $data, $prediction)) {
            hmd_redirect('edit-listing.php?id=' . $listingId, 'The listing was not updated.');
        }
    } catch (InvalidArgumentException $error) {
        hmd_redirect('edit-listing.php?id=' . $listingId, $error->getMessage());
    } catch (mysqli_sql_exception $error) {
        error_log('HandMeDown listing update database error: ' . $error->getMessage());
        hmd_redirect('edit-listing.php?id=' . $listingId, 'A database error prevented the listing from being updated.');
    } catch (RuntimeException $error) {
        error_log('HandMeDown listing update error: ' . $error->getMessage());
        hmd_redirect('edit-listing.php?id=' . $listingId, $error->getMessage());
    }
    hmd_redirect('my-listings.php', 'Listing updated.', 'success');
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit listing | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
  <header class="intro"><span class="eyebrow">Your listing</span><h1>Edit item details</h1></header>
  <p><a href="my-listings.php">Back to my listings</a></p>
  <?php if ($error !== ''): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
  <form class="hmd-form panel" method="post">
    <input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>">
    <input type="hidden" name="listing_id" value="<?= (int) $listingId ?>">
    <label>Title</label><input name="title" maxlength="200" value="<?= hmd_escape($listing['title']) ?>" required>
    <div class="grid">
      <?php foreach (['category', 'course', 'semester', 'condition_score', 'age_months', 'original_price', 'price_min', 'price_max', 'asking_price', 'pickup_location'] as $field): ?>
        <div><label for="<?= hmd_escape($field) ?>"><?= hmd_escape(ucwords(str_replace('_', ' ', $field))) ?></label>
          <input id="<?= hmd_escape($field) ?>" name="<?= hmd_escape($field) ?>"
            value="<?= hmd_escape($listing[$field]) ?>" required>
        </div>
      <?php endforeach; ?>
    </div>
    <label>Description</label><textarea name="description" required><?= hmd_escape($listing['description']) ?></textarea>
    <label for="story">Why are you passing it on? <span class="field-hint">Share a memory or how another student could use it. (Optional)</span></label><textarea id="story" name="story"><?= hmd_escape($listing['story']) ?></textarea>
    <label><input type="checkbox" name="allow_bids" value="1" style="width:auto" <?= (int) $listing['allow_bids'] === 1 ? 'checked' : '' ?>> Allow bids</label>
    <button type="submit">Save changes</button>
  </form>
  </main>
</body>
</html>
