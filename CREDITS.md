# HandMeDown project notes

## Existing shop files and feature mapping

| File | Purpose |
| --- | --- |
| `includes/config.php` | Connects the PHP shop to the `web` MySQL database. |
| `web.sql` | Existing shop database schema and seed data. |
| `signup.php`, `includes/signup.inc.php`, `includes/signupFn.inc.php` | Customer signup form, validation, and account creation. |
| `login.php`, `logout.php` | Customer login and logout. |
| `index.php` | HandMeDown marketplace landing page, latest listings, recommendations, and stories. |
| `browse.php`, `listing.php` | Listing search/filtering and item details. |
| `sell.php`, `save-listing.php`, `my-listings.php`, `edit-listing.php` | Seller listing creation and management. |
| `place-bid.php`, `accept-bid.php`, `my-bids.php` | Bid submission, seller acceptance, and buyer bid status. |
| `buy.php`, `my-orders.php`, `payment.php` | Cash-on-pickup and simulated UPI checkout, buyer orders, and payment information. The demo does not transfer money. |
| `stories.php`, `image-search.php` | Seller stories and approximate photo search. |
| `functions/sell-functions.php`, `functions/browse-functions.php`, `functions/ml-functions.php` | HandMeDown database and pricing helpers. |
| `hmd-schema.sql` | Adds HandMeDown fields and tables to the existing `web` database. |
| `hmd-sample-data.sql` | Optional, clearly fictional local listings and orders; do not import into production. |
| `admin/listings.php`, `admin/orders.php`, `admin/charts.php` | Administrator listing/order management and marketplace charts. |
| `ml/train_price_model_synthetic.py` | Reproducibly trains an illustrative linear-regression model on synthetic data. |
| `ml/train_price_model.py`, `ml/similar_items.py`, `ml/build_image_vectors.py`, `ml/image_search_service.py` | Paid-sales trainer, recommendation generator, and approximate image-search tools. |

## Base project and pre-event work

- Base repository: [Ali Moradi Dev's PHP E-commerce Platform](https://github.com/AliDev-0/PHP-E-commerce-Platform).
- The base repository is distributed under the MIT License. Its `LICENSE` file identifies Ali Moradi, copyright 2025. Keep the original copyright and license notice with redistributed portions and review the license before deployment.
- Before the event, this repository was a PHP/MySQL e-commerce shop with existing customer/account, product catalog, cart/checkout, and administrator functionality. HandMeDown's campus listings, bids, stories, recommendations, sample data, and related workflows were added for this project.

## Libraries, assets, data, and AI

- **Bootstrap:** Bootstrap 5.2.3 is referenced by the signup page from jsDelivr; the base repository also contains local Bootstrap files.
- **Chart.js:** marketplace charts reference Chart.js from jsDelivr.
- **Python packages:** pandas, NumPy, and scikit-learn are used by the synthetic price-model trainer; Flask, mysql-connector-python, and Pillow support the optional image-search service.
- **Logo:** the team supplied the HandMeDown logo and identified Canva as its creation tool. The team should retain its Canva project/export to document authorship.
- **Synthetic listings/orders:** `hmd-sample-data.sql` contains fictional local sample accounts and orders. Its distinct product photos come from the existing shop catalog and are illustrative, not photos of real student-owned second-hand items. The data is for preview/testing only, is not real users or sales, and must not be imported into production.
- **Synthetic training data:** `ml/train_price_model_synthetic.py` deterministically generates `ml/sample_dataset.csv` and trains `ml/model.json` with scikit-learn LinearRegression. This is an illustrative demonstration model, not trained or validated on genuine marketplace sales and not evidence of production price accuracy. Do not describe the synthetic rows as real sales.
- **Paid-sales model:** `ml/train_price_model.py` is a separate trainer intended for verified paid cash-on-pickup transactions only; it requires at least 30 qualifying sales.
- **Photo search:** compares coarse color histograms and finds visual look-alikes; it does not identify exact products.
- **Payment:** cash-on-pickup and UPI demo are order-flow options. UPI demo does not initiate a transfer or mark the payment paid.
- **Campus email:** signup/profile check the configured email domain but do not verify mailbox ownership.
- **AI tools:** GitHub Copilot in VS Code was used to inspect and modify the project, write and validate marketplace code/documentation, and create the sample-data and synthetic-model tooling. Team members should add any other AI tools they used before submission.
