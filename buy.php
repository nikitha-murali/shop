<?php
require_once __DIR__ . '/functions/browse-functions.php';
hmd_require_login();

$listingId = filter_var($_POST['listing_id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
$listing = $listingId ? hmd_get_listing($conn, $listingId) : null;
if (!$listing || $listing['status'] !== 'active') {
    hmd_redirect('browse.php', 'This listing is not available.');
}
if ((int) $listing['seller_id'] === hmd_current_user_id()) {
    hmd_redirect('listing.php?id=' . $listingId, 'You cannot buy your own listing.');
}

$error = $_GET['error'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('buy.php?id=' . $listingId, 'Your form session expired. Please try again.');
    }
    try {
        $paymentMethod = $_POST['payment_method'] ?? '';
        hmd_buy_listing($conn, $listingId, hmd_current_user_id(), $paymentMethod);
        $message = $paymentMethod === 'upi_demo'
            ? 'Demo order placed. No UPI transfer was made; arrange payment and pickup with the seller.'
            : 'Order placed. Arrange cash payment and pickup with the seller.';
        hmd_redirect('my-orders.php', $message, 'success');
    } catch (mysqli_sql_exception $error) {
        error_log('HandMeDown purchase database error: ' . $error->getMessage());
        $error = 'A database error prevented checkout from starting.';
    } catch (RuntimeException $error) {
        $error = $error->getMessage();
    }
}
?>
<!DOCTYPE html><html lang="en" dir="ltr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Checkout | HandMeDown</title><link rel="stylesheet" href="css/hmd-marketplace.css"></head>
<body><?php include __DIR__ . '/includes/hmd-header.php'; ?><main class="hmd-content"><header class="intro"><span class="eyebrow">One more step</span><h1>Checkout</h1></header><p><a href="listing.php?id=<?= (int) $listingId ?>">← Back to item</a></p>
<?php if ($error): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
<section class="box"><h2><?= hmd_escape($listing['title']) ?></h2><p class="price">Total: ₹<?= hmd_escape(number_format((float) $listing['asking_price'], 2)) ?></p>
<form class="hmd-form" method="post"><input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>"><input type="hidden" name="listing_id" value="<?= (int) $listingId ?>">
<label><input type="radio" name="payment_method" value="cash_on_pickup" required> Cash on pickup</label>
<label><input type="radio" name="payment_method" value="upi_demo" required> UPI (demo)</label>
<p class="meta">UPI demo only records a sample order. It does not open a payment app or transfer money.</p><button type="submit">Place order</button></form>
</section></main></body></html>
