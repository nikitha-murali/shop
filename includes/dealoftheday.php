<?php
require_once './includes/config.php';

// دریافت پیشنهاد روز
$deals_of_the_day = get_deal_of_day();
?>

<!-- Deal of the day -->
<div class="product-featured">
  <h2 class="title">Deal of the day</h2>

  <div class="showcase-wrapper has-scrollbar">
    <!-- display data from db -->
    <?php
    if (mysqli_num_rows($deals_of_the_day) > 0) {
        while ($row = mysqli_fetch_assoc($deals_of_the_day)) {
    ?>
      <div class="showcase-container">
        <div class="showcase">
          <div class="showcase-banner">
            <img src="./admin/upload/<?php echo htmlspecialchars($row['deal_image']); ?>" alt="<?php echo htmlspecialchars($row['deal_title']); ?>" class="showcase-img" />
          </div>
          <div class="showcase-content">
            <div class="showcase-rating">
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
            </div>
            <a href="./viewdetail.php?id=<?php echo htmlspecialchars($row['deal_id']); ?>&category=deal_of_day">
              <h3 class="showcase-title">
                <?php echo htmlspecialchars($row['deal_title']); ?>
              </h3>
            </a>
            <p class="showcase-desc">
                <?php echo htmlspecialchars($row['deal_description'] ?: 'Today’s special offer!'); ?>
            </p>
            <div class="price-box">
              <p class="price"><?php echo formatPrice($row['deal_net_price']); ?></p>
              <del><?php echo formatPrice($row['deal_discounted_price']); ?></del>
            </div>
            <button class="add-cart-btn">Premium</button>
            <div class="showcase-status">
              <div class="wrapper">
                <p>Sold: <b><?php echo htmlspecialchars($row['sold_deal']); ?></b></p>
                <p>Available: <b><?php echo htmlspecialchars($row['available_deal']); ?></b></p>
              </div>
              <div class="showcase-status-bar"></div>
            </div>
            <div class="countdown-box">
              <p class="countdown-desc">Hurry up! This offer ends soon:</p>
              <div class="countdown">
                <div class="countdown-content">
                  <p class="display-number" id="days-<?php echo $row['deal_id']; ?>">00</p>
                  <p class="display-text">Days</p>
                </div>
                <div class="countdown-content">
                  <p class="display-number" id="hours-<?php echo $row['deal_id']; ?>">00</p>
                  <p class="display-text">Hours</p>
                </div>
                <div class="countdown-content">
                  <p class="display-number" id="minutes-<?php echo $row['deal_id']; ?>">00</p>
                  <p class="display-text">Minutes</p>
                </div>
                <div class="countdown-content">
                  <p class="display-number" id="seconds-<?php echo $row['deal_id']; ?>">00</p>
                  <p class="display-text">Seconds</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
        }
    } else {
    ?>
      <p>Deal of the day is currently unavailable.</p>
    <?php
    }
    ?>
  </div>
</div>

<script>
// Set the end time to the end of the current day
const endTime = new Date();
endTime.setHours(23, 59, 59, 999);

function updateCountdown(dealId) {
    const now = new Date();
    const timeLeft = endTime - now;

    const daysElement = document.getElementById(`days-${dealId}`);
    const hoursElement = document.getElementById(`hours-${dealId}`);
    const minutesElement = document.getElementById(`minutes-${dealId}`);
    const secondsElement = document.getElementById(`seconds-${dealId}`);

    if (!daysElement || !hoursElement || !minutesElement || !secondsElement) {
        return; // اگر عناصر وجود نداشتند، خارج شو
    }

    if (timeLeft <= 0) {
        daysElement.parentElement.parentElement.parentElement.innerHTML = '<p>Deal of the day has ended!</p>';
        return;
    }

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    daysElement.textContent = String(days).padStart(2, '0');
    hoursElement.textContent = String(hours).padStart(2, '0');
    minutesElement.textContent = String(minutes).padStart(2, '0');
    secondsElement.textContent = String(seconds).padStart(2, '0');
}

// Start the countdown for each deal of the day
<?php
mysqli_data_seek($deals_of_the_day, 0);
while ($row = mysqli_fetch_assoc($deals_of_the_day)) {
    echo "setInterval(() => updateCountdown({$row['deal_id']}), 1000);\n";
    echo "updateCountdown({$row['deal_id']});\n";
}
?>
</script>