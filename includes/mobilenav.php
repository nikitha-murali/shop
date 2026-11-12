<?php
$total_cart_items = 0;
if (isset($_SESSION['mycart'])) {
    $total_cart_items = count($_SESSION['mycart']);
}
?>

<!-- mobile bottom navigation -->
<div class="mobile-bottom-navigation">
  <button class="action-btn" data-mobile-menu-open-btn>
    <ion-icon name="menu-outline"></ion-icon>
  </button>
  <button class="action-btn">
    <a href="./cart.php">
      <ion-icon name="bag-handle-outline"></ion-icon>
    </a>
    <span class="count">
      <?php echo $total_cart_items; ?>
    </span>
  </button>
  <button class="action-btn">
    <a href="./index.php">
      <ion-icon name="home-outline"></ion-icon>
    </a>
  </button>
  <button class="action-btn">
    <ion-icon name="heart-outline"></ion-icon>
    <span class="count">0</span>
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
      <button class="accordion-menu" data-accordion-btn>
        <p class="menu-title"><a href="contact.php" class="menu-title">Contact us</a></p>
        <div>
        </div>
      </button>
    </li>
    <li class="menu-category">
      <button class="accordion-menu" data-accordion-btn>
        <p class="menu-title"><a href="about.php" class="menu-title">About us</a></p>
        <div>
        </div>
      </button>
    </li>
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