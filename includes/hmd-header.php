<header class="site-header shell">
  <a class="brand" href="index.php" aria-label="HandMeDown home">
    <img src="images/brand/handmedown-logo.jpg" alt="HandMeDown">
  </a>
  <nav class="site-nav" aria-label="Main navigation">
    <a href="browse.php">Browse</a>
    <a href="sell.php">Sell</a>
    <a href="stories.php">Stories</a>
    <a href="image-search.php">Photo search</a>
    <?php if (!empty($_SESSION['id'])): ?>
      <a href="my-listings.php">My listings</a>
      <a href="my-orders.php">Orders</a>
      <a href="my-bids.php">Bids</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Log out</a>
    <?php else: ?>
      <a href="login.php">Log in</a>
      <a class="nav-cta" href="signup.php">Join your campus</a>
    <?php endif; ?>
  </nav>
</header>
