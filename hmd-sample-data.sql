-- Clearly synthetic local sample data for HandMeDown.
-- Do not import this file into production; the accounts and orders are fictional.
-- Import hmd-schema.sql first, then run this file against the web database.

START TRANSACTION;

INSERT IGNORE INTO customer
    (customer_fname, customer_email, customer_pwd, customer_phone, customer_address, customer_role, hostel, course, semester)
VALUES
    ('Sample Textbook Seller', 'sample-textbooks@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3'),
    ('Sample Notes Seller', 'sample-notes@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3'),
    ('Sample Electronics Seller', 'sample-electronics@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3'),
    ('Sample Hostel Seller', 'sample-hostel@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3'),
    ('Sample Cycle Seller', 'sample-cycle@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3'),
    ('Sample Buyer', 'sample-buyer@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3');

SET @seller_textbooks = (SELECT customer_id FROM customer WHERE customer_email = 'sample-textbooks@handmedown.invalid');
SET @seller_notes = (SELECT customer_id FROM customer WHERE customer_email = 'sample-notes@handmedown.invalid');
SET @seller_electronics = (SELECT customer_id FROM customer WHERE customer_email = 'sample-electronics@handmedown.invalid');
SET @seller_hostel = (SELECT customer_id FROM customer WHERE customer_email = 'sample-hostel@handmedown.invalid');
SET @seller_cycle = (SELECT customer_id FROM customer WHERE customer_email = 'sample-cycle@handmedown.invalid');
SET @sample_buyer = (SELECT customer_id FROM customer WHERE customer_email = 'sample-buyer@handmedown.invalid');

DROP TEMPORARY TABLE IF EXISTS hmd_sample_seed_ids;
CREATE TEMPORARY TABLE hmd_sample_seed_ids (id INT PRIMARY KEY);
INSERT INTO hmd_sample_seed_ids (id)
SELECT id FROM hmd_listings
WHERE title LIKE 'HMD sample:%'
  AND seller_id IN (@seller_textbooks, @seller_notes, @seller_electronics, @seller_hostel, @seller_cycle);

DELETE r FROM hmd_recommendations r
JOIN hmd_sample_seed_ids s ON s.id = r.listing_id OR s.id = r.similar_id;
DELETE b FROM hmd_bids b JOIN hmd_sample_seed_ids s ON s.id = b.listing_id;
DELETE o FROM hmd_orders o JOIN hmd_sample_seed_ids s ON s.id = o.listing_id;
DELETE v FROM hmd_image_vectors v JOIN hmd_sample_seed_ids s ON s.id = v.listing_id;
DELETE l FROM hmd_listings l JOIN hmd_sample_seed_ids s ON s.id = l.id;

INSERT INTO hmd_listings
    (seller_id, title, category, course, semester, description, story, pickup_location,
     condition_score, age_months, original_price, price_min, price_max, asking_price,
     predicted_price, deal_badge, image, allow_bids, status, created_at)
