<?php
require_once __DIR__ . '/functions/sell-functions.php';

$error = $_GET['error'] ?? '';
$matches = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('image-search.php', 'Your form session expired. Please try again.');
    }
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK
        || $_FILES['image']['size'] > 5 * 1024 * 1024) {
        $error = 'Choose an image up to 5 MB.';
    } else {
        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
        if ($imageInfo === false || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $error = 'Upload a valid JPEG, PNG, or WebP image.';
        } else {
            $serviceUrl = getenv('HMD_IMAGE_SEARCH_URL') ?: 'http://127.0.0.1:8765/search';
            $handle = curl_init($serviceUrl);
            $file = new CURLFile($_FILES['image']['tmp_name'], $mime, 'search-image');
            curl_setopt_array($handle, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => ['image' => $file],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_TIMEOUT => 15,
            ]);
            $response = curl_exec($handle);
            $httpStatus = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $curlError = curl_error($handle);
            curl_close($handle);
            if ($response === false || $httpStatus !== 200) {
                error_log('Image search service request failed: ' . $curlError . ' (HTTP ' . $httpStatus . ')');
                $error = 'Image search is unavailable. Start the local image search service and build item descriptors.';
            } else {
                $result = json_decode($response, true);
                $ids = is_array($result['listing_ids'] ?? null)
                    ? array_values(array_filter(array_map('intval', $result['listing_ids']), static function ($id) { return $id > 0; }))
                    : [];
                if ($ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $types = str_repeat('i', count($ids));
                    $stmt = $conn->prepare(
                        "SELECT * FROM hmd_listings WHERE status = 'active' AND id IN ({$placeholders})"
                    );
                    $parameters = [$types];
                    foreach ($ids as $index => &$id) {
                        $parameters[] = &$id;
                    }
                    call_user_func_array([$stmt, 'bind_param'], $parameters);
                    $stmt->execute();
                    $resultSet = $stmt->get_result();
                    while ($row = $resultSet->fetch_assoc()) {
                        $matches[$row['id']] = $row;
                    }
                    $stmt->close();
                    $matches = array_values(array_filter(array_map(static function ($id) use ($matches) {
                        return $matches[$id] ?? null;
                    }, $ids)));
                }
            }
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Search by photo | HandMeDown</title><link rel="stylesheet" href="css/hmd-marketplace.css"></head>
<body><?php include __DIR__ . '/includes/hmd-header.php'; ?><main class="hmd-content"><header class="intro"><span class="eyebrow">Visual search</span><h1>Find it by photo</h1><p>Upload an item photo to find listings with visually similar colors. Results are look-alikes, not exact product identification.</p></header>
<?php if ($error): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
<form class="hmd-form panel" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>"><label>Choose an item photo (JPEG, PNG, or WebP, max 5 MB)<input type="file" name="image" accept="image/jpeg,image/png,image/webp" required></label><button type="submit">Find similar items</button></form>
<?php if ($matches): ?><h2>Closest visual matches</h2><section class="cards"><?php foreach ($matches as $listing): ?><article><a href="listing.php?id=<?= (int) $listing['id'] ?>"><img src="admin/upload/<?= rawurlencode($listing['image']) ?>" alt="<?= hmd_escape($listing['title']) ?>"><h3><?= hmd_escape($listing['title']) ?></h3></a><p>₹<?= hmd_escape(number_format((float) $listing['asking_price'], 2)) ?></p></article><?php endforeach; ?></section><?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error): ?><p>No image matches yet. Ensure listing image vectors have been built.</p><?php endif; ?>
</main></body></html>
