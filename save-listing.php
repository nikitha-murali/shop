<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    hmd_redirect('sell.php', 'Submit the listing form to continue.');
}
if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    hmd_redirect('sell.php', 'Your form session expired. Please try again.');
}

$uploadedPath = null;
try {
    $data = hmd_validate_listing_input($_POST);
    $prediction = hmd_predict_price($data);

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Choose an image to upload.');
    }
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('The image must be 5 MB or smaller.');
    }

    $imageInfo = getimagesize($_FILES['image']['tmp_name']);
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fileInfo->file($_FILES['image']['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($imageInfo === false || !isset($extensions[$mime])) {
        throw new InvalidArgumentException('Upload a valid JPEG, PNG, or WebP image.');
    }

    $uploadDirectory = __DIR__ . '/admin/upload';
    if (!is_dir($uploadDirectory) || !is_writable($uploadDirectory)) {
        throw new RuntimeException('The shop image upload folder is not writable.');
    }
    $imageName = 'hmd-' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $uploadedPath = $uploadDirectory . '/' . $imageName;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadedPath)) {
        throw new RuntimeException('The image could not be saved.');
    }

    $listingId = hmd_create_listing($conn, hmd_current_user_id(), $data, $prediction, $imageName);
    if (!$listingId) {
        throw new RuntimeException('The listing could not be saved.');
    }
    hmd_redirect('my-listings.php', 'Your listing has been posted.', 'success');
} catch (InvalidArgumentException $error) {
    if ($uploadedPath !== null && is_file($uploadedPath)) {
        unlink($uploadedPath);
    }
    hmd_redirect('sell.php', $error->getMessage());
} catch (mysqli_sql_exception $error) {
    if ($uploadedPath !== null && is_file($uploadedPath)) {
        unlink($uploadedPath);
    }
    error_log('HandMeDown listing database error: ' . $error->getMessage());
    hmd_redirect('sell.php', 'A database error prevented the listing from being saved.');
} catch (RuntimeException $error) {
    if ($uploadedPath !== null && is_file($uploadedPath)) {
        unlink($uploadedPath);
    }
    error_log('HandMeDown listing error: ' . $error->getMessage());
    hmd_redirect('sell.php', $error->getMessage());
}
