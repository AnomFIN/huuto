-- Sample data for testing the auction platform
USE huuto_db;

-- Insert test users (password for all: demo123)
INSERT INTO users (username, email, password_hash, full_name, phone) VALUES
('jari_m', 'jari.m@example.com', '$2y$10$doRst.f.4Mh2pj/YKVtPH.BYFEFJltpP7vMD2FgpdoYaaUz9VSSSW', 'Jari Mäkelä', '+358 40 123 4567'),
('anna_k', 'anna.k@example.com', '$2y$10$4UTE4FzkmBZDtbNqUJZ/j.ppGOU.LC8OHdSC5c9QI6ZWH6TwMvpN6', 'Anna Korhonen', '+358 45 234 5678'),
('mikko_v', 'mikko.v@example.com', '$2y$10$bDAxBiDGc0i5py9eUxwn3urN9FMaw4E/zJ6o5iV.eODjfg.6b7bVK', 'Mikko Virtanen', '+358 50 345 6789');

-- Sample auctions for Kiinteistöt (Real Estate)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 1, 'Omakotitalotontti Espoo', 'Kaunis 1500 m² tontti rauhallisella alueella Espoossa. Tontti on rakennuskelpoinen ja sille on myönnetty rakennuslupa. Sijainti lähellä palveluita ja hyvät liikenneyhteydet.', 50000.00, 52000.00, 80000.00, 120000.00, 1000.00, DATE_ADD(NOW(), INTERVAL 3 DAY), 'active', 'Espoo', 'Uusi'),
(2, 1, 'Mökki Järvenrannalla', '45 m² hirsimökki idyllisellä järvenrannalla Savossa. Sauna, ruokailutila, makuuhuone ja keittiö. Ranta-alue n. 25 metriä. Hyvät kalastusmahdollisuudet.', 35000.00, 38500.00, NULL, 65000.00, 500.00, DATE_ADD(NOW(), INTERVAL 5 DAY), 'active', 'Savonlinna', 'Hyvä'),
(3, 1, 'Metsäpalsta 10 hehtaaria', 'Metsäpalsta Keski-Suomessa, pinta-ala 10 hehtaaria. Sekametsää, hyvä puusto. Metsätie kulkee tilan vierestä. Sopii sijoitukseksi tai omaan metsästyskäyttöön.', 45000.00, 45000.00, NULL, NULL, 1000.00, DATE_ADD(NOW(), INTERVAL 7 DAY), 'active', 'Jyväskylä', 'Hyvä');

-- Sample auctions for Ajoneuvot (Vehicles)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 2, 'Toyota Corolla 2018', 'Hyvin huollettu Toyota Corolla 2018, ajettu 85 000 km. 1.6 bensiinimoottori, automaattivaihteisto. Katsastettu äskettäin, seuraava katsastus 2026. Varusteet: ilmastointi, cruise control, peruutuskamera.', 12000.00, 13500.00, 14000.00, 16500.00, 200.00, DATE_ADD(NOW(), INTERVAL 4 DAY), 'active', 'Helsinki', 'Hyvä'),
(2, 2, 'Yamaha MT-07 Moottoripyörä', 'Yamaha MT-07 2019, ajettu 18 000 km. 689 cc kaksisylinterinen moottori. Erinomaisessa kunnossa, aina säilytetty sisätiloissa. Huoltokirja ja kaikki alkuperäiset paperit mukana.', 5500.00, 6200.00, NULL, 7500.00, 100.00, DATE_ADD(NOW(), INTERVAL 2 DAY), 'active', 'Tampere', 'Erinomainen'),
(3, 2, 'Volkswagen Transporter Pakettiauto', 'VW Transporter 2015, 2.0 TDI diesel. Ajettu 185 000 km. Hyötyajoneuvona käytetty. Iso tavaratila, hyllyt ja jakajat mukana. Luotettava ja taloudellinen.', 15000.00, 15800.00, NULL, NULL, 200.00, DATE_ADD(NOW(), INTERVAL 6 DAY), 'active', 'Turku', 'Tyydyttävä');

