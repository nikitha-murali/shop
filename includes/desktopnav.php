<!-- desktop navigation -->
<nav class="desktop-navigation-menu">
  <div class="container">
    <ul class="desktop-menu-category-list">
      <li class="menu-category">
        <a href="index.php?id=<?php echo (isset($_SESSION['customer_name'])) ? $_SESSION['id'] : 'unknown'; ?>" class="menu-title">
          Home
        </a>
      </li>
      <li class="menu-category">
        <a href="browse.php" class="menu-title">Browse items</a>
      </li>
      <li class="menu-category">
        <a href="sell.php" class="menu-title">Sell an item</a>
      </li>
      <li class="menu-category">
        <a href="stories.php" class="menu-title">Item stories</a>
      </li>
      <li class="menu-category">
        <a href="image-search.php" class="menu-title">Photo search</a>
      </li>
      <li class="menu-category">
        <a href="contact.php" class="menu-title">Contact us</a>
      </li>
      <!-- Profile Link Setup -->
      <?php if (isset($_SESSION['id'])) { ?>
        <li class="menu-category" style="opacity:1">
          <a href="profile.php?id=<?php echo (isset($_SESSION['customer_name'])) ? $_SESSION['id'] : 'unknown'; ?>" class="menu-title">
            Profile
          </a>
        </li>
      <?php } else { ?>
        <li class="menu-category" style="opacity:0.5">
          <a style="cursor: not-allowed;" href="#?loginfirst" class="menu-title">
            Profile (please log in)
          </a>
        </li>
      <?php } ?>
      <?php if (isset($_SESSION['id'])) { ?>
        <li class="menu-category"><a href="my-listings.php" class="menu-title">My listings</a></li>
        <li class="menu-category"><a href="my-orders.php" class="menu-title">My orders</a></li>
      <?php } ?>
      <!-- Visit Admin Panel After Login -->
      <?php if (isset($_SESSION['logged-in'])) { ?>
        <li class="menu-category">
          <a href="admin/post.php" class="menu-title">
            Admin panel
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>
</nav>