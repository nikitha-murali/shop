<?php
include_once('./includes/headerNav.php');
include_once('./includes/restriction.php');
include "includes/config.php";

// بررسی آپدیت محصول
if (isset($_POST['update'])) {
    $product_id = intval($_GET['id']);
    $title = $_POST['title'];
    $catag = intval($_POST['catag']);
    $price = intval($_POST['price']);
    $discount = floatval($_POST['discount']);
    $desc = $_POST['desc'];
    $noofitem = intval($_POST['noofitem']);

    // مدیریت آپلود فایل
    $img = $_SESSION['previous_img'];
    if (isset($_FILES['newimg']) && $_FILES['newimg']['error'] == 0) {
        $target_dir = "upload/";
        $img = $target_dir . basename($_FILES['newimg']['name']);
        move_uploaded_file($_FILES['newimg']['tmp_name'], $img);
    }

    // کوئری آپدیت با Prepared Statement
    $sql = "UPDATE products SET product_title = ?, category_id = ?, product_price = ?, discounted_price = ?, product_desc = ?, product_img = ?, product_left = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siidsisi", $title, $catag, $price, $discount, $desc, $img, $noofitem, $product_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: post.php?status=updated");
        exit();
    } else {
        echo "خطا در به‌روزرسانی محصول: " . $conn->error;
    }
    $stmt->close();
}

// دریافت اطلاعات محصول برای نمایش در فرم
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// ذخیره مقادیر قبلی در سشن
$_SESSION['previous_title'] = $row['product_title'];
$_SESSION['previous_desc'] = $row['product_desc'];
$_SESSION['previous_catag'] = $row['category_id'];
$_SESSION['previous_price'] = $row['product_price'];
$_SESSION['previous_discount'] = $row['discounted_price'];
$_SESSION['previous_no'] = $row['product_left'];
$_SESSION['previous_img'] = $row['product_img'];

$stmt->close();
$conn->close();
?>

<head>
    <style>
        .content-box-post {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .update {
            border: 1px solid black;
            width: 80%;
            padding: 25px;
            border-radius: 16px;
            background-color: #f1f1f1;
        }
    </style>
</head>
<div class="content-box-post">
    <div class="update">
        <h5>ویرایش محصول</h5>
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']; ?>" method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-12">
                <label for="inputAddress" class="form-label">عنوان</label>
                <input class="form-control" type="text" name="title" value="<?php echo htmlspecialchars($_SESSION['previous_title']); ?>" required />
            </div>
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">قیمت (تومان)</label>
                <input class="form-control" type="number" name="price" value="<?php echo $_SESSION['previous_price']; ?>" required />
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">تخفیف (تومان)</label>
                <input class="form-control" type="number" name="discount" value="<?php echo $_SESSION['previous_discount']; ?>" />
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">توضیحات</label>
                <textarea class="form-control" rows="3" name="desc" required><?php echo htmlspecialchars($_SESSION['previous_desc']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label for="inputCity" class="form-label">تعداد اقلام</label>
                <input class="form-control" type="number" name="noofitem" value="<?php echo $_SESSION['previous_no']; ?>" required />
            </div>
            <div class="col-md-6">
                <label for="inputState" class="form-label">دسته‌بندی</label>
                <select name="catag" class="form-select" required>
                    <?php
                    include "includes/config.php";
                    $sql = "SELECT id, name FROM category";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($_SESSION['previous_catag'] == $row['id']) ? 'selected' : '';
                        echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                    }
                    $conn->close();
                    ?>
                </select>
            </div>
            <div class="col-12">
                <label for="inputAddress" class="form-label">تصویر</label>
                <input type="file" name="newimg" class="form-control" />
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked />
                <label class="form-check-label" for="flexRadioDefault2">موجود</label>
            </div>
            <div class="col-12">
                <button type="submit" name="update" class="btn btn-primary">بروزرسانی</button>
            </div>
        </form>
    </div>
</div>