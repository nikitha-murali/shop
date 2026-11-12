<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment information | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content panel">
    <span class="eyebrow">Secure checkout</span><h1>Payment information</h1>
    <p>Checkout offers cash on pickup or a simulated UPI option for testing the order flow.</p>
    <p>The UPI demo does not open a payment app, collect payment details, or transfer money. Orders remain marked as pending until payment is arranged with the seller.</p>
    <p><a href="browse.php">Browse campus listings</a> · <a href="index.php">HandMeDown home</a></p>
  </main>
</body>
</html>
