<?php
require_once __DIR__ . '/includes/hmd-admin.php';
$categoryResult = $conn->query(
    "SELECT category, COUNT(*) AS c
     FROM hmd_listings
     WHERE status = 'sold'
     GROUP BY category
     ORDER BY c DESC, category ASC"
);
$categories = [];
$soldCounts = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category'];
    $soldCounts[] = (int) $row['c'];
}
$savingsResult = $conn->query(
    'SELECT l.category, AVG(l.original_price - o.price) AS average_savings
     FROM hmd_orders o JOIN hmd_listings l ON l.id = o.listing_id
     WHERE o.payment_status IN (\'pending\', \'paid\')
     GROUP BY l.category ORDER BY l.category'
);
$savingCategories = [];
$savings = [];
while ($row = $savingsResult->fetch_assoc()) {
    $savingCategories[] = $row['category'];
    $savings[] = round((float) $row['average_savings'], 2);
}
$statusResult = $conn->query(
    "SELECT COUNT(*) AS total, COALESCE(SUM(status = 'sold'), 0) AS sold,
            COALESCE(SUM(status = 'sold') / NULLIF(COUNT(*), 0) * 100, 0) AS conversion_rate
     FROM hmd_listings"
);
$status = $statusResult->fetch_assoc();
$totalListings = (int) $status['total'];
$soldListings = (int) $status['sold'];
$conversionRate = round((float) $status['conversion_rate'], 1);
$unsoldListings = max(0, $totalListings - $soldListings);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Marketplace charts</title>
<link rel="stylesheet" href="../css/hmd-marketplace.css"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head>
<body class="admin-dashboard"><nav class="admin-nav"><a href="post.php">Admin</a><a href="listings.php">Listings</a><a href="orders.php">Orders</a><a href="charts.php">Charts</a><a href="logout.php">Log out</a></nav>
<main class="admin-content"><header class="intro"><span class="eyebrow">Campus marketplace</span><h1>Marketplace analytics</h1><p>These are activity metrics, not verified-payment or page-view analytics. Sample records are synthetic.</p></header>
<div class="chart-grid">
<section class="chart"><h2>Top-selling categories</h2><canvas id="sales"></canvas></section>
<section class="chart"><h2>Average savings versus original price</h2><p class="meta">Includes pending and paid orders; excludes failed or cancelled orders.</p><canvas id="savings"></canvas></section>
<section class="chart"><h2>Conversion rate</h2><p><?= $soldListings ?> sold out of <?= $totalListings ?> listings · <?= $conversionRate ?>%</p><p class="meta">Calculated as sold listings divided by all listings; this does not measure page views.</p><canvas id="sold"></canvas></section>
</div></main>
<script>
new Chart(document.getElementById('sales'),{type:'bar',data:{labels:<?= json_encode($categories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,datasets:[{label:'Sold listings',data:<?= json_encode($soldCounts) ?>,backgroundColor:'#1034a6'}]},options:{responsive:true,plugins:{legend:{display:false}}}});
new Chart(document.getElementById('savings'),{type:'bar',data:{labels:<?= json_encode($savingCategories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,datasets:[{label:'Average savings (INR)',data:<?= json_encode($savings) ?>,backgroundColor:'#718da5'}]},options:{responsive:true}});
new Chart(document.getElementById('sold'),{type:'doughnut',data:{labels:['Sold','Not sold'],datasets:[{data:[<?= $soldListings ?>,<?= $unsoldListings ?>],backgroundColor:['#1034a6','#e2e7ef']}]},options:{responsive:true}});
</script></body></html>
