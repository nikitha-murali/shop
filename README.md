# HandMeDown Campus Marketplace

[![PHP Version][php-shield]][php-url]
[![MySQL Shield][mysql-shield]][mysql-url]
[![License: MIT][license-shield]][license-url]
[![GitHub issues][issues-shield]][issues-url]
[![GitHub forks][forks-shield]][forks-url]
[![GitHub stars][stars-shield]][stars-url]

A student-to-student campus resale marketplace added to this existing PHP and MySQL shop repository. Students can list and discover second-hand items, place bids, and record cash-on-pickup or simulated UPI orders. The UPI option does not transfer money. The original shop database is retained; HandMeDown data is stored in separate `hmd_` tables.

---

### ✨ Key Features

The project includes a student marketplace and an administrator area for managing listings, orders, and marketplace analytics.

#### 🛍️ Customer-Facing Features:
- **Student accounts:** Signup captures hostel, course, and semester, and can restrict addresses to configured campus email domains.
- **Campus listings:** Students can post items, images, stories, condition, pickup details, and price ranges.
- **Browse and recommendations:** Search/filter listings and see course-based or similar-item suggestions.
- **Bidding and demo orders:** Place bids, accept a bid, or record an order without processing real payments.
- **Illustrative price model:** A scikit-learn linear-regression model demonstrates price badges using synthetic sample data; it is not trained on real sales.
- **Approximate photo search:** Compare image color histograms to find visual look-alikes.

#### ⚙️ Admin Panel Features:
- **Listing and order management:** Review campus listings and orders in the admin area.
- **Marketplace charts:** View sold listings by category, average savings versus original price, and sold-listing share (not a page-view conversion rate).

---

### 🛠️ Tech Stack

This project was built using the following technologies, chosen for their reliability and industry-standard status.

| Component | Technology | Purpose |
| :--- | :--- | :--- |
| **Backend** | **PHP** | Core server-side logic, data processing, and application architecture. |
| **Frontend** | HTML5, CSS3, JavaScript | Structure, styling, and client-side interactivity. |
| **Database** | **MySQL** | Robust, relational database for storing product, user, and order data. |
| **Web Server**| Apache / Nginx | Powers the application. Fully compatible with local environments like XAMPP. |

---

### 🚀 Getting Started

Follow these steps to set up and run the project on your local machine.

#### Prerequisites
- A local server environment such as [XAMPP](https://www.apachefriends.org/index.html) or [WAMP](http://www.wampserver.com/en/) must be installed.
- Access to a database management tool like `phpMyAdmin`.

#### Installation Steps
Use the [HandMeDown campus marketplace setup](#handmedown-campus-marketplace) below after placing this repository under XAMPP's `htdocs` directory. The database is configured in `includes/config.php`.

---

## HandMeDown campus marketplace

This repository now includes a student resale marketplace alongside its original shop files. The marketplace stores its data in the existing `web` database and keeps its additional tables prefixed with `hmd_`.

The HandMeDown brand palette uses Egyptian blue (`#1034A6`), stone blue (`#718DA5`), and scarlet red (`#C9364B`) as a restrained accent. The team-supplied logo has a transparent, palette-matched version at `images/brand/handmedown-logo.png`; the homepage includes responsive campus-shopping and seller banners. Item stories are presented as the previous owner's backstory, not as a second product description.

### Local setup

1. Start Apache and MySQL in XAMPP, create/select the `web` database, and import `web.sql` only if the existing shop tables are not already present.
2. Back up the database, then run `hmd-schema.sql` against `web` to add student fields and marketplace tables.
3. Optional, local-only preview data: run `hmd-sample-data.sql` after the schema. It adds 20 clearly fictional listings with varied product photos from the existing shop catalog, plus five pending sample orders using `@handmedown.invalid` accounts. Never import this sample data into production; the photos are catalog examples, not photos of real student-owned second-hand items.
4. Check `includes/config.php` for the local MySQL credentials.
5. Signup allows `presidencyuniversity.in` by default. Override the allowed domain list with the `HMD_COLLEGE_EMAIL_DOMAINS` environment variable; multiple domains can be comma-separated. Signup checks the email domain but does not verify mailbox ownership.
6. To generate the illustrative sample price model, install `pandas`, `numpy`, and `scikit-learn`, then run `python ml/train_price_model_synthetic.py`. It creates `ml/model.json` and `ml/sample_dataset.csv` from deterministic synthetic data. Price badges are demonstrations only; the estimates are not based on verified transactions.
7. `python ml/train_price_model.py` is a separate real-sales trainer. It requires at least 30 verified paid cash-on-pickup orders and does not use or create synthetic training data.
8. To enable recommendations and photo search, install `ml/requirements.txt`, set `HMD_DB_HOST`, `HMD_DB_PORT`, `HMD_DB_USER`, `HMD_DB_PASSWORD`, and `HMD_DB_NAME` for Python, and run:
   - `python ml/similar_items.py`
   - `python ml/build_image_vectors.py`
   - `python ml/image_search_service.py`
9. Open the project at `http://localhost/shop/`.

Photo matching compares color histograms to find visual look-alikes; it does not identify exact products.

### Demo checkout

Checkout offers **Cash on pickup** and **UPI (demo)**. Both save an order and mark the listing sold; the UPI demo does not open a payment app, collect payment details, transfer money, or mark payment as paid. Do not present it as a live payment gateway. Review `CREDITS.md` for the project file map and data/AI notes.

---

### 📄 License

This project is distributed under the MIT License. See the `LICENSE` file for more information.

---

### 👨‍💻 Author

**Ali Moradi Dev**

*   **GitHub:** [https://github.com/AliDev-0](https://github.com/AliDev-0)
*   **LinkedIn:** [https://www.linkedin.com/in/alimoradidev](https://www.linkedin.com/in/alimoradidev)
*   **Portfolio:** [https://alidev-0.github.io](https://alidev-0.github.io)

---

<!-- README Badges -->
[php-shield]: https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php
[php-url]: https://www.php.net/
[mysql-shield]: https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql
[mysql-url]: https://www.mysql.com/
[license-shield]: https://img.shields.io/github/license/AliDev-0/PHP-E-commerce-Platform?style=for-the-badge
[license-url]: https://github.com/AliDev-0/PHP-E-commerce-Platform/blob/main/LICENSE
[issues-shield]: https://img.shields.io/github/issues/AliDev-0/PHP-E-commerce-Platform?style=for-the-badge
[issues-url]: https://github.com/AliDev-0/PHP-E-commerce-Platform/issues
[forks-shield]: https://img.shields.io/github/forks/AliDev-0/PHP-E-commerce-Platform?style=for-the-badge
[forks-url]: https://github.com/AliDev-0/PHP-E-commerce-Platform/network/members
[stars-shield]: https://img.shields.io/github/stars/AliDev-0/PHP-E-commerce-Platform?style=for-the-badge
[stars-url]: https://github.com/AliDev-0/PHP-E-commerce-Platform/stargazers






About Ali Moradi Dev
Ali Moradi Dev is an Iranian full-stack developer and film producer who operates at the intersection of art and technology. Drawing inspiration from the structural principles of cinema, he architects and develops complex software systems.
Official Website: alidev-0.github.io
IMDb Profile: imdb.com/name/nm17111758
Technical Projects on GitHub: github.com/AliDev-0
Professional Profile on LinkedIn: linkedin.com/in/alimoradidev
