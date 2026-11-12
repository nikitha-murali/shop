<?php
require_once __DIR__ . '/functions/sell-functions.php';
hmd_require_login();

$userId = hmd_current_user_id();
$stmt = $conn->prepare(
    'SELECT customer_fname, customer_email, customer_phone, customer_address, customer_role
     FROM customer WHERE customer_id = ? LIMIT 1'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$profile) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$_SESSION['customer_name'] = $profile['customer_fname'];
$_SESSION['customer_email'] = $profile['customer_email'];
$_SESSION['customer_phone'] = $profile['customer_phone'];
$_SESSION['customer_address'] = $profile['customer_address'];
$_SESSION['customer_role'] = $profile['customer_role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('profile.php', 'Your form session expired. Please try again.');
    }

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $emailDomain = substr(strrchr($email, '@') ?: '', 1);
    $emailUnchanged = strcasecmp($email, (string) $profile['customer_email']) === 0;

    if ($name === '' || strlen($name) > 50
        || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100
        || (!$emailUnchanged && !in_array($emailDomain, HMD_COLLEGE_EMAIL_DOMAINS, true))
        || strlen($phone) > 15 || $address === '') {
        hmd_redirect('profile.php', 'Enter a valid name, campus email, phone number, and address.');
    }

    $update = $conn->prepare(
        'UPDATE customer
         SET customer_fname = ?, customer_email = ?, customer_phone = ?, customer_address = ?
         WHERE customer_id = ?'
    );
    $update->bind_param('ssssi', $name, $email, $phone, $address, $userId);
    try {
        $update->execute();
    } catch (mysqli_sql_exception $error) {
        error_log('HandMeDown profile update failed: ' . $error->getMessage());
        $update->close();
        hmd_redirect('profile.php', 'That campus email is already in use, or the profile could not be saved.');
    }
    $update->close();
    $_SESSION['customer_name'] = $name;
    $_SESSION['customer_email'] = $email;
    $_SESSION['customer_phone'] = $phone;
    $_SESSION['customer_address'] = $address;
    hmd_redirect('profile.php', 'Your profile has been updated.', 'success');
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My profile | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
    <header class="intro">
      <span class="eyebrow">Your account</span>
      <h1>My profile</h1>
      <p>Keep your campus contact and pickup details up to date.</p>
    </header>
    <?php if ($error !== ''): ?><p class="error" role="alert"><?= hmd_escape($error) ?></p><?php endif; ?>
    <?php if ($success !== ''): ?><p class="empty-state" role="status"><?= hmd_escape($success) ?></p><?php endif; ?>
    <section class="panel">
      <form class="hmd-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>">
        <div class="grid">
          <label for="name">Name<input id="name" name="name" maxlength="50" value="<?= hmd_escape($profile['customer_fname']) ?>" autocomplete="name" required></label>
          <label for="email">Campus email<input id="email" name="email" type="email" maxlength="100" value="<?= hmd_escape($profile['customer_email']) ?>" autocomplete="email" required></label>
          <label for="phone">Phone number<input id="phone" name="phone" maxlength="15" value="<?= hmd_escape($profile['customer_phone']) ?>" autocomplete="tel"></label>
          <label for="address">Campus / pickup address<input id="address" name="address" value="<?= hmd_escape($profile['customer_address']) ?>" autocomplete="street-address" required></label>
        </div>
        <p>Account type: <strong><?= hmd_escape(ucfirst($profile['customer_role'])) ?></strong></p>
        <button type="submit">Save profile</button>
      </form>
    </section>
    <p><a class="details-link" href="change-password.php">Change password →</a></p>
    <?php if ($profile['customer_role'] === 'admin'): ?><p><a class="details-link" href="admin/post.php">Open admin dashboard →</a></p><?php endif; ?>
  </main>
</body>
</html>
