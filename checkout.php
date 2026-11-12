<?php include_once('./includes/headerNav.php'); ?>
<div class="overlay" data-overlay></div>

<header>
    <?php require_once './includes/topheadactions.php'; ?>
    <?php require_once './includes/mobilenav.php'; ?>

    <style>
        * {
            font-family: 'Vazir', Arial, sans-serif;
            box-sizing: border-box;
        }

        :root {
            --main-color: #007BFF; /* رنگ اصلی آبی */
            --secondary-color: #6610f2; /* رنگ فرعی بنفش */
            --dark-color: #2C3E50; /* رنگ تیره */
            --light-color: #ECF0F1; /* رنگ روشن */
            --blur-effect: 5px;
            --transition-duration: 0.3s;
            --glass-bg: rgba(255, 255, 255, 0.1); /* پس‌زمینه شیشه‌ای */
            --neon-glow: 0 0 5px #007BFF, 0 0 10px #007BFF; /* افکت نئون کم‌شدت */
        }

        body {
            background-color: #ffffff; /* پس‌زمینه سفید */
            font-family: 'Vazir', Arial, sans-serif;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        .appointments-section {
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            animation: fadeInUp 1s ease-in-out forwards;
        }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
            from { opacity: 0; transform: translateY(20px); }
        }

        input {
            border: none;
            outline: none;
            background-color: transparent;
            color: #000000;
            font-weight: 700;
            width: 100%;
        }

        .appointment-heading {
            text-align: center;
        }

        .appointment-head {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 0;
            color: var(--main-color);
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .appointment-line {
            width: 160px;
            height: 3px;
            border-radius: 10px;
            background-color: var(--main-color);
            display: inline-block;
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .edit-detail-field .child-detail-inner {
            width: 100%;
            display: flex;
            margin-top: 10px;
            justify-content: space-between;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .Add-child-section {
            margin-top: 40px;
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .Add-child-section .child-heading-t {
            font-size: 25px;
            font-weight: 700;
            color: var(--main-color);
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .Add-child-section .child-fields1,
        .Add-child-section .child-fields2,
        .Add-child-section .child-fields3,
        .Add-child-section .child-fields4,
        .Add-child-section .child-fields5 {
            width: 49%;
            height: 55px;
            border: 1px solid var(--main-color);
            border-radius: 5px;
            margin-bottom: 30px;
            padding: 15px;
            background-color: var(--glass-bg);
            backdrop-filter: blur(10px);
            position: relative;
            box-shadow: var(--neon-glow);
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .Add-child-section .child-fields5 {
            width: 100%;
        }

        .Add-child-section .child-fields1::before,
        .Add-child-section .child-fields2::before,
        .Add-child-section .child-fields3::before,
        .Add-child-section .child-fields4::before,
        .Add-child-section .child-fields5::before {
            position: absolute;
            top: -10px;
            background-image: linear-gradient(#FFFCF6, #FFFFFF);
            padding-left: 4px;
            padding-right: 4px;
            color: var(--main-color);
            font-weight: 600;
            font-size: 13px;
        }

        .Add-child-section .child-fields1::before {
            content: "Game ID (ID)";
        }

        .Add-child-section .child-fields2::before {
            content: "Game name";
        }

        .Add-child-section .child-fields3::before {
            content: "Name";
        }

        .Add-child-section .child-fields4::before {
            content: "Mobile number";
        }

        .Add-child-section .child-fields5::before {
            content: "Email (optional)";
        }

        .Add-child-section input {
            color: #000000;
            font-weight: 700;
            width: 100%;
            background-color: transparent;
        }

        .child-register-btn {
            padding-top: 5px;
            animation: fadeInUp 1s ease-in-out forwards;
        }

        .child-register-btn p {
            width: 550px;
            height: 60px;
            background-color: var(--main-color);
            box-shadow: var(--neon-glow);
            line-height: 60px;
            color: #FFFFFF;
            margin-left: auto;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            font-size: 19px;
            font-weight: 600;
            transition: background-color var(--transition-duration), transform var(--transition-duration);
            animation: neonPulse 1.5s infinite alternate;
        }

        @keyframes neonPulse {
            from { box-shadow: var(--neon-glow); }
            to { box-shadow: 0 0 15px var(--main-color), 0 0 30px var(--main-color); }
        }

        .child-register-btn p:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        @media screen and (max-width: 794px) {
            .child-register-btn p {
                width: 100%;
            }

            .edit-detail-field .child-detail-inner {
                width: 100%;
                display: flex;
                flex-direction: column;
                margin-top: 0px;
                justify-content: unset;
            }

            .child-fields1,
            .child-fields2,
            .child-fields3,
            .child-fields4,
            .child-fields5 {
                width: 100% !important;
            }
        }

        @media screen and (max-width: 629px) {
            .Add-child-section {
                width: 90%;
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media screen and (max-width: 628px) {
            .appointments-section {
                width: 100% !important;
            }
        }

        .error-ms {
            color: var(--main-color);
            margin-bottom: 10px;
            animation: fadeInUp 1s ease-in-out forwards;
        }
    </style>
</header>

<body>
    <div class="appointments-section">
        <div class="appointment-heading">
            <p class="appointment-head">Complete purchase information</p>
            <span class="appointment-line"></span>
        </div>

        <div class="inner-appointment">
            <section class="edit-detail-field">
                <div class="Add-child-section">
                    <div class="child-detail-inner">
                        <div class="child-fields1">
                            <input type="text" style="color: #676767;" placeholder="Example: 123456789">
                        </div>
                        <div class="child-fields2">
                            <input type="text" style="color: #676767;" placeholder="Example: Clash of Clans">
                        </div>
                    </div>
                    <div class="child-detail-inner">
                        <div class="child-fields3">
                            <input type="text" placeholder="Example: John Smith">
                        </div>
                        <div class="child-fields4">
                            <input type="text" placeholder="Example: 09121234567">
                        </div>
                    </div>
                    <div class="child-detail-inner">
                        <div class="child-fields5">
                            <input type="text" placeholder="Example: example@email.com">
                        </div>
                    </div>
                    <div class="child-register-btn">
                        <span class="error-ms"></span>
                        <p onclick="window.location.href='payment.php'">Proceed to payment</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        let fields = document.querySelectorAll('.child-fields1 input, .child-fields2 input, .child-fields3 input, .child-fields4 input');
        let error = document.querySelector('.error-ms');
        function checkFields() {
            for (let i = 0; i < fields.length; i++) {
                if (fields[i].value === '') {
                    error.innerHTML = "Please fill in all required fields.";
                    return;
                }
            }
            error.innerHTML = "Redirecting to payment...";
        }
    </script>
</body>

<?php require_once './includes/footer.php'; ?>