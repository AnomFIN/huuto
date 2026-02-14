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

-- Sample bids
INSERT INTO bids (auction_id, user_id, amount) VALUES
(1, 2, 50000.00),
(1, 3, 51000.00),
(1, 2, 52000.00),
(2, 1, 35000.00),
(2, 3, 37000.00),
(2, 1, 38500.00),
(4, 2, 12000.00),
(4, 3, 12500.00),
(4, 2, 13000.00),
(4, 3, 13500.00),
(5, 1, 5500.00),
(5, 3, 6000.00),
(5, 1, 6200.00),
(7, 2, 800.00),
(7, 3, 850.00),
(7, 2, 875.00);

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
