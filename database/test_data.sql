-- Sample auction data for testing
INSERT OR IGNORE INTO auctions (id, user_id, category_id, title, description, starting_price, current_price, status, featured, end_time) VALUES
(1, 1, 1, 'iPhone 14 Pro - Erinomainen kunto', 'Käytetty iPhone 14 Pro, 256GB, musta. Toimii moitteettomasti, mukana alkuperäiset tarvikkeet.', 600.00, 650.00, 'active', 1, datetime('now', '+3 days')),
(2, 1, 1, 'Samsung Galaxy S24 Ultra', 'Uusi Samsung Galaxy S24 Ultra, 512GB. Avaamaton paketti, täysi takuu voimassa.', 1200.00, 1250.00, 'active', 1, datetime('now', '+5 days')),
(3, 1, 2, 'Bosch-astianpesukone', 'Bosch astianpesukone, 5 vuotta vanha mutta hyvin toimiva. A++ energialuokka.', 200.00, 220.00, 'active', 0, datetime('now', '+2 days')),
(4, 1, 3, 'Vintage Levis-farkut', 'Alkuperäiset Levis 501 farkut 80-luvulta. Koko 32/34, harvinainen löytö.', 80.00, 95.00, 'active', 1, datetime('now', '+4 days')),
(5, 1, 4, 'Salomon-laskettelusukset', 'Salomon X-Pro 120 laskettelusukset, koko 42. Käytetty yhden kauden.', 150.00, 150.00, 'active', 0, datetime('now', '+1 day')),
(6, 1, 5, 'Haruki Murakami -kirjakokoelma', 'Täydellinen Haruki Murakami kokoelma suomennoksina. 12 kirjaa erinomainen kunto.', 120.00, 140.00, 'active', 0, datetime('now', '+6 days')),
(7, 1, 6, 'LEGO Creator Expert -sarja', 'LEGO Creator Expert Taj Mahal setti #10256. Avaamaton paketti, harvinais löytö.', 400.00, 430.00, 'active', 1, datetime('now', '+3 days')),
(8, 1, 7, 'BMW 3-sarja 320d', 'BMW 320d Touring 2018, 85000km. Hyvin huollettu, katsastettu äsketti.', 25000.00, 25200.00, 'active', 1, datetime('now', '+7 days')),
(9, 1, 8, 'Eames-tuoli replica', 'Korkealaatuinen Eames Lounge Chair replica. Nahka ja pähkinäpuu, erinomainen kunto.', 800.00, 850.00, 'active', 0, datetime('now', '+4 days')),
(10, 1, 9, 'Fender Stratocaster kitara', 'Fender American Standard Stratocaster 2015. Sunburst väri, alkup kotelossa.', 1100.00, 1150.00, 'active', 1, datetime('now', '+5 days')),
(11, 1, 1, 'MacBook Pro M2', 'Apple MacBook Pro 14" M2 Pro chip, 16GB RAM, 512GB SSD. Käytössä 6kk, täydellinen.', 2200.00, 2300.00, 'active', 1, datetime('now', '+2 hours')),
(12, 1, 2, 'Dyson V15-imuri', 'Dyson V15 Detect langaton imuri. Käytetty vähän, kaikki lisäosat mukana.', 450.00, 480.00, 'active', 0, datetime('now', '+3 hours')),
(13, 1, 3, 'Marimekko Unikko -mekko', 'Alkuperäinen Marimekko Unikko mekko koko 38. Vintage 70-luvulta, harvinainen.', 250.00, 280.00, 'active', 1, datetime('now', '+1 hour')),
(14, 1, 4, 'Rossignol-sukset + sauvat', 'Rossignol Experience 80 sukset 170cm + Rossignol sauvat. Erinomainen alkkeille.', 300.00, 320.00, 'active', 0, datetime('now', '+4 hours')),
(15, 1, 5, 'Harry Potter -kirjasarja', 'Koko Harry Potter sarja kovakantisina, ensimmäiset painokset. Täydellinen kunto.', 200.00, 230.00, 'active', 0, datetime('now', '+6 hours'));

-- Add some bids to make auctions more realistic
INSERT OR IGNORE INTO bids (auction_id, user_id, amount) VALUES
(1, 1, 650.00),
(2, 1, 1250.00), 
(3, 1, 220.00),
(4, 1, 95.00),
(6, 1, 140.00),
(7, 1, 430.00),
(8, 1, 25200.00),
(9, 1, 850.00),
(10, 1, 1150.00),
(11, 1, 2300.00),
(12, 1, 480.00),
(13, 1, 280.00),
(14, 1, 320.00),
(15, 1, 230.00);

-- Add some auction images (placeholder paths)
INSERT OR IGNORE INTO auction_images (auction_id, image_path, is_primary, sort_order) VALUES
(1, '/uploads/auctions/iphone14pro_1.jpg', 1, 0),
(2, '/uploads/auctions/samsung_s24_1.jpg', 1, 0),
(3, '/uploads/auctions/bosch_dishwasher_1.jpg', 1, 0),
(4, '/uploads/auctions/levis_vintage_1.jpg', 1, 0),
(5, '/uploads/auctions/salomon_skis_1.jpg', 1, 0),
(6, '/uploads/auctions/murakami_books_1.jpg', 1, 0),
(7, '/uploads/auctions/lego_tajmahal_1.jpg', 1, 0),
(8, '/uploads/auctions/bmw_320d_1.jpg', 1, 0),
(9, '/uploads/auctions/eames_chair_1.jpg', 1, 0),
(10, '/uploads/auctions/fender_strat_1.jpg', 1, 0),
(11, '/uploads/auctions/macbook_m2_1.jpg', 1, 0),
(12, '/uploads/auctions/dyson_v15_1.jpg', 1, 0),
(13, '/uploads/auctions/marimekko_dress_1.jpg', 1, 0),
(14, '/uploads/auctions/rossignol_skis_1.jpg', 1, 0),
(15, '/uploads/auctions/harry_potter_1.jpg', 1, 0);