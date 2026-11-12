<!-- mobile bottom navigation -->
<div class="mobile-bottom-navigation">
  <button class="action-btn" data-mobile-menu-open-btn>
    <ion-icon name="menu-outline"></ion-icon>
  </button>
  <button class="action-btn">
    <a href="./browse.php">
      <ion-icon name="search-outline"></ion-icon>
    </a>
  </button>
  <button class="action-btn">
    <a href="./index.php">
      <ion-icon name="home-outline"></ion-icon>
    </a>
  </button>
  <button class="action-btn">
    <a href="./sell.php">
      <ion-icon name="add-circle-outline"></ion-icon>
    </a>
  </button>
  <button class="action-btn" data-mobile-menu-open-btn>
    <ion-icon name="grid-outline"></ion-icon>
  </button>
</div>

<nav class="mobile-navigation-menu has-scrollbar" data-mobile-menu>
  <div class="menu-top">
    <h2 class="menu-title">Menu</h2>
    <button class="menu-close-btn" data-mobile-menu-close-btn>
      <ion-icon name="close-outline"></ion-icon>
    </button>
  </div>
  <ul class="mobile-menu-category-list">
    <li class="menu-category">
      <a href="./index.php" class="menu-title">Home</a>
    </li>
    <li class="menu-category">
      <a href="browse.php" class="menu-title">Browse items</a>
    </li>
    <li class="menu-category">
      <a href="sell.php" class="menu-title">Sell an item</a>
    </li>
    <li class="menu-category"><a href="stories.php" class="menu-title">Item stories</a></li>
    <li class="menu-category"><a href="image-search.php" class="menu-title">Photo search</a></li>
    <?php if (isset($_SESSION['id'])): ?>
      <li class="menu-category"><a href="my-listings.php" class="menu-title">My listings</a></li>
      <li class="menu-category"><a href="my-orders.php" class="menu-title">My orders</a></li>
      <li class="menu-category"><a href="my-bids.php" class="menu-title">My bids</a></li>
    <?php endif; ?>
  </ul>
  <div class="menu-bottom">
    <ul class="menu-social-container">
      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-facebook"></ion-icon>
        </a>
      </li>
      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-twitter"></ion-icon>
        </a>
      </li>
      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-instagram"></ion-icon>
        </a>
      </li>
      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-linkedin"></ion-icon>
        </a>
      </li>
    </ul>
  </div>
</nav>