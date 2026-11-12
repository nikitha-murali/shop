<?php 
include_once('./includes/headerNav.php');
include_once('./includes/restriction.php');
?>

<h1>دسته‌بندی محصولات</h1>
<hr>

<div class="table-cont">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Number</th>
                <th scope="col">Category</th>
                <th scope="col">Product count/th>
            </tr>
        </thead>
        <tbody class="table-group-divider">

<?php
include "includes/config.php";

// دریافت لیست دسته‌بندی‌ها از جدول category
$sql_categories = "SELECT id, name FROM category";
$result_categories = $conn->query($sql_categories);

if ($result_categories && $result_categories->num_rows > 0) {
    $sn = 1; // شماره ردیف
    while ($category = $result_categories->fetch_assoc()) {
        $category_id = $category['id'];
        $category_name = $category['name'];

        // کوئری برای شمارش تعداد محصولات در این دسته‌بندی
        $sql = "SELECT COUNT(*) as total FROM products WHERE category_id = '$category_id'";
        $result = $conn->query($sql);
        
        // دریافت تعداد محصولات
        $total_post = 0; // مقدار پیش‌فرض
        if ($result && $row = $result->fetch_assoc()) {
            $total_post = $row['total'];
        }

        // نمایش ردیف در جدول
        ?>
        <tr>
            <th scope="row"><?php echo $sn; ?></th>
            <td><?php echo $category_name; ?></td>
            <td><?php echo $total_post; ?></td>    
        </tr>
        <?php
        $sn++;
    }
} else {
    echo "<tr><td colspan='3'>No categories found</td></tr>";
}

$conn->close();
?>

        </tbody>
    </table>
</div>
<br>