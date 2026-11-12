<?php
require_once __DIR__ . '/functions/browse-functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    hmd_redirect('browse.php', 'Your form session expired. Please try again.');
}

$action = $_POST['action'] ?? '';
$listingId = filter_var($_POST['listing_id'] ?? null, FILTER_VALIDATE_INT);
if (empty($_SESSION['hmd_cart']) || !is_array($_SESSION['hmd_cart'])) {
    $_SESSION['hmd_cart'] = [];
}

if ($action === 'remove' && $listingId) {
    unset($_SESSION['hmd_cart'][$listingId]);
    hmd_redirect('hmd-cart.php', 'Item removed from your cart.', 'success');
}

if ($action !== 'add' || !$listingId) {
    hmd_redirect('browse.php', 'Choose a valid item to add to your cart.');
}

$listing = hmd_get_listing($conn, $listingId);
if (!$listing || $listing['status'] !== 'active') {
    hmd_redirect('browse.php', 'This item is no longer available.');
}
if (!empty($_SESSION['id']) && (int) $listing['seller_id'] === hmd_current_user_id()) {
    hmd_redirect('listing.php?id=' . $listingId, 'You cannot add your own listing to the cart.');
}

$_SESSION['hmd_cart'][$listingId] = 1;
hmd_redirect('hmd-cart.php', 'Item added to your cart.', 'success');
