<?php
// get best sellers
$best_sellers = get_best_sellers();

// Get categories
$categories = get_categories();
$outfits = get_outfits_category();
$bundles = get_bundles_category();
?>

<!--
          - SIDEBAR
        -->
<div class="sidebar has-scrollbar" data-mobile-menu>
  <div class="sidebar-category">
    <div class="sidebar-top">
      <h2 class="sidebar-title">Categories</h2>
      <button class="sidebar-close-btn" data-mobile-menu-close-btn>
        <ion-icon name="close-outline"></ion-icon>
      </button>
    </div>
    <ul class="sidebar-menu-category-list">
      <!-- get data from categories table -->
      <?php
      while ($row = mysqli_fetch_assoc($categories)) {
      ?>
        <li class="sidebar-menu-category">
          <button class="sidebar-accordion-menu" data-accordion-btn>
            <div class="menu-title-flex">
              <img src="./images/icons/<?php echo $row['img'] ?>" alt="لباس" width="20" height="20" class="menu-title-img" />
              <p class="menu-title"><?php echo $row['name'] ?></p>
            </div>
            <div>
              <ion-icon name="add-outline" class="add-icon"></ion-icon>
              <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
            </div>
          </button>
          <ul class="sidebar-submenu-category-list" data-accordion>
            <!-- get category data from table -->
            <!-- outfits category -->
            <?php
            if ($row['name'] == "outfits" || $row['name'] == "outfits") {
              while ($outfitsrow = mysqli_fetch_assoc($outfits)) {
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $outfitsrow['outfits_category_name'] ?>" />
                    <button type="submit" name="submit" class="sidebar-submenu-title">
                      <p class="product-name">
                        <?php echo $outfitsrow['outfits_category_name'] ?>
                      </p>
                    </button>
                  </form>    
                </li>
            <?php
              }
            }
            ?>

            
            <!-- bundles category -->
            <?php
            if ($row['name'] == "Bundles" || $row['name'] == "bundles") {
              while ($bundlesrow = mysqli_fetch_assoc($bundles)) {
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $bundlesrow['game_category_name'] ?>" />
                    <button type="submit" name="submit" class="sidebar-submenu-title">
                      <p class="product-name">
                        <?php echo $bundlesrow['game_category_name'] ?>
                      </p>
                    </button>
                  </form>                  
                </li>
            <?php
              }
            }
            ?>
          </ul>
        </li>
      <?php
      }
      ?>
    </ul>
  </div>
  <!-- Best Sellers -->
  <div class="product-showcase">
    <h3 class="showcase-heading">Best sellers</h3>
    <div class="showcase-wrapper">
      <div class="showcase-container">
        <!-- display data from best seller table -->
        <?php
        while ($row = mysqli_fetch_assoc($best_sellers)) {
        ?>
          <div class="showcase">
            <!-- sending two variables in url -->
            <a href="./viewdetail.php?id=<?php echo $row['product_id']; ?>&category=<?php echo $row['category_id']; ?>" class="showcase-img-box">
              <img src="./admin/upload/<?php echo $row['product_img'] ?>" alt="تصویر پرفروش‌ها" width="75" height="75" class="showcase-img" />
            </a>
            <div class="showcase-content">
              <!-- sending two variables in url -->
              <a href="./viewdetail.php?id=<?php echo $row['product_id']; ?>&category=<?php echo $row['category_id']; ?>">
                <h4 class="showcase-title">
                  <?php echo $row['product_title'] ?>
                </h4>
              </a>
              <div class="showcase-rating">
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
              </div>
              <div class="price-box">
                <del><?php echo $row['product_price'] ?> تومان</del>
                <p class="price"><?php echo $row['product_price'] ?> تومان</p>
              </div>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>