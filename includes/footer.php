<!DOCTYPE html>
<html>
<body>
<!--
    - FOOTER
-->
<footer>
  <link rel="stylesheet" href="css/stylefooter.css">

  <div class="footer-category">
    <ul class="footer-nav-list">
      <li class="footer-nav-item">
        <h2>Contact</h2>
      </li>
      <li class="footer-nav-item flex">
        <div class="icon-box">
          <ion-icon name="location-outline"></ion-icon>
        </div>
        <address class="content">
          <?php echo $site_address; ?>
        </address>
      </li>
      <li class="footer-nav-item flex">
        <div class="icon-box">
          <ion-icon name="call-outline"></ion-icon>
        </div>
        <a href="tel:<?php echo $site_contact_num; ?>" class="footer-nav-link"><?php echo $site_contact_num; ?></a>
      </li>
      <li class="footer-nav-item flex">
        <div class="icon-box">
          <ion-icon name="mail-outline"></ion-icon>
        </div>
        <a href="mailto:<?php echo $site_info_email; ?>" class="footer-nav-link"><?php echo $site_info_email; ?></a>
      </li>
    </ul>

    <ul class="footer-nav-list">
      <li class="footer-nav-item">
        <h2 class="nav-title">Follow us</h2>
      </li>
      <li>
        <ul class="social-link">
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">
              <ion-icon name="logo-facebook"></ion-icon>
            </a>
          </li>
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">
              <ion-icon name="logo-twitter"></ion-icon>
            </a>
          </li>
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">
              <ion-icon name="logo-linkedin"></ion-icon>
            </a>
          </li>
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">
              <ion-icon name="logo-instagram"></ion-icon>
            </a>
          </li>
          <li class="footer-nav-item">
            <a href="#" class="footer-nav-link">
              <ion-icon name="logo-youtube"></ion-icon>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <img src="./images/payment.png" alt="Payment methods" class="payment-img">
      <p class="copyright">
        Copyright © <a href="#"><?php echo $_SESSION['web-footer']; ?></a> All rights reserved.
      </p>
    </div>
  </div>
</footer>

<!--
    - custom js link
-->
<script src="./js/notification.js"></script>
<script src="./js/mobilemenu.js"></script>
<script src="./js/datamodal.js"></script>
<script src="./js/dataaccordion.js"></script>
<script src="./ajax/I.js"></script>
<!--
    - ionicon link
-->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="./js/jquery.js" type="text/javascript"></script>
<script src="./js/bootstrap.min.js" type="text/javascript"></script>
<script src="./js/electricshop.js" type="text/javascript"></script>
<script src="./js/main.js" type="text/javascript"></script>




<!-- Dark Mode Toggle -->
<style>
  .dark-mode {
    background-color: #121212 !important;
    color: #e0e0e0 !important;
  }

  .dark-mode * {
    color: #e0e0e0 !important;
    background-color: inherit !important;
    border-color: #555 !important;
  }

  #darkModeToggle {
    position: fixed;
    bottom: 70px; /* تغییر از top به bottom */
    right: 20px; /* تغییر از left به right */
    z-index: 9999;
    background: #fff;
    color: #000;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
  }

  body.dark-mode #darkModeToggle {
    background: #333;
    color: #fff;
  }
</style>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const button = document.createElement("button");
    button.id = "darkModeToggle";
    button.innerHTML = "🌙";
    document.body.appendChild(button);

    // بررسی حالت ذخیره‌شده
    if (localStorage.getItem('dark-mode') === 'true') {
      document.body.classList.add("dark-mode");
      button.innerHTML = "☀️";
    }

    button.addEventListener("click", () => {
      document.body.classList.toggle("dark-mode");
      const isDark = document.body.classList.contains("dark-mode");
      localStorage.setItem('dark-mode', isDark);
      button.innerHTML = isDark ? "☀️" : "🌙";
    });
  });
</script>





</body>
</html>