-- Sample auctions for Elektroniikka (Electronics)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 3, 'iPhone 14 Pro 256GB', 'Apple iPhone 14 Pro, 256GB, Space Black. Ostettu vuosi sitten, käytetty vain vähän. Alkuperäinen pakkaus ja tarvikkeet mukana. Ei naarmuja tai kolhuja. Akun kapasiteetti 96%.', 800.00, 875.00, 900.00, 1100.00, 25.00, DATE_ADD(NOW(), INTERVAL 1 DAY), 'active', 'Helsinki', 'Erinomainen'),
(2, 3, 'Samsung 65" 4K Smart TV', 'Samsung 65 tuuman 4K Smart TV, malli QN65Q80A. QLED-teknologia, HDR10+. Ostettu 2022. Toimii moitteetta, upea kuvanlaatu. Mukana kaukosäädin ja kaapelit.', 600.00, 650.00, NULL, 900.00, 25.00, DATE_ADD(NOW(), INTERVAL 3 DAY), 'active', 'Espoo', 'Hyvä'),
(3, 3, 'Dell XPS 15 Kannettava', 'Dell XPS 15 kannettava tietokone. Intel i7 11. sukupolvi, 16GB RAM, 512GB SSD, NVIDIA GTX 1650 Ti näytönohjain. 15.6" 4K näyttö. Hieno kone luovaan työhön ja pelaamiseen.', 900.00, 950.00, NULL, 1300.00, 50.00, DATE_ADD(NOW(), INTERVAL 4 DAY), 'active', 'Oulu', 'Erinomainen');

-- Sample auctions for Kodin tavarat (Home items)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 4, 'Design Sohva 3-istuttava', 'Moderni design-sohva, 3-istuttava. Harmaa kangasverhoilu. Pituus 220 cm. Mukava istua ja upea ilme olohuoneeseen. Ei lemmikkejä tai tupakointia.', 300.00, 340.00, NULL, 600.00, 20.00, DATE_ADD(NOW(), INTERVAL 2 DAY), 'active', 'Helsinki', 'Hyvä'),
(2, 4, 'Antiikki Ruokapöytä Tammi', 'Kaunis antiikki ruokapöytä massiivitammea. 1950-luvulta. Pituus 180 cm, leveys 90 cm. Patinoidutpinta, muutama käytön jälki mutta kokonaisuus hieno. 6 tuolia mukana.', 400.00, 450.00, NULL, NULL, 25.00, DATE_ADD(NOW(), INTERVAL 5 DAY), 'active', 'Turku', 'Tyydyttävä'),
(3, 4, 'Miele Astianpesukone', 'Miele astianpesukone, malli G 7310 SC. Ostettu 2020. Hiljainen, energiatehokas (A+++). Laaja ohjelmavalikoima. Toimii täydellisesti.', 400.00, 400.00, NULL, 700.00, 25.00, DATE_ADD(NOW(), INTERVAL 3 DAY), 'active', 'Vantaa', 'Erinomainen');

-- Sample auctions for Urheilu (Sports)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 5, 'Maastopyörä Giant Trance 2023', 'Giant Trance X 29er maastopyörä, malli 2023. Full suspension, 12-vaihteinen. Ajettu n. 500 km. Huollettu säännöllisesti. Koko L. Upea pyörä trail-ajoon.', 1500.00, 1700.00, NULL, 2500.00, 50.00, DATE_ADD(NOW(), INTERVAL 4 DAY), 'active', 'Lahti', 'Erinomainen'),
(2, 5, 'Suksisetti + Monot', 'Maastohiihtosukset Fischer Speedmax Skating Plus, pituus 186 cm. Salomon monot koko 44. Käytetty vain yhden kauden. Hyväkuntoiset.', 200.00, 220.00, NULL, 350.00, 10.00, DATE_ADD(NOW(), INTERVAL 2 DAY), 'active', 'Rovaniemi', 'Hyvä'),
(3, 5, 'Kuntosali Penkkipunnerrus', 'Säädettävä penkkipunnerruspenkki + 100kg levypainosetti. Toimii täydellisesti. Hieno lisä kotikuntosalille.', 250.00, 275.00, NULL, NULL, 15.00, DATE_ADD(NOW(), INTERVAL 6 DAY), 'active', 'Jyväskylä', 'Hyvä');

