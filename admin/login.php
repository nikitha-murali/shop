<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['userEmail'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $loginError = 'Enter a valid email address and password.';
    } else {
        $stmt = $conn->prepare(
            'SELECT customer_id, customer_email, customer_pwd, customer_role
             FROM customer WHERE customer_email = ?'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $valid = false;
        if ($admin && $admin['customer_role'] === 'admin') {
            $valid = password_verify($password, $admin['customer_pwd']);
            if (!$valid && hash_equals($admin['customer_pwd'], $password)) {
                $valid = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upgrade = $conn->prepare(
                    'UPDATE customer SET customer_pwd = ? WHERE customer_id = ?'
                );
                $upgrade->bind_param('si', $newHash, $admin['customer_id']);
                if (!$upgrade->execute()) {
                    error_log('Unable to upgrade legacy admin password: ' . $upgrade->error);
                }
                $upgrade->close();
            }
        }
        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['id'] = (int) $admin['customer_id'];
            $_SESSION['customer_role'] = 'admin';
            $_SESSION['logged-in'] = '1';
            header('Location: post.php');
            exit();
        }
        $loginError = 'The email or password is incorrect, or this account is not an administrator.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HandMeDown Admin | Login</title>
  <style>
    *{box-sizing:border-box}body{font-family:Arial,sans-serif;max-width:440px;margin:12vh auto;padding:24px;color:#202720}
    form{padding:24px;border:1px solid #dce5df;border-radius:12px}label{display:block;margin:14px 0 6px}
    input{width:100%;padding:11px;border:1px solid #cbd6ce;border-radius:6px}button{margin-top:18px;padding:11px 18px;background:#1034a6;color:white;border:0;border-radius:6px}
    .error{color:#a00}
  </style>
</head>
<body>
<form method="post">
  <h1>HandMeDown admin</h1>
  <?php if ($loginError !== ''): ?><p class="error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <label for="userEmail">Admin email</label><input id="userEmail" name="userEmail" type="email" required>
  <label for="password">Password</label><input id="password" name="password" type="password" required>
  <button type="submit">Log in</button>
</form>
</body>
</html>
