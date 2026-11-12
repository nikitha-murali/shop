<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

if (!empty($_SESSION['id'])) {
    header('Location: profile.php?id=' . (int) $_SESSION['id']);
    exit();
}

$loginError = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['pwd'] ?? '';

    if ($email === '' || $password === '') {
        $loginError = 'Enter both your email and password.';
    } else {
        $stmt = $conn->prepare(
            'SELECT customer_id, customer_pwd, customer_role
             FROM customer WHERE customer_email = ? LIMIT 1'
        );
        if (!$stmt) {
            error_log('Unable to prepare customer login query: ' . $conn->error);
            $loginError = 'Login is temporarily unavailable. Please try again.';
        } else {
            $stmt->bind_param('s', $email);
            if (!$stmt->execute()) {
                error_log('Unable to execute customer login query: ' . $stmt->error);
                $loginError = 'Login is temporarily unavailable. Please try again.';
            } else {
                $row = $stmt->get_result()->fetch_assoc();
                if ($row) {
                    $storedPassword = (string) $row['customer_pwd'];
                    $passwordIsValid = password_verify($password, $storedPassword);
                    $legacyPassword = !$passwordIsValid
                        && strlen($storedPassword) < 40
                        && hash_equals($storedPassword, $password);

                    if ($legacyPassword) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        if ($newHash === false) {
                            error_log('Unable to hash legacy customer password after login.');
                            $passwordIsValid = false;
                        } else {
                            $upgrade = $conn->prepare(
                                'UPDATE customer SET customer_pwd = ? WHERE customer_id = ?'
                            );
                            if (!$upgrade) {
                                error_log('Unable to prepare legacy password upgrade: ' . $conn->error);
                                $passwordIsValid = false;
                            } else {
                                $upgrade->bind_param('si', $newHash, $row['customer_id']);
                                $passwordIsValid = $upgrade->execute();
                                if (!$passwordIsValid) {
                                    error_log('Unable to upgrade legacy password: ' . $upgrade->error);
                                }
                                $upgrade->close();
                            }
                        }
                    }

                    if ($passwordIsValid) {
                        session_regenerate_id(true);
                        $_SESSION['id'] = (int) $row['customer_id'];
                        $_SESSION['customer_role'] = $row['customer_role'];
                        $stmt->close();
                        header('Location: profile.php?id=' . (int) $row['customer_id']);
                        exit();
                    }
                }

                $loginError = 'Email or password is incorrect. Check your details or sign up if you are new.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Log in | HandMeDown</title>
    <link rel="stylesheet" href="css/hmd-marketplace.css">
  </head>
  <body>
    <?php include __DIR__ . '/includes/hmd-header.php'; ?>
    <main class="auth-card">
      <h1>HandMeDown</h1>
      <h2>Log in</h2>
      <?php if ($loginError !== ''): ?>
        <p class="error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <form class="hmd-form" action="login.php" method="post">
        <label for="inputEmail">College email</label>
        <input
          id="inputEmail"
          name="email"
          type="email"
          value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
          autocomplete="username"
          required
        />
        <label for="inputPassword">Password</label>
        <input
          id="inputPassword"
          name="pwd"
          type="password"
          autocomplete="current-password"
          required
        />
        <button type="submit">Log in</button>
      </form>
      <p class="signup">New here? <a href="signup.php">Create an account</a></p>
    </main>
  </body>
</html>