-- Sample auctions for Vaatteet (Clothing)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 6, 'Canada Goose Talvitakki', 'Canada Goose Expedition Parka, koko M. Musta. Ostettu 2021. Lämpöinen ja laadukas talvitakki. Turkis irrotettavissa. Pesty ja huollettu ammattilaisella.', 400.00, 450.00, NULL, 700.00, 25.00, DATE_ADD(NOW(), INTERVAL 3 DAY), 'active', 'Helsinki', 'Erinomainen'),
(2, 6, 'Nike Air Max Tennarit', 'Nike Air Max 90, koko 42. Valkoinen/punainen väritys. Käytetty muutaman kerran. Mukana alkuperäinen laatikko.', 60.00, 75.00, NULL, 120.00, 5.00, DATE_ADD(NOW(), INTERVAL 1 DAY), 'active', 'Tampere', 'Erinomainen'),
(3, 6, 'Vintage Farkkutakki Levi\'s', 'Vintage Levi\'s farkkutakki 1980-luvulta. Koko M. Hieno retro-ilme. Muutama pieni kuluma, lisää tunnelmaa.', 50.00, 50.00, NULL, NULL, 5.00, DATE_ADD(NOW(), INTERVAL 5 DAY), 'active', 'Turku', 'Hyvä');

-- Sample auctions for Keräily (Collectibles)
INSERT INTO auctions (user_id, category_id, title, description, starting_price, current_price, reserve_price, buy_now_price, bid_increment, end_time, status, location, condition_description) VALUES
(1, 7, 'Vanha Rolex Kello 1970-luku', 'Alkuperäinen Rolex Datejust 1970-luvulta. Automaattinen. Toimii moitteetta. Patina ja käytön jälkiä, mutta juuri se tekee kellosta aidon. Keräilykappale.', 2000.00, 2300.00, 3000.00, NULL, 100.00, DATE_ADD(NOW(), INTERVAL 7 DAY), 'active', 'Helsinki', 'Tyydyttävä'),
(2, 7, 'Pokemon Korttipakka 1. Painos', 'Alkuperäinen Pokemon Base Set booster box, sinetöity. 1. painos 1999. Erittäin harvinainen ja arvostettu keräilykohde. Täydellinen kunto.', 5000.00, 5500.00, NULL, 8000.00, 200.00, DATE_ADD(NOW(), INTERVAL 4 DAY), 'active', 'Espoo', 'Erinomainen'),
(3, 7, 'Antiikki Samovaari', 'Venäläinen antiikki samovaari 1800-luvun lopulta. Messinkiä. Kauniisti koristeltu. Harvinainen kappale. Upea sisustuselementti tai keräilykohde.', 400.00, 450.00, NULL, NULL, 25.00, DATE_ADD(NOW(), INTERVAL 6 DAY), 'active', 'Tampere', 'Hyvä');

-- Sample bids with realistic timestamps and auto-bid patterns
-- Bids reference the auction IDs inserted above (1-21 in order)

