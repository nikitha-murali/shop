<?php
require_once __DIR__ . '/includes/hmd-admin.php';
$result = $conn->query(
    'SELECT o.id, o.price, o.source, o.payment_method, o.payment_status, o.created_at, l.title, b.customer_fname AS buyer_name
     FROM hmd_orders o JOIN hmd_listings l ON l.id = o.listing_id
     JOIN customer b ON b.customer_id = o.buyer_id
     ORDER BY o.created_at DESC LIMIT 500'
);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage orders</title>
<style>body{font-family:Arial,sans-serif;margin:24px}nav a{margin-right:14px}table{border-collapse:collapse;width:100%;margin-top:20px}td,th{padding:9px;border:1px solid #ddd;text-align:left}</style></head>
<body><nav><a href="post.php">Admin</a><a href="listings.php">Listings</a><a href="orders.php">Orders</a><a href="charts.php">Charts</a><a href="logout.php">Log out</a></nav>
<h1>Orders</h1><table><thead><tr><th>Order</th><th>Item</th><th>Buyer</th><th>Price</th><th>Source</th><th>Payment option</th><th>Payment state</th><th>Placed</th></tr></thead><tbody>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= (int) $row['id'] ?></td><td><?= hmd_admin_escape($row['title']) ?></td><td><?= hmd_admin_escape($row['buyer_name']) ?></td><td>₹<?= hmd_admin_escape(number_format((float) $row['price'], 2)) ?></td><td><?= hmd_admin_escape($row['source']) ?></td><td><?= hmd_admin_escape(str_replace('_', ' ', $row['payment_method'])) ?></td><td><?= hmd_admin_escape(str_replace('_', ' ', $row['payment_status'])) ?></td><td><?= hmd_admin_escape($row['created_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></body></html>
