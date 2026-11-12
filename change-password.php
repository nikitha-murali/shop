<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int) $_SESSION['id'];
if (empty($_SESSION['hmd_password_csrf'])) {
    $_SESSION['hmd_password_csrf'] = bin2hex(random_bytes(32));
}
$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['hmd_password_csrf'], $token)) {
        $message = 'Your form session expired. Reload the page and try again.';
        $isError = true;
    } elseif (strlen($newPassword) < 10) {
        $message = 'Choose a password with at least 10 characters.';
        $isError = true;
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'The new passwords do not match.';
        $isError = true;
    } else {
        $stmt = $conn->prepare('SELECT customer_pwd FROM customer WHERE customer_id = ?');
        if (!$stmt) {
            error_log('Unable to prepare change-password lookup: ' . $conn->error);
            $message = 'Password change is temporarily unavailable.';
            $isError = true;
        } else {
            $stmt->bind_param('i', $userId);
            if (!$stmt->execute()) {
                error_log('Unable to load current password for account: ' . $stmt->error);
                $message = 'Password change is temporarily unavailable.';
                $isError = true;
            } else {
                $account = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $storedPassword = (string) ($account['customer_pwd'] ?? '');
                $validCurrentPassword = $account
                    && (password_verify($currentPassword, $storedPassword)
                        || (strlen($storedPassword) < 40 && hash_equals($storedPassword, $currentPassword)));

                if (!$validCurrentPassword) {
                    $message = 'Your current password is incorrect.';
                    $isError = true;
                } else {
                    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $update = $conn->prepare('UPDATE customer SET customer_pwd = ? WHERE customer_id = ?');
                    if ($newHash === false || !$update) {
                        error_log('Unable to prepare password update: ' . $conn->error);
                        $message = 'Password change is temporarily unavailable.';
                        $isError = true;
                    } else {
                        $update->bind_param('si', $newHash, $userId);
                        if ($update->execute()) {
                            $message = 'Your password has been updated.';
                            $_SESSION['hmd_password_csrf'] = bin2hex(random_bytes(32));
                        } else {
                            error_log('Unable to update account password: ' . $update->error);
                            $message = 'Your password could not be updated. Please try again.';
                            $isError = true;
                        }
                        $update->close();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change password | HandMeDown</title>
  <style>
    body{font-family:Arial,sans-serif;max-width:560px;margin:40px auto;padding:0 16px;color:#20352a}
    label{display:block;margin:14px 0 6px;font-weight:600}
    input{box-sizing:border-box;width:100%;padding:11px;border:1px solid #b9c9bf;border-radius:6px}
    button{margin-top:18px;padding:11px 18px;border:0;border-radius:6px;background:#1034a6;color:#fff;font-weight:700}
    .message{padding:10px;background:<?= $isError ? "'#fff0ef'" : "'#eef2fb'" ?>;color:<?= $isError ? "'#9b1c16'" : "'#1034a6'" ?>}
  </style>
</head>
<body>
  <main>
    <h1>Change password</h1>
    <p><a href="profile.php">Back to profile</a></p>
    <?php if ($message !== ''): ?><p class="message" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['hmd_password_csrf'], ENT_QUOTES, 'UTF-8') ?>">
      <label for="current_password">Current password</label>
      <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
      <label for="new_password">New password (at least 10 characters)</label>
      <input id="new_password" name="new_password" type="password" minlength="10" autocomplete="new-password" required>
      <label for="confirm_password">Confirm new password</label>
      <input id="confirm_password" name="confirm_password" type="password" minlength="10" autocomplete="new-password" required>
      <button type="submit">Update password</button>
    </form>
  </main>
</body>
</html>