-- Auction 1: Omakotitalotontti Espoo (start=50000, incr=1000) – 7 bids, users 2 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(1, 2, 50000.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(1, 3, 51000.00, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(1, 2, 52000.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(1, 3, 53000.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(1, 2, 54000.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(1, 3, 55000.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 2, 56000.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Auction 2: Mökki Järvenrannalla (start=35000, incr=500) – 9 bids, users 1 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(2, 3, 35000.00, 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 1, 35500.00, 0, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(2, 3, 36000.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(2, 1, 36500.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(2, 3, 37000.00, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(2, 1, 37500.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 3, 38000.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 1, 38500.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 3, 39000.00, 0, DATE_SUB(NOW(), INTERVAL 12 HOUR));

-- Auction 3: Metsäpalsta – 0 bids

-- Auction 4: Toyota Corolla 2018 (start=12000, incr=200) – 18 bids, users 2 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(4, 2, 12000.00, 0, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(4, 3, 12200.00, 0, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(4, 2, 12400.00, 1, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(4, 3, 12600.00, 1, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(4, 2, 12800.00, 0, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(4, 3, 13000.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(4, 2, 13200.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(4, 3, 13400.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(4, 2, 13600.00, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(4, 3, 13800.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(4, 2, 14000.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(4, 3, 14200.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(4, 2, 14400.00, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 3, 14600.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 2, 14800.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 3, 15000.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 2, 15200.00, 1, DATE_SUB(NOW(), INTERVAL 10 HOUR)),
(4, 3, 15400.00, 0, DATE_SUB(NOW(), INTERVAL 3 HOUR));

-- Auction 5: Yamaha MT-07 (start=5500, incr=100) – 11 bids, users 1 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(5, 1, 5500.00, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(5, 3, 5600.00, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(5, 1, 5700.00, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(5, 3, 5800.00, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(5, 1, 5900.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(5, 3, 6000.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(5, 1, 6100.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 3, 6200.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 1, 6300.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 3, 6400.00, 0, DATE_SUB(NOW(), INTERVAL 18 HOUR)),
(5, 1, 6500.00, 0, DATE_SUB(NOW(), INTERVAL 6 HOUR));

-- Auction 6: VW Transporter – 0 bids

-- Auction 7: iPhone 14 Pro (start=800, incr=25) – 22 bids, users 1, 2 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(7, 3, 800.00,  0, DATE_SUB(NOW(), INTERVAL 14 DAY)),
(7, 2, 825.00,  0, DATE_SUB(NOW(), INTERVAL 13 DAY)),
(7, 1, 850.00,  0, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(7, 3, 875.00,  1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(7, 2, 900.00,  1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(7, 3, 925.00,  1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(7, 2, 950.00,  0, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(7, 1, 975.00,  0, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(7, 3, 1000.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(7, 1, 1025.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(7, 2, 1050.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(7, 3, 1075.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(7, 2, 1100.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(7, 3, 1125.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 1, 1150.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(7, 2, 1175.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(7, 1, 1200.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(7, 3, 1225.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 2, 1250.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 1, 1275.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 3, 1300.00, 0, DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(7, 2, 1325.00, 0, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- Auction 8: Samsung 65" TV – 3 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(8, 1, 600.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(8, 3, 625.00, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(8, 1, 650.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Auction 9: Dell XPS – 2 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(9, 2, 900.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(9, 1, 950.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Auction 10: Design Sohva – 5 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(10, 3, 300.00, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(10, 1, 320.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(10, 3, 340.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(10, 1, 360.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(10, 3, 380.00, 0, DATE_SUB(NOW(), INTERVAL 12 HOUR));

-- Auction 11: Antiikki Ruokapöytä – 3 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(11, 2, 400.00, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(11, 1, 425.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(11, 2, 450.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Auction 12: Miele Astianpesukone – 0 bids

-- Auction 13: Maastopyörä Giant (start=1500, incr=50) – 14 bids, users 2 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(13, 2, 1500.00, 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(13, 3, 1550.00, 0, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(13, 2, 1600.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(13, 3, 1650.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(13, 2, 1700.00, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(13, 3, 1750.00, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(13, 2, 1800.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(13, 3, 1850.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(13, 2, 1900.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(13, 3, 1950.00, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(13, 2, 2000.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(13, 3, 2050.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(13, 2, 2100.00, 0, DATE_SUB(NOW(), INTERVAL 18 HOUR)),
(13, 3, 2150.00, 0, DATE_SUB(NOW(), INTERVAL 4 HOUR));

-- Auction 14: Suksisetti – 5 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(14, 1, 200.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(14, 3, 210.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(14, 1, 220.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(14, 3, 230.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(14, 1, 240.00, 0, DATE_SUB(NOW(), INTERVAL 6 HOUR));

-- Auction 15: Penkkipunnerrus – 2 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(15, 2, 250.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(15, 1, 265.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Auction 16: Canada Goose Takki (start=400, incr=25) – 8 bids, users 1 & 2
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(16, 2, 400.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(16, 1, 425.00, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(16, 2, 450.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(16, 1, 475.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(16, 2, 500.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(16, 1, 525.00, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(16, 2, 550.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(16, 1, 575.00, 0, DATE_SUB(NOW(), INTERVAL 8 HOUR));

-- Auction 17: Nike Air Max (start=60, incr=5) – 10 bids, users 2 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(17, 2, 60.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(17, 3, 65.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(17, 2, 70.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(17, 3, 75.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(17, 2, 80.00, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(17, 3, 85.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(17, 2, 90.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(17, 3, 95.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(17, 2, 100.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(17, 3, 105.00, 0, DATE_SUB(NOW(), INTERVAL 4 HOUR));

-- Auction 18: Vintage Farkkutakki – 0 bids

-- Auction 19: Rolex (start=2000, incr=100) – 20 bids, users 1, 2 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(19, 1, 2000.00, 0, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(19, 3, 2100.00, 0, DATE_SUB(NOW(), INTERVAL 14 DAY)),
(19, 2, 2200.00, 0, DATE_SUB(NOW(), INTERVAL 13 DAY)),
(19, 1, 2300.00, 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(19, 3, 2400.00, 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(19, 1, 2500.00, 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(19, 2, 2600.00, 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(19, 3, 2700.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(19, 2, 2800.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(19, 1, 2900.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(19, 3, 3000.00, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(19, 2, 3100.00, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(19, 3, 3200.00, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(19, 1, 3300.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(19, 3, 3400.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(19, 1, 3500.00, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(19, 2, 3600.00, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(19, 3, 3700.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(19, 1, 3800.00, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(19, 2, 3900.00, 0, DATE_SUB(NOW(), INTERVAL 6 HOUR));

-- Auction 20: Pokemon Korttipakka (start=5000, incr=200) – 15 bids, users 1 & 3
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(20, 1, 5000.00, 0, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(20, 3, 5200.00, 0, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(20, 1, 5400.00, 1, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(20, 3, 5600.00, 1, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(20, 1, 5800.00, 0, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(20, 3, 6000.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(20, 1, 6200.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(20, 3, 6400.00, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(20, 1, 6600.00, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(20, 3, 6800.00, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(20, 1, 7000.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(20, 3, 7200.00, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(20, 1, 7400.00, 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(20, 3, 7600.00, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(20, 1, 7800.00, 0, DATE_SUB(NOW(), INTERVAL 4 HOUR));

-- Auction 21: Antiikki Samovaari – 4 bids
INSERT INTO bids (auction_id, user_id, amount, is_auto_bid, bid_time) VALUES
(21, 2, 400.00, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(21, 1, 425.00, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(21, 2, 450.00, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(21, 1, 475.00, 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Sync current_price with the most recent bid for each auction
-- (matches the behaviour of Auction::placeBid which sets current_price to the last placed bid)
UPDATE auctions a
JOIN (
    SELECT auction_id, amount
    FROM bids b1
    WHERE bid_time = (SELECT MAX(bid_time) FROM bids b2 WHERE b2.auction_id = b1.auction_id)
    GROUP BY auction_id
) latest ON a.id = latest.auction_id
SET a.current_price = latest.amount;

-- Sample watchlist entries
INSERT INTO watchlist (user_id, auction_id) VALUES
(1, 4),
(1, 7),
(2, 1),
(2, 5),
(3, 2),
(3, 7);

-- Note: Image paths would need actual images. For demo, they're referenced but not included.
-- You would need to add auction_images entries with actual image paths