VALUES
    (@seller_textbooks, 'HMD sample: Introduction to Algorithms, 4th Edition', 'Textbook', 'Computer Science', '3', 'Synthetic sample listing. A well-kept algorithms textbook for a new student.', 'Used throughout my first algorithms course; ready for another student.', 'Library entrance', 4.5, 14, 4200, 1300, 2200, 1750, 1900, 'Fair', 'hmd-sample-textbook.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@seller_textbooks, 'HMD sample: Discrete Mathematics textbook', 'Textbook', 'Computer Science', '2', 'Synthetic sample listing. Discrete mathematics text with light highlighting.', 'This made proof practice much easier for me.', 'Main campus gate', 4.0, 18, 1800, 500, 1000, 750, 800, 'Fair', 'hmd-sample-textbook.png', 0, 'active', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (@seller_textbooks, 'HMD sample: Database Systems textbook', 'Textbook', 'Computer Science', '4', 'Synthetic sample listing. Database concepts text in good condition.', 'A useful companion for the database course.', 'Library entrance', 4.3, 10, 2500, 800, 1400, 1100, 1250, 'Fair', 'hmd-sample-textbook.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (@seller_textbooks, 'HMD sample: Operating Systems textbook', 'Textbook', 'Computer Science', '4', 'Synthetic sample listing. Operating systems textbook with a few notes in the margins.', 'Helped me revise before exams; passing it on now.', 'Academic block', 4.2, 20, 2200, 650, 1200, 900, 980, 'Fair', 'hmd-sample-textbook.png', 0, 'sold', DATE_SUB(NOW(), INTERVAL 10 DAY)),

    (@seller_notes, 'HMD sample: Data Structures revision notes', 'Notes', 'Computer Science', '3', 'Synthetic sample listing. Organized revision notes with diagrams and practice problems.', 'I made these while preparing for the midterm.', 'Academic block', 4.8, 2, 500, 100, 300, 180, 210, 'Great deal', 'hmd-sample-notes.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (@seller_notes, 'HMD sample: Calculus lecture notes', 'Notes', 'Mathematics', '1', 'Synthetic sample listing. Complete first-semester calculus notes.', 'Sharing these because the worked examples helped me a lot.', 'Library entrance', 4.7, 6, 350, 80, 200, 125, 150, 'Great deal', 'hmd-sample-notes.png', 0, 'active', DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@seller_notes, 'HMD sample: Computer Networks summaries', 'Notes', 'Computer Science', '5', 'Synthetic sample listing. Concise chapter summaries for computer networks.', 'A quick review set for the final units.', 'Main campus gate', 4.4, 4, 400, 100, 240, 160, 185, 'Fair', 'hmd-sample-notes.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@seller_notes, 'HMD sample: Statistics problem workbook', 'Notes', 'Mathematics', '2', 'Synthetic sample listing. Practice workbook with solutions and clean pages.', 'Good extra practice for probability and statistics.', 'Academic block', 4.6, 8, 600, 150, 360, 250, 290, 'Fair', 'hmd-sample-notes.png', 0, 'sold', DATE_SUB(NOW(), INTERVAL 9 DAY)),

    (@seller_electronics, 'HMD sample: Scientific calculator', 'Electronics', 'Engineering', '2', 'Synthetic sample listing. Tested scientific calculator with protective cover.', 'Used for several semesters and still works reliably.', 'Student centre', 4.5, 18, 1600, 500, 950, 700, 790, 'Fair', 'hmd-sample-electronics.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (@seller_electronics, 'HMD sample: USB-C desk lamp', 'Electronics', 'Any course', '1', 'Synthetic sample listing. Adjustable LED desk lamp with USB-C power.', 'A handy light for late study sessions.', 'Hostel reception', 4.1, 12, 1200, 350, 750, 520, 600, 'Fair', 'hmd-sample-electronics.png', 0, 'active', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (@seller_electronics, 'HMD sample: Wired headphones', 'Electronics', 'Any course', '1', 'Synthetic sample listing. Working wired headphones with an inline microphone.', 'Great for online classes; I switched to wireless.', 'Student centre', 4.0, 9, 900, 250, 600, 420, 470, 'Fair', 'hmd-sample-electronics.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (@seller_electronics, 'HMD sample: Compact Bluetooth speaker', 'Electronics', 'Any course', '1', 'Synthetic sample listing. Portable speaker with charging cable.', 'Used mostly in the common room.', 'Hostel reception', 4.2, 16, 2000, 600, 1200, 850, 940, 'Fair', 'hmd-sample-electronics.png', 0, 'sold', DATE_SUB(NOW(), INTERVAL 8 DAY)),

    (@seller_hostel, 'HMD sample: Study desk organizer', 'Hostel Essentials', 'Any course', '1', 'Synthetic sample listing. Compact organizer for books, stationery, and cables.', 'Helped keep my desk tidy in a small room.', 'Hostel reception', 4.4, 7, 700, 180, 420, 300, 340, 'Fair', 'hmd-sample-hostel.png', 0, 'active', DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@seller_hostel, 'HMD sample: Reusable laundry basket', 'Hostel Essentials', 'Any course', '1', 'Synthetic sample listing. Foldable laundry basket in good condition.', 'Folds flat when you need more room.', 'Hostel reception', 4.1, 10, 650, 150, 380, 250, 300, 'Fair', 'hmd-sample-hostel.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (@seller_hostel, 'HMD sample: Stainless steel kettle', 'Hostel Essentials', 'Any course', '1', 'Synthetic sample listing. Working compact kettle, cleaned and ready for pickup.', 'Useful for tea and quick snacks.', 'Student centre', 4.3, 11, 1400, 400, 850, 620, 710, 'Fair', 'hmd-sample-hostel.png', 0, 'active', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@seller_hostel, 'HMD sample: Clip-on reading light', 'Hostel Essentials', 'Any course', '1', 'Synthetic sample listing. Small reading light with adjustable neck.', 'Passed quiet study nights with this little light.', 'Hostel reception', 4.6, 5, 500, 120, 300, 200, 240, 'Great deal', 'hmd-sample-hostel.png', 0, 'sold', DATE_SUB(NOW(), INTERVAL 7 DAY)),

    (@seller_cycle, 'HMD sample: Single-speed campus cycle', 'Cycle', 'Any course', '1', 'Synthetic sample listing. Functional single-speed cycle; inspect before purchase.', 'Made getting across campus much easier.', 'Sports complex', 4.0, 24, 8500, 3000, 5200, 4200, 4500, 'Fair', 'hmd-sample-cycle.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (@seller_cycle, 'HMD sample: U-lock for bicycle', 'Cycle', 'Any course', '1', 'Synthetic sample listing. Sturdy bicycle U-lock with two keys.', 'No longer needed after moving closer to class.', 'Sports complex', 4.5, 15, 1200, 350, 750, 550, 630, 'Fair', 'hmd-sample-cycle.png', 0, 'active', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (@seller_cycle, 'HMD sample: Front and rear cycle lights', 'Cycle', 'Any course', '1', 'Synthetic sample listing. Rechargeable front and rear safety lights.', 'Both lights work; USB charging cable included.', 'Main campus gate', 4.1, 8, 1000, 250, 600, 400, 460, 'Fair', 'hmd-sample-cycle.png', 1, 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (@seller_cycle, 'HMD sample: Adjustable cycle helmet', 'Cycle', 'Any course', '1', 'Synthetic sample listing. Adjustable helmet in good condition; inspect fit before use.', 'Kept me comfortable on my campus rides.', 'Sports complex', 4.3, 13, 1800, 500, 1000, 750, 830, 'Fair', 'hmd-sample-cycle.png', 0, 'sold', DATE_SUB(NOW(), INTERVAL 6 DAY));

INSERT INTO hmd_orders
    (listing_id, buyer_id, price, source, payment_method, payment_status, created_at)
SELECT l.id, @sample_buyer, l.asking_price, 'buy', 'cash_on_pickup', 'pending',
       DATE_ADD(l.created_at, INTERVAL 24 HOUR)
FROM hmd_listings l
WHERE l.title IN (
    'HMD sample: Operating Systems textbook',
    'HMD sample: Statistics problem workbook',
    'HMD sample: Compact Bluetooth speaker',
    'HMD sample: Clip-on reading light',
    'HMD sample: Adjustable cycle helmet'
)
  AND l.status = 'sold'
  AND l.seller_id IN (@seller_textbooks, @seller_notes, @seller_electronics, @seller_hostel, @seller_cycle);

COMMIT;
