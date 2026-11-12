<?php
require_once __DIR__ . '/functions/sell-functions.php';

$result = $conn->query(
    'SELECT id, title, story, image, category FROM hmd_listings
     WHERE status IN (\'active\', \'sold\') AND story IS NOT NULL AND story <> \'\'
     ORDER BY created_at DESC'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Item stories | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
    <header class="intro"><span class="eyebrow">Little campus memories</span><h1>More than just stuff</h1><p>Read why a student loved an item, where it went with them, and why they’re ready to pass it along.</p></header>
    <?php if ($result->num_rows === 0): ?>
      <p class="empty-state">Item stories from your campus will appear here.</p>
    <?php else: ?>
      <section class="stories" aria-label="Item stories">
        <?php while ($story = $result->fetch_assoc()): ?>
          <article class="story story-card">
            <a href="listing.php?id=<?= (int) $story['id'] ?>"><img src="admin/upload/<?= rawurlencode($story['image']) ?>" alt="<?= hmd_escape($story['title']) ?>"><span class="story-kicker"><?= hmd_escape($story['category']) ?> · A student's story</span><h2><?= hmd_escape($story['title']) ?></h2></a>
            <blockquote><?= nl2br(hmd_escape($story['story'])) ?></blockquote>
            <a class="story-read-more" href="listing.php?id=<?= (int) $story['id'] ?>">See the item <span aria-hidden="true">&nbsp;→</span></a>
          </article>
        <?php endwhile; ?>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
