<?php
require_once __DIR__ . '/functions/browse-functions.php';
hmd_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    hmd_redirect('browse.php', 'Please submit a valid bid form.');
}
$listingId = filter_var($_POST['listing_id'] ?? null, FILTER_VALIDATE_INT);
$amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);
if (!$listingId || $amount === false || $amount <= 0) {
    hmd_redirect('browse.php', 'Enter a valid bid amount.');
}
try {
    hmd_place_bid($conn, $listingId, hmd_current_user_id(), $amount);
} catch (mysqli_sql_exception $error) {
    error_log('HandMeDown bid database error: ' . $error->getMessage());
    hmd_redirect('listing.php?id=' . $listingId, 'A database error prevented your bid from being saved.');
} catch (RuntimeException $error) {
    hmd_redirect('listing.php?id=' . $listingId, $error->getMessage());
}
hmd_redirect('my-bids.php', 'Your bid was submitted.', 'success');
