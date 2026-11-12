<?php
require_once './includes/config.php';

// get banner products and details
function get_banner_details() {
    global $conn;
    $query = "SELECT * FROM banner WHERE banner.banner_status = 1";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving banner details: " . mysqli_error($conn));
    }
    return $result;
}

// get category bar products
function get_category_bar_products() {
    global $conn;
    $query = "SELECT c.*, COUNT(p.product_id) as category_quantity
              FROM category c
              LEFT JOIN products p ON c.id = p.category_id
              WHERE c.status = 1
              GROUP BY c.id";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving category bar products: " . mysqli_error($conn));
    }
    return $result;
}

// get categories 
function get_categories() {
    global $conn;
    $query = "SELECT * FROM category WHERE category.status = 1";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving categories: " . mysqli_error($conn));
    }
    return $result;
}

// get outfits category
function get_outfits_category() {
    global $conn;
    $query = "SELECT * FROM outfits WHERE outfits.coloth_category_status = 1";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving outfits category: " . mysqli_error($conn));
    }
    return $result;
}



// get bundles category
function get_bundles_category() {
    global $conn;
    $query = "SELECT * FROM bundles WHERE bundles.bundles_category_status = 1";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving bundles category: " . mysqli_error($conn));
    }
    return $result;
}

// get best sellers from product table
function get_best_sellers() {
    global $conn;
    $query = "SELECT * FROM products LIMIT 4;";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving best sellers: " . mysqli_error($conn));
    }
    return $result;
}

// get new arrivals
function get_new_arrivals() {
    global $conn;
    $query = "SELECT * FROM products LIMIT 8 OFFSET 0;";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving new arrivals: " . mysqli_error($conn));
    }
    return $result;
}

// get trending products
function get_trending_products() {
    global $conn;
    $query = "SELECT * FROM products LIMIT 8 OFFSET 8;";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving trending products: " . mysqli_error($conn));
    }
    return $result;
}

// get top rated products
function get_top_rated_products() {
    global $conn;
    $query = "SELECT * FROM products LIMIT 8 OFFSET 16;";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving top rated products: " . mysqli_error($conn));
    }
    return $result;
}

// get deal of the day
function get_deal_of_day() {
    global $conn;
    $query = "SELECT * FROM deal_of_the_day WHERE deal_of_the_day.deal_status = 1";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving deal of the day: " . mysqli_error($conn));
    }
    return $result;
}

function get_new_products($offset, $limit) {
    global $conn;
    $query = "SELECT * FROM products ORDER BY products.product_id DESC LIMIT {$offset},{$limit}";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving new products: " . mysqli_error($conn));
    }
    return $result;
}

function display_electronic_category() {
    global $conn;
    $query = "SELECT * FROM category_electronics WHERE category_electronics.status = 1";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error retrieving electronic category: " . mysqli_error($conn));
    }
    return $result;
}

// get product through id from product table 
function get_product($id) {
    global $conn;
    $query = "SELECT * FROM products WHERE products.product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if (!$result) {
        die("Error retrieving product: " . mysqli_error($conn));
    }
    return $result;
}

// get specific category
function get_items_by_category_items($category_id) {
    global $conn;
    $query = "SELECT * FROM products WHERE products.category_id = ? AND products.status = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if (!$result) {
        die("Error retrieving category items: " . mysqli_error($conn));
    }
    return $result;
}
?>