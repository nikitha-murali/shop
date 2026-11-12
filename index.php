<?php
require_once __DIR__ . '/functions/browse-functions.php';

$latest = hmd_fetch_listings($conn);
$latest = array_slice($latest, 0, 8);
$storiesResult = $conn->query(
    "SELECT id, title, story, image FROM hmd_listings
     WHERE status IN ('active', 'sold') AND story IS NOT NULL AND story <> ''
     ORDER BY created_at DESC LIMIT 3"
);
$recommendations = [];
if (!empty($_SESSION['id'])) {
    $userId = (int) $_SESSION['id'];
    $stmt = $conn->prepare(
        'SELECT course FROM customer WHERE customer_id = ?'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($customer && !empty($customer['course'])) {
        $stmt = $conn->prepare(
            "SELECT * FROM hmd_listings WHERE status = 'active' AND course = ? AND seller_id <> ?
             ORDER BY created_at DESC LIMIT 4"
        );
        $stmt->bind_param('si', $customer['course'], $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recommendations[] = $row;
        }
        $stmt->close();
    }
}

function hmd_render_cards(array $listings) {
    if (!$listings) {
        echo '<p class="empty-state">No listings yet. Be the first to share something useful.</p>';
        return;
    }
    echo '<div class="listing-grid">';
    foreach ($listings as $listing) {
        $id = (int) $listing['id'];
        $title = hmd_escape($listing['title']);
        $image = rawurlencode($listing['image']);
        $price = hmd_escape(number_format((float) $listing['asking_price'], 2));
        $category = hmd_escape($listing['category']);
        echo '<article class="listing-card"><a href="listing.php?id=' . $id . '">';
        echo '<img src="admin/upload/' . $image . '" alt="' . $title . '">';
        echo '<div class="listing-info"><span class="eyebrow">' . $category . '</span><h3>' . $title . '</h3>';
        echo '<p class="price">₹' . $price . '</p></div></a></article>';
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Buy and sell pre-loved items with students on your campus.">
  <title>HandMeDown — campus reuse, made easy</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
<?php include __DIR__ . '/includes/hmd-header.php'; ?>
<main class="home-main">
  <section class="hero"><div class="shell hero-inner">
    <div><span class="eyebrow">Made for campus life</span>
      <h1>Good things deserve a second campus life.</h1>
      <p>Buy, sell, and pass on useful items with students in your campus community. Find a fair deal, arrange pickup, and keep good things in use.</p>
      <div class="actions"><a class="button" href="browse.php">Browse items</a><a class="button secondary" href="sell.php">Sell an item</a></div>
    </div>
    <div class="hero-art"><span class="hero-art-label">A little more life for every find</span><strong>Good finds. Fair prices. A campus that shares.</strong><div class="hero-art-points"><span>Books &amp; notes</span><span>Room essentials</span><span>Pickup on campus</span></div></div>
  </div></section>
  <section class="shell promo-banners" aria-label="Marketplace highlights">
    <article class="promo-banner"><h2>Find useful campus essentials for less.</h2><p>Browse books, electronics, and more shared by students in your community.</p><a href="browse.php">Explore listings</a></article>
    <article class="promo-banner"><h2>Have something others can use?</h2><p>Pass it on, set your price, and arrange a convenient campus pickup.</p><a href="sell.php">Post an item <span class="accent">→</span></a></article>
  </section>
  <section class="content shell">
    <div class="section-heading"><div><span class="eyebrow">Fresh finds</span><h2>Latest listings</h2></div><a href="browse.php">Browse all →</a></div>
    <?php hmd_render_cards($latest); ?>
  </section>
  <?php if ($recommendations): ?><section class="content shell">
    <div class="section-heading"><div><span class="eyebrow">Picked for your course</span><h2>Recommended for you</h2></div></div>
    <?php hmd_render_cards($recommendations); ?>
  </section><?php endif; ?>
  <section class="content shell">
    <div class="section-heading"><div><span class="eyebrow">Passed along with care</span><h2>Latest item stories</h2></div><a href="stories.php">Read all stories →</a></div>
    <?php if ($storiesResult->num_rows === 0): ?><p class="empty-state">Item stories from your campus will appear here.</p>
    <?php else: ?><div class="stories"><?php while ($story = $storiesResult->fetch_assoc()): ?>
      <article class="story story-card"><a href="listing.php?id=<?= (int) $story['id'] ?>"><img src="admin/upload/<?= rawurlencode($story['image']) ?>" alt="<?= hmd_escape($story['title']) ?>"><span class="story-kicker">A note from its previous owner</span><h3><?= hmd_escape($story['title']) ?></h3></a><blockquote><?= nl2br(hmd_escape(mb_strimwidth($story['story'], 0, 220, '…'))) ?></blockquote><a class="story-read-more" href="listing.php?id=<?= (int) $story['id'] ?>">Read this item's story <span aria-hidden="true">&nbsp;→</span></a></article>
    <?php endwhile; ?></div><?php endif; ?>
  </section>
</main>
<footer><div class="shell">HandMeDown <span class="footer-accent">·</span> Reuse more. Waste less. Built for your campus.</div></footer>
</body>
</html>
