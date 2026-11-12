<?php
include_once('./includes/headerNav.php');
include "includes/config.php";
?>

<div class="overlay" data-overlay></div>
<!--
    - HEADER
  -->
<header>
  <!-- top head action, search etc in php -->
  <?php require_once './includes/topheadactions.php'; ?>
  <!-- mobile nav in php -->
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
      <?php require_once './includes/categorysidebar.php'; ?>
      <!-- ############################# -->
      <div class="product-box">
        <!-- get id and url for each category and display its dat from table her in this secton -->
        <div class="product-main">
          <h2 class="title">
            جستجو: 
            <?php 
              // Get Item name searching for
              if(isset($_POST['search'])) {
                echo $_POST['search'];
              } elseif(isset($_GET['category_id'])) {
                $category_id = intval($_GET['category_id']);
                $sql = "SELECT name FROM category WHERE id = $category_id";
                $result = $conn->query($sql);
                if ($row = $result->fetch_assoc()) {
                  echo $row['name'];
                }
              }
             ?> 
          </h2>
          <div class="product-grid">
            <!-- display data from table new products -->
            <?php
            if ((isset($_POST['submit']) && !empty($_POST['search'])) || isset($_GET['category_id']) || isset($_GET['catag'])) {
              if (isset($_GET['catag'])) {
                $search_term = mysqli_real_escape_string($conn, $_GET['catag']);
              } elseif (isset($_POST['search'])) {
                $search_term = mysqli_real_escape_string($conn, $_POST['search']);
              } else {
                $search_term = '';
              }
              if (isset($_GET['page'])) {
                $page = $_GET['page'];
              } else {
                $page = 1;
              }
              $limit = 8;
              $offset = ($page - 1) * $limit;

              $search_query = "SELECT * FROM products WHERE 1=1";
              if (!empty($search_term)) {
                $search_term_array = explode(" ", $search_term);
                $search_query .= " AND (";
                for ($i = 0; $i < count($search_term_array); $i++) {
                  if ($i > 0) $search_query .= " OR ";
                  $search_query .= "product_title LIKE '%{$search_term_array[$i]}%'";
                }
                $search_query .= ")";
              }
              if (isset($_GET['category_id'])) {
                $category_id = intval($_GET['category_id']);
                $search_query .= " AND category_id = $category_id";
              }
              $search_query .= " ORDER BY product_id DESC LIMIT $offset, $limit";

              $search_result = $conn->query($search_query);
              $new_product_counter = 1;
              if ($search_result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($search_result)) {
            ?>
              <!-- display all category products -->
              <div class="showcase">
                <div class="showcase-banner">
                  <img src="./admin/upload/<?php echo $row['product_img'] ?>" alt="کت‌های چرمی زمستانی مردانه" width="300" class="product-img default" />
                  <img src="./admin/upload/<?php echo $row['product_img'] ?>" alt="تصاویر نتایج جستجو" width="300" class="product-img hover" />
                  <div class="showcase-actions">
                    <button class="btn-action">
                      <ion-icon name="heart-outline"></ion-icon>
                    </button>
                    <button class="btn-action">
                      <ion-icon name="eye-outline"></ion-icon>
                    </button>
                    <button class="btn-action">
                      <ion-icon name="repeat-outline"></ion-icon>
                    </button>
                    <button class="btn-action">
                      <ion-icon name="bag-add-outline"></ion-icon>
                    </button>
                  </div>
                </div>
                <div class="showcase-content">
                  <a href="./viewdetail.php?id=<?php echo $row['product_id'] ?>&category=<?php echo $row['category_id'] ?>" class="showcase-category">
                    <?php echo $row['product_title'] ?>
                  </a>
                  <a href="./viewdetail.php?id=<?php echo $row['product_id'] ?>&category=<?php echo $row['category_id'] ?>">
                    <h3 class="showcase-title">
                      <?php echo $row['product_desc'] ?>
                    </h3>
                  </a>
                  <div class="showcase-rating">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                  <div class="price-box">
                    <p class="price">
                      <?php echo $row['discounted_price'] ?> تومان
                    </p>
                    <del>
                      <?php echo $row['product_price'] ?> تومان
                    </del>
                  </div>
                </div>
              </div>
            <?php
                  $new_product_counter++;
                }
              } else { 
                echo "<h4 style='color:red; margin-right:8%;border:1px solid aliceblue'>"."هیچ رکوردی یافت نشد"."</h4>";
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<!--Pagination-->
<div class="pag-cont-search">
<?php
if (isset($search_result) && $search_result->num_rows > 0) {
  $total_products = $search_result->num_rows;
  $total_page = ceil($total_products / $limit);
  echo "<div class='pagination'>";  
  for ($i = 1; $i <= $total_page; $i++) {
    $active = ($page == $i) ? "active" : "";
    $url = "search.php?page=$i";
    if (isset($_GET['category_id'])) {
      $url .= "&category_id=" . $_GET['category_id'];
    }
    if (isset($search_term) && !empty($search_term)) {
      $url .= "&search=" . urlencode($search_term);
    }
    echo "<a href='$url' class='$active'>$i</a>";
  }
  echo "</div>";
} else {
  echo "<h4 style='color:red; margin-right:8%;border:1px solid aliceblue'>"."خطا در جستجو"."</h4>";
}
$conn->close();
?>
</div>
</main>
<?php require_once './includes/footer.php'; ?>