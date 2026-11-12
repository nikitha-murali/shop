<?php
require_once __DIR__ . '/functions/razorpay-functions.php';
hmd_release_expired_razorpay_holds($conn);

if (empty($_SESSION['hmd_cart']) || !is_array($_SESSION['hmd_cart'])) {
    $_SESSION['hmd_cart'] = [];
}

$cartIds = array_values(array_unique(array_filter(
    array_map('intval', array_keys($_SESSION['hmd_cart'])),
    static function ($id) { return $id > 0; }
)));
$items = [];
$unavailableCount = 0;
if ($cartIds) {
    $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
    $types = str_repeat('i', count($cartIds));
    $stmt = $conn->prepare(
        "SELECT id, title, image, category, asking_price, pickup_location
         FROM hmd_listings WHERE status = 'active' AND id IN ({$placeholders})
         ORDER BY FIELD(id, {$placeholders})"
    );
    $params = array_merge($cartIds, $cartIds);
    $bindParams = [$types . $types];
    foreach ($params as $index => &$param) {
        $bindParams[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], $bindParams);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $items[(int) $row['id']] = $row;
    }
    $stmt->close();

    foreach ($cartIds as $cartId) {
        if (!isset($items[$cartId])) {
            unset($_SESSION['hmd_cart'][$cartId]);
            $unavailableCount++;
        }
    }
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$razorpayTestAvailable = false;
try {
    list($razorpayKeyId) = hmd_razorpay_credentials();
    $razorpayTestAvailable = strpos($razorpayKeyId, 'rzp_test_') === 0;
} catch (RuntimeException $exception) {
    $razorpayTestAvailable = false;
}
$total = array_sum(array_map(
    static function ($item) { return (float) $item['asking_price']; },
    $items
));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    hmd_require_login();
    if (!hmd_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        hmd_redirect('hmd-cart.php', 'Your form session expired. Please try again.');
    }
    if (!$items) {
        hmd_redirect('hmd-cart.php', 'Your cart is empty.');
    }

    try {
        $paymentMethod = $_POST['payment_method'] ?? '';
        if ($paymentMethod === 'razorpay') {
            throw new RuntimeException('Use the Razorpay button to start secure checkout.');
        }
        hmd_buy_cart($conn, array_keys($items), hmd_current_user_id(), $paymentMethod);
        $_SESSION['hmd_cart'] = [];
        $message = $paymentMethod === 'upi_demo'
            ? 'Demo orders placed. No UPI transfer was made; arrange payment and pickup with the sellers.'
            : 'Orders placed. Arrange cash payment and pickup with the sellers.';
        hmd_redirect('my-orders.php', $message, 'success');
    } catch (mysqli_sql_exception $exception) {
        error_log('HandMeDown cart checkout database error: ' . $exception->getMessage());
        $error = 'A database error prevented checkout. Your cart has not been cleared.';
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your cart | HandMeDown</title>
  <link rel="stylesheet" href="css/hmd-marketplace.css">
</head>
<body>
  <?php include __DIR__ . '/includes/hmd-header.php'; ?>
  <main class="hmd-content">
    <header class="intro"><span class="eyebrow">Saved for later</span><h1>Your cart</h1><p>Review your finds before placing your orders.</p></header>
    <?php if ($success !== ''): ?><p class="empty-state" role="status"><?= hmd_escape($success) ?></p><?php endif; ?>
    <?php if ($error !== ''): ?><p class="error" role="alert"><?= hmd_escape($error) ?></p><?php endif; ?>
    <?php if ($unavailableCount > 0): ?><p class="error" role="status"><?= $unavailableCount ?> item<?= $unavailableCount === 1 ? ' is' : 's are' ?> no longer available and <?= $unavailableCount === 1 ? 'has' : 'have' ?> been removed.</p><?php endif; ?>
    <?php if (!$items): ?>
      <section class="empty-state"><h2>Your cart is empty</h2><p>Browse campus listings and add items you like.</p><a class="button" href="browse.php">Browse items</a></section>
    <?php else: ?>
      <div class="cart-layout">
        <section class="cart-items" aria-label="Items in your cart">
          <div class="cart-section-heading"><div><span class="eyebrow">Your selection</span><h2>Items in your cart</h2></div><a href="browse.php">Keep browsing</a></div>
          <?php foreach ($items as $item): ?>
            <article class="cart-item">
              <a class="cart-item-image" href="listing.php?id=<?= (int) $item['id'] ?>"><img src="admin/upload/<?= rawurlencode($item['image']) ?>" alt="<?= hmd_escape($item['title']) ?>"></a>
              <div class="cart-item-info"><span class="tag"><?= hmd_escape($item['category']) ?></span><h2><a href="listing.php?id=<?= (int) $item['id'] ?>"><?= hmd_escape($item['title']) ?></a></h2><p class="meta">Pickup: <?= hmd_escape($item['pickup_location']) ?></p><p class="price">₹<?= hmd_escape(number_format((float) $item['asking_price'], 2)) ?></p></div>
              <form method="post" action="cart-action.php"><input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="listing_id" value="<?= (int) $item['id'] ?>"><button class="btn btn-secondary" type="submit">Remove</button></form>
            </article>
          <?php endforeach; ?>
        </section>
        <section class="cart-summary" aria-labelledby="cart-summary-title">
          <span class="eyebrow">Almost yours</span><h2 id="cart-summary-title">Order summary</h2>
          <div class="summary-row"><span><?= count($items) ?> item<?= count($items) === 1 ? '' : 's' ?> subtotal</span><strong>₹<?= hmd_escape(number_format($total, 2)) ?></strong></div>
          <div class="summary-total"><span>Total</span><strong>₹<?= hmd_escape(number_format($total, 2)) ?></strong></div>
          <form class="hmd-form" method="post" data-razorpay-checkout>
            <input type="hidden" name="csrf_token" value="<?= hmd_escape(hmd_csrf_token()) ?>">
            <fieldset><legend>How would you like to pay?</legend>
              <label class="payment-choice"><input type="radio" name="payment_method" value="cash_on_pickup" required><span><strong>Cash on pickup</strong><small>Arrange payment directly when you meet the seller.</small></span></label>
              <?php if ($razorpayTestAvailable): ?><label class="payment-choice"><input type="radio" name="payment_method" value="razorpay" required><span><strong>Razorpay <span class="test-badge">TEST</span></strong><small>Test checkout only. No real payment will be collected.</small></span></label><?php endif; ?>
            </fieldset>
            <?php if (!$razorpayTestAvailable): ?><p class="payment-note">Online payment is not set up yet. You can still reserve your order and arrange cash on pickup.</p><?php endif; ?>
            <button type="submit">Place order<?= count($items) === 1 ? '' : 's' ?></button>
            <small class="checkout-caption">Your item is only marked sold once the order is confirmed.</small>
          </form>
        </section>
      </div>
    <?php endif; ?>
  </main>
<script>
(() => {
  const form = document.querySelector('[data-razorpay-checkout]');
  if (!form) return;
  const button = form.querySelector('button[type="submit"]');
  const csrf = form.querySelector('input[name="csrf_token"]').value;
  const showError = (message) => {
    let alert = form.querySelector('[role="alert"]');
    if (!alert) {
      alert = document.createElement('p');
      alert.className = 'error';
      alert.setAttribute('role', 'alert');
      form.prepend(alert);
    }
    alert.textContent = message;
  };
  const cancelOrder = async (orderId) => {
    if (!orderId) return;
    const response = await fetch('razorpay-cancel.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'X-CSRF-Token': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ razorpay_order_id: orderId })
    });
    const result = await response.json();
    if (!response.ok) throw new Error(result.error || 'Unable to update the payment attempt.');
    if (result.redirect) window.location.assign(result.redirect);
  };
  form.addEventListener('submit', async (event) => {
    const selected = form.querySelector('input[name="payment_method"]:checked');
    if (!selected || selected.value !== 'razorpay') return;
    event.preventDefault();
    button.disabled = true;
    button.textContent = 'Connecting to Razorpay…';
    try {
      const response = await fetch('razorpay-create-order.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'X-CSRF-Token': csrf }
      });
      const order = await response.json();
      if (!response.ok) throw new Error(order.error || 'Unable to start checkout.');
      await new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        script.onload = resolve;
        script.onerror = () => reject(new Error('Razorpay checkout could not be loaded.'));
        document.head.appendChild(script);
      });
      const checkout = new Razorpay({
        key: order.key_id,
        amount: order.amount,
        currency: order.currency,
        name: order.name,
        description: order.description,
        order_id: order.order_id,
        handler: async (payment) => {
          try {
            const body = new URLSearchParams(payment);
            const verifyResponse = await fetch('razorpay-verify.php', {
              method: 'POST',
              credentials: 'same-origin',
              headers: { 'X-CSRF-Token': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
              body
            });
            const result = await verifyResponse.json();
            if (!verifyResponse.ok) throw new Error(result.error || 'Payment confirmation failed.');
            window.location.assign(result.redirect);
          } catch (error) {
            showError(error.message);
            button.disabled = false;
            button.textContent = 'Place order';
          }
        },
        modal: { ondismiss: () => {
          cancelOrder(order.order_id).catch(error => showError(error.message));
          button.disabled = false;
          button.textContent = 'Place order';
        } },
        theme: { color: '#1034a6' }
      });
      checkout.on('payment.failed', (failure) => {
        showError(failure.error && failure.error.description ? failure.error.description : 'The test payment failed. You can try again.');
        cancelOrder(order.order_id).catch(error => showError(error.message));
        button.disabled = false;
        button.textContent = 'Place order';
      });
      checkout.open();
    } catch (error) {
      showError(error.message);
      button.disabled = false;
      button.textContent = 'Place order';
    }
  });
})();
</script>
</body>
</html>
