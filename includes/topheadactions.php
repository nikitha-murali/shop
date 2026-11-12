<div class="header-top">
  <div class="container">
    <ul class="header-social-container">
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
    <div class="header-alert-news">
      <p><b>HandMeDown</b> — campus reuse, made easy</p>
    </div>
    <div class="header-top-actions">
      <!-- <select name="currency">
        <option value="usd">Toman</option>
        <option value="eur">EUR €</option>
      </select> -->
      <!-- <select name="language">
        <option value="fa-IR">Persian</option>
        <option value="en-US">English</option>
        <option value="fr">French</option>
      </select> -->
    </div>
  </div>
</div>

<div class="header-main">
  <div class="container">
    <!-- logo section -->
    <a href="./index.php" class="header-logo" aria-label="HandMeDown home">
      <img src="./images/brand/handmedown-logo.jpg" alt="HandMeDown" style="width: 150px; height: 90px; object-fit: contain; mix-blend-mode: multiply;">
    </a>
    <!-- search input -->
    <div class="header-search-container">
      <form class="search-form" method="get" action="./browse.php">
        <input type="search" name="q" class="search-field" placeholder="Search campus listings..." required />
        <button class="search-btn" type="submit" name="submit">
          <ion-icon name="search-outline"></ion-icon>
        </button>
      </form>
    </div>
    <div class="header-user-actions">
      <!-- Logout button -->
      <?php if (isset($_SESSION['id'])) { ?>
        <button id="lg-btn" class="action-btn">
          <a href="logout.php" id="a" role="button">
            <ion-icon name="log-out-outline"></ion-icon>
          </a>
        </button> 
        <!-- TODO: This script does not execute: Work on this, Directly logout user -->
        <script src="./js/logout.js"></script>
      <?php } else { ?>
        <!-- Login Button -->
        <button class="action-btn">
          <a href="./login.php" id="a">
            <ion-icon name="person-outline"></ion-icon>
          </a>
        </button>
      <?php } ?>
      <?php if (isset($_SESSION['id'])) { ?>
        <a class="action-btn" href="./my-orders.php" aria-label="My orders">Orders</a>
        <a class="action-btn" href="./my-bids.php" aria-label="My bids">Bids</a>
      <?php } ?>
    </div>
  </div>
</div>