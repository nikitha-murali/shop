<?php include_once('./includes/headerNav.php'); ?>

<div class="overlay" data-overlay></div>
<!--
    - HEADER
  -->

<!-- get tables data from db -->

<header>
  <!-- top head action, search etc in php -->
  <!-- inc/topheadactions.php -->
  <?php require_once './includes/topheadactions.php'; ?>
  <!-- desktop navigation -->
  <!-- inc/desktopnav.php -->
  <?php require_once './includes/desktopnav.php' ?>
  <!-- mobile nav in php -->
  <!-- inc/mobilenav.php -->
  <?php require_once './includes/mobilenav.php'; ?>

</header>

<!--
    - MAIN
  -->

<main>

  <div class="product-container">
    <div class="container">
      <!--
          - SIDEBAR
        -->
      <!-- CATEGORY SIDE BAR MOBILE MENU -->
      <?php require_once './includes/categorysidebar.php' ?>
      <!-- ############################# -->

      <div class="product-box">
        <!-- get id and url for each category and display its dat from table her in this secton -->
        <div class="product-main">

          <!-- contact cards -->
          <!-- MAIL -->
          <div class="contact-card-container mail">
            <div class="contact-icon">
              <a href="#">
                <ion-icon class="contact-icons mail-icon" name="mail-outline"></ion-icon>
              </a>
            </div>
            <div class="contact-details">
              <contact-title>
                <h2>Email</h2>
              </contact-title>
              <p>
                <a href="mailto:<?php echo($site_info_email) ?>"><?php echo($site_info_email) ?></a>
              </p>
            </div>
          </div>
          <!--  -->

          <!-- Whatsapp -->
          <div class="contact-card-container whatsapp">
            <div class="contact-icon">
              <a href="#">
                <ion-icon class="contact-icons whatsapp-icon" name="logo-whatsapp"></ion-icon>
              </a>
            </div>
            <div class="contact-details">
              <contact-title>
                <h2>WhatsApp</h2>
              </contact-title>
              <p>
                <a href="#"><?php echo($site_contact_num) ?></a>
              </p>
            </div>
          </div>
          <!--  -->

          <!-- Location -->
          <div class="contact-card-container location">
            <div class="contact-icon">
              <a href="#">
                <ion-icon class="contact-icons location-icon" name="location-outline"></ion-icon>
              </a>
            </div>
            <div class="contact-details">
              <contact-title>
                <h2>Location</h2>
              </contact-title>
              <p>
              <?php echo($site_address) ?>
              </p>
            </div>
          </div>
          <!--  -->

        </div>

            <!-- Map -->
    	<div class="row">
	<div class="span12">
	<iframe style="width:100%; height:300; border: 0px" scrolling="no" src="https://maps.google.co.uk/maps?f=q&source=s_q&hl=en&geocode=&q=18+California,+Fresno,+CA,+United+States&aq=0&oq=18+California+united+state&sll=39.9589,-120.955336&sspn=0.007114,0.016512&ie=UTF8&hq=&hnear=18,+Fresno,+California+93727,+United+States&t=m&ll=36.732762,-119.695787&spn=0.017197,0.100336&z=14&output=embed">
  </iframe>
    <br />
	<small>
    <a href="https://maps.google.co.uk/maps?f=q&source=embed&hl=en&geocode=&q=18+California,+Fresno,+CA,+United+States&aq=0&oq=18+California+united+state&sll=39.9589,-120.955336&sspn=0.007114,0.016512&ie=UTF8&hq=&hnear=18,+Fresno,+California+93727,+United+States&t=m&ll=36.732762,-119.695787&spn=0.017197,0.100336&z=14" style="color:#0000FF;text-align:left">
    View larger map
    </a>
  </small>
	</div>
	</div>

      </div>
    </div>

  </div>

  <!--
      - TESTIMONIALS, CTA & SERVICE
    -->

  <!--
      - BLOG
    -->

<script src="./js/jquery.js" type="text/javascript"></script>
<script src="./js/bootstrap.min.js" type="text/javascript"></script>

</main>

<?php require_once './includes/footer.php'; ?>