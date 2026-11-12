<?php
require_once __DIR__ . '/includes/hmd-admin.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hmd_admin_verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Form expired. Refresh and try again.';
    } else {
        $listingId = filter_var($_POST['listing_id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        if (!$listingId || !in_array($status, ['active', 'removed'], true)) {
            $error = 'Invalid listing update.';
        } else {
            $stmt = $conn->prepare('UPDATE hmd_listings SET status = ? WHERE id = ? AND status <> \'sold\'');
            $stmt->bind_param('si', $status, $listingId);
            if (!$stmt->execute()) {
                error_log('Admin listing update failed: ' . $stmt->error);
                $error = 'The listing could not be updated.';
            } else {
                header('Location: listings.php');
                exit();
            }
            $stmt->close();
        }
    }
}
$result = $conn->query(
    'SELECT l.id, l.title, l.category, l.status, l.asking_price, l.created_at, c.customer_fname
     FROM hmd_listings l JOIN customer c ON c.customer_id = l.seller_id
     ORDER BY l.created_at DESC LIMIT 500'
);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage listings</title>
<style>body{font-family:Arial,sans-serif;margin:24px}nav a{margin-right:14px}table{border-collapse:collapse;width:100%;margin-top:20px}td,th{padding:9px;border:1px solid #ddd;text-align:left}.error{color:#a00}</style></head>
<body><nav><a href="post.php">Admin</a><a href="listings.php">Listings</a><a href="orders.php">Orders</a><a href="charts.php">Charts</a><a href="logout.php">Log out</a></nav>
<h1>Campus listings</h1><?php if ($error): ?><p class="error"><?= hmd_admin_escape($error) ?></p><?php endif; ?>
<table><thead><tr><th>Title</th><th>Category</th><th>Seller</th><th>Asking price</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= hmd_admin_escape($row['title']) ?></td><td><?= hmd_admin_escape($row['category']) ?></td><td><?= hmd_admin_escape($row['customer_fname']) ?></td><td>₹<?= hmd_admin_escape(number_format((float) $row['asking_price'], 2)) ?></td><td><?= hmd_admin_escape($row['status']) ?></td><td>
<?php if ($row['status'] !== 'sold'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= hmd_admin_escape(hmd_admin_csrf_token()) ?>"><input type="hidden" name="listing_id" value="<?= (int) $row['id'] ?>"><select name="status"><option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="removed" <?= $row['status'] === 'removed' ? 'selected' : '' ?>>Removed</option></select><button>Save</button></form><?php else: ?>—<?php endif; ?>
</td></tr><?php endwhile; ?>
</tbody></table></body></html>
