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
            if (!function_exists('proc_open')) {
                $error = 'Photo search is unavailable because the server cannot start its image matcher.';
            } else {
                $python = getenv('HMD_PYTHON_PATH') ?: 'python';
                $script = __DIR__ . DIRECTORY_SEPARATOR . 'ml' . DIRECTORY_SEPARATOR . 'search_images.py';
                $descriptors = [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ];
                $environment = getenv();
                if (!is_array($environment)) {
                    $environment = [];
                }
                $environment['HMD_DB_HOST'] = $serverName;
                $environment['HMD_DB_PORT'] = getenv('HMD_DB_PORT') ?: '3306';
                $environment['HMD_DB_USER'] = $dBUsername;
                $environment['HMD_DB_PASSWORD'] = $dBPassword;
                $environment['HMD_DB_NAME'] = $dBName;
                $processWarning = '';
                set_error_handler(static function ($severity, $message) use (&$processWarning) {
                    $processWarning = $message;
                    return true;
                });
                try {
                    $process = proc_open(
                        [$python, $script, $_FILES['image']['tmp_name']],
                        $descriptors,
                        $pipes,
                        __DIR__,
                        $environment
                    );
                } finally {
                    restore_error_handler();
                }
                if (!is_resource($process)) {
                    error_log('Unable to start photo search Python process: ' . $processWarning);
                    $error = 'Photo search could not start. Configure the Python executable for Apache.';
                } else {
                    fclose($pipes[0]);
                    $response = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    $serviceError = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);
                    $exitCode = proc_close($process);
                    $result = json_decode((string) $response, true);
                    if ($exitCode !== 0 || !is_array($result)) {
                        error_log('Photo search failed: ' . substr(trim((string) $serviceError), 0, 1000));
                        $error = 'Photo search could not complete. Check the Python, Pillow, and MySQL connector setup.';
                    } else {
                        $ids = is_array($result['listing_ids'] ?? null)
                            ? array_values(array_unique(array_filter(array_map('intval', $result['listing_ids']), static function ($id) { return $id > 0; })))
                            : [];
                    }
                }

                if (!empty($ids)) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $types = str_repeat('i', count($ids));
                    $stmt = $conn->prepare(
                        "SELECT * FROM hmd_listings WHERE status = 'active' AND category = 'Dresses' AND id IN ({$placeholders})"
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
<body><?php include __DIR__ . '/includes/hmd-header.php'; ?><main class="hmd-content"><header class="intro"><span class="eyebrow">Visual search</span><h1>Find dresses by photo</h1><p>Upload any item photo to browse available dresses, ranked by visual color similarity when listing photos can be compared.</p></header>
<?php if ($error): ?><p class="error"><?= hmd_escape($error) ?></p><?php endif; ?>
<form class="hmd-form panel" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>"><label>Choose an item photo (JPEG, PNG, or WebP, max 5 MB)<input type="file" name="image" accept="image/jpeg,image/png,image/webp" required></label><p class="meta">Your photo is used to rank active dress listings when their images can be compared, and is not saved.</p><button type="submit">Find similar items</button></form>
<?php if ($matches): ?><h2>Dress listings</h2><p class="meta">Illustrated sample covers are included in the results but cannot be ranked by photo similarity.</p><section class="cards"><?php foreach ($matches as $listing): ?><article><a href="listing.php?id=<?= (int) $listing['id'] ?>"><img src="admin/upload/<?= rawurlencode($listing['image']) ?>" alt="<?= hmd_escape($listing['title']) ?>"><h3><?= hmd_escape($listing['title']) ?></h3></a><p>₹<?= hmd_escape(number_format((float) $listing['asking_price'], 2)) ?></p></article><?php endforeach; ?></section><?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error): ?><p>No active dress listings are available yet. Try again when a campus seller lists one.</p><?php endif; ?>
</main></body></html>
