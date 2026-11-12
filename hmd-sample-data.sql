-- Clearly synthetic local sample data for HandMeDown.
-- Do not import this file into production; the accounts and orders are fictional.
-- Import hmd-schema.sql first, then run this file against the web database.

START TRANSACTION;

INSERT IGNORE INTO customer
    (customer_fname, customer_email, customer_pwd, customer_phone, customer_address, customer_role, hostel, course, semester)
VALUES
    ('Sample Bags Seller', 'sample-textbooks@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Any course', '1'),
    ('Sample Shoes Seller', 'sample-notes@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Any course', '1'),
    ('Sample Sports Seller', 'sample-electronics@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Any course', '1'),
    ('Sample Outerwear Seller', 'sample-hostel@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Any course', '1'),
    ('Sample Watches Seller', 'sample-cycle@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Any course', '1'),
    ('Sample Buyer', 'sample-buyer@handmedown.invalid', '$2y$12$IAx2kbvzZxjM0Yl99ptyRun4Iu1e.CcK2cO8QjitggWCrpDj8.Ele', '0000000000', 'Synthetic sample account - not a real pickup address', 'normal', 'Sample hostel', 'Computer Science', '3');

UPDATE customer SET customer_fname = 'Sample Bags Seller', course = 'Any course', semester = '1' WHERE customer_email = 'sample-textbooks@handmedown.invalid';
UPDATE customer SET customer_fname = 'Sample Shoes Seller', course = 'Any course', semester = '1' WHERE customer_email = 'sample-notes@handmedown.invalid';
UPDATE customer SET customer_fname = 'Sample Sports Seller', course = 'Any course', semester = '1' WHERE customer_email = 'sample-electronics@handmedown.invalid';
UPDATE customer SET customer_fname = 'Sample Outerwear Seller', course = 'Any course', semester = '1' WHERE customer_email = 'sample-hostel@handmedown.invalid';
UPDATE customer SET customer_fname = 'Sample Watches Seller', course = 'Any course', semester = '1' WHERE customer_email = 'sample-cycle@handmedown.invalid';

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
WHERE seller_id IN (@seller_textbooks, @seller_notes, @seller_electronics, @seller_hostel, @seller_cycle);

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
    (@seller_textbooks, 'Canvas everyday tote', 'Bags', 'Any course', '1', 'A roomy canvas tote with sturdy straps and plenty of space for notebooks and daily essentials.', 'This bag came with me to my first club fair, then practically every lecture after that. I finally have a smaller bag, so I hope someone else gets as much use out of it as I did.', 'Library entrance', 4.5, 8, 1800, 500, 1100, 800, 920, 'Fair', 'bag1.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@seller_textbooks, 'Brown mini backpack', 'Bags', 'Any course', '1', 'Compact brown backpack with a secure flap and adjustable straps.', 'I bought this for days when I only needed a notebook and water bottle. It made my commute much easier, but I need more room for my laptop now.', 'Main campus gate', 3.0, 14, 2200, 600, 1300, 900, 980, 'Fair', 'bag2.jpg', 0, 'active', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (@seller_textbooks, 'Black everyday backpack', 'Bags', 'Any course', '1', 'Classic black backpack with several pockets for everyday essentials.', 'This backpack carried my laptop through late library sessions and group projects. I have switched to a smaller one, so this dependable bag is ready for another student.', 'Library entrance', 4.3, 10, 2600, 800, 1500, 1150, 1280, 'Fair', 'bag3.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (@seller_textbooks, 'Quilted shoulder bag', 'Bags', 'Any course', '1', 'Black quilted shoulder bag with a roomy main compartment.', 'I carried this to my first campus presentation and it has been my everyday bag ever since. I am downsizing, but it still has plenty of adventures left.', 'Academic block', 4.2, 18, 3000, 700, 1600, 1050, 1180, 'Fair', 'bag4.jpg', 0, 'sold', DATE_SUB(NOW(), INTERVAL 10 DAY)),

    (@seller_notes, 'Patterned ballet flats', 'Shoes', 'Any course', '1', 'Comfortable patterned flats in good shape. Please check the photos for the design.', 'I wore these to a graduation celebration and got so many compliments. They are still comfortable, but I have too many pairs, so I would love for someone else to enjoy them.', 'Academic block', 5.0, 5, 1800, 400, 1000, 650, 760, 'Great deal', 'p10.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (@seller_notes, 'Classic lace-up shoes', 'Shoes', 'Any course', '1', 'Brown lace-up shoes with a polished finish and clean soles.', 'These were my go-to shoes for presentations and interviews. They have been looked after well; I am passing them on because I found a pair that fits me better.', 'Library entrance', 3.7, 7, 3200, 900, 1900, 1350, 1500, 'Great deal', 'shoe-1.jpg', 0, 'active', DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@seller_notes, 'Brown leather-look shoes', 'Shoes', 'Any course', '1', 'Formal brown shoes with a simple, versatile style.', 'I saved these for special occasions and wore them to a few campus events. They are still in good shape, and I would rather see them get used than sit in my cupboard.', 'Main campus gate', 4.4, 9, 3500, 900, 2000, 1400, 1570, 'Fair', 'shoe-2.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@seller_notes, 'Black campus dress shoes', 'Shoes', 'Any course', '1', 'Black lace-up shoes with a classic shape and low heel.', 'I wore these through a full semester of presentations and campus events. My new pair fits better, so I am happy these have already found a new owner.', 'Academic block', 4.6, 11, 3000, 800, 1750, 1250, 1390, 'Fair', 'shoe-3.jpg', 0, 'sold', DATE_SUB(NOW(), INTERVAL 9 DAY)),

    (@seller_electronics, 'White golf shoes', 'Sports & Outdoors', 'Any course', '1', 'Lightweight white golf shoes with a clean upper and grippy sole.', 'I picked these up for a few games with friends and they made those weekends memorable. I do not play as often now, so I hope another sports lover can take them out again.', 'Student centre', 4.5, 12, 5200, 1500, 3000, 2200, 2500, 'Fair', 'sports-1.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (@seller_electronics, 'Dark trail trainers', 'Sports & Outdoors', 'Any course', '1', 'Dark sport shoes with a sturdy sole for walks and outdoor activities.', 'These have joined me on plenty of evening walks around campus. They have some wear from those miles, but still have more paths to explore.', 'Hostel reception', 3.2, 10, 4200, 1000, 2400, 1650, 1880, 'Fair', 'sports-2.jpg', 0, 'active', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (@seller_electronics, 'Blue high-top trainers', 'Sports & Outdoors', 'Any course', '1', 'Blue high-top trainers with a cushioned sole.', 'I bought these for sports and wore them for a handful of games. My routine changed, and they deserve to be worn more than they are in my wardrobe.', 'Student centre', 4.0, 8, 3900, 950, 2200, 1500, 1740, 'Fair', 'sports-3.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (@seller_electronics, 'Black and lime trainers', 'Sports & Outdoors', 'Any course', '1', 'Black trainers with lime accents and a textured outdoor sole.', 'These were my favorite pair for weekend walks. I have moved on to another size, and I am glad this pair is getting a second life.', 'Hostel reception', 4.2, 13, 4400, 1000, 2500, 1700, 1930, 'Fair', 'sports-4.jpg', 0, 'sold', DATE_SUB(NOW(), INTERVAL 8 DAY)),

    (@seller_hostel, 'Brown casual jacket', 'Clothing', 'Any course', '1', 'Brown casual jacket with a collared neckline and front pockets.', 'This jacket kept me warm on early mornings when I was rushing to class. The weather is warmer now, so I am passing it along before the next cold spell.', 'Hostel reception', 4.4, 8, 4800, 1200, 2700, 1900, 2180, 'Fair', 'jacket-1.jpg', 0, 'active', DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (@seller_hostel, 'Dark winter coat', 'Clothing', 'Any course', '1', 'Warm dark-brown coat in a timeless button-front style.', 'I bought this for a chilly semester and it got me through many early lectures. It has been freshly cleaned, and I hope it keeps someone else cozy.', 'Hostel reception', 3.0, 10, 6200, 1500, 3400, 2300, 2640, 'Fair', 'jacket-2.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (@seller_hostel, 'Grey everyday jacket', 'Clothing', 'Any course', '1', 'Grey jacket with a simple shape that is easy to layer.', 'This was my reliable grab-and-go layer for long days on campus. I am clearing some closet space, but it is still in very good shape.', 'Student centre', 4.3, 11, 5000, 1300, 2900, 2050, 2320, 'Fair', 'jacket-3.jpg', 0, 'active', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (@seller_hostel, 'Blue campus jacket', 'Clothing', 'Any course', '1', 'Blue jacket with a neat fit and practical pockets.', 'I wore this almost every day during my first year. I have outgrown it, and I am happy it has found someone who can give it another season.', 'Hostel reception', 4.6, 5, 5400, 1400, 3100, 2200, 2510, 'Great deal', 'jacket-4.jpg', 0, 'sold', DATE_SUB(NOW(), INTERVAL 7 DAY)),

    (@seller_cycle, 'Blue-strap smartwatch', 'Watches', 'Any course', '1', 'Digital smartwatch with a bright blue strap. Please check the photos for the exact model and features.', 'This watch helped me keep track of busy class days and walks across campus. I upgraded recently, so I hope it becomes a useful everyday companion for someone else.', 'Sports complex', 3.5, 16, 6500, 1700, 3600, 2500, 2840, 'Fair', 'watch-1.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (@seller_cycle, 'Minimal black watch', 'Watches', 'Any course', '1', 'Minimal black wristwatch with a clean, easy-to-read face.', 'I wore this simple watch every day because it went with everything. I have started wearing another one, and this one still deserves wrist time.', 'Sports complex', 4.5, 9, 4200, 1100, 2400, 1700, 1940, 'Fair', 'watch-2.jpg', 0, 'active', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (@seller_cycle, 'Gold-tone classic watch', 'Watches', 'Any course', '1', 'Gold-tone analog wristwatch with a classic round face.', 'This was a gift I wore for special campus occasions. I have kept it carefully in its case, and I hope it makes another celebration feel special.', 'Main campus gate', 4.1, 8, 5000, 1300, 2800, 1950, 2220, 'Fair', 'watch-3.jpg', 1, 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (@seller_cycle, 'Vintage pocket watch', 'Watches', 'Any course', '1', 'Decorative vintage-style pocket watch with a hinged cover.', 'I found this in a family keepsake box and kept it as a little reminder of home. I no longer display it, so I hope it finds a new place to be appreciated.', 'Sports complex', 4.3, 13, 3600, 900, 2000, 1400, 1590, 'Fair', 'watch-4.jpg', 0, 'sold', DATE_SUB(NOW(), INTERVAL 6 DAY));

INSERT INTO hmd_orders
    (listing_id, buyer_id, price, source, payment_method, payment_status, created_at)
SELECT l.id, @sample_buyer, l.asking_price, 'buy', 'cash_on_pickup', 'pending',
       DATE_ADD(l.created_at, INTERVAL 24 HOUR)
FROM hmd_listings l
WHERE l.title IN (
    'Quilted shoulder bag',
    'Black campus dress shoes',
    'Black and lime trainers',
    'Blue campus jacket',
    'Vintage pocket watch'
)
  AND l.status = 'sold'
  AND l.seller_id IN (@seller_textbooks, @seller_notes, @seller_electronics, @seller_hostel, @seller_cycle);

COMMIT;
