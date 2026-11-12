<?php 
include_once('./includes/headerNav.php');
include_once('./includes/restriction.php');

// بررسی بروزرسانی قبل از ارسال هر خروجی
if (isset($_POST['update'])) {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: http://localhost/WebMain/admin/users.php");
        exit;
    }
    include "includes/config.php";
    // استفاده از Prepared Statement برای جلوگیری از تزریق SQL
    $sql1 = "UPDATE customer 
             SET customer_fname = ?, customer_phone = ?, customer_address = ?, customer_role = ?
             WHERE customer_id = ?";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("ssssi", $_POST['name'], $_POST['phone'], $_POST['address'], $_POST['role'], $_GET['id']);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    // هدایت به صفحه کاربران با پیام موفقیت
    header("Location: http://localhost/WebMain/admin/users.php?success=updated");
    exit; // اطمینان از توقف اجرای اسکریپت پس از تغییر مسیر
}

// بررسی وجود و اعتبار id برای دریافت اطلاعات کاربر
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: http://localhost/WebMain/admin/users.php");
    exit;
}

// دریافت اطلاعات کاربر برای نمایش در فرم
include "includes/config.php";
$sql = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$_SESSION['previous_name'] = $row['customer_fname'] ?? '';
$_SESSION['previous_phone'] = $row['customer_phone'] ?? '';
$_SESSION['previous_address'] = $row['customer_address'] ?? '';
$_SESSION['previous_role'] = $row['customer_role'] ?? 'normal';
$stmt->close();
$conn->close();
?>
<head>
    <style>
        .content-box {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .update {
            border: 1px solid black;
            width: 60%;
            padding: 25px;
            border-radius: 16px;
            background-color: #f1f1f1;
        }
    </style>
</head>

<div class="content-box">
    <div class="update">
        <h1>بروزرسانی اطلاعات کاربر</h1>
        <form class="row g-3" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . htmlspecialchars($_GET['id']); ?>" method="POST">
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">نام</label>
                <input
                    name="name"
                    type="text"
                    class="form-control"
                    value="<?php echo htmlspecialchars($_SESSION['previous_name']) ?>"
                />
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">تلفن</label>
                <input
                    type="number"
                    name="phone"
                    class="form-control"
                    value="<?php echo htmlspecialchars($_SESSION['previous_phone']) ?>"
                />
            </div>
            <div class="col-12">
                <label for="inputAddress" class="form-label">آدرس</label>
                <input
                    type="text"
                    name="address"
                    class="form-control"
                    placeholder="خیابان اصلی 1234"
                    value="<?php echo htmlspecialchars($_SESSION['previous_address']) ?>"
                />
            </div>
            <div class="col-md-4">
                <label for="inputState" class="form-label">نقش</label>
                <select id="role_update" name="role" class="form-select">
                    <?php 
                    if ($_SESSION['previous_role'] == 'admin') {
                    ?>
                        <option value="admin" selected>مدیر</option>
                        <option value="normal">عادی</option>
                    <?php } else { ?>
                        <option value="admin">مدیر</option>
                        <option value="normal" selected>عادی</option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="update">
                    بروزرسانی
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once('../includes/footer.php');
?>