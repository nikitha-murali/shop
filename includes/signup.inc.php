<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    header('Location: ../signup.php?error=' . urlencode('Submit the signup form to continue.'));
    exit();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hmd-config.php';
require_once __DIR__ . '/signupFn.inc.php';

$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$address = trim($_POST['address'] ?? '');
$number = trim($_POST['number'] ?? '');
$hostel = trim($_POST['hostel'] ?? '');
$course = trim($_POST['course'] ?? '');
$semester = trim($_POST['semester'] ?? '');
$pwd = $_POST['pwd'] ?? '';
$rpwd = $_POST['rpwd'] ?? '';

if ($name === '' || $email === '' || $address === '' || $number === ''
    || $hostel === '' || $course === '' || $semester === '' || $pwd === '' || $rpwd === '') {
    header('Location: ../signup.php?error=' . urlencode('Please complete every field.'));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../signup.php?error=' . urlencode('Enter a valid email address.'));
    exit();
}

if (HMD_COLLEGE_EMAIL_DOMAINS === []) {
    header('Location: ../signup.php?error=' . urlencode('College email domain is not configured yet.'));
    exit();
}

$emailDomain = substr(strrchr($email, '@'), 1);
$allowedDomains = array_map('strtolower', HMD_COLLEGE_EMAIL_DOMAINS);
if (!in_array($emailDomain, $allowedDomains, true)) {
    header('Location: ../signup.php?error=' . urlencode('Use your college email address.'));
    exit();
}

if (strlen($name) > 50 || strlen($email) > 100 || strlen($number) > 15
    || strlen($hostel) > 100 || strlen($course) > 100 || strlen($semester) > 50) {
    header('Location: ../signup.php?error=' . urlencode('One or more fields are too long.'));
    exit();
}

if (!pwdMatch($pwd, $rpwd)) {
    header('Location: ../signup.php?error=' . urlencode('Passwords do not match.'));
    exit();
}

$emailCheck = $conn->prepare('SELECT customer_id FROM customer WHERE customer_email = ? LIMIT 1');
if (!$emailCheck) {
    error_log('Unable to prepare duplicate customer email check: ' . $conn->error);
    header('Location: ../signup.php?error=' . urlencode('Signup is temporarily unavailable. Please try again.'));
    exit();
}
$emailCheck->bind_param('s', $email);
if (!$emailCheck->execute()) {
    error_log('Unable to check customer email: ' . $emailCheck->error);
    $emailCheck->close();
    header('Location: ../signup.php?error=' . urlencode('Signup is temporarily unavailable. Please try again.'));
    exit();
}
$existingAccount = $emailCheck->get_result()->num_rows > 0;
$emailCheck->close();
if ($existingAccount) {
    header('Location: ../signup.php?error=' . urlencode('This email already has an account. Please log in.'));
    exit();
}

if (!createUser($conn, $name, $email, $address, $pwd, $number, $hostel, $course, $semester)) {
    header('Location: ../signup.php?error=' . urlencode('Could not create account. The email may already be registered.'));
    exit();
}

header('Location: ../signup.php?success=registered');
exit();
?>