<?php
session_start();

$total_price = 0;
if (isset($_SESSION['mycart'])) {
    foreach ($_SESSION['mycart'] as $item) {
        $total_price += $item['price'] * $item['product_qty'];
    }
}
function format_price($price) {
    return number_format($price) . " Rial";
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Payment gateway</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            font-family: 'Vazirmatn', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background: #f3f4f6;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        .container {
            max-width: 1100px;
            margin: 10px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 0 10px;
            flex-grow: 1;
        }
        .card-box {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 10px 6px rgba(0,0,0,0.06);
        }
        .main {
            flex: 1 1 65%;
            order: 2;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .sidebar {
            flex: 1 1 30%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            order: 1;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        @media (min-width: 769px) {
            .container {
                flex-direction: row;
            }
            .main {
                order: 1;
            }
            .sidebar {
                order: 2;
            }
        }
        .top-info-box {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 10px 6px rgba(0,0,0,0.06);
            margin: 10px auto;
            max-width: 1100px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
        }
        .top-info-box p {
            font-size: 14px;
            color: #333;
        }
        .top-info-box .value {
            font-weight: bold;
            color: #1a202c;
        }
        .top-info-box img {
            width: 85px;
            height: 42px;
            object-fit: contain;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #2563eb;
            background: #e0edff;
            padding: 8px;
            border-radius: 6px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
            background-color: #f0f5ff;
        }
        .form-row {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .form-row .form-group {
            flex: 1;
        }
        .captcha {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-start;
            margin-bottom: 10px;
        }
        .captcha input {
            flex: 0.4;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
            background-color: #f0f5ff;
        }
        .captcha-image-wrapper {
            flex: 0.6;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #e0edff;
            border-radius: 6px;
            padding: 2px;
            width: auto;
            height: auto;
        }
        .captcha-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            border-radius: 4px;
            display: block;
        }
        .captcha-refresh {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #3a7bd5;
            padding: 2px;
        }
        .submit-btn, .pay-btn {
            width: 100%;
            background: #3a7bd5;
            color: white;
            padding: 8px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .submit-btn:hover, .pay-btn:hover {
            background: #2c66c7;
        }
        .checkbox {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }
        .otp-button-wrapper {
            flex: 0.6;
            margin-top: 0;
        }
        #otp-button {
            padding: 8px;
            font-size: 14px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar .info-card h3 {
            font-size: 12px;
            font-weight: normal;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .sidebar .info-card p {
            margin: 5px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        .amount-label {
            font-size: 12px;
            color: #6b7280;
        }
        .line {
            height: 1px;
            background: #e5e7eb;
            margin: 10px 0;
        }
        .small-label {
            font-size: 11px;
            color: #6b7280;
        }
        .bold-text {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }
        .time-inside {
            background: #e0edff;
            color: #1e3a8a;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            text-align: left;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                margin: 5px auto;
                padding: 0 5px;
            }
            .main, .sidebar {
                flex: 1 1 100%;
                max-height: none;
            }
            .sidebar {
                order: -1;
            }
            .main {
                order: unset;
            }
            .form-row:has(input[placeholder="ماه"]) {
                flex-direction: row;
            }
            .form-row:has(input[placeholder="ماه"]) .form-group {
                width: auto;
            }
            .captcha {
                flex-direction: row;
                align-items: center;
                justify-content: flex-start;
                flex-wrap: wrap;
            }
            .captcha input {
                flex: 0.4;
                width: auto;
            }
            .captcha-image-wrapper {
                flex: 0.6;
                width: auto;
            }
            .captcha-refresh {
                align-self: center;
            }
            .form-row:has(#otp-input) {
                flex-direction: row;
                align-items: center;
            }
            .form-row:has(#otp-input) .form-group {
                flex: 1;
            }
            .otp-button-wrapper {
                width: auto;
                margin-top: 0;
            }
            #otp-button {
                height: auto;
            }
            .top-info-box {
                flex-direction: column;
                align-items: center;
                margin: 5px auto;
                padding: 5px;
            }
            .top-info-box img {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="top-info-box">
        <img src="payimg/sep.png" alt="sep">
        <p>Payment gateway<span class="value"> Sep Online</span></p>
        <img src="payimg/shaparak.jpg" alt="shaparak">
    </div>
    <div class="container">
        <div class="card-box sidebar">
            <div class="info-card">
                <div class="time-inside">
                    Time remaining: <span id="main-timer">10:00</span>
                </div>
                <h3>Acceptor</h3>
                <p class="bold-text">Podro Store</p>
                <div class="amount-label">Amount</div>
                <p class="amount"><?php echo format_price($total_price); ?></p>
                <div class="line"></div>
                <p class="small-label">Acceptor Number / Terminal</p>
                <p class="bold-text">14875889 / 16411439</p>
                <p class="small-label">Acceptor Website</p>
                <p class="bold-text">podro.shop</p>
            </div>
        </div>
        <div class="card-box main">
            <h2>Enter your card information</h2>
            <div class="form-group">
                <label>Card number</label>
                <input type="text" maxlength="19" placeholder="---- ---- ---- ----" oninput="formatCardNumber(this)">
            </div>
            <div class="form-group">
                <label>Second Identification Number (CVV2)</label>
                <input type="text" maxlength="3" placeholder="CVV2" oninput="this.value=this.value.replace(/[^\d]/g,'');">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Expiration date</label>
                    <input type="text" maxlength="2" placeholder="Month" oninput="this.value=this.value.replace(/[^\d]/g,'');">
                </div>
                <div class="form-group">
                    <label style="color:white">Year</label>
                    <input type="text" maxlength="2" placeholder="Year" oninput="this.value=this.value.replace(/[^\d]/g,'');">
                </div>
            </div>
            <div class="form-row captcha">
                <input type="text" maxlength="5" placeholder="Security code" oninput="this.value=this.value.replace(/[^\d]/g,'');">
                <div class="captcha-image-wrapper">
                    <img id="captchaImage" src="payimg/cap1.jpg" alt="Security code" class="captcha-image">
                </div>
                <button class="captcha-refresh" title="Change code" onclick="changeCaptchaImage()">🔄</button>
            </div>
            <div class="form-row">
                <div class="form-group" style="flex: 1">
                    <label>Second password</label>
                    <input id="otp-input" type="text" maxlength="5" placeholder="Second password" oninput="this.value=this.value.replace(/[^\d]/g,'');">
                </div>
                <div class="otp-button-wrapper">
                    <button class="submit-btn" onclick="startTimer(this)" id="otp-button">Get dynamic password</button>
                </div>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="saveCard">
                <label for="saveCard">Save card number</label>
            </div>
            <button class="pay-btn">Pay</button>
        </div>
    </div>
    <script>
        function formatCardNumber(input) {
            input.value = input.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        }
        function startTimer(button) {
            let duration = 120;
            button.disabled = true;
            const originalText = "Get dynamic password";
            const interval = setInterval(() => {
                const minutes = Math.floor(duration / 60);
                const seconds = duration % 60;
                button.textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
                if (--duration < 0) {
                    clearInterval(interval);
                    button.textContent = originalText;
                    button.disabled = false;
                }
            }, 1000);
        }
        let mainTime = 600;
        const mainTimer = document.getElementById("main-timer");
        const mainInterval = setInterval(() => {
            const minutes = Math.floor(mainTime / 60);
            const seconds = mainTime % 60;
            mainTimer.textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
            if (--mainTime < 0) clearInterval(mainInterval);
        }, 1000);
        const captchaImages = [
            'payimg/cap1.jpg', 'payimg/cap2.jpg', 'payimg/cap3.jpg', 'payimg/cap4.jpg'
        ];
        let currentCaptchaIndex = 0;
        function changeCaptchaImage() {
            currentCaptchaIndex = (currentCaptchaIndex + 1) % captchaImages.length;
            const captchaImageElement = document.getElementById('captchaImage');
            captchaImageElement.src = captchaImages[currentCaptchaIndex];
        }
        document.addEventListener('DOMContentLoaded', () => {
            changeCaptchaImage();
        });
    </script>
</body>
</html>