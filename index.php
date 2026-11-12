<?php
include_once('./includes/headerNav.php');

// Price formatter
function formatPrice($price) {
    return number_format($price, 0, '.', ',') . ' IRR';
}

// Get all banner products
$banner_products = get_banner_details();
if (!$banner_products) {
    die("Error loading banner data.");
}

// Get all category bar products
$catgeory_bar_products = get_category_bar_products();
if (!$catgeory_bar_products) {
    die("Error loading category data.");
}

// Get categories
$categories = get_categories();
$outfits = get_outfits_category();
$bundles = get_bundles_category();

// Get all new arrivals
$new_arrivals1 = get_new_arrivals();
$new_arrivals2 = get_new_arrivals();

// Get trending products
$trending_products1 = get_trending_products();
$trending_products2 = get_trending_products();

// Get top rated products
$top_rated_products1 = get_top_rated_products();
$top_rated_products2 = get_top_rated_products();
?>

<div class="overlay" data-overlay></div>

<header>
    <?php require_once './includes/topheadactions.php'; ?>
    <?php require_once './includes/desktopnav.php' ?>
    <?php require_once './includes/mobilenav.php'; ?>
</header>

<main>
    <div class="banner">
        <div class="container">
            <div class="slider-container has-scrollbar">
                <?php
                if (mysqli_num_rows($banner_products) > 0) {
                    while ($row = mysqli_fetch_assoc($banner_products)) {
                        $imagePath = "images/carousel/" . $row['banner_image'];
                        if (file_exists($imagePath)) {
                ?>
                            <div class="slider-item">
                                <img src="<?php echo $imagePath; ?>" alt="Latest gaming packages" class="banner-img" />
                            </div>
                <?php
                        } else {
                            echo "Banner image not found: " . $imagePath;
                        }
                    }
                } else {
                    echo "No banner found.";
                }
                ?>
            </div>
        </div>
    </div>

    <div class="category">
        <div class="container">
            <div class="category-item-container has-scrollbar">
                <?php
                while ($row = mysqli_fetch_assoc($catgeory_bar_products)) {
                ?>
                    <div class="category-item">
                        <div class="category-img-box">
                            <img src="./images/icons/<?php echo $row['img'] ?>" alt="Game logo" width="30" />
                        </div>
                        <div class="category-content-box">
                            <div class="category-content-flex">
                                <h3 class="category-item-title"><?php echo $row['name'] ?></h3>
                                <p class="category-item-amount">(<?php echo $row['category_quantity'] ?>)</p>
                            </div>
                            <form class="search-form" method="get" action="./search.php">
                                <input type="hidden" name="category_id" value="<?php echo $row['id'] ?>" />
                                <button type="submit" class="sidebar-submenu-title">
                                    <p class="category-btn">View packages</p>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

    <div class="product-container">
        <div class="container">
            <?php require_once './includes/categorysidebar.php' ?>
            <div class="product-box">
                <div class="product-minimal">
                    <div class="product-showcase">
                        <h2 class="title">New packages</h2>
                        <div class="showcase-wrapper has-scrollbar">
                            <div class="showcase-container">
                                <?php
                                $itemID;
                                $counter = 0;
                                while ($row1 = mysqli_fetch_assoc($new_arrivals1)) {
                                    if ($counter == 4) {
                                        break;
                                    }
                                ?>
                                    <div class="showcase">
                                        <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>" class="showcase-img-box">
                                            <img src="./admin/upload/<?php echo $row1['product_img']; ?>" alt="پکیج جم کلش آو کلنز" width="70" class="showcase-img" />
                                        </a>
                                        <div class="showcase-content">
                                            <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>">
                                                <h4 class="showcase-title"><?php echo $row1['product_title']; ?></h4>
                                            </a>
                                            <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>" class="showcase-category">New!</a>
                                            <div class="price-box">
                                                <p class="price"><?php echo formatPrice($row1['discounted_price']); ?></p>
                                                <del><?php echo formatPrice($row1['product_price']); ?></del>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $itemID = $row1['product_id'];
                                    $counter += 1;
                                }
                                ?>
                            </div>
                            <div class="showcase-container">
                                <?php
                                $counter = 0;
                                while ($row2 = mysqli_fetch_assoc($new_arrivals2)) {
                                    if ($counter == 4) {
                                        break;
                                    }
                                    if ($row2['product_id'] > $itemID) {
                                ?>
                                    <div class="showcase">
                                        <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>" class="showcase-img-box">
                                            <img src="./admin/upload/<?php echo $row2['product_img']; ?>" alt="پکیج یوسی پابجی" class="showcase-img" width="70" />
                                        </a>
                                        <div class="showcase-content">
                                            <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>">
                                                <h4 class="showcase-title"><?php echo $row2['product_title']; ?></h4>
                                            </a>
                                            <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>" class="showcase-category">New!</a>
                                            <div class="price-box">
                                                <p class="price"><?php echo formatPrice($row2['discounted_price']); ?></p>
                                                <del><?php echo formatPrice($row2['product_price']); ?></del>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                        $counter += 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="product-showcase">
                        <h2 class="title">Popular</h2>
                        <div class="showcase-wrapper has-scrollbar">
                            <div class="showcase-container">
                                <?php
                                $itemID;
                                $counter = 0;
                                while ($row1 = mysqli_fetch_assoc($trending_products1)) {
                                    if ($counter == 4) {
                                        break;
                                    }
                                ?>
                                    <div class="showcase">
                                        <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>" class="showcase-img-box">
                                            <img src="./admin/upload/<?php echo $row1['product_img']; ?>" alt="پکیج سی‌پی کالاف دیوتی" width="70" class="showcase-img" />
                                        </a>
                                        <div class="showcase-content">
                                            <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>">
                                                <h4 class="showcase-title"><?php echo $row1['product_title']; ?></h4>
                                            </a>
                                            <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>" class="showcase-category">Popular!</a>
                                            <div class="price-box">
                                                <p class="price"><?php echo formatPrice($row1['discounted_price']); ?></p>
                                                <del><?php echo formatPrice($row1['product_price']); ?></del>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $itemID = $row1['product_id'];
                                    $counter += 1;
                                }
                                ?>
                            </div>
                            <div class="showcase-container">
                                <?php
                                $counter = 0;
                                while ($row2 = mysqli_fetch_assoc($trending_products2)) {
                                    if ($counter == 4) {
                                        break;
                                    }
                                    if ($row2['product_id'] > $itemID) {
                                ?>
                                    <div class="showcase">
                                        <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>" class="showcase-img-box">
                                            <img src="./admin/upload/<?php echo $row2['product_img']; ?>" alt="پکیج جم فری فایر" class="showcase-img" width="70" />
                                        </a>
                                        <div class="showcase-content">
                                            <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>">
                                                <h4 class="showcase-title"><?php echo $row2['product_title']; ?></h4>
                                            </a>
                                            <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>" class="showcase-category">Popular!</a>
                                            <div class="price-box">
                                                <p class="price"><?php echo formatPrice($row2['discounted_price']); ?></p>
                                                <del><?php echo formatPrice($row2['product_price']); ?></del>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                        $counter += 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="product-showcase">
                        <h2 class="title">Top packages</h2>
                        <div class="showcase-wrapper has-scrollbar">
                            <div class="showcase-container">
                                <?php
                                $itemID;
                                $counter = 0;
                                while ($row1 = mysqli_fetch_assoc($top_rated_products1)) {
                                    if ($counter == 4) {
                                        break;
                                    }
                                ?>
                                    <div class="showcase">
                                        <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>" class="showcase-img-box">
                                            <img src="./admin/upload/<?php echo $row1['product_img']; ?>" alt="پکیج والورانت پوینت" width="70" class="showcase-img" />
                                        </a>
                                        <div class="showcase-content">
                                            <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>">
                                                <h4 class="showcase-title"><?php echo $row1['product_title']; ?></h4>
                                            </a>
                                            <a href="./viewdetail.php?id=<?php echo $row1['product_id'] ?>&category=<?php echo $row1['category_id'] ?>" class="showcase-category">Top!</a>
                                            <div class="price-box">
                                                <p class="price"><?php echo formatPrice($row1['discounted_price']); ?></p>
                                                <del><?php echo formatPrice($row1['product_price']); ?></del>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $itemID = $row1['product_id'];
                                    $counter += 1;
                                }
                                ?>
                            </div>
                            <div class="showcase-container">
                                <?php
                                $counter = 0;
                                while ($row2 = mysqli_fetch_assoc($top_rated_products2)) {
                                    if ($counter == 4) {
                                        break;
                                    }
                                    if ($row2['product_id'] > $itemID) {
                                ?>
                                    <div class="showcase">
                                        <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>" class="showcase-img-box">
                                            <img src="./admin/upload/<?php echo $row2['product_img']; ?>" alt="پکیج جم براول استارز" class="showcase-img" width="70" />
                                        </a>
                                        <div class="showcase-content">
                                            <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>">
                                                <h4 class="showcase-title"><?php echo $row2['product_title']; ?></h4>
                                            </a>
                                            <a href="./viewdetail.php?id=<?php echo $row2['product_id'] ?>&category=<?php echo $row2['category_id'] ?>" class="showcase-category">Top!</a>
                                            <div class="price-box">
                                                <p class="price"><?php echo formatPrice($row2['discounted_price']); ?></p>
                                                <del><?php echo formatPrice($row2['product_price']); ?></del>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                        $counter += 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php require_once './includes/dealoftheday.php' ?>

                <div class="product-main">
                    <h2 class="title">New bundles</h2>
                    <div class="product-grid">
                        <?php
                        $limit = 8;
                        if (isset($_GET['page'])) {
                            $page = $_GET['page'];
                        } else {
                            $page = 1;
                        }
                        $offset = ($page - 1) * $limit;

                        $product_left = array();
                        $new_product_counter = 1;

                        $new_products_result = get_new_products($offset, $limit);
                        if ($new_products_result->num_rows > 0) {
                            while ($row = mysqli_fetch_assoc($new_products_result)) {
                        ?>
                            <div class="showcase">
                                <div class="showcase-banner">
                                    <img src="./admin/upload/<?php echo $row['product_img'] ?>" alt="پکیج ۱۰۰۰ جم کلش آو کلنز" width="300" class="product-img" />
                                    <?php
                                    if ($new_product_counter == 1) {
                                    ?>
                                        <p class="showcase-badge">۲۰٪</p>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if ($new_product_counter == 3) {
                                    ?>
                                        <p class="showcase-badge angle black">Special discount</p>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="showcase-content">
                                    <a href="./viewdetail.php?id=<?php echo $row['product_id'] ?>&category=<?php echo $row['category_id'] ?>" class="showcase-category"><?php echo $row['product_title'] ?></a>
                                    <a href="./viewdetail.php?id=<?php echo $row['product_id'] ?>&category=<?php echo $row['category_id'] ?>">
                                        <h3 class="showcase-title"><?php echo $row['product_desc'] ?></h3>
                                    </a>
                                    <div class="showcase-rating">
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                    </div>
                                    <div class="price-box">
                                        <p class="price"><?php echo formatPrice($row['discounted_price']); ?></p>
                                        <del><?php echo formatPrice($row['product_price']); ?></del>
                                    </div>
                                </div>
                            </div>
                        <?php
                                $new_product_counter = $new_product_counter + 1;
                            }
                        } else {
                            echo "No packages found.";
                        }
                        $conn->close();
                        ?>
                    </div>
                </div>

                <?php
                include "includes/config.php";
                $sql1 = "SELECT * FROM products";
                $result1 = mysqli_query($conn, $sql1) or die("Query Failed.");

                if (mysqli_num_rows($result1) > 0) {
                    $total_products = mysqli_num_rows($result1);
                    $total_page = ceil($total_products / $limit);
                ?>
                    <nav class="main-pagination" style="margin-left: 10px;">
                        <ul class="pagination-ul">
                            <?php
                            for ($i = 1; $i <= $total_page; $i++) {
                                if ($page == $i) {
                                    $active = "page-active";
                                } else {
                                    $active = "";
                                }
                            ?>
                                <li class="page-item-number <?php echo $active; ?>">
                                    <a class="page-number-link" href="index.php?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </nav>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

    <div>
        <div class="container">
            <div class="testimonials-box">
                <div class="testimonial">
                    <h2 class="title">Gamer reviews</h2>
                    <div class="testimonial-card">
                        <img src="./images/testimonial-1.jpg" alt="امیر گیمر" class="testimonial-banner" width="80" height="80" />
                        <p class="testimonial-name">Amir Gamer</p>
                        <p class="testimonial-title">Professional Clash of Clans gamer</p>
                        <p class="testimonial-desc">The top-up purchase was very fast, and the support was excellent!</p>
                    </div>
                </div>
                <div class="cta-container">
                    <img src="./images/cta-banner-sale.jpg" alt="پکیج ویژه گیمینگ" class="cta-banner" />
                    <a href="#" class="cta-content">
                        <p class="discount">25% OFF</p>
                        <h2 class="cta-title">Gaming special package</h2>
                        <p class="cta-text">Starting from <?php echo formatPrice(50000); ?></p>
                        <button class="cta-btn">Buy now</button>
                    </a>
                </div>
                <div class="service">
                    <h2 class="title">Our services</h2>
                    <div class="service-container">
                        <a href="#" class="service-item">
                            <div class="service-icon">
                                <ion-icon name="flash-outline"></ion-icon>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title" style="direction: ltr; text-align: left;">Instant top-up</h3>
                                <p class="service-desc" style="direction: ltr; text-align: left;">Fast delivery of gems and UC after payment</p>
                            </div>
                        </a>
                        <a href="#" class="service-item">
                            <div class="service-icon">
                                <ion-icon name="lock-closed-outline"></ion-icon>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title" style="direction: ltr; text-align: left;">Secure payment</h3>
                                <p class="service-desc" style="direction: ltr; text-align: left;">Connected to trusted Iranian payment gateways</p>
                            </div>
                        </a>
                        <a href="#" class="service-item">
                            <div class="service-icon">
                                <ion-icon name="call-outline"></ion-icon>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title" style="direction: ltr; text-align: left;">24/7 support</h3>
                                <p class="service-desc" style="direction: ltr; text-align: left;">Available from 8 AM to 12 AM</p>
                            </div>
                        </a>
                        <a href="#" class="service-item">
                            <div class="service-icon">
                                <ion-icon name="ticket-outline"></ion-icon>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title" style="direction: ltr; text-align: left;">Special offers</h3>
                                <p class="service-desc" style="direction: ltr; text-align: left;">For orders above <?php echo formatPrice(500000); ?></p>
                            </div>
                        </a>
                        <a href="#" class="service-item">
                            <div class="service-icon">
                                <ion-icon name="gift-outline"></ion-icon>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title" style="direction: ltr; text-align: left;">Gaming rewards</h3>
                                <p class="service-desc" style="direction: ltr; text-align: left;">Chance to win special items</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="blog">
        <div class="container">
            <div class="blog-container has-scrollbar">
                <div class="blog-card">
                    <a href="https://igame.ir/blog/180/">
                        <img src="./images/blog/blog-1.jpg" alt="آموزش اتصال کالاف موبایل به اکتیویژن و فیسبوک[آپدیت 2025]" width="300" class="blog-banner" />
                    </a>
                    <div class="blog-content">
                        <a href="https://igame.ir/blog/180/" class="blog-category">Mobile Call of Duty</a>
                        <a href="https://igame.ir/blog/180/">
                            <h3 class="blog-title">How to link Mobile COD to Activision and Facebook [2025 update]</h3>
                        </a>
                        <p class="blog-meta">
                            By <cite>Mohammad Gamer</cite> /
                            <time datetime="1403-01-15">March 15, 2024</time>
                        </p>
                    </div>
                </div>
                <div class="blog-card">
                    <a href="https://igame.ir/blog/225/">
                        <img src="./images/blog/blog-2.jpg" alt="بازی کالاف دیوتی وارزون موبایل؛ از شایعه تا واقعیت" class="blog-banner" width="300" />
                    </a>
                    <div class="blog-content">
                        <a href="https://igame.ir/blog/225/" class="blog-category">Mobile Call of Duty</a>
                        <h3>
                            <a href="https://igame.ir/blog/225/" class="blog-title">Call of Duty Mobile: from rumor to reality</a>
                        </h3>
                        <p class="blog-meta">
                            By <cite>Sara Player</cite> /
                            <time datetime="1403-02-10">April 10, 2024</time>
                        </p>
                    </div>
                </div>
                <div class="blog-card">
                    <a href="https://igame.ir/blog/345/">
                        <img src="./images/blog/blog-3.jpg" alt="آموزش چیت زدن و معرفی انواع چیت های بازی کالاف دیوتی موبایل" class="blog-banner" width="300" />
                    </a>
                    <div class="blog-content">
                        <a href="https://igame.ir/blog/345/" class="blog-category">Mobile Call of Duty</a>
                        <h3>
                            <a href="https://igame.ir/blog/345/" class="blog-title">Cheat guide and types of cheats for Call of Duty Mobile</a>
                        </h3>
                        <p class="blog-meta">
                            By <cite>Reza Shooter</cite> /
                            <time datetime="1403-03-20">June 20, 2024</time>
                        </p>
                    </div>
                </div>
                <div class="blog-card">
                    <a href="https://igame.ir/blog/286/">
                        <img src="./images/blog/blog-4.jpg" alt="با خرید سی پی در گردونه کالاف شرکت کنید" class="blog-banner" width="300" />
                    </a>
                    <div class="blog-content">
                        <a href="https://igame.ir/blog/286/" class="blog-category">Mobile Call of Duty</a>
                        <h3>
                            <a href="https://igame.ir/blog/286/" class="blog-title">Join the Call of Duty CP draw by purchasing CP</a>
                        </h3>
                        <p class="blog-meta">
                            By <cite>Nigar Gamer</cite> /
                            <time datetime="1403-04-05">June 26, 2024</time>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once './includes/footer.php'; ?>