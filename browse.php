<?php
require_once __DIR__ . '/functions/browse-functions.php';

$filters = [
    'q' => trim($_GET['q'] ?? ''),
    'category' => trim($_GET['category'] ?? ''),
    'course' => trim($_GET['course'] ?? ''),
    'semester' => trim($_GET['semester'] ?? ''),
    'max_price' => trim($_GET['max_price'] ?? ''),
    'condition' => is_string($_GET['condition'] ?? null) ? trim($_GET['condition']) : '',
    'bids_open' => isset($_GET['bids_open']),
    'sort' => is_string($_GET['sort'] ?? null) ? $_GET['sort'] : 'newest',
];
$sortOptions = ['newest' => 'Newest first', 'price_low' => 'Price: low to high', 'price_high' => 'Price: high to low'];
if (!isset($sortOptions[$filters['sort']])) {
    $filters['sort'] = 'newest';
}
$hasActiveFilters = $filters['q'] !== '' || $filters['category'] !== '' || $filters['course'] !== ''
    || $filters['semester'] !== '' || $filters['max_price'] !== '' || $filters['condition'] !== ''
    || $filters['bids_open'];
$listings = hmd_fetch_listings($conn, $filters);
$categories = hmd_get_listing_categories($conn);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Browse campus listings | HandMeDown</title><link rel="stylesheet" href="css/hmd-marketplace.css"></head>
<body>
<?php include __DIR__ . '/includes/hmd-header.php'; ?>
<div class="shell">
  <main>
    <section class="intro"><span class="eyebrow">Campus marketplace</span><h1>Find your next good thing.</h1><p>Browse clear product photos, compare prices, and find something you’ll love.</p></section>
    <form class="filter-panel" method="get">
      <div class="filter-grid">
        <div class="field"><label for="q">What are you looking for?</label><input id="q" name="q" type="search" placeholder="Try a bag, shoes, or watch" value="<?= hmd_escape($filters['q']) ?>"></div>
        <div class="field"><label for="category">Item type</label><select id="category" name="category"><option value="">Everything</option><?php foreach ($categories as $category): ?><option value="<?= hmd_escape($category) ?>" <?= $category === $filters['category'] ? 'selected' : '' ?>><?= hmd_escape($category) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label for="course">Course</label><input id="course" name="course" placeholder="Any course" value="<?= hmd_escape($filters['course']) ?>"></div>
        <div class="field"><label for="semester">Semester</label><input id="semester" name="semester" inputmode="numeric" placeholder="Any semester" value="<?= hmd_escape($filters['semester']) ?>"></div>
        <div class="field"><label for="max_price">Your budget (up to ₹)</label><input id="max_price" name="max_price" type="number" min="0" step="0.01" placeholder="Any price" value="<?= hmd_escape($filters['max_price']) ?>"></div>
        <div class="field"><label for="condition">Condition (minimum rating)</label><select id="condition" name="condition"><option value="">Any condition</option><?php foreach ([1 => '1 star · Poor or better', 2 => '2 stars · Fair or better', 3 => '3 stars · Good or better', 4 => '4 stars · Very good or better', 5 => '5 stars · Like new'] as $condition => $conditionLabel): ?><option value="<?= $condition ?>" <?= $filters['condition'] === (string) $condition ? 'selected' : '' ?>><?= hmd_escape($conditionLabel) ?></option><?php endforeach; ?></select></div>
      </div>
      <div class="filter-bottom">
        <label class="check-label"><input type="checkbox" name="bids_open" value="1" <?= $filters['bids_open'] ? 'checked' : '' ?>> Show items open to offers</label>
        <div class="filter-actions"><button class="button" type="submit">Find items</button><?php if ($hasActiveFilters): ?><a class="clear-link" href="browse.php">Start fresh</a><?php endif; ?></div>
      </div>
    </form>
    <div class="results-head">
      <p class="results-count" aria-live="polite"><?= count($listings) ?> <?= count($listings) === 1 ? 'campus find' : 'campus finds' ?><?= $hasActiveFilters ? ' for you' : '' ?></p>
      <form class="sort-field" method="get" aria-label="Sort listings">
        <?php foreach (['q', 'category', 'course', 'semester', 'max_price', 'condition'] as $filterKey): ?><?php if ($filters[$filterKey] !== ''): ?><input type="hidden" name="<?= hmd_escape($filterKey) ?>" value="<?= hmd_escape($filters[$filterKey]) ?>"><?php endif; ?><?php endforeach; ?>
        <?php if ($filters['bids_open']): ?><input type="hidden" name="bids_open" value="1"><?php endif; ?>
        <label for="sort">Sort by</label>
        <select id="sort" name="sort" onchange="this.form.submit()"><?php foreach ($sortOptions as $sortKey => $sortLabel): ?><option value="<?= hmd_escape($sortKey) ?>" <?= $filters['sort'] === $sortKey ? 'selected' : '' ?>><?= hmd_escape($sortLabel) ?></option><?php endforeach; ?></select>
      </form>
    </div>
    <?php if (!$listings): ?>
      <section class="empty-state"><h2>No matching items yet</h2><p>Try a different search or clear some filters to see more campus finds.</p><a class="button" href="browse.php">Show all items</a></section>
    <?php else: ?>
      <section class="product-grid" aria-label="Campus listings">
        <?php foreach ($listings as $listing): ?>
          <article class="listing-card">
            <a class="image-link" href="listing.php?id=<?= (int) $listing['id'] ?>"><img src="admin/upload/<?= rawurlencode($listing['image']) ?>" alt="<?= hmd_escape($listing['title']) ?>" loading="lazy"></a>
            <div class="card-content">
              <div class="card-top"><span class="tag"><?= hmd_escape($listing['category']) ?></span><?php if ((int) $listing['allow_bids'] === 1): ?><span class="tag bidding">Bids open</span><?php endif; ?></div>
              <h2><a href="listing.php?id=<?= (int) $listing['id'] ?>"><?= hmd_escape($listing['title']) ?></a></h2>
              <p class="meta"><?= hmd_escape($listing['course']) ?> · Semester <?= hmd_escape($listing['semester']) ?></p>
              <?php hmd_render_condition_rating($listing['condition_score']); ?>
              <div class="card-footer"><p class="price">₹<?= hmd_escape(number_format((float) $listing['asking_price'], 2)) ?></p><?php if ($listing['deal_badge']): ?><span class="tag deal-badge"><?= hmd_escape($listing['deal_badge']) ?></span><?php endif; ?></div>
              <a class="details-link" href="listing.php?id=<?= (int) $listing['id'] ?>">View item <span aria-hidden="true">&nbsp;→</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </main>
</div>
</body></html>
