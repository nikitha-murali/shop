<?php
include_once('./includes/headerNav.php');
// این محدودیت از تزریق مسیر صفحات جلوگیری می‌کند
if (!(isset($_SESSION['id']))) {
    header("location:index.php?UnathorizedUser");
    exit;
}

$sql8 = "SELECT * FROM customer WHERE customer_id='{$_SESSION['id']}';";
$result8 = $conn->query($sql8);

if ($result8 && $result8->num_rows > 0) {
    $row8 = $result8->fetch_assoc();
    $_SESSION['customer_name'] = $row8['customer_fname'] ?? '';
    $_SESSION['customer_email'] = $row8['customer_email'] ?? '';
    $_SESSION['customer_phone'] = $row8['customer_phone'] ?? '';
    $_SESSION['customer_address'] = $row8['customer_address'] ?? '';
} else {
    $_SESSION['customer_name'] = '';
    $_SESSION['customer_email'] = '';
    $_SESSION['customer_phone'] = '';
    $_SESSION['customer_address'] = '';
}

$conn->close();
?>
<head>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
        crossorigin="anonymous"
    />
    <title>پروفایل گیمر</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Vazir', Arial, sans-serif;
        }
        .card-container {
            margin-top: 25px;
            background: #f9f9f9;
            width: 100%;
            padding: 50px;
            border-radius: 16px;
            border: 1px solid #a3a3a3;
        }
        .edit-container {
            border: none;
            height: 40%;
            color: rgb(32, 69, 32);
            display: flex;
            justify-content: center;
        }
        #edit {
            margin-left: 5%;
            background: aliceblue;
            height: 200px;
            width: 25%;
            overflow: hidden;
        }
        h1 {
            text-align: center;
            text-transform: uppercase;
        }
        #role {
            color: orange;
        }
        h4 {
            text-decoration: underline;
            color: dark;
        }
        h4 a {
            text-align: center;
            color: blue;
        }
        #admin {
            text-decoration: none;
            font-weight: bold;
        }
        .profile_edit,
        .address_edit,
        .contact_edit {
            display: none;
        }
        .show {
            display: inline;
        }
        @media (max-width: 500px) {
            .edit-container {
                width: 100%;
                border: none;
                color: rgb(32, 69, 32);
                text-align: center;
                flex-direction: column;
            }
            #edit {
                height: 100%;
                margin-bottom: 5%;
                background: aliceblue;
                width: 85%;
            }
            marquee {
                width: 40%;
            }
        }
    </style>
</head>
<header>
    <?php require_once './includes/topheadactions.php'; ?>
    <?php require_once './includes/desktopnav.php' ?>
    <?php require_once './includes/mobilenav.php'; ?>
</header>
<hr>
<h1>
    سلام، 
    <span id="role">
        <?php echo ($_SESSION['customer_role'] == 'admin') ? 'مدیر' : $_SESSION['customer_name']; ?> 
    </span>
    به پروفایل گیمینگ خود خوش آمدید
</h1>
<h1>مدیریت حساب گیمر</h1>

