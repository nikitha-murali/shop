-- HandMeDown additions for the existing shop database.
-- Select the existing `web` database before running this file.

ALTER TABLE customer
    ADD COLUMN IF NOT EXISTS hostel VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS course VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS semester VARCHAR(50) NULL;

CREATE TABLE IF NOT EXISTS hmd_listings (
    id INT NOT NULL AUTO_INCREMENT,
    seller_id INT(100) NOT NULL,
    title VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    semester VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    story TEXT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    condition_score DECIMAL(3,2) NOT NULL,
    age_months INT UNSIGNED NOT NULL,
    original_price DECIMAL(10,2) NOT NULL,
    price_min DECIMAL(10,2) NOT NULL,
    price_max DECIMAL(10,2) NOT NULL,
    asking_price DECIMAL(10,2) NOT NULL,
    predicted_price DECIMAL(10,2) NULL,
    deal_badge VARCHAR(50) NULL,
    image VARCHAR(255) NOT NULL,
    allow_bids TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_hmd_listings_seller_status (seller_id, status),
    KEY idx_hmd_listings_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS hmd_bids (
    id INT NOT NULL AUTO_INCREMENT,
    listing_id INT NOT NULL,
    bidder_id INT(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_hmd_bids_listing_status (listing_id, status),
    KEY idx_hmd_bids_bidder (bidder_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS hmd_orders (
    id INT NOT NULL AUTO_INCREMENT,
    listing_id INT NOT NULL,
    buyer_id INT(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    source ENUM('buy', 'bid') NOT NULL,
    payment_method ENUM('cash_on_pickup', 'upi_demo', 'razorpay') NOT NULL DEFAULT 'cash_on_pickup',
    payment_status ENUM('pending', 'paid', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hmd_orders_listing (listing_id),
    KEY idx_hmd_orders_buyer (buyer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE hmd_orders
    ADD COLUMN IF NOT EXISTS payment_method VARCHAR(32) NOT NULL DEFAULT 'cash_on_pickup',
    ADD COLUMN IF NOT EXISTS payment_status VARCHAR(20) NOT NULL DEFAULT 'pending';

UPDATE hmd_orders SET payment_status = 'cancelled' WHERE payment_status = 'demo_recorded';
ALTER TABLE hmd_orders
    MODIFY payment_method ENUM('cash_on_pickup', 'upi_demo', 'razorpay') NOT NULL DEFAULT 'cash_on_pickup',
    MODIFY payment_status ENUM('pending', 'paid', 'failed', 'cancelled') NOT NULL DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS hmd_recommendations (
    listing_id INT NOT NULL,
    similar_id INT NOT NULL,
    score DECIMAL(8,6) NOT NULL,
    PRIMARY KEY (listing_id, similar_id),
    KEY idx_hmd_recommendations_similar (similar_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS hmd_image_vectors (
    listing_id INT NOT NULL,
    `vector` MEDIUMTEXT NOT NULL,
    PRIMARY KEY (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS hmd_payment_attempts (
    id INT NOT NULL AUTO_INCREMENT,
    listing_id INT NOT NULL,
    buyer_id INT NOT NULL,
    amount_paise INT UNSIGNED NOT NULL,
    gateway_order_id VARCHAR(64) NULL,
    gateway_payment_id VARCHAR(64) NULL,
    status ENUM('creating', 'created', 'paid', 'expired', 'failed', 'refunded') NOT NULL DEFAULT 'creating',
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hmd_payment_gateway_order (gateway_order_id),
    UNIQUE KEY uq_hmd_payment_gateway_payment (gateway_payment_id),
    KEY idx_hmd_payment_listing_status (listing_id, status, expires_at),
    KEY idx_hmd_payment_buyer (buyer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS hmd_payment_attempt_items (
    attempt_id INT NOT NULL,
    listing_id INT NOT NULL,
    amount_paise INT UNSIGNED NOT NULL,
    PRIMARY KEY (attempt_id, listing_id),
    KEY idx_hmd_payment_items_listing (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
