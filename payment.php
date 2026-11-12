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
    <span class="eyebrow">Checkout options</span><h1>Payment information</h1>
    <p>Cash on pickup and UPI demo only record an order; neither transfers money. Razorpay test mode is available from the cart after the server is configured with Razorpay test keys.</p>
    <p>Razorpay test mode uses the gateway's test environment and does not charge real money. A payment must be confirmed by Razorpay before the order is marked paid.</p>
    <p><a href="browse.php">Browse campus listings</a> · <a href="hmd-cart.php">Open your cart</a> · <a href="index.php">HandMeDown home</a></p>
  </main>
</body>
</html>
