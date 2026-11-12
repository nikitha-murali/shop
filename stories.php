<?php
require_once __DIR__ . '/functions/sell-functions.php';

$result = $conn->query(
    'SELECT id, title, story, image FROM hmd_listings
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
    <header class="intro"><span class="eyebrow">Passed along with care</span><h1>Stories behind the stuff</h1><p>Every useful thing has a next chapter. Meet the items finding a new home on campus.</p></header>
    <?php if ($result->num_rows === 0): ?>
      <p class="empty-state">Item stories from your campus will appear here.</p>
    <?php else: ?>
      <section class="stories" aria-label="Item stories">
        <?php while ($story = $result->fetch_assoc()): ?>
          <article class="story">
            <a href="listing.php?id=<?= (int) $story['id'] ?>"><img src="admin/upload/<?= rawurlencode($story['image']) ?>" alt="<?= hmd_escape($story['title']) ?>"><h2><?= hmd_escape($story['title']) ?></h2></a>
            <p><?= nl2br(hmd_escape($story['story'])) ?></p>
          </article>
        <?php endwhile; ?>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
