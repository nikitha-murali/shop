<?php 
include_once('./includes/headerNav.php');
include_once('./includes/restriction.php');
if (!(isset($_SESSION['logged-in']))) {
    header("Location:login.php?unauthorizedAccess");
}
?>

<div class="d-flex" style="justify-content: space-between; padding: 18px">
    <h1>Products</h1>
    <button type="button" class="btn btn-primary btn-lg">
        <a class="btn" href="add-post.php">Add Products</a>
    </button>
</div>
<hr>

<?php
include "includes/config.php"; 

// نمایش پیام موفقیت برای افزودن، حذف یا به‌روزرسانی
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'added') {
        echo "<p style='color: green; text-align: center;'>Product added successfully</p>";
    } elseif ($_GET['status'] == 'deleted') {
        echo "<p style='color: green; text-align: center;'>Product deleted successfully</p>";
    } elseif ($_GET['status'] == 'updated') {
        echo "<p style='color: green; text-align: center;'>Product updated successfully</p>";
    }
}

/* define how much data to show in a page from database */
$limit = 10;
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {
        case 1: $sn = 0; break;
        case 2: $sn = 4; break;
        case 3: $sn = 8; break;
        case 4: $sn = 12; break;
        case 5: $sn = 16; break;
        case 6: $sn = 20; break;
    }
} else {
    $page = 1;
    $sn = 0; // مقدار پیش‌فرض برای صفحه اول
}
// define from which row to start extracting data from database
$offset = ($page - 1) * $limit;

if ($_SESSION["customer_role"] == 'admin') {
    $sql = "SELECT products.*, category.name AS category_name 
            FROM products 
            LEFT JOIN category ON products.category_id = category.id 
            ORDER BY products.product_id DESC LIMIT {$offset},{$limit}";
} elseif ($_SESSION["customer_role"] == 'normal') {
    $sql = "SELECT products.*, category.name AS category_name 
            FROM products 
            LEFT JOIN category ON products.category_id = category.id 
            WHERE products.product_author='{$_SESSION['customer_name']}' 
            ORDER BY products.product_id DESC LIMIT {$offset},{$limit}";
}

$result = $conn->query($sql) or die("Error in query.");
if ($result->num_rows > 0) {
?>

<div class="table-cont">
<table class="table">
    <thead>
        <tr>
            <th scope="col">Number</th>
            <th scope="col">Title</th>
            <th scope="col">Category</th>
            <th scope="col">Date</th>
            <th scope="col">Author</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        <?php
        while ($row = $result->fetch_assoc()) {
            $sn = $sn + 1;
        ?>
        <tr>
            <th scope="row"><?php echo $sn ?></th>
            <td><?php echo $row["product_title"] ?></td>
            <td><?php echo $row["category_name"] ?? 'No Category'; ?></td>
            <td><?php echo $row["product_date"] ?></td>
            <td><?php echo $row["product_author"] ?></td>
            <td>
                <a class="fn_link" href="update-post.php?id=<?php echo $row["product_id"] ?>">
                    <i class='fa fa-edit'></i>
                </a>
            </td>
            <td>
                <a class="fn_link" href="remove-post.php?id=<?php echo $row["product_id"] ?>">
                    <i class='fa fa-trash'></i>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>

<?php
} else {
    echo "No results found";
}
$conn->close();
?>

<!-- Pagination -->
<?php
include "includes/config.php";
$sql1 = "SELECT * FROM products";
$result1 = mysqli_query($conn, $sql1) or die("Error in query.");

if (mysqli_num_rows($result1) > 0) {
    $total_products = mysqli_num_rows($result1);
    $total_page = ceil($total_products / $limit);
?>
    <nav aria-label="..." style="margin-left: 10px;">
        <ul class="pagination pagination-sm">
            <?php 
            for ($i = 1; $i <= $total_page; $i++) {
                $active = ($page == $i) ? "active" : "";
            ?>
            <li class="page-item">
                <a class="page-link <?php echo $active; ?>" href="post.php?page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
<?php } ?>