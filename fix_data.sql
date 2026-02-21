-- Fix for missing categories and users that prevent auctions from showing
-- Run this SQL to populate missing categories and user

-- Create user id=2 if it doesn't exist
INSERT IGNORE INTO users (id, username, email, password_hash, full_name, created_at) VALUES
(2, 'demolisher', 'demo@huuto247.fi', '$2y$10$doRst.f.4Mh2pj/YKVtPH.BYFEFJltpP7vMD2FgpdoYaaUz9VSSSW', 'Demo Myyjä', NOW());

-- Create categories 1-29 that are referenced in auctions.sql
INSERT IGNORE INTO categories (id, name, slug) VALUES
(1, 'Keräily ja antiikki', 'keraily-ja-antiikki'),
(2, 'Ajoneuvot', 'ajoneuvot'),
(3, 'Kodin tavarat', 'kodin-tavarat'), 
(4, 'Vaatteet ja asusteet', 'vaatteet-ja-asusteet'),
(5, 'Elektroniikka', 'elektroniikka'),
(6, 'Urheilu ja kunto', 'urheilu-ja-kunto'),
(7, 'Kauneus ja terveys', 'kauneus-ja-terveys'),
(8, 'Autotarvikkeet', 'autotarvikkeet'),
(9, 'Kotieläinten tavarat', 'kotielainten-tavarat'),
(10, 'Työkalut', 'tyokalut'),
(11, 'Työkoneet', 'tyokoneet'),
(12, 'Kiinteistöt', 'kiinteistot'),
(13, 'Rakentaminen', 'rakentaminen'),
(14, 'Puutarha', 'puutarha'),
(15, 'Käsityöt', 'kasityot'),
(16, 'Musiikki', 'musiikki'),
(17, 'Taide', 'taide'),
(18, 'Kirjat', 'kirjat'),
(19, 'Lelut ja pelit', 'lelut-ja-pelit'),
(20, 'Matkailu', 'matkailu'),
(21, 'Kulttuuri', 'kulttuuri'),
(22, 'Ruoka ja juoma', 'ruoka-ja-juoma'),
(23, 'Tekstiilit', 'tekstiilit'),
(24, 'Veneet ja moottoriajoneuvot', 'veneet-ja-moottoriajoneuvot'),
(25, 'Maatalous', 'maatalous'),
(26, 'Ammatilliset tuotteet', 'ammatilliset-tuotteet'),
(27, 'Yrityksen tavarat', 'yrityksen-tavarat'),
(28, 'Erikoiskalusto', 'erikoiskalusto'),
(29, 'Muut', 'muut');

-- Test query to check if data is now visible
SELECT 
    a.id, 
    a.title, 
    a.current_price,
    c.name as category_name,
    u.username
FROM auctions a 
JOIN categories c ON a.category_id = c.id 
JOIN users u ON a.user_id = u.id 
WHERE a.status = 'active' 
LIMIT 5;