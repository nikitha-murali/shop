<?php
if (isset($_POST['submit'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $number = $_POST['number'] ?? '';
    $pwd = $_POST['pwd'];
    $rpwd = $_POST['rpwd'];

    require_once 'config.php';
    require_once 'signupFn.inc.php';

    if (emptyInputSignup($pwd, $rpwd) == true) {
        header("location: ../signup.php?error=" . urlencode("رمز عبور اجباری است"));
        exit();
    }

    if (pwdMatch($pwd, $rpwd) !== true) {
        header("location: ../signup.php?error=" . urlencode("عدم_تطابق_رمز"));
        exit();
    }

    createUser($conn, $name, $email, $address, $pwd, $number);
} else {
    header("location: ../signup.php?error=" . urlencode("خطایی_رخ_داده"));
    exit();
}
?>