<!-- profile setup -->
<div class="container text-center card-container">
    <div class="row" style="gap: 25px">
        <div class="col d-flex justify-content-center">
            <!-- Card Start -->
            <div class="card" style="width: 18rem">
                <div class="card-body">
                    <h5 class="card-title">پروفایل گیمر</h5>
                    <!-- user edit button -->
                    <div style="float: none" class="col">
                        <a class="btn btn-primary" href="#/profile/edit" id="edit_link1">ویرایش</a>
                    </div>
                    <br>
                    <br>
                    <!-- user edit -->
                    <div class="profile_edit">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <br />
                            <br />
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <input
                                        type="text" 
                                        name="name" 
                                        id=""
                                        class="form-control"
                                        placeholder="نام گیمر جدید (مثال: AliGamer)"
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <input
                                        type="email" 
                                        name="email" 
                                        id=""
                                        class="form-control"
                                        placeholder="ایمیل جدید (مثال: ali@example.com)"
                                    />
                                </div>
                                <!-- Save button -->
                                <br />
                                <br />
                                <div style="float: left" class="col">
                                    <button 
                                        type="submit" 
                                        name="save" 
                                        class="btn btn-primary">
                                        ذخیره
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- end user edit -->
                    <p class="card-text">
                        <?php echo $_SESSION['customer_name'] . " (" . $_SESSION['customer_role'] . ")" ?>
                    </p>
                    <p class="card-text">
                        <?php echo $_SESSION['customer_email'] ?>
                    </p>
                    <?php 
                    if ($_SESSION['customer_role'] == 'admin') {      
                    ?>            
                    <a 
                        id='admin' 
                        href='admin/login.php' 
                        class="btn btn-primary">
                        بازدید از پنل مدیریت
                    </a>
                    <?php } ?>
                </div>
            </div>
            <!-- Card End -->
        </div>

        <!-- New Col -->
        <div class="col d-flex justify-content-center">
            <!-- Card Start -->
            <div class="card" style="width: 18rem">
                <div class="card-body">
                    <h5 class="card-title">شناسه بازی</h5>
                    <a 
                        href="#/address/edit" 
                        id="edit_link2" 
                        class="btn btn-primary">
                        ویرایش
                    </a>
                    <!-- game ID form -->
                    <div class="address_edit">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <br />
                            <br />
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <input
                                        type="text" 
                                        name="address" 
                                        id=""
                                        class="form-control"
                                        placeholder="شناسه بازی جدید (مثال: ۱۲۳۴۵۶۷۸۹)"
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <!-- Save button -->
                                <br />
                                <br />
                                <div style="float: left" class="col">
                                    <button 
                                        type="submit" 
                                        name="save" 
                                        class="btn btn-primary">
                                        ذخیره
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- end of game ID form -->
                    <p class="card-text">
                        <?php echo $_SESSION['customer_address'] ?>
                    </p>
                </div>
            </div>
            <!-- Card End -->
        </div>

        <!-- New Col -->
        <div class="col d-flex justify-content-center">
            <!-- Card Start -->
            <div class="card" style="width: 18rem">
                <div class="card-body">
                    <h5 class="card-title">اطلاعات تماس</h5>
                    <a 
                        href="#/contact/edit" 
                        id="edit_link3"
                        class="btn btn-primary">
                        ویرایش
                    </a>
                    <!-- contact form -->
                    <div class="contact_edit">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <br />
                            <br />
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <input
                                        type="number" 
                                        name="number" 
                                        id=""
                                        class="form-control"
                                        placeholder="شماره موبایل جدید (مثال: ۰۹۱۲۱۲۳۴۵۶۷)"
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <!-- Save button -->
                                <br />
                                <br />
                                <div style="float: left" class="col">
                                    <button 
                                        type="submit" 
                                        name="save" 
                                        class="btn btn-primary">
                                        ذخیره
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- end of contact form -->
                    <p class="card-text">
                        <?php echo $_SESSION['customer_phone'] ?>
                    </p>
                </div>
            </div>
            <!-- Card End -->
        </div>
        <!-- Col End -->
    </div>
</div>

<?php
// برای ویرایش اطلاعات کاربران در backend
if (isset($_POST['save'])) {
    if (!empty($_POST['name']) && !empty($_POST['email'])) {
        include "includes/config.php";
        $sql6 = "UPDATE customer 
                 SET customer_fname='{$_POST['name']}', 
                     customer_email='{$_POST['email']}' 
                 WHERE customer_id='{$_SESSION['id']}'";
        $conn->query($sql6);   
        $conn->close();
        echo "موفقیت";
    }
    if (!empty($_POST['address'])) {
        include "includes/config.php";
        $sql6 = "UPDATE customer 
                 SET customer_address='{$_POST['address']}'
                 WHERE customer_id='{$_SESSION['id']}'";
        $conn->query($sql6);   
        $conn->close();
        echo "موفقیت";
    }
    if (!empty($_POST['number'])) {
        include "includes/config.php";
        $sql6 = "UPDATE customer 
                 SET customer_phone='{$_POST['number']}'
                 WHERE customer_id='{$_SESSION['id']}'";
        $conn->query($sql6);   
        $conn->close();
        echo "موفقیت";
    }
}
?>

<?php include_once('./includes/footer.php'); ?>

<script src="./js/jquery.js" type="text/javascript"></script>
<script src="./js/bootstrap.min.js" type="text/javascript"></script>
<script src="./js/edit.js"></script>