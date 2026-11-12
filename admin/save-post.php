<?php
include "includes/config.php";

if (isset($_FILES['prod-img'])) {
    $file_name = $_FILES['prod-img']['name'];
    $file_size = $_FILES['prod-img']['size'];
    $file_tmp = $_FILES['prod-img']['tmp_name']; 
    $file_type = $_FILES['prod-img']['type'];
    $tmp = explode('.',$file_name);
    $file_ext = end($tmp);
    $extensions = array("jpeg","jpg","png");

    if (in_array($file_ext, $extensions) === false) {
        echo "این فرمت مجاز نیست، لطفاً فایل jpg، jpeg یا png انتخاب کنید.";
        die();
    } elseif ($file_size >= 2097152) {
        echo "حجم فایل باید کمتر از 2 مگابایت باشد.";
        die();
    } else {
        move_uploaded_file($file_tmp, "upload/" . $file_name);
    }
}

if (isset($_POST['submit'])) {
    session_start();
    $today_date = date("j,n,Y"); 
    $author = $_SESSION['customer_name'];
    $sql = "INSERT INTO products 
            (product_title, product_price, discounted_price, product_desc, product_date, product_img, product_left, product_author, category_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $price = intval($_POST['prod-price']);
    $discount = floatval($_POST['prod-discount']);
    $category_id = intval($_POST['prod-category-id']);
    $stmt->bind_param("siddssisi", 
        $_POST['prod-title'], 
        $price, 
        $discount, 
        $_POST['prod-desc'], 
        $today_date, 
        $file_name, 
        $_POST['noofitem'], 
        $author,
        $category_id
    );
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: post.php?status=added");
        exit();
    } else {
        echo "خطا در افزودن محصول";
    }
}
?>