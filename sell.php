<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sell an item | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
  <h1>List an item for sale</h1>
  <p><a href="my-listings.php">My listings</a> · <a href="index.php">Shop home</a></p>
  <?php if ($error !== ''): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
  <p class="note">Condition is rated from 1 (poor) to 5 (like new). Asking price must be within your price range. The price suggestion is indicative, not a guarantee.</p>
  <form class="hmd-form panel" action="save-listing.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>">
    <label for="title">Item title</label><input id="title" name="title" maxlength="200" required>
    <div class="grid">
      <div><label for="category">Category</label><input id="category" name="category" maxlength="100" required></div>
      <div><label for="course">Course</label><input id="course" name="course" maxlength="100" required></div>
      <div><label for="semester">Semester</label><input id="semester" name="semester" maxlength="50" required></div>
      <div><label for="condition_score">Condition (1–5)</label><input id="condition_score" name="condition_score" type="number" min="1" max="5" step="0.1" required></div>
      <div><label for="age_months">Age in months</label><input id="age_months" name="age_months" type="number" min="0" required></div>
      <div><label for="original_price">Original price (₹)</label><input id="original_price" name="original_price" type="number" min="0.01" step="0.01" required></div>
      <div><label for="price_min">Minimum price (₹)</label><input id="price_min" name="price_min" type="number" min="0.01" step="0.01" required></div>
      <div><label for="price_max">Maximum price (₹)</label><input id="price_max" name="price_max" type="number" min="0.01" step="0.01" required></div>
      <div><label for="asking_price">Asking price (₹)</label><input id="asking_price" name="asking_price" type="number" min="0.01" step="0.01" required></div>
      <div><label for="pickup_location">Pickup location</label><input id="pickup_location" name="pickup_location" maxlength="255" required></div>
    </div>
    <label for="description">Description</label><textarea id="description" name="description" required></textarea>
    <label for="story">Item story (optional)</label><textarea id="story" name="story"></textarea>
    <label for="image">Photo (JPEG, PNG, or WebP; up to 5 MB)</label>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required>
    <label><input type="checkbox" name="allow_bids" value="1" style="width:auto"> Allow bids</label>
    <button type="submit">Save listing</button>
  </form>
  </main>
</body>
</html>
