-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 10.04.2026 klo 10:11
-- Palvelimen versio: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dajnpsku_jussi`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `auctions`
--

CREATE TABLE `auctions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `starting_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `reserve_price` decimal(10,2) DEFAULT NULL,
  `buy_now_price` decimal(10,2) DEFAULT NULL,
  `bid_increment` decimal(10,2) NOT NULL DEFAULT 1.00,
  `start_time` timestamp NULL DEFAULT current_timestamp(),
  `end_time` timestamp NOT NULL,
  `status` enum('draft','active','ended','cancelled') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `location` varchar(200) DEFAULT NULL,
  `condition_description` varchar(50) DEFAULT NULL,
  `ai_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `map_coordinates` varchar(50) DEFAULT NULL,
  `seller_commitment` tinyint(1) DEFAULT 0,
  `short_summary` varchar(255) DEFAULT NULL COMMENT 'Brief auction description for listings',
  `featured` tinyint(1) DEFAULT 0 COMMENT 'Featured auction flag',
  `condition_grade` varchar(50) DEFAULT NULL COMMENT 'Overall condition grade',
  `seller_notes` text DEFAULT NULL COMMENT 'Additional seller information',
  `pickup_info` text DEFAULT NULL COMMENT 'Pickup instructions and details',
  `shipping_info` text DEFAULT NULL COMMENT 'Shipping options and costs',
  `payment_info` text DEFAULT NULL COMMENT 'Payment methods and deadlines',
  `inspection_info` text DEFAULT NULL COMMENT 'Inspection opportunities',
  `included_items` text DEFAULT NULL COMMENT 'What is included in the sale',
  `defects` text DEFAULT NULL COMMENT 'Known defects and issues',
  `warranty_info` varchar(255) DEFAULT NULL COMMENT 'Warranty status',
  `model_reference` varchar(100) DEFAULT NULL COMMENT 'Model number or reference',
  `serial_number` varchar(100) DEFAULT NULL COMMENT 'Serial number if applicable',
  `delivery_available` tinyint(1) DEFAULT 0 COMMENT 'Delivery option available',
  `pickup_available` tinyint(1) DEFAULT 1 COMMENT 'Pickup option available',
  `payment_deadline_days` int(11) DEFAULT 1 COMMENT 'Payment deadline in days',
  `storage_fee_info` varchar(255) DEFAULT NULL COMMENT 'Storage fee details'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `auctions`
--

INSERT INTO `auctions` (`id`, `user_id`, `category_id`, `title`, `description`, `starting_price`, `current_price`, `reserve_price`, `buy_now_price`, `bid_increment`, `start_time`, `end_time`, `status`, `views`, `location`, `condition_description`, `ai_details`, `created_at`, `updated_at`, `map_coordinates`, `seller_commitment`, `short_summary`, `featured`, `condition_grade`, `seller_notes`, `pickup_info`, `shipping_info`, `payment_info`, `inspection_info`, `included_items`, `defects`, `warranty_info`, `model_reference`, `serial_number`, `delivery_available`, `pickup_available`, `payment_deadline_days`, `storage_fee_info`) VALUES
(1, 2, 11, 'Kobelco SK 230 SR LC', 'Myydään siistikuntoinen ja toimintavarma Kobelco SK 230 SR LC, vuosimalli 2015. Kone ollut ammattikäytössä, huollot ajallaan ja dokumentoitu. Lyhytperäinen SR-runko tekee tästä näppärän työmaan koneen myös ahtaampiin kohteisiin.\r\n\r\nKäyttötunnit: 5 716 h\r\nAlusta ja hydrauliikka toimivat tasaisesti, ei ylimääräisiä ääniä. Veto vahva ja puomitarkkuus hyvä – ProBo-ohjaus tekee ajosta tarkkaa.\r\n\r\nMukaan kattava varustepaketti:\r\n- S70-liitin\r\n- Tiltti\r\n- Luiska- ja kaapelikauha\r\n- Peruskauha + pyörityshydrauliikka valmius\r\n- Työvalot, peruutuskamera, webasto\r\n- Keskusvoitelu\r\n- Lisähydrauliikat (2 piiriä)\r\n\r\nKohde myydään “sellaisena kuin on”, mutta kone on tarkastettu ja koeajettavissa sopimuksella.', 17000.00, 29000.00, 17000.00, 32900.00, 1.00, '2026-04-06 11:51:49', '2026-04-15 11:51:49', 'active', 20, 'Ruotsi', 'Hyvä', NULL, '2026-04-06 11:51:49', '2026-04-10 03:24:50', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(2, 2, 1, 'Polkupyörä 28\" - toimiva kunto #9478', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 19.00, 39000.00, NULL, 98.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:18:23', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(3, 2, 1, 'Laadukas sohva - mukava ja hyväkuntoinen #1153', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 447.00, 457.00, NULL, 914.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-09 19:36:08', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(5, 2, 2, 'Polkupyörä 28\" - toimiva kunto #3029', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 43.00, 53.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-09 19:35:50', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(6, 2, 2, 'Talonrakennustarvikkeita - iso erä #1900', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 455.00, 495.00, NULL, 990.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-10 21:05:35', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(7, 2, 3, 'Laadukas sohva - mukava ja hyväkuntoinen #7942', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: poor\r\nSijainti: Varsinais-Suomi\r\nLisätietoja: Ota yhteyttä myyjään.', 298.00, 298.00, NULL, NULL, 1.00, '2026-04-06 11:51:49', '2026-04-15 11:51:49', 'active', 10, '', '', NULL, '2026-04-06 11:51:49', '2026-04-10 02:50:27', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(8, 2, 3, 'Polkupyörä 28\" - toimiva kunto #4707', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 494.00, 514.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 8, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 02:57:32', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(9, 2, 3, 'Puutarhatyökalut 15 kpl - käytetty #4816', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 429.00, 469.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 10, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 17:04:21', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(10, 2, 4, 'Polkupyörä 28\" - toimiva kunto #5201', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 129.00, 159.00, NULL, 318.00, 1.00, '2026-02-15 12:51:49', '2026-03-08 12:51:49', 'active', 10, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 06:25:07', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(11, 2, 4, 'Polkupyörä 28\" - toimiva kunto #9122', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 147.00, 197.00, NULL, 394.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 13:14:20', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(12, 2, 4, 'Puutarhatyökalut 15 kpl - käytetty #3489', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 134.00, 184.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 5, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-09 19:35:35', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(13, 2, 5, 'Talonrakennustarvikkeita - iso erä #3909', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 228.00, 248.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-18 06:49:35', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(14, 2, 5, 'Talonrakennustarvikkeita - iso erä #2260', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 84.00, 94.00, NULL, 188.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 15:24:03', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(15, 2, 5, 'Vanha rahapussi - keräilijän kohde #8909', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 428.00, 448.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-27 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-11 18:03:24', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(16, 2, 6, 'Puutarhatyökalut 15 kpl - käytetty #2822', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 339.00, 369.00, NULL, 738.00, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 13:15:22', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(17, 2, 6, 'Vanha rahapussi - keräilijän kohde #7381', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 216.00, 266.00, NULL, 532.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-03 14:36:25', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(18, 2, 6, 'Polkupyörä 28\" - toimiva kunto #6692', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 158.00, 158.00, NULL, 316.00, 1.00, '2026-02-15 12:51:49', '2026-03-14 12:51:49', 'active', 14, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 10:54:12', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(19, 2, 7, 'Talonrakennustarvikkeita - iso erä #1014', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 126.00, 126.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-12 10:36:45', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(20, 2, 7, 'Vanha rahapussi - keräilijän kohde #3164', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 433.00, 453.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-13 05:23:53', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(21, 2, 7, 'Vanha rahapussi - keräilijän kohde #7228', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 498.00, 538.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 15:02:40', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(22, 2, 8, 'Talonrakennustarvikkeita - iso erä #1272', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: new\r\nSijainti: Pirkanmaa\r\nLisätietoja: Ota yhteyttä myyjään.', 428.00, 448.00, NULL, NULL, 1.00, '2026-04-06 11:51:49', '2026-04-15 11:51:49', 'active', 16, '', '', NULL, '2026-04-06 11:51:49', '2026-04-10 03:17:39', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(24, 2, 8, 'Puutarhatyökalut 15 kpl - käytetty #4430', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 281.00, 291.00, NULL, 582.00, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 12, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 13:03:09', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(25, 2, 9, 'Talonrakennustarvikkeita - iso erä #7271', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 260.00, 310.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-04 04:10:23', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(26, 2, 9, 'Laadukas sohva - mukava ja hyväkuntoinen #9546', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 31.00, 61.00, NULL, 122.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 12, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:41:02', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(27, 2, 9, 'Vanha rahapussi - keräilijän kohde #1911', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 493.00, 513.00, NULL, NULL, 1.00, '2026-04-04 11:51:49', '2026-04-08 11:51:49', 'active', 14, NULL, NULL, NULL, '2026-04-04 11:51:49', '2026-04-08 12:49:41', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(28, 2, 11, 'Talonrakennustarvikkeita - iso erä #1584', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 194.00, 214.00, NULL, 428.00, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 12, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 09:22:02', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(29, 2, 11, 'Vanha rahapussi - keräilijän kohde #4643', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 288.00, 328.00, NULL, 656.00, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-18 06:48:31', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(30, 2, 11, 'Laadukas sohva - mukava ja hyväkuntoinen #1453', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 334.00, 334.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 14, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 11:02:54', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(31, 2, 12, 'Polkupyörä 28\" - toimiva kunto #7440', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 448.00, 498.00, NULL, 996.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 10:42:33', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(32, 2, 12, 'Puutarhatyökalut 15 kpl - käytetty #6028', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 239.00, 289.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-22 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 12:49:46', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(33, 2, 12, 'Polkupyörä 28\" - toimiva kunto #4031', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 385.00, 395.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-02 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-02-22 15:18:05', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(34, 2, 13, 'Talonrakennustarvikkeita - iso erä #9234', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 144.00, 144.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-22 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-10 19:27:37', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(35, 2, 13, 'Puutarhatyökalut 15 kpl - käytetty #1820', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 354.00, 394.00, NULL, 788.00, 1.00, '2026-02-15 12:51:49', '2026-03-07 12:51:49', 'active', 9, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 02:04:56', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(36, 2, 13, 'Puutarhatyökalut 15 kpl - käytetty #9896', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: new\r\nSijainti: Satakunta\r\nLisätietoja: Ota yhteyttä myyjään.', 426.00, 426.00, NULL, 852.00, 1.00, '2026-04-06 11:51:49', '2026-04-15 11:51:49', 'active', 12, '', '', NULL, '2026-04-06 11:51:49', '2026-04-10 04:43:30', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(37, 2, 14, 'Talonrakennustarvikkeita - iso erä #1431', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 50.00, 70.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-08 13:00:19', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(38, 2, 14, 'Vanha rahapussi - keräilijän kohde #7479', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 42.00, 62.00, NULL, 124.00, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 8, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 11:31:10', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(39, 2, 14, 'Laadukas sohva - mukava ja hyväkuntoinen #9079', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 40.00, 60.00, NULL, 120.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-09 23:36:54', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(40, 2, 15, 'Puutarhatyökalut 15 kpl - käytetty #1283', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 362.00, 392.00, NULL, 784.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-22 13:34:36', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(41, 2, 15, 'Vanha rahapussi - keräilijän kohde #1965', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 17.00, 47.00, NULL, 94.00, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-10 19:28:21', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(42, 2, 15, 'Polkupyörä 28\" - toimiva kunto #5997', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 392.00, 422.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-10 12:51:49', 'active', 9, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 07:24:36', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(43, 2, 16, 'Polkupyörä 28\" - toimiva kunto #7968', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 145.00, 195.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:02', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(44, 2, 16, 'Puutarhatyökalut 15 kpl - käytetty #1004', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 70.00, 70.00, NULL, 140.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-04 00:12:13', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(45, 2, 16, 'Polkupyörä 28\" - toimiva kunto #1876', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 16.00, 16.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 15, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-06 15:12:22', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(46, 2, 17, 'Talonrakennustarvikkeita - iso erä #3389', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 59.00, 99.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-13 07:27:10', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(47, 2, 17, 'Talonrakennustarvikkeita - iso erä #9343', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 491.00, 521.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 19, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 06:44:33', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(48, 2, 17, 'Vanha rahapussi - keräilijän kohde #1835', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 416.00, 436.00, NULL, 872.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 9, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 19:32:31', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(49, 2, 20, 'Laadukas sohva - mukava ja hyväkuntoinen #6821', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 128.00, 138.00, NULL, 276.00, 1.00, '2026-02-15 12:51:49', '2026-03-06 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 00:42:33', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(50, 2, 20, 'Laadukas sohva - mukava ja hyväkuntoinen #2268', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 317.00, 337.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-19 12:51:49', 'active', 14, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-03 23:41:57', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(51, 2, 20, 'Talonrakennustarvikkeita - iso erä #7169', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 79.00, 89.00, NULL, 178.00, 1.00, '2026-02-15 12:51:49', '2026-03-17 12:51:49', 'active', 18, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 17:20:21', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(52, 2, 22, 'Vanha rahapussi - keräilijän kohde #4851', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 267.00, 287.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:17:49', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(53, 2, 22, 'Talonrakennustarvikkeita - iso erä #6353', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 255.00, 285.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 8, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 06:24:20', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(54, 2, 22, 'Vanha rahapussi - keräilijän kohde #1313', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 205.00, 255.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:18:02', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(55, 2, 23, 'Laadukas sohva - mukava ja hyväkuntoinen #4829', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 481.00, 521.00, NULL, 1042.00, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-02-22 15:53:36', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(56, 2, 23, 'Laadukas sohva - mukava ja hyväkuntoinen #7022', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 318.00, 318.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-02 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:17:59', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(57, 2, 23, 'Vanha rahapussi - keräilijän kohde #1669', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 374.00, 414.00, NULL, 828.00, 1.00, '2026-02-15 12:51:49', '2026-03-04 12:51:49', 'active', 6, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-21 20:58:53', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(58, 2, 24, 'Polkupyörä 28\" - toimiva kunto #8481', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 143.00, 163.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 4, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 13:12:52', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(59, 2, 24, 'Talonrakennustarvikkeita - iso erä #3308', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: new\r\nSijainti: Pirkanmaa\r\nLisätietoja: Ota yhteyttä myyjään.', 45.00, 85.00, NULL, 170.00, 1.00, '2026-04-06 11:51:49', '2026-04-15 11:51:49', 'active', 37, '', '', NULL, '2026-04-06 11:51:49', '2026-04-07 18:33:55', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(60, 2, 24, 'Talonrakennustarvikkeita - iso erä #8269', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 311.00, 321.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-26 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-10 19:28:01', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(61, 2, 25, 'Vanha rahapussi - keräilijän kohde #2747', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 157.00, 207.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:17:37', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(62, 2, 25, 'Puutarhatyökalut 15 kpl - käytetty #2171', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 39.00, 79.00, NULL, 158.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 9, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 20:19:20', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(63, 2, 3, 'Sähkökitara', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: excellent\r\nSijainti: Pohjois-Pohjanmaa\r\nLisätietoja: Ota yhteyttä myyjään.', 80.00, 130.00, 100.00, 150.00, 1.00, '2026-04-06 11:51:49', '2026-04-15 08:46:31', 'active', 18, 'Helsinki', 'Hyvä', NULL, '2026-04-06 11:51:49', '2026-04-10 02:59:02', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(64, 2, 26, 'Polkupyörä 28\" - toimiva kunto #6213', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 386.00, 406.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-08 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 22:17:53', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(65, 2, 26, 'Polkupyörä 28\" - toimiva kunto #2746', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 499.00, 499.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-22 18:44:36', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(66, 2, 26, 'Vanha rahapussi - keräilijän kohde #7265', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 301.00, 351.00, NULL, 702.00, 1.00, '2026-02-15 12:51:49', '2026-02-27 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 13:08:01', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(67, 2, 27, 'Polkupyörä 28\" - toimiva kunto #5658', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 348.00, 398.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-04 12:51:49', 'active', 9, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 17:24:58', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(68, 2, 27, 'Vanha rahapussi - keräilijän kohde #9560', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 336.00, 366.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-26 12:51:49', 'active', 3, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-03-10 19:27:54', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(69, 2, 27, 'Vanha rahapussi - keräilijän kohde #3019', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 47.00, 87.00, NULL, 174.00, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 19, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 17:51:58', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(70, 2, 28, 'Laadukas sohva - mukava ja hyväkuntoinen #1971', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 67.00, 77.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 2, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-02-23 20:59:29', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(71, 2, 28, 'Polkupyörä 28\" - toimiva kunto #4417', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 472.00, 492.00, NULL, 984.00, 1.00, '2026-02-15 12:51:49', '2026-03-07 12:51:49', 'active', 9, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 11:08:09', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(72, 2, 28, 'Laadukas sohva - mukava ja hyväkuntoinen #8805', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 361.00, 401.00, NULL, 802.00, 1.00, '2026-02-15 12:51:49', '2026-03-06 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-08 15:48:04', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(73, 2, 29, 'Talonrakennustarvikkeita - iso erä #9805', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 480.00, 480.00, NULL, 960.00, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-09 05:24:40', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(74, 2, 29, 'Laadukas sohva - mukava ja hyväkuntoinen #4203', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 469.00, 509.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 11, NULL, NULL, NULL, '2026-02-15 12:51:49', '2026-04-07 03:31:48', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(75, 2, 29, 'iPhone 13', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti. Menöö myös postilla. Soiva peli\r\n\r\nKunto: fair\r\nSijainti: Pohjois-Pohjanmaa\r\nLisätietoja: Ota yhteyttä myyjään.', 248.00, 268.00, NULL, 536.00, 1.00, '2026-04-06 11:51:49', '2026-04-15 11:51:49', 'active', 16, '', '', NULL, '2026-04-06 11:51:49', '2026-04-10 03:57:48', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(76, 2, 2, 'Harley Davidson- custom moottoripyörä', 'Myyntiin hyväkuntoinen harrikka.', 4000.00, 4000.00, 6000.00, 5500.00, 100.00, '2026-04-06 12:54:02', '2026-04-15 12:54:02', 'active', 21, 'Harjavalta', 'Hyvä', NULL, '2026-04-06 12:54:02', '2026-04-10 03:45:48', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(77, 2, 15, 'Makita & Kärscher imurit', 'Myydään\r\nMakita VC2512L (2023) + Kärcher VC 3 (ERP) – 2 imuria. Toimivuus testattu Kone Korpelan korjaamolla.\r\n\r\n\r\n\r\nKohde voidaan toimittaa Kiitolinjan, autonkuljetusauton tai matkahuollon jne. kanssa riippuen kohteesta, ostajan maksaessa kaikki kuljetus, lähetys ja pakkaus kulut. Voidaan toimittaa myös Manner-Suomen ulkopuolelle lisäkustannusta vastaan. Lähettämästämme noutamatta jätetystä kohteesta / kohteista veloitamme saman suuruisen toimituskulun kuin kohteessa on ollut sekä palautuksesta meille että uudelleen lähetyksestä. Kohde tulee noutaa 3 arkivuorokauden sisällä kohteen päättymisestä tai ilmoituksessa kerrottuna ajankohtana (niissä kohteissa joissa nouto on mahdollinen). Tämän jälkeen veloitamme säilytyksestä 10 €/vrk.\r\n\r\n\r\n\r\nToimituskuluun sisältyy:\r\n\r\n\r\n\r\nPakkausmateriaalit\r\n\r\nPakkaus (henkilöstökulut)\r\n\r\nLähetyslappujen teko\r\n\r\nNäitä kuluja ei saa sisällytettyä tuotteen hintaa huutokaupassa niin kuin verkkokaupat tekevät, koska hinta ei ole kiinteä. Huutohinta ja toimituskulut ovat tuotteen kokonaishinta toimitettuna arvonlisäveroineen.\r\n\r\nUseampi tuote voidaan pakata samaan pakettiin\r\n\r\n\r\n\r\nAA Realisointi Oy\r\n\r\nInkereentie 1021\r\n\r\n25190 Pertteli (Salo)\r\n\r\nViat ja muut havainnot\r\nTuotteen huutokauppailmoitus on tehty myyjän havaintoihin perustuen, tuotteessa saattaa olla piileviä vikoja tai virheitä, mitä myyjä ei ole huutokauppailmoitusta laatiessa havainnut.', 80.00, 90.00, 60.00, 100.00, 1.00, '2026-04-06 14:35:37', '2026-04-15 07:34:14', 'active', 42, 'Saloa', 'Hyvä', NULL, '2026-04-06 14:35:37', '2026-04-10 01:43:55', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(78, 2, 8, 'Vannesetti', 'Myynnissä neljä alumiinivannetta, joissa on hyväkuntoiset renkaat. Vanteet ovat tyylikkäät ja modernit, ja niissä on musta/hopea väriyhdistelmä. Renkaissa on riittävästi kulutuspintaa ja ne sopivat useisiin automalleihin. Tämä on loistava tilaisuus päivittää autosi ulkonäkö.', 50.00, 50.00, NULL, NULL, 1.00, '2026-04-06 13:28:38', '2026-04-15 13:28:51', 'active', 10, '', 'Uusi', NULL, '2026-04-06 13:28:38', '2026-04-10 01:38:43', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(79, 2, 17, 'Ilmeikäs valokuvaaja- taulu', 'Taulu, jossa henkilö ilmeilee ja pitää kädessään jotain, mikä näyttää olevan valokuvausväline. Taustalla näkyy kodin sisustus ja muita esineitä. Valo tulee huoneeseen, mikä luo mielenkiintoisen tunnelman.', 100.00, 100.00, 120.00, 150.00, 1.00, '2026-04-06 16:18:37', '2026-04-15 16:19:19', 'active', 17, 'Salo', 'Erinomainen', NULL, '2026-04-06 16:18:37', '2026-04-10 03:38:52', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(80, 2, 16, 'Custom Chopper Motorcycle', 'Tyylikäs ja voimakas custom chopper -moottoripyörä, jossa on musta viimeistely ja kiiltävät kromiosat. Moottoripyörässä on laaja takarengas, joka lisää vakautta ja ajomukavuutta. Pyörä on varustettu tehokkaalla moottorilla ja sporttisella pakoputkella, joka tuottaa syvän ja voimakkaan äänen.', 15000.00, 15000.00, NULL, NULL, 1.00, '2026-04-06 18:46:21', '2026-04-15 18:46:47', 'active', 16, '', 'Erinomainen', NULL, '2026-04-06 18:46:21', '2026-04-10 01:36:34', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(81, 2, 15, 'Custom Cruiser Moottoripyörä', 'Tyylikäs ja voimakas custom cruiser moottoripyörä, jossa on näyttävä muotoilu ja vahva moottori. Moottoripyörässä on kromiset yksityiskohdat ja leveä takarengas, joka takaa erinomaisen pidon ja ajokokemuksen. Sopii erinomaisesti sekä kaupunkiin että pidemmille matkoille.', 15000.00, 15000.00, NULL, NULL, 1.00, '2026-04-06 18:48:05', '2026-04-15 18:48:09', 'active', 10, 'Suomi', 'Erinomainen', NULL, '2026-04-06 18:48:05', '2026-04-10 02:44:10', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(82, 2, 11, 'Kaivinkone  Liebherr R924 Compact Litronic', 'Vuosimallin 2014 kaivuri, 10 200 käyttötuntia. 26,300 kg, 120 Kw. Moottori D934S', 10000.00, 10000.00, NULL, NULL, 100.00, '2026-04-05 23:16:08', '2026-04-14 23:16:32', 'active', 9, 'Espoo', 'Hyvä', NULL, '2026-04-05 23:16:08', '2026-04-10 01:28:31', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(83, 2, 2, 'Harley Davidsson', 'Tyylikäs ja yksilöllinen chopper-tyylinen moottoripyörä, jossa on voimakas moottori ja erikoisrakenteinen runko. Musta väri yhdistettynä kiiltävään kromiin luo näyttävän kokonaisuuden. Renkaat ovat leveät ja sopivat hyvin moottorin tehoon, mikä takaa erinomaisen ajokokemuksen. Sopii niin katuajoon kuin näyttelyihin.', 15000.00, 15000.00, 16500.00, 18000.00, 100.00, '2026-04-06 09:28:17', '2026-04-15 09:29:15', 'active', 67, 'Oulu', 'Erinomainen', NULL, '2026-04-06 09:28:17', '2026-04-10 01:22:33', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(84, 2, 2, 'Viper', 'Tyylikäs musta Viper-moottorivene, varustettu mukavilla istuimilla ja modernilla ohjaamolla. Veneessä on tilava avotila sekä laadukas sisustus. Varustettu suojapeitteellä ja trailerilla, mikä helpottaa kuljetusta. Vene on hyvässä kunnossa ja valmis vesille.', 125000.00, 125000.00, 180000.00, 150000.00, 1.00, '2026-04-06 09:30:10', '2026-04-15 09:30:37', 'active', 16, 'Puola', 'Erinomainen', NULL, '2026-04-06 09:30:10', '2026-04-10 01:18:42', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(85, 9, 2, 'Talvirenkaat alumiinivanteilla', 'Neljä talvirengasta alumiinivanteilla, hyvässä kunnossa. Renkaat tarjoavat erinomaisen pidon talvisissa olosuhteissa ja ovat valmiita asennettavaksi. Vanteet ovat tyylikkäät ja hyvässä kunnossa.', 200.00, 200.00, 250.00, 350.00, 1.00, '2026-04-06 19:15:50', '2026-04-15 19:16:35', 'active', 12, 'Helsinki', 'Hyvä, käytetty mutta ei kulunut', NULL, '2026-04-06 19:15:50', '2026-04-10 03:32:38', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(86, 9, 8, 'Mansikkalaatikot (pelkkä pahvi)', 'Laadukkaita suomalaisia mansikkalaatikoita, jotka on pakattu useisiin lavoihin. lava (1000kpl) 1000e', 1000.00, 1100.00, 1000.00, 1500.00, 1.00, '2026-04-05 22:01:45', '2026-04-14 22:02:10', 'active', 36, 'Suomi', 'Erinomainen, tuore tuote', NULL, '2026-04-05 22:01:45', '2026-04-10 01:10:46', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(87, 9, 11, 'Teollinen lattianpesukone TASKI', 'Käytetty teollinen lattianpesukone TASKI, joka on suunniteltu tehokkaaseen lattian puhdistamiseen. Koneessa on näkyviä käytön jälkiä, mutta se on edelleen toimiva. Sopii hyvin liiketiloihin, teollisuuteen tai suuriin tiloihin.', 100.00, 100.00, 150.00, 130.00, 1.00, '2026-04-06 04:42:21', '2026-04-15 04:42:27', 'active', 33, 'Rovaniemi', 'Tuote on käytetty teollinen lattianpesukone TASKI,', NULL, '2026-04-06 04:42:21', '2026-04-10 02:38:08', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(88, 9, 2, 'Neljä vanteellista rengasta', 'Käytetyt, mutta hyväkuntoiset vanteelliset renkaat. Vanteiden muotoilu on tyylikäs ja ne ovat sopivat useisiin ajoneuvoihin. Renkaissa on vielä riittävästi kulutuspintaa ja ne ovat valmiita asennettavaksi. Kuvassa näkyvät renkaat ovat tasapainoiset ja niissä ei ole näkyviä vaurioita.', 150.00, 150.00, 200.00, 300.00, 1.00, '2026-04-06 06:04:48', '2026-04-15 06:05:15', 'active', 23, 'Helsinki', 'Hyvässä kunnossa, käyttökelpoiset.', NULL, '2026-04-06 06:04:48', '2026-04-10 01:05:04', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(89, 9, 2, 'Vanteet 22\"', 'Neljä käytettyä auton rengasta, joissa on alumiinivanteet. Renkaat ovat hyvässä kunnossa ja niissä on riittävästi kulutuspintaa. Sopivat useisiin eri automalleihin. Vanteet ovat tyylikkäät ja lisäävät auton ulkonäköä.', 150.00, 150.00, 200.00, 300.00, 1.00, '2026-04-06 06:57:42', '2026-04-15 06:58:36', 'active', 12, 'Helsinki', 'Hyvä, käytetty', NULL, '2026-04-06 06:57:42', '2026-04-10 01:00:30', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(90, 9, 2, 'Ford 5700 traktori', 'Hyvin toimiva Ford 5700 traktori, varustettuna etukuormaimella. Soveltuu monenlaisiin maataloustöihin ja pihapiirin kunnossapitoon. Traktorissa on hyvä moottori ja se on huollettu säännöllisesti.', 4500.00, 4500.00, 5000.00, 6000.00, 1.00, '2026-04-06 08:57:35', '2026-04-15 08:57:49', 'active', 14, 'Suomi', 'Käytetty, hyvässä kunnossa, normaaleja käytön jälk', NULL, '2026-04-06 08:57:35', '2026-04-10 00:53:24', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(91, 9, 3, 'Inogen One G3 happikonsentraattori', 'Inogen One G3 on kannettava happikonsentraattori, suunniteltu tarjoamaan lisähappea käyttäjille, jotka tarvitsevat apua hengityksessään. Laite on kompakti ja helppokäyttöinen, varustettu selkeällä käyttöliittymällä, jossa on säädettävä happivirta ja indikaattorivalot. Sopii erityisesti koti- ja matkakäyttöön.', 300.00, 300.00, 400.00, NULL, 1.00, '2026-04-06 09:15:30', '2026-04-15 09:15:39', 'active', 11, 'Helsinki', 'Hyväkuntoinen, käyttövalmis. Soita suoraan 040 623', NULL, '2026-04-06 09:15:30', '2026-04-10 00:46:54', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(92, 9, 15, 'Ilmastointilaitteen huoltotyökalu', 'Facom AC.222 -ilmastointilaitteen huoltotyökalu, jota käytetään ajoneuvojen ilmastointijärjestelmien huoltamiseen. Laitteessa on virtapainike, näyttö, sekä liittimet kylmäaineen täyttämistä varten. Työkalu on käytetty ja siinä on näkyviä kulumia, mutta se on toiminnallinen.', 150.00, 150.00, 400.00, 399.00, 1.00, '2026-04-06 09:41:29', '2026-04-15 09:42:15', 'active', 8, 'Helsinki', 'Käytetty, mutta toimiva. Näkyviä kulumia.', NULL, '2026-04-06 09:41:29', '2026-04-10 00:39:44', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(93, 9, 15, 'Työkalulaatikko', 'Mustasta metallista valmistettu työkalulaatikko, jossa on viisi laatikkoa. Laatikot on merkitty eri työkalutyypeillä, kuten sähkötöiden ja muiden työkalujen säilyttämiseen. Laatikko on käytetty ja siinä on näkyviä kulumisen merkkejä, mutta se on edelleen toimiva.', 100.00, 100.00, 150.00, 200.00, 1.00, '2026-04-06 09:42:44', '2026-04-15 09:43:34', 'active', 9, 'Helsinki, Suomi', 'Käytetty, mutta hyvässä kunnossa', NULL, '2026-04-06 09:42:44', '2026-04-10 06:55:41', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(94, 9, 2, 'BIG Detroit Series 60 Turbo', 'Laadukas turboahdin, joka parantaa moottorin tehokkuutta ja suorituskykyä. Sopii useisiin ajoneuvoihin ja on valmistettu kestävästä materiaalista. Tuote on uusi ja se toimitetaan kaikkien tarvittavien osien kanssa. Taitaa olla ns. tarvikeosa. malli BIG Detroit Series 60 Turbo', 1000.00, 1200.00, 1000.00, 1500.00, 1.00, '2026-04-06 09:52:42', '2026-04-15 09:53:31', 'active', 11, 'Helsinki', 'Uusi', NULL, '2026-04-06 09:52:42', '2026-04-10 00:33:37', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(95, 9, 2, 'Renkaanvaihtokone', 'Tämmönen Hofmann Monty 3300 lähtee nyt eteenpäin. Ei mikään lelukone vaan ihan korjaamotason vehje.\r\nYläpuolinen apuvarsi (Easymont Pro) mukana, eli runflatit ja jäykät kumit ei oo mikään itkupotku-raivari.\r\nMukana myös apusäiliö, eli tubelessit saa asettumaan nätisti.\r\n\r\nKone on ollut työssä, eli ei kiillotettu näyttelypeli — mutta tekee sen mitä pitää.\r\nUlko-kiinnitys luokkaa 24” (normi henkilöautot/vannekoot).\r\nTarvitsee ulkopuolisen kompressorin, koneessa ei omaa kompressoria.\r\n\r\n\r\nNouto tai kyytiin, painava. Autan lastauksessa.', 500.00, 500.00, 1000.00, 600.00, 1.00, '2026-04-06 12:00:13', '2026-04-15 12:00:42', 'active', 41, 'Helsinki', 'Hyvässä kunnossa, käyttövalmis', NULL, '2026-04-06 12:00:13', '2026-04-10 05:19:21', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(96, 9, 4, 'Työkalukaappi', 'Mustasta metallista valmistettu työkalukaappi, jossa on useita laatikoita eri työkaluille. Kaappi on käytetty, pinnassa on naarmuja ja kulumia, mutta se on edelleen toimiva. Sopii erinomaisesti työpajoihin tai autotalliin työkalujen järjestämiseen.', 100.00, 100.00, 150.00, 250.00, 1.00, '2026-04-05 23:55:58', '2026-04-14 23:56:08', 'active', 8, 'Helsinki', 'Käytetty, mutta hyvässä kunnossa.', NULL, '2026-04-05 23:55:58', '2026-04-10 00:15:30', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(99, 3, 1, 'Metsäpalsta 10 hehtaaria', 'Metsäpalsta Keski-Suomessa, pinta-ala 10 hehtaaria. Sekametsää, hyvä puusto. Metsätie kulkee tilan vierestä. Sopii sijoitukseksi tai omaan metsästyskäyttöön.', 45000.00, 45000.00, NULL, NULL, 1000.00, '2026-02-23 19:06:07', '2026-03-02 19:06:07', 'active', 3, 'Jyväskylä', 'Hyvä', NULL, '2026-02-23 19:06:07', '2026-03-12 11:24:26', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(162, 24, 1, 'Metsäpalsta (Kuusamossa)', 'Metsäpalsta Kuusamon alueella. Hyvä tieyhteys, sekapuustoa ja kasvatusmetsää. Sopii sijoitukseen tai omaan puuntarpeeseen. Pinta-ala n. 6,2 ha.', 18000.00, 18000.00, NULL, 28000.00, 250.00, '2026-02-23 19:23:01', '2026-03-03 19:23:01', 'active', 3, 'Kuusamo', 'Hyvä', NULL, '2026-02-23 19:23:01', '2026-03-19 17:55:37', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(181, 22, 1, 'Omakotitalotontti Espoo', 'Kaunis 1500 m² tontti rauhallisella alueella Espoossa. Tontti on rakennuskelpoinen ja sille on myönnetty rakennuslupa. Sijainti lähellä palveluita ja hyvät liikenneyhteydet.', 50000.00, 56000.00, 80000.00, 120000.00, 1000.00, '2026-02-23 19:28:17', '2026-02-26 19:28:17', 'active', 18, 'Espoo', 'Uusi', NULL, '2026-02-23 19:28:17', '2026-02-26 13:34:09', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(182, 23, 1, 'Mökki Järvenrannalla', '45 m² hirsimökki idyllisellä järvenrannalla Savossa. Sauna, ruokailutila, makuuhuone ja keittiö. Ranta-alue n. 25 metriä. Hyvät kalastusmahdollisuudet.', 35000.00, 39000.00, NULL, 65000.00, 500.00, '2026-02-23 19:28:17', '2026-02-28 19:28:17', 'active', 1, 'Savonlinna', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-02-27 17:39:32', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL);
INSERT INTO `auctions` (`id`, `user_id`, `category_id`, `title`, `description`, `starting_price`, `current_price`, `reserve_price`, `buy_now_price`, `bid_increment`, `start_time`, `end_time`, `status`, `views`, `location`, `condition_description`, `ai_details`, `created_at`, `updated_at`, `map_coordinates`, `seller_commitment`, `short_summary`, `featured`, `condition_grade`, `seller_notes`, `pickup_info`, `shipping_info`, `payment_info`, `inspection_info`, `included_items`, `defects`, `warranty_info`, `model_reference`, `serial_number`, `delivery_available`, `pickup_available`, `payment_deadline_days`, `storage_fee_info`) VALUES
(183, 22, 2, 'Toyota Corolla 2018', 'Hyvin huollettu Toyota Corolla 2018, ajettu 85 000 km. 1.6 bensiinimoottori, automaattivaihteisto. Katsastettu äskettäin, seuraava katsastus 2026. Varusteet: ilmastointi, cruise control, peruutuskamera.', 12000.00, 15400.00, 14000.00, 16500.00, 200.00, '2026-02-23 19:28:17', '2026-02-27 19:28:17', 'active', 5, 'Helsinki', 'Hyvä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Toyota Corolla\"},{\"label\":\"Rekisteritunnus\",\"value\":\"ABC-123\"},{\"label\":\"Valmistenumero\",\"value\":\"JTDBU4EE2B9123456\"},{\"label\":\"Vuosimalli\",\"value\":\"2018\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"3/2018\"},{\"label\":\"Mittarilukema\",\"value\":\"85 000 km\"},{\"label\":\"Moottori\",\"value\":\"1.6l Bensiini, 97 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Etuveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Automaatti\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:28:17', '2026-02-27 18:01:10', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(184, 23, 2, 'Yamaha MT-07 Moottoripyörä', 'Yamaha MT-07 2019, ajettu 18 000 km. 689 cc kaksisylinterinen moottori. Erinomaisessa kunnossa, aina säilytetty sisätiloissa. Huoltokirja ja kaikki alkuperäiset paperit mukana.', 5500.00, 6500.00, NULL, 7500.00, 100.00, '2026-02-23 19:28:17', '2026-02-25 19:28:17', 'active', 1, 'Tampere', 'Erinomainen', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Yamaha MT-07\"},{\"label\":\"Rekisteritunnus\",\"value\":\"MP-789\"},{\"label\":\"Valmistenumero\",\"value\":\"JYARM19E2KA012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2019\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"5/2019\"},{\"label\":\"Mittarilukema\",\"value\":\"18 000 km\"},{\"label\":\"Moottori\",\"value\":\"0.7l Bensiini, 54 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:28:17', '2026-02-24 20:04:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(185, 24, 2, 'Volkswagen Transporter Pakettiauto', 'VW Transporter 2015, 2.0 TDI diesel. Ajettu 185 000 km. Hyötyajoneuvona käytetty. Iso tavaratila, hyllyt ja jakajat mukana. Luotettava ja taloudellinen.', 15000.00, 15800.00, NULL, NULL, 200.00, '2026-02-23 19:28:17', '2026-03-01 19:28:17', 'active', 3, 'Turku', 'Tyydyttävä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Volkswagen Transporter T6\"},{\"label\":\"Rekisteritunnus\",\"value\":\"TUR-456\"},{\"label\":\"Valmistenumero\",\"value\":\"WV1ZZZ7HZFH012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2015\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"1/2015\"},{\"label\":\"Mittarilukema\",\"value\":\"185 000 km\"},{\"label\":\"Moottori\",\"value\":\"2.0l Diesel, 103 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:28:17', '2026-03-01 09:41:41', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(186, 22, 3, 'iPhone 14 Pro 256GB', 'Apple iPhone 14 Pro, 256GB, Space Black. Ostettu vuosi sitten, käytetty vain vähän. Alkuperäinen pakkaus ja tarvikkeet mukana. Ei naarmuja tai kolhuja. Akun kapasiteetti 96%.', 800.00, 1325.00, 900.00, 1100.00, 25.00, '2026-02-23 19:28:17', '2026-02-24 19:28:17', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-02-23 22:38:10', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(187, 23, 3, 'Samsung 65\" 4K Smart TV', 'Samsung 65 tuuman 4K Smart TV, malli QN65Q80A. QLED-teknologia, HDR10+. Ostettu 2022. Toimii moitteetta, upea kuvanlaatu. Mukana kaukosäädin ja kaapelit.', 600.00, 650.00, NULL, 900.00, 25.00, '2026-02-23 19:28:17', '2026-02-26 19:28:17', 'active', 2, 'Espoo', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-02-26 13:32:45', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(188, 24, 3, 'Dell XPS 15 Kannettava', 'Dell XPS 15 kannettava tietokone. Intel i7 11. sukupolvi, 16GB RAM, 512GB SSD, NVIDIA GTX 1650 Ti näytönohjain. 15.6\" 4K näyttö. Hieno kone luovaan työhön ja pelaamiseen.', 900.00, 950.00, NULL, 1300.00, 50.00, '2026-02-23 19:28:17', '2026-02-27 19:28:17', 'active', 2, 'Oulu', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-02-27 16:09:51', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(189, 22, 4, 'Design Sohva 3-istuttava', 'Moderni design-sohva, 3-istuttava. Harmaa kangasverhoilu. Pituus 220 cm. Mukava istua ja upea ilme olohuoneeseen. Ei lemmikkejä tai tupakointia.', 300.00, 380.00, NULL, 600.00, 20.00, '2026-02-23 19:28:17', '2026-02-25 19:28:17', 'active', 2, 'Helsinki', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-03-13 07:35:25', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(190, 23, 4, 'Antiikki Ruokapöytä Tammi', 'Kaunis antiikki ruokapöytä massiivitammea. 1950-luvulta. Pituus 180 cm, leveys 90 cm. Patinoidutpinta, muutama käytön jälki mutta kokonaisuus hieno. 6 tuolia mukana.', 400.00, 450.00, NULL, NULL, 25.00, '2026-02-23 19:28:17', '2026-02-28 19:28:17', 'active', 1, 'Turku', 'Tyydyttävä', NULL, '2026-02-23 19:28:17', '2026-02-27 19:04:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(191, 24, 4, 'Miele Astianpesukone', 'Miele astianpesukone, malli G 7310 SC. Ostettu 2020. Hiljainen, energiatehokas (A+++). Laaja ohjelmavalikoima. Toimii täydellisesti.', 400.00, 400.00, NULL, 700.00, 25.00, '2026-02-23 19:28:17', '2026-02-26 19:28:17', 'active', 5, 'Vantaa', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-04-08 09:22:35', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(192, 22, 5, 'Maastopyörä Giant Trance 2023', 'Giant Trance X 29er maastopyörä, malli 2023. Full suspension, 12-vaihteinen. Ajettu n. 500 km. Huollettu säännöllisesti. Koko L. Upea pyörä trail-ajoon.', 1500.00, 2150.00, NULL, 2500.00, 50.00, '2026-02-23 19:28:17', '2026-02-27 19:28:17', 'active', 1, 'Lahti', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-02-27 19:03:03', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(193, 23, 5, 'Suksisetti + Monot', 'Maastohiihtosukset Fischer Speedmax Skating Plus, pituus 186 cm. Salomon monot koko 44. Käytetty vain yhden kauden. Hyväkuntoiset.', 200.00, 240.00, NULL, 350.00, 10.00, '2026-02-23 19:28:17', '2026-02-25 19:28:17', 'active', 1, 'Rovaniemi', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-02-23 22:36:54', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(194, 24, 5, 'Kuntosali Penkkipunnerrus', 'Säädettävä penkkipunnerruspenkki + 100kg levypainosetti. Toimii täydellisesti. Hieno lisä kotikuntosalille.', 250.00, 265.00, NULL, NULL, 15.00, '2026-02-23 19:28:17', '2026-03-01 19:28:17', 'active', 2, 'Jyväskylä', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-03-01 09:42:44', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(195, 22, 6, 'Canada Goose Talvitakki', 'Canada Goose Expedition Parka, koko M. Musta. Ostettu 2021. Lämpöinen ja laadukas talvitakki. Turkis irrotettavissa. Pesty ja huollettu ammattilaisella.', 400.00, 575.00, NULL, 700.00, 25.00, '2026-02-23 19:28:17', '2026-02-26 19:28:17', 'active', 2, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-02-26 13:50:58', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(196, 23, 6, 'Nike Air Max Tennarit', 'Nike Air Max 90, koko 42. Valkoinen/punainen väritys. Käytetty muutaman kerran. Mukana alkuperäinen laatikko.', 60.00, 105.00, NULL, 120.00, 5.00, '2026-02-23 19:28:17', '2026-02-24 19:28:17', 'active', 2, 'Tampere', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-03-12 08:51:48', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(197, 24, 6, 'Vintage Farkkutakki Levi\'s', 'Vintage Levi\'s farkkutakki 1980-luvulta. Koko M. Hieno retro-ilme. Muutama pieni kuluma, lisää tunnelmaa.', 50.00, 50.00, NULL, NULL, 5.00, '2026-02-23 19:28:17', '2026-02-28 19:28:17', 'active', 1, 'Turku', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-02-27 19:01:31', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(198, 22, 7, 'Vanha Rolex Kello 1970-luku', 'Alkuperäinen Rolex Datejust 1970-luvulta. Automaattinen. Toimii moitteetta. Patina ja käytön jälkiä, mutta juuri se tekee kellosta aidon. Keräilykappale.', 2000.00, 3900.00, 3000.00, NULL, 100.00, '2026-02-23 19:28:17', '2026-03-02 19:28:17', 'active', 2, 'Helsinki', 'Tyydyttävä', NULL, '2026-02-23 19:28:17', '2026-03-01 03:39:36', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(199, 23, 7, 'Pokemon Korttipakka 1. Painos', 'Alkuperäinen Pokemon Base Set booster box, sinetöity. 1. painos 1999. Erittäin harvinainen ja arvostettu keräilykohde. Täydellinen kunto.', 5000.00, 7800.00, NULL, 8000.00, 200.00, '2026-02-23 19:28:17', '2026-02-27 19:28:17', 'active', 9, 'Espoo', 'Erinomainen', NULL, '2026-02-23 19:28:17', '2026-04-07 15:54:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(200, 24, 7, 'Antiikki Samovaari', 'Venäläinen antiikki samovaari 1800-luvun lopulta. Messinkiä. Kauniisti koristeltu. Harvinainen kappale. Upea sisustuselementti tai keräilykohde.', 400.00, 475.00, NULL, NULL, 25.00, '2026-02-23 19:28:17', '2026-03-01 19:28:17', 'active', 3, 'Tampere', 'Hyvä', NULL, '2026-02-23 19:28:17', '2026-03-01 04:07:28', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(201, 22, 1, 'Omakotitalotontti Espoo', 'Kaunis 1500 m² tontti rauhallisella alueella Espoossa. Tontti on rakennuskelpoinen ja sille on myönnetty rakennuslupa. Sijainti lähellä palveluita ja hyvät liikenneyhteydet.', 50000.00, 56000.00, 80000.00, 120000.00, 1000.00, '2026-02-23 19:46:00', '2026-02-26 19:46:00', 'active', 1, 'Espoo', 'Uusi', NULL, '2026-02-23 19:46:00', '2026-02-26 13:33:45', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(202, 23, 1, 'Mökki Järvenrannalla', '45 m² hirsimökki idyllisellä järvenrannalla Savossa. Sauna, ruokailutila, makuuhuone ja keittiö. Ranta-alue n. 25 metriä. Hyvät kalastusmahdollisuudet.', 35000.00, 39000.00, NULL, 65000.00, 500.00, '2026-02-23 19:46:00', '2026-02-28 19:46:00', 'active', 0, 'Savonlinna', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-02-23 19:46:00', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(203, 24, 1, 'Metsäpalsta Kuhmo', 'Metsäpalsta n. 6,2 ha. Hyvät metsäautotiet perille, sekapuustoa ja varttunutta kuusikkoa. Sopii sijoittajalle tai omiin polttopuihin.', 20000.00, 20000.00, NULL, NULL, 250.00, '2026-02-23 19:46:00', '2026-03-05 19:46:00', 'active', 9, 'Kuhmo', 'Tyydyttävä', NULL, '2026-02-23 19:46:00', '2026-04-09 03:56:21', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(204, 22, 2, 'Toyota Corolla 2018', 'Hyvin huollettu Toyota Corolla 2018, ajettu 85 000 km. 1.6 bensiinimoottori, automaattivaihteisto. Katsastettu äskettäin, seuraava katsastus 2026. Varusteet: ilmastointi, cruise control, peruutuskamera.', 12000.00, 15400.00, 14000.00, 16500.00, 200.00, '2026-02-23 19:46:00', '2026-02-27 19:46:00', 'active', 2, 'Helsinki', 'Hyvä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Toyota Corolla\"},{\"label\":\"Rekisteritunnus\",\"value\":\"ABC-123\"},{\"label\":\"Valmistenumero\",\"value\":\"JTDBU4EE2B9123456\"},{\"label\":\"Vuosimalli\",\"value\":\"2018\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"3/2018\"},{\"label\":\"Mittarilukema\",\"value\":\"85 000 km\"},{\"label\":\"Moottori\",\"value\":\"1.6l Bensiini, 97 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Etuveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Automaatti\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:00', '2026-02-27 16:04:09', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(205, 23, 2, 'Yamaha MT-07 Moottoripyörä', 'Yamaha MT-07 2019, ajettu 18 000 km. 689 cc kaksisylinterinen moottori. Erinomaisessa kunnossa, aina säilytetty sisätiloissa. Huoltokirja ja kaikki alkuperäiset paperit mukana.', 5500.00, 6500.00, NULL, 7500.00, 100.00, '2026-02-23 19:46:00', '2026-02-25 19:46:00', 'active', 1, 'Tampere', 'Erinomainen', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Yamaha MT-07\"},{\"label\":\"Rekisteritunnus\",\"value\":\"MP-789\"},{\"label\":\"Valmistenumero\",\"value\":\"JYARM19E2KA012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2019\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"5/2019\"},{\"label\":\"Mittarilukema\",\"value\":\"18 000 km\"},{\"label\":\"Moottori\",\"value\":\"0.7l Bensiini, 54 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:00', '2026-02-23 22:41:27', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(206, 24, 2, 'Volkswagen Transporter Pakettiauto', 'VW Transporter 2015, 2.0 TDI diesel. Ajettu 185 000 km. Hyötyajoneuvona käytetty. Iso tavaratila, hyllyt ja jakajat mukana. Luotettava ja taloudellinen.', 15000.00, 15800.00, NULL, NULL, 200.00, '2026-02-23 19:46:00', '2026-03-01 19:46:00', 'active', 2, 'Turku', 'Tyydyttävä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Volkswagen Transporter T6\"},{\"label\":\"Rekisteritunnus\",\"value\":\"TUR-456\"},{\"label\":\"Valmistenumero\",\"value\":\"WV1ZZZ7HZFH012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2015\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"1/2015\"},{\"label\":\"Mittarilukema\",\"value\":\"185 000 km\"},{\"label\":\"Moottori\",\"value\":\"2.0l Diesel, 103 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:00', '2026-03-01 09:42:14', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(207, 22, 3, 'iPhone 14 Pro 256GB', 'Apple iPhone 14 Pro, 256GB, Space Black. Ostettu vuosi sitten, käytetty vain vähän. Alkuperäinen pakkaus ja tarvikkeet mukana. Ei naarmuja tai kolhuja. Akun kapasiteetti 96%.', 800.00, 1325.00, 900.00, 1100.00, 25.00, '2026-02-23 19:46:00', '2026-02-24 19:46:00', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-02-23 22:39:50', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(208, 23, 3, 'Samsung 65\" 4K Smart TV', 'Samsung 65 tuuman 4K Smart TV, malli QN65Q80A. QLED-teknologia, HDR10+. Ostettu 2022. Toimii moitteetta, upea kuvanlaatu. Mukana kaukosäädin ja kaapelit.', 600.00, 650.00, NULL, 900.00, 25.00, '2026-02-23 19:46:00', '2026-02-26 19:46:00', 'active', 1, 'Espoo', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-02-26 13:39:35', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(209, 24, 3, 'Dell XPS 15 Kannettava', 'Dell XPS 15 kannettava tietokone. Intel i7 11. sukupolvi, 16GB RAM, 512GB SSD, NVIDIA GTX 1650 Ti näytönohjain. 15.6\" 4K näyttö. Hieno kone luovaan työhön ja pelaamiseen.', 900.00, 950.00, NULL, 1300.00, 50.00, '2026-02-23 19:46:00', '2026-02-27 19:46:00', 'active', 3, 'Oulu', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-02-27 18:19:52', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(210, 22, 4, 'Design Sohva 3-istuttava', 'Moderni design-sohva, 3-istuttava. Harmaa kangasverhoilu. Pituus 220 cm. Mukava istua ja upea ilme olohuoneeseen. Ei lemmikkejä tai tupakointia.', 300.00, 380.00, NULL, 600.00, 20.00, '2026-02-23 19:46:00', '2026-02-25 19:46:00', 'active', 1, 'Helsinki', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-02-23 22:32:22', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(211, 23, 4, 'Antiikki Ruokapöytä Tammi', 'Kaunis antiikki ruokapöytä massiivitammea. 1950-luvulta. Patinoitunut pinta, muutama käytön jälki mutta kokonaisuus hieno. 6 tuolia mukana.', 400.00, 450.00, NULL, NULL, 25.00, '2026-02-23 19:46:00', '2026-02-28 19:46:00', 'active', 1, 'Turku', 'Tyydyttävä', NULL, '2026-02-23 19:46:00', '2026-02-27 18:14:59', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(212, 24, 4, 'Miele Astianpesukone', 'Miele astianpesukone, malli G 7310 SC. Ostettu 2020. Hiljainen, energiatehokas. Laaja ohjelmavalikoima. Toimii täydellisesti.', 400.00, 400.00, NULL, 700.00, 25.00, '2026-02-23 19:46:00', '2026-02-26 19:46:00', 'active', 1, 'Vantaa', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-02-26 13:33:14', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(213, 22, 5, 'Maastopyörä Giant Trance 2023', 'Giant Trance X 29er maastopyörä, malli 2023. Full suspension, 12-vaihteinen. Ajettu n. 500 km. Huollettu säännöllisesti. Koko L.', 1500.00, 2150.00, NULL, 2500.00, 50.00, '2026-02-23 19:46:00', '2026-02-27 19:46:00', 'active', 2, 'Lahti', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-02-27 18:28:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(214, 23, 5, 'Suksisetti + Monot', 'Maastohiihtosukset Fischer Speedmax Skating Plus, pituus 186 cm. Salomon monot koko 44. Käytetty vain yhden kauden. Hyväkuntoiset.', 200.00, 240.00, NULL, 350.00, 10.00, '2026-02-23 19:46:00', '2026-02-25 19:46:00', 'active', 1, 'Rovaniemi', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-02-23 22:43:58', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(215, 24, 5, 'Kuntosali Penkkipunnerrus', 'Säädettävä penkkipunnerruspenkki + 100kg levypainosetti. Toimii täydellisesti. Hieno lisä kotikuntosalille.', 250.00, 265.00, NULL, NULL, 15.00, '2026-02-23 19:46:00', '2026-03-01 19:46:00', 'active', 3, 'Jyväskylä', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-04-07 16:21:38', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(216, 22, 6, 'Canada Goose Talvitakki', 'Canada Goose Expedition Parka, koko M. Musta. Ostettu 2021. Lämpöinen ja laadukas talvitakki. Turkis irrotettavissa. Pesty ja huollettu ammattilaisella.', 400.00, 575.00, NULL, 700.00, 25.00, '2026-02-23 19:46:00', '2026-02-26 19:46:00', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-02-26 13:37:27', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(217, 23, 6, 'Nike Air Max Tennarit', 'Nike Air Max 90, koko 42. Valkoinen/punainen väritys. Käytetty muutaman kerran. Mukana alkuperäinen laatikko.', 60.00, 105.00, NULL, 120.00, 5.00, '2026-02-23 19:46:00', '2026-02-24 19:46:00', 'active', 1, 'Tampere', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-02-23 22:36:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(218, 24, 6, 'Vintage Farkkutakki Levi\'s', 'Vintage Levi\'s farkkutakki 1980-luvulta. Koko M. Hieno retro-ilme. Muutama pieni kuluma, lisää tunnelmaa.', 50.00, 50.00, NULL, NULL, 5.00, '2026-02-23 19:46:00', '2026-02-28 19:46:00', 'active', 1, 'Turku', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-02-27 17:17:31', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(219, 22, 7, 'Vanha Rolex Kello 1970-luku', 'Alkuperäinen Rolex Datejust 1970-luvulta. Automaattinen. Toimii moitteetta. Patina ja käytön jälkiä, mutta juuri se tekee kellosta aidon. Keräilykappale.', 2000.00, 3900.00, 3000.00, NULL, 100.00, '2026-02-23 19:46:00', '2026-03-02 19:46:00', 'active', 4, 'Helsinki', 'Tyydyttävä', NULL, '2026-02-23 19:46:00', '2026-03-01 04:47:17', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(220, 23, 7, 'Pokemon Korttipakka 1. Painos', 'Alkuperäinen Pokemon Base Set booster box, sinetöity. 1. painos 1999. Erittäin harvinainen ja arvostettu keräilykohde. Täydellinen kunto.', 5000.00, 7800.00, NULL, 8000.00, 200.00, '2026-02-23 19:46:00', '2026-02-27 19:46:00', 'active', 2, 'Espoo', 'Erinomainen', NULL, '2026-02-23 19:46:00', '2026-03-17 05:46:56', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(221, 24, 7, 'Antiikki Samovaari', 'Venäläinen antiikki samovaari 1800-luvun lopulta. Messinkiä. Kauniisti koristeltu. Harvinainen kappale. Upea sisustuselementti tai keräilykohde.', 400.00, 475.00, NULL, NULL, 25.00, '2026-02-23 19:46:00', '2026-03-01 19:46:00', 'active', 2, 'Tampere', 'Hyvä', NULL, '2026-02-23 19:46:00', '2026-03-01 04:07:20', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(222, 22, 1, 'Omakotitalotontti Espoo', 'Kaunis 1500 m² tontti rauhallisella alueella Espoossa. Tontti on rakennuskelpoinen ja sille on myönnetty rakennuslupa. Sijainti lähellä palveluita ja hyvät liikenneyhteydet.', 50000.00, 56000.00, 80000.00, 120000.00, 1000.00, '2026-02-23 19:46:23', '2026-02-26 19:46:23', 'active', 1, 'Espoo', 'Uusi', NULL, '2026-02-23 19:46:23', '2026-02-26 13:34:20', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(223, 23, 1, 'Mökki Järvenrannalla', '45 m² hirsimökki idyllisellä järvenrannalla Savossa. Sauna, ruokailutila, makuuhuone ja keittiö. Ranta-alue n. 25 metriä. Hyvät kalastusmahdollisuudet.', 35000.00, 39000.00, NULL, 65000.00, 500.00, '2026-02-23 19:46:23', '2026-02-28 19:46:23', 'active', 1, 'Savonlinna', 'Hyvä', NULL, '2026-02-23 19:46:23', '2026-02-27 18:31:15', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(224, 24, 1, 'Metsäpalsta Kuhmo', 'Metsäpalsta n. 6,2 ha. Hyvät metsäautotiet perille, sekapuustoa ja varttunutta kuusikkoa. Sopii sijoittajalle tai omiin polttopuihin.', 20000.00, 20000.00, NULL, NULL, 250.00, '2026-02-23 19:46:23', '2026-03-05 19:46:23', 'active', 10, 'Kuhmo', 'Tyydyttävä', NULL, '2026-02-23 19:46:23', '2026-04-08 14:15:47', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(225, 22, 2, 'Toyota Corolla 2018', 'Hyvin huollettu Toyota Corolla 2018, ajettu 85 000 km. 1.6 bensiinimoottori, automaattivaihteisto. Katsastettu äskettäin, seuraava katsastus 2026. Varusteet: ilmastointi, cruise control, peruutuskamera.', 12000.00, 15400.00, 14000.00, 16500.00, 200.00, '2026-02-23 19:46:23', '2026-02-27 19:46:23', 'active', 2, 'Helsinki', 'Hyvä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Toyota Corolla\"},{\"label\":\"Rekisteritunnus\",\"value\":\"ABC-123\"},{\"label\":\"Valmistenumero\",\"value\":\"JTDBU4EE2B9123456\"},{\"label\":\"Vuosimalli\",\"value\":\"2018\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"3/2018\"},{\"label\":\"Mittarilukema\",\"value\":\"85 000 km\"},{\"label\":\"Moottori\",\"value\":\"1.6l Bensiini, 97 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Etuveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Automaatti\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:23', '2026-02-27 18:26:22', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(226, 23, 2, 'Yamaha MT-07 Moottoripyörä', 'Yamaha MT-07 2019, ajettu 18 000 km. 689 cc kaksisylinterinen moottori. Erinomaisessa kunnossa, aina säilytetty sisätiloissa. Huoltokirja ja kaikki alkuperäiset paperit mukana.', 5500.00, 6500.00, NULL, 7500.00, 100.00, '2026-02-23 19:46:23', '2026-02-25 19:46:23', 'active', 1, 'Tampere', 'Erinomainen', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Yamaha MT-07\"},{\"label\":\"Rekisteritunnus\",\"value\":\"MP-789\"},{\"label\":\"Valmistenumero\",\"value\":\"JYARM19E2KA012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2019\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"5/2019\"},{\"label\":\"Mittarilukema\",\"value\":\"18 000 km\"},{\"label\":\"Moottori\",\"value\":\"0.7l Bensiini, 54 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:23', '2026-02-23 22:39:19', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(227, 24, 2, 'Volkswagen Transporter Pakettiauto', 'VW Transporter 2015, 2.0 TDI diesel. Ajettu 185 000 km. Hyötyajoneuvona käytetty. Iso tavaratila, hyllyt ja jakajat mukana. Luotettava ja taloudellinen.', 15000.00, 15800.00, NULL, NULL, 200.00, '2026-02-23 19:46:23', '2026-03-01 19:46:23', 'active', 3, 'Turku', 'Tyydyttävä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Volkswagen Transporter T6\"},{\"label\":\"Rekisteritunnus\",\"value\":\"TUR-456\"},{\"label\":\"Valmistenumero\",\"value\":\"WV1ZZZ7HZFH012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2015\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"1/2015\"},{\"label\":\"Mittarilukema\",\"value\":\"185 000 km\"},{\"label\":\"Moottori\",\"value\":\"2.0l Diesel, 103 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:23', '2026-03-01 17:40:21', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(228, 22, 3, 'iPhone 14 Pro 256GB', 'Apple iPhone 14 Pro, 256GB, Space Black. Ostettu vuosi sitten, käytetty vain vähän. Alkuperäinen pakkaus ja tarvikkeet mukana. Ei naarmuja tai kolhuja. Akun kapasiteetti 96%.', 800.00, 1325.00, 900.00, 1100.00, 25.00, '2026-02-23 19:46:23', '2026-02-24 19:46:23', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-02-23 22:34:37', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(229, 23, 3, 'Samsung 65\" 4K Smart TV', 'Samsung 65 tuuman 4K Smart TV, malli QN65Q80A. QLED-teknologia, HDR10+. Ostettu 2022. Toimii moitteetta, upea kuvanlaatu. Mukana kaukosäädin ja kaapelit.', 600.00, 650.00, NULL, 900.00, 25.00, '2026-02-23 19:46:23', '2026-02-26 19:46:23', 'active', 2, 'Espoo', 'Hyvä', NULL, '2026-02-23 19:46:23', '2026-02-26 13:36:57', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(230, 24, 3, 'Dell XPS 15 Kannettava', 'Dell XPS 15 kannettava tietokone. Intel i7 11. sukupolvi, 16GB RAM, 512GB SSD, NVIDIA GTX 1650 Ti näytönohjain. 15.6\" 4K näyttö. Hieno kone luovaan työhön ja pelaamiseen.', 900.00, 950.00, NULL, 1300.00, 50.00, '2026-02-23 19:46:23', '2026-02-27 19:46:23', 'active', 4, 'Oulu', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-04-02 09:55:01', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(231, 22, 4, 'Design Sohva 3-istuttava', 'Moderni design-sohva, 3-istuttava. Harmaa kangasverhoilu. Pituus 220 cm. Mukava istua ja upea ilme olohuoneeseen. Ei lemmikkejä tai tupakointia.', 300.00, 380.00, NULL, 600.00, 20.00, '2026-02-23 19:46:23', '2026-02-25 19:46:23', 'active', 1, 'Helsinki', 'Hyvä', NULL, '2026-02-23 19:46:23', '2026-02-23 22:42:32', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(232, 23, 4, 'Antiikki Ruokapöytä Tammi', 'Kaunis antiikki ruokapöytä massiivitammea. 1950-luvulta. Patinoitunut pinta, muutama käytön jälki mutta kokonaisuus hieno. 6 tuolia mukana.', 400.00, 450.00, NULL, NULL, 25.00, '2026-02-23 19:46:23', '2026-02-28 19:46:23', 'active', 1, 'Turku', 'Tyydyttävä', NULL, '2026-02-23 19:46:23', '2026-02-27 18:55:46', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(233, 24, 4, 'Miele Astianpesukone', 'Miele astianpesukone, malli G 7310 SC. Ostettu 2020. Hiljainen, energiatehokas. Laaja ohjelmavalikoima. Toimii täydellisesti.', 400.00, 400.00, NULL, 700.00, 25.00, '2026-02-23 19:46:23', '2026-02-26 19:46:23', 'active', 1, 'Vantaa', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-02-26 13:37:19', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(234, 22, 5, 'Maastopyörä Giant Trance 2023', 'Giant Trance X 29er maastopyörä, malli 2023. Full suspension, 12-vaihteinen. Ajettu n. 500 km. Huollettu säännöllisesti. Koko L.', 1500.00, 2150.00, NULL, 2500.00, 50.00, '2026-02-23 19:46:23', '2026-02-27 19:46:23', 'active', 2, 'Lahti', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-02-27 19:12:38', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(235, 23, 5, 'Suksisetti + Monot', 'Maastohiihtosukset Fischer Speedmax Skating Plus, pituus 186 cm. Salomon monot koko 44. Käytetty vain yhden kauden. Hyväkuntoiset.', 200.00, 240.00, NULL, 350.00, 10.00, '2026-02-23 19:46:23', '2026-02-25 19:46:23', 'active', 1, 'Rovaniemi', 'Hyvä', NULL, '2026-02-23 19:46:23', '2026-02-23 22:42:04', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(236, 24, 5, 'Kuntosali Penkkipunnerrus', 'Säädettävä penkkipunnerruspenkki + 100kg levypainosetti. Toimii täydellisesti. Hieno lisä kotikuntosalille.', 250.00, 265.00, NULL, NULL, 1.00, '2026-04-06 18:46:23', '2026-04-15 18:46:23', 'active', 15, 'Jyväskylä', 'Hyvä', NULL, '2026-04-06 18:46:23', '2026-04-10 05:30:30', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(237, 22, 6, 'Canada Goose Talvitakki', 'Canada Goose Expedition Parka, koko M. Musta. Ostettu 2021. Lämpöinen ja laadukas talvitakki. Turkis irrotettavissa. Pesty ja huollettu ammattilaisella.', 400.00, 575.00, NULL, 700.00, 25.00, '2026-02-23 19:46:23', '2026-02-26 19:46:23', 'active', 2, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-02-26 13:36:37', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(238, 23, 6, 'Nike Air Max Tennarit', 'Nike Air Max 90, koko 42. Valkoinen/punainen väritys. Käytetty muutaman kerran. Mukana alkuperäinen laatikko.', 60.00, 105.00, NULL, 120.00, 5.00, '2026-02-23 19:46:23', '2026-02-24 19:46:23', 'active', 1, 'Tampere', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-02-23 22:43:00', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(239, 24, 6, 'Vintage Farkkutakki Levi\'s', 'Vintage Levi\'s farkkutakki 1980-luvulta. Koko M. Hieno retro-ilme. Muutama pieni kuluma, lisää tunnelmaa.', 50.00, 50.00, NULL, NULL, 5.00, '2026-02-23 19:46:23', '2026-02-28 19:46:23', 'active', 1, 'Turku', 'Hyvä', NULL, '2026-02-23 19:46:23', '2026-02-27 18:06:28', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(240, 22, 7, 'Vanha Rolex Kello 1970-luku', 'Alkuperäinen Rolex Datejust 1970-luvulta. Automaattinen. Toimii moitteetta. Patina ja käytön jälkiä, mutta juuri se tekee kellosta aidon. Keräilykappale.', 2000.00, 3900.00, 3000.00, NULL, 100.00, '2026-02-23 19:46:23', '2026-03-02 19:46:23', 'active', 3, 'Helsinki', 'Tyydyttävä', NULL, '2026-02-23 19:46:23', '2026-03-23 10:05:58', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(241, 23, 7, 'Pokemon Korttipakka 1. Painos', 'Alkuperäinen Pokemon Base Set booster box, sinetöity. 1. painos 1999. Erittäin harvinainen ja arvostettu keräilykohde. Täydellinen kunto.', 5000.00, 7800.00, NULL, 8000.00, 200.00, '2026-02-23 19:46:23', '2026-02-27 19:46:23', 'active', 2, 'Espoo', 'Erinomainen', NULL, '2026-02-23 19:46:23', '2026-02-27 19:11:07', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(242, 24, 7, 'Antiikki Samovaari', 'Venäläinen antiikki samovaari 1800-luvun lopulta. Messinkiä. Kauniisti koristeltu. Harvinainen kappale. Upea sisustuselementti tai keräilykohde.', 400.00, 475.00, NULL, NULL, 25.00, '2026-02-23 19:46:23', '2026-03-01 19:46:23', 'active', 7, 'Tampere', 'Hyvä', NULL, '2026-02-23 19:46:23', '2026-03-25 05:37:23', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(243, 22, 1, 'Omakotitalotontti Espoo', 'Kaunis 1500 m² tontti rauhallisella alueella Espoossa. Tontti on rakennuskelpoinen ja sille on myönnetty rakennuslupa. Sijainti lähellä palveluita ja hyvät liikenneyhteydet.', 50000.00, 56000.00, 80000.00, 120000.00, 1000.00, '2026-02-23 19:46:33', '2026-02-26 19:46:33', 'active', 1, 'Espoo', 'Uusi', NULL, '2026-02-23 19:46:33', '2026-02-26 13:39:23', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(244, 23, 1, 'Mökki Järvenrannalla', '45 m² hirsimökki idyllisellä järvenrannalla Savossa. Sauna, ruokailutila, makuuhuone ja keittiö. Ranta-alue n. 25 metriä. Hyvät kalastusmahdollisuudet.', 35000.00, 39000.00, NULL, 65000.00, 500.00, '2026-02-23 19:46:33', '2026-02-28 19:46:33', 'active', 1, 'Savonlinna', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-02-27 18:09:05', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(245, 24, 1, 'Metsäpalsta Kuhmo', 'Metsäpalsta n. 6,2 ha. Hyvät metsäautotiet perille, sekapuustoa ja varttunutta kuusikkoa. Sopii sijoittajalle tai omiin polttopuihin.', 20000.00, 20000.00, NULL, NULL, 250.00, '2026-02-23 19:46:33', '2026-03-05 19:46:33', 'active', 8, 'Kuhmo', 'Tyydyttävä', NULL, '2026-02-23 19:46:33', '2026-04-08 13:29:45', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(246, 22, 2, 'Toyota Corolla 2018', 'Hyvin huollettu Toyota Corolla 2018, ajettu 85 000 km. 1.6 bensiinimoottori, automaattivaihteisto. Katsastettu äskettäin, seuraava katsastus 2026. Varusteet: ilmastointi, cruise control, peruutuskamera.', 12000.00, 15400.00, 14000.00, 16500.00, 200.00, '2026-02-23 19:46:33', '2026-02-27 19:46:33', 'active', 3, 'Helsinki', 'Hyvä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Toyota Corolla\"},{\"label\":\"Rekisteritunnus\",\"value\":\"ABC-123\"},{\"label\":\"Valmistenumero\",\"value\":\"JTDBU4EE2B9123456\"},{\"label\":\"Vuosimalli\",\"value\":\"2018\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"3/2018\"},{\"label\":\"Mittarilukema\",\"value\":\"85 000 km\"},{\"label\":\"Moottori\",\"value\":\"1.6l Bensiini, 97 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Etuveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Automaatti\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:33', '2026-04-03 21:53:34', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(247, 23, 2, 'Yamaha MT-07 Moottoripyörä', 'Yamaha MT-07 2019, ajettu 18 000 km. 689 cc kaksisylinterinen moottori. Erinomaisessa kunnossa, aina säilytetty sisätiloissa. Huoltokirja ja kaikki alkuperäiset paperit mukana.', 5500.00, 6500.00, NULL, 7500.00, 100.00, '2026-02-23 19:46:33', '2026-02-25 19:46:33', 'active', 20, 'Tampere', 'Erinomainen', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Yamaha MT-07\"},{\"label\":\"Rekisteritunnus\",\"value\":\"MP-789\"},{\"label\":\"Valmistenumero\",\"value\":\"JYARM19E2KA012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2019\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"5/2019\"},{\"label\":\"Mittarilukema\",\"value\":\"18 000 km\"},{\"label\":\"Moottori\",\"value\":\"0.7l Bensiini, 54 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:33', '2026-02-24 00:02:03', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(248, 24, 2, 'Volkswagen Transporter Pakettiauto', 'VW Transporter 2015, 2.0 TDI diesel. Ajettu 185 000 km. Hyötyajoneuvona käytetty. Iso tavaratila, hyllyt ja jakajat mukana. Luotettava ja taloudellinen.', 15000.00, 15800.00, NULL, NULL, 200.00, '2026-02-23 19:46:33', '2026-03-01 19:46:33', 'active', 2, 'Turku', 'Tyydyttävä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Volkswagen Transporter T6\"},{\"label\":\"Rekisteritunnus\",\"value\":\"TUR-456\"},{\"label\":\"Valmistenumero\",\"value\":\"WV1ZZZ7HZFH012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2015\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"1/2015\"},{\"label\":\"Mittarilukema\",\"value\":\"185 000 km\"},{\"label\":\"Moottori\",\"value\":\"2.0l Diesel, 103 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:46:33', '2026-03-01 09:41:54', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(249, 22, 3, 'iPhone 14 Pro 256GB', 'Apple iPhone 14 Pro, 256GB, Space Black. Ostettu vuosi sitten, käytetty vain vähän. Alkuperäinen pakkaus ja tarvikkeet mukana. Ei naarmuja tai kolhuja. Akun kapasiteetti 96%.', 800.00, 1325.00, 900.00, 1100.00, 25.00, '2026-02-23 19:46:33', '2026-02-24 19:46:33', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-02-23 22:44:24', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(250, 23, 3, 'Samsung 65\" 4K Smart TV', 'Samsung 65 tuuman 4K Smart TV, malli QN65Q80A. QLED-teknologia, HDR10+. Ostettu 2022. Toimii moitteetta, upea kuvanlaatu. Mukana kaukosäädin ja kaapelit.', 600.00, 650.00, NULL, 900.00, 25.00, '2026-02-23 19:46:33', '2026-02-26 19:46:33', 'active', 0, 'Espoo', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-02-23 19:46:33', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(251, 24, 3, 'Dell XPS 15 Kannettava', 'Dell XPS 15 kannettava tietokone. Intel i7 11. sukupolvi, 16GB RAM, 512GB SSD, NVIDIA GTX 1650 Ti näytönohjain. 15.6\" 4K näyttö. Hieno kone luovaan työhön ja pelaamiseen.', 900.00, 950.00, NULL, 1300.00, 50.00, '2026-02-23 19:46:33', '2026-02-27 19:46:33', 'active', 5, 'Oulu', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-03-19 05:16:23', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(252, 22, 4, 'Design Sohva 3-istuttava', 'Moderni design-sohva, 3-istuttava. Harmaa kangasverhoilu. Pituus 220 cm. Mukava istua ja upea ilme olohuoneeseen. Ei lemmikkejä tai tupakointia.', 300.00, 380.00, NULL, 600.00, 20.00, '2026-02-23 19:46:33', '2026-02-25 19:46:33', 'active', 2, 'Helsinki', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-03-13 23:52:47', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(253, 23, 4, 'Antiikki Ruokapöytä Tammi', 'Kaunis antiikki ruokapöytä massiivitammea. 1950-luvulta. Patinoitunut pinta, muutama käytön jälki mutta kokonaisuus hieno. 6 tuolia mukana.', 400.00, 450.00, NULL, NULL, 25.00, '2026-02-23 19:46:33', '2026-02-28 19:46:33', 'active', 2, 'Turku', 'Tyydyttävä', NULL, '2026-02-23 19:46:33', '2026-03-06 13:36:42', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(254, 24, 4, 'Miele Astianpesukone', 'Miele astianpesukone, malli G 7310 SC. Ostettu 2020. Hiljainen, energiatehokas. Laaja ohjelmavalikoima. Toimii täydellisesti.', 400.00, 400.00, NULL, 700.00, 25.00, '2026-02-23 19:46:33', '2026-02-26 19:46:33', 'active', 1, 'Vantaa', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-03-17 14:45:27', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(255, 22, 5, 'Maastopyörä Giant Trance 2023', 'Giant Trance X 29er maastopyörä, malli 2023. Full suspension, 12-vaihteinen. Ajettu n. 500 km. Huollettu säännöllisesti. Koko L.', 1500.00, 2150.00, NULL, 2500.00, 50.00, '2026-02-23 19:46:33', '2026-02-27 19:46:33', 'active', 2, 'Lahti', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-03-13 17:45:27', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(256, 23, 5, 'Suksisetti + Monot', 'Maastohiihtosukset Fischer Speedmax Skating Plus, pituus 186 cm. Salomon monot koko 44. Käytetty vain yhden kauden. Hyväkuntoiset.', 200.00, 240.00, NULL, 350.00, 10.00, '2026-02-23 19:46:33', '2026-02-25 19:46:33', 'active', 1, 'Rovaniemi', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-02-23 22:45:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(257, 24, 5, 'Kuntosali Penkkipunnerrus', 'Säädettävä penkkipunnerruspenkki + 100kg levypainosetti. Toimii täydellisesti. Hieno lisä kotikuntosalille.', 250.00, 265.00, NULL, NULL, 15.00, '2026-02-23 19:46:33', '2026-03-01 19:46:33', 'active', 5, 'Jyväskylä', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-03-20 02:28:24', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(258, 22, 6, 'Canada Goose Talvitakki', 'Canada Goose Expedition Parka, koko M. Musta. Ostettu 2021. Lämpöinen ja laadukas talvitakki. Turkis irrotettavissa. Pesty ja huollettu ammattilaisella.', 400.00, 575.00, NULL, 700.00, 25.00, '2026-02-23 19:46:33', '2026-02-26 19:46:33', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-03-14 04:18:44', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(259, 23, 6, 'Nike Air Max Tennarit', 'Nike Air Max 90, koko 42. Valkoinen/punainen väritys. Käytetty muutaman kerran. Mukana alkuperäinen laatikko.', 60.00, 105.00, NULL, 120.00, 5.00, '2026-02-23 19:46:33', '2026-02-24 19:46:33', 'active', 1, 'Tampere', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-02-23 22:33:40', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(260, 24, 6, 'Vintage Farkkutakki Levi\'s', 'Vintage Levi\'s farkkutakki 1980-luvulta. Koko M. Hieno retro-ilme. Muutama pieni kuluma, lisää tunnelmaa.', 50.00, 50.00, NULL, NULL, 5.00, '2026-02-23 19:46:33', '2026-02-28 19:46:33', 'active', 1, 'Turku', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-02-27 18:22:18', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(261, 22, 7, 'Vanha Rolex Kello 1970-luku', 'Alkuperäinen Rolex Datejust 1970-luvulta. Automaattinen. Toimii moitteetta. Patina ja käytön jälkiä, mutta juuri se tekee kellosta aidon. Keräilykappale.', 2000.00, 3900.00, 3000.00, NULL, 100.00, '2026-02-23 19:46:33', '2026-03-02 19:46:33', 'active', 3, 'Helsinki', 'Tyydyttävä', NULL, '2026-02-23 19:46:33', '2026-03-01 04:07:26', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(262, 23, 7, 'Pokemon Korttipakka 1. Painos', 'Alkuperäinen Pokemon Base Set booster box, sinetöity. 1. painos 1999. Erittäin harvinainen ja arvostettu keräilykohde. Täydellinen kunto.', 5000.00, 7800.00, NULL, 8000.00, 200.00, '2026-02-23 19:46:33', '2026-02-27 19:46:33', 'active', 2, 'Espoo', 'Erinomainen', NULL, '2026-02-23 19:46:33', '2026-03-16 19:46:34', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(263, 24, 7, 'Antiikki Samovaari', 'Venäläinen antiikki samovaari 1800-luvun lopulta. Messinkiä. Kauniisti koristeltu. Harvinainen kappale. Upea sisustuselementti tai keräilykohde.', 400.00, 475.00, NULL, NULL, 25.00, '2026-02-23 19:46:33', '2026-03-01 19:46:33', 'active', 2, 'Tampere', 'Hyvä', NULL, '2026-02-23 19:46:33', '2026-03-01 04:05:35', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(264, 22, 1, 'Omakotitalotontti Espoo', 'Kaunis 1500 m² tontti rauhallisella alueella Espoossa. Tontti on rakennuskelpoinen ja sille on myönnetty rakennuslupa. Sijainti lähellä palveluita ja hyvät liikenneyhteydet.', 50000.00, 56000.00, 80000.00, 120000.00, 1000.00, '2026-02-23 19:52:46', '2026-02-26 19:52:46', 'active', 2, 'Espoo', 'Uusi', NULL, '2026-02-23 19:52:46', '2026-03-21 15:55:11', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(265, 23, 1, 'Mökki Järvenrannalla', '45 m² hirsimökki idyllisellä järvenrannalla Savossa. Sauna, ruokailutila, makuuhuone ja keittiö. Ranta-alue n. 25 metriä. Hyvät kalastusmahdollisuudet.', 35000.00, 39000.00, NULL, 65000.00, 500.00, '2026-02-23 19:52:46', '2026-02-28 19:52:46', 'active', 3, 'Savonlinna', 'Hyvä', NULL, '2026-02-23 19:52:46', '2026-02-27 16:38:58', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(266, 24, 1, 'Metsäpalsta Kuhmo', 'Metsäpalsta n. 6,2 ha. Hyvät metsäautotiet perille, sekapuustoa ja varttunutta kuusikkoa. Sopii sijoittajalle tai omiin polttopuihin.', 20000.00, 20000.00, NULL, NULL, 250.00, '2026-02-23 19:52:46', '2026-03-05 19:52:46', 'active', 11, 'Kuhmo', 'Tyydyttävä', NULL, '2026-02-23 19:52:46', '2026-04-09 03:03:27', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(267, 22, 2, 'Toyota Corolla 2018', 'Hyvin huollettu Toyota Corolla 2018, ajettu 85 000 km. 1.6 bensiinimoottori, automaattivaihteisto. Katsastettu äskettäin, seuraava katsastus 2026. Varusteet: ilmastointi, cruise control, peruutuskamera.', 12000.00, 15400.00, 14000.00, 16500.00, 200.00, '2026-02-23 19:52:46', '2026-02-27 19:52:46', 'active', 3, 'Helsinki', 'Hyvä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Toyota Corolla\"},{\"label\":\"Rekisteritunnus\",\"value\":\"ABC-123\"},{\"label\":\"Valmistenumero\",\"value\":\"JTDBU4EE2B9123456\"},{\"label\":\"Vuosimalli\",\"value\":\"2018\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"3/2018\"},{\"label\":\"Mittarilukema\",\"value\":\"85 000 km\"},{\"label\":\"Moottori\",\"value\":\"1.6l Bensiini, 97 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Etuveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Automaatti\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:52:46', '2026-02-27 18:59:12', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(268, 23, 2, 'Yamaha MT-07 Moottoripyörä', 'Yamaha MT-07 2019, ajettu 18 000 km. 689 cc kaksisylinterinen moottori. Erinomaisessa kunnossa, aina säilytetty sisätiloissa. Huoltokirja ja kaikki alkuperäiset paperit mukana.', 5500.00, 6500.00, NULL, 7500.00, 100.00, '2026-02-23 19:52:46', '2026-02-25 19:52:46', 'active', 1, 'Tampere', 'Erinomainen', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Yamaha MT-07\"},{\"label\":\"Rekisteritunnus\",\"value\":\"MP-789\"},{\"label\":\"Valmistenumero\",\"value\":\"JYARM19E2KA012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2019\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"5/2019\"},{\"label\":\"Mittarilukema\",\"value\":\"18 000 km\"},{\"label\":\"Moottori\",\"value\":\"0.7l Bensiini, 54 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:52:46', '2026-02-23 22:34:42', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(269, 24, 2, 'Volkswagen Transporter Pakettiauto', 'VW Transporter 2015, 2.0 TDI diesel. Ajettu 185 000 km. Hyötyajoneuvona käytetty. Iso tavaratila, hyllyt ja jakajat mukana. Luotettava ja taloudellinen.', 15000.00, 15800.00, NULL, NULL, 200.00, '2026-02-23 19:52:46', '2026-03-01 19:52:46', 'active', 2, 'Turku', 'Tyydyttävä', '{\"fields\":[{\"label\":\"Malli\",\"value\":\"Volkswagen Transporter T6\"},{\"label\":\"Rekisteritunnus\",\"value\":\"TUR-456\"},{\"label\":\"Valmistenumero\",\"value\":\"WV1ZZZ7HZFH012345\"},{\"label\":\"Vuosimalli\",\"value\":\"2015\"},{\"label\":\"Käyttöönottokuukausi\",\"value\":\"1/2015\"},{\"label\":\"Mittarilukema\",\"value\":\"185 000 km\"},{\"label\":\"Moottori\",\"value\":\"2.0l Diesel, 103 kW\"},{\"label\":\"Vetotapa\",\"value\":\"Takaveto\"},{\"label\":\"Vaihteisto\",\"value\":\"Manuaali 6-vaihteinen\"},{\"label\":\"Päästömuunneltu\",\"value\":\"Ei\"}]}', '2026-02-23 19:52:46', '2026-03-01 09:41:50', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(270, 22, 3, 'iPhone 14 Pro 256GB', 'Apple iPhone 14 Pro, 256GB, Space Black. Ostettu vuosi sitten, käytetty vain vähän. Alkuperäinen pakkaus ja tarvikkeet mukana. Ei naarmuja tai kolhuja. Akun kapasiteetti 96%.', 800.00, 1325.00, 900.00, 1100.00, 25.00, '2026-02-23 19:52:46', '2026-02-24 19:52:46', 'active', 1, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-02-24 18:19:06', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL);
INSERT INTO `auctions` (`id`, `user_id`, `category_id`, `title`, `description`, `starting_price`, `current_price`, `reserve_price`, `buy_now_price`, `bid_increment`, `start_time`, `end_time`, `status`, `views`, `location`, `condition_description`, `ai_details`, `created_at`, `updated_at`, `map_coordinates`, `seller_commitment`, `short_summary`, `featured`, `condition_grade`, `seller_notes`, `pickup_info`, `shipping_info`, `payment_info`, `inspection_info`, `included_items`, `defects`, `warranty_info`, `model_reference`, `serial_number`, `delivery_available`, `pickup_available`, `payment_deadline_days`, `storage_fee_info`) VALUES
(271, 23, 3, 'Samsung 65\" 4K Smart TV', 'Samsung 65 tuuman 4K Smart TV, malli QN65Q80A. QLED-teknologia, HDR10+. Ostettu 2022. Toimii moitteetta, upea kuvanlaatu. Mukana kaukosäädin ja kaapelit.', 600.00, 650.00, NULL, 900.00, 25.00, '2026-02-23 19:52:46', '2026-02-26 19:52:46', 'active', 1, 'Espoo', 'Hyvä', NULL, '2026-02-23 19:52:46', '2026-03-17 01:49:06', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(272, 24, 3, 'Dell XPS 15 Kannettava', 'Dell XPS 15 kannettava tietokone. Intel i7 11. sukupolvi, 16GB RAM, 512GB SSD, NVIDIA GTX 1650 Ti näytönohjain. 15.6\" 4K näyttö. Hieno kone luovaan työhön ja pelaamiseen.', 900.00, 950.00, NULL, 1300.00, 50.00, '2026-02-23 19:52:46', '2026-02-27 19:52:46', 'active', 2, 'Oulu', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-03-14 21:59:38', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(273, 22, 4, 'Design Sohva 3-istuttava', 'Moderni design-sohva, 3-istuttava. Harmaa kangasverhoilu. Pituus 220 cm. Mukava istua ja upea ilme olohuoneeseen. Ei lemmikkejä tai tupakointia.', 300.00, 380.00, NULL, 600.00, 20.00, '2026-02-23 19:52:46', '2026-02-25 19:52:46', 'active', 2, 'Helsinki', 'Hyvä', NULL, '2026-02-23 19:52:46', '2026-03-13 23:52:16', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(274, 23, 4, 'Antiikki Ruokapöytä Tammi', 'Kaunis antiikki ruokapöytä massiivitammea. 1950-luvulta. Patinoitunut pinta, muutama käytön jälki mutta kokonaisuus hieno. 6 tuolia mukana.', 400.00, 450.00, NULL, NULL, 25.00, '2026-02-23 19:52:46', '2026-02-28 19:52:46', 'active', 4, 'Turku', 'Tyydyttävä', NULL, '2026-02-23 19:52:46', '2026-03-15 14:08:21', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(275, 24, 4, 'Miele Astianpesukone', 'Miele astianpesukone, malli G 7310 SC. Ostettu 2020. Hiljainen, energiatehokas. Laaja ohjelmavalikoima. Toimii täydellisesti.', 400.00, 400.00, NULL, 700.00, 25.00, '2026-02-23 19:52:46', '2026-02-26 19:52:46', 'active', 1, 'Vantaa', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-03-17 03:54:18', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(276, 22, 5, 'Maastopyörä Giant Trance 2023', 'Giant Trance X 29er maastopyörä, malli 2023. Full suspension, 12-vaihteinen. Ajettu n. 500 km. Huollettu säännöllisesti. Koko L.', 1500.00, 2150.00, NULL, 2500.00, 50.00, '2026-02-23 19:52:46', '2026-02-27 19:52:46', 'active', 2, 'Lahti', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-03-15 11:47:03', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(277, 23, 5, 'Suksisetti + Monot', 'Maastohiihtosukset Fischer Speedmax Skating Plus, pituus 186 cm. Salomon monot koko 44. Käytetty vain yhden kauden. Hyväkuntoiset.', 200.00, 240.00, NULL, 350.00, 10.00, '2026-02-23 19:52:46', '2026-02-25 19:52:46', 'active', 2, 'Rovaniemi', 'Hyvä', NULL, '2026-02-23 19:52:46', '2026-02-24 04:01:01', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(278, 24, 5, 'Kuntosali Penkkipunnerrus', 'Säädettävä penkkipunnerruspenkki + 100kg levypainosetti. Toimii täydellisesti. Hieno lisä kotikuntosalille.', 250.00, 265.00, NULL, NULL, 15.00, '2026-02-23 19:52:46', '2026-03-01 19:52:46', 'active', 7, 'Jyväskylä', 'Hyvä', NULL, '2026-02-23 19:52:46', '2026-03-20 02:28:15', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(279, 22, 6, 'Canada Goose Talvitakki', 'Canada Goose Expedition Parka, koko M. Musta. Ostettu 2021. Lämpöinen ja laadukas talvitakki. Turkis irrotettavissa. Pesty ja huollettu ammattilaisella.', 400.00, 575.00, NULL, 700.00, 25.00, '2026-02-23 19:52:46', '2026-02-26 19:52:46', 'active', 2, 'Helsinki', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-04-01 07:30:07', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(280, 23, 6, 'Nike Air Max Tennarit', 'Nike Air Max 90, koko 42. Valkoinen/punainen väritys. Käytetty muutaman kerran. Mukana alkuperäinen laatikko.', 60.00, 105.00, NULL, 120.00, 5.00, '2026-02-23 19:52:46', '2026-02-24 19:52:46', 'active', 1, 'Tampere', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-02-24 07:59:10', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(281, 24, 6, 'Vintage Farkkutakki Levi\'s', 'Vintage Levi\'s farkkutakki 1980-luvulta. Koko M. Hieno retro-ilme. Muutama pieni kuluma, lisää tunnelmaa.', 50.00, 50.00, NULL, NULL, 5.00, '2026-02-23 19:52:46', '2026-02-28 19:52:46', 'active', 1, 'Turku', 'Hyvä', NULL, '2026-02-23 19:52:46', '2026-02-27 16:14:07', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(282, 22, 7, 'Vanha Rolex Kello 1970-luku', 'Alkuperäinen Rolex Datejust 1970-luvulta. Automaattinen. Toimii moitteetta. Patina ja käytön jälkiä, mutta juuri se tekee kellosta aidon. Keräilykappale.', 2000.00, 3900.00, 3000.00, NULL, 1.00, '2026-02-23 19:52:46', '2026-03-02 19:52:46', 'active', 9, 'Helsinki', 'Tyydyttävä', NULL, '2026-02-23 19:52:46', '2026-04-06 05:54:51', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(283, 23, 7, 'Pokemon Korttipakka 1. Painos', 'Alkuperäinen Pokemon Base Set booster box, sinetöity. 1. painos 1999. Erittäin harvinainen ja arvostettu keräilykohde. Täydellinen kunto.', 5000.00, 7800.00, NULL, 8000.00, 200.00, '2026-02-23 19:52:46', '2026-02-27 19:52:46', 'active', 25, 'Espoo', 'Erinomainen', NULL, '2026-02-23 19:52:46', '2026-03-15 19:42:17', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(286, 23, 2, 'Talvirenkaat neljä kappaletta', 'Myynnissä neljä käytettyä talvirengasta. Renkaita on käytetty, mutta niiden urasyvyys on vielä riittävä talvikäyttöön. Renkaita on säilytetty kuivassa paikassa, ja ne ovat hyvässä kunnossa. Sopivat useimpiin henkilöautoihin. Tarkista aina renkaiden yhteensopivuus ajoneuvosi kanssa.', 120.00, 120.00, 200.00, 220.00, 1.00, '2026-04-06 20:59:52', '2026-04-14 21:00:04', 'active', 10, 'Helsinki', 'Käytetty, mutta hyvässä kunnossa', NULL, '2026-04-06 20:59:52', '2026-04-10 02:21:00', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(287, 23, 3, 'Inogen Home Happilaites', 'Inogen Home on kotikäyttöön suunniteltu happilaitteisto, joka tarjoaa lisähappea käyttäjille. Laite on varustettu selkeillä ohjauspainikkeilla ja LED-näytöllä, joka näyttää laitteiston toimintatilan. Se on suunniteltu helpottamaan hengittämistä erityisesti henkilöille, joilla on hengitysvaikeuksia. Laitteen kompakti muotoilu mahdollistaa sen sijoittamisen eri ympäristöihin.', 300.00, 300.00, 600.00, 800.00, 1.00, '2026-04-05 21:13:19', '2026-04-14 21:13:20', 'active', 9, 'Helsinki', 'Hyvä, käytetty mutta toimintakuntoinen', NULL, '2026-04-05 21:13:19', '2026-04-10 03:14:06', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL),
(288, 23, 11, 'Traktori', 'Priima Traktori', 50000.00, 50000.00, 55000.00, 53000.00, 1.00, '2026-04-06 16:08:04', '2026-04-15 16:08:23', 'active', 20, 'Helsinki', 'vimosen piäl', NULL, '2026-04-06 16:08:04', '2026-04-10 03:06:09', NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Rakenne taululle `auction_images`
--

CREATE TABLE `auction_images` (
  `id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `auction_images`
--

INSERT INTO `auction_images` (`id`, `auction_id`, `image_path`, `caption`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 76, '/uploads/6991cffa5de4a_1f3eb519-654a-4b10-9219-22e03de46a57.jpg', NULL, 0, 0, '2026-02-15 13:54:02'),
(3, 76, '/uploads/6991cffa5ead6_5be8435f-07ab-40fb-9a00-c8547d7a72aa.jpg', NULL, 1, 2, '2026-02-15 13:54:02'),
(4, 76, '/uploads/6991cffa5ef77_6fd85647-1f45-42a5-bd14-55d78115689f.jpg', NULL, 0, 3, '2026-02-15 13:54:02'),
(5, 76, '/uploads/6991cffa5f3f9_045cc395-9e30-4aa7-b73e-070b4ec68a33.jpg', NULL, 0, 4, '2026-02-15 13:54:02'),
(8, 78, '/uploads/6995cc963b6be_WhatsApp Image 2026-02-13 at 14.02.24.jpeg', NULL, 1, 0, '2026-02-18 14:28:38'),
(10, 80, '/uploads/6997688d7cff7_6fd85647-1f45-42a5-bd14-55d78115689f.jpg', NULL, 1, 0, '2026-02-19 19:46:21'),
(11, 81, '/uploads/699768f5b8077_5be8435f-07ab-40fb-9a00-c8547d7a72aa.jpg', NULL, 1, 0, '2026-02-19 19:48:05'),
(12, 82, '/uploads/6997a7c86e00a_lieb1.jpg', NULL, 1, 0, '2026-02-20 00:16:08'),
(13, 83, '/uploads/69983741f2c29_1f3eb519-654a-4b10-9219-22e03de46a57_wm.jpg', NULL, 0, 0, '2026-02-20 10:28:18'),
(14, 83, '/uploads/69983741f300b_4caaaaaa-297a-4168-9512-a93e0d40b55b_wm.jpg', NULL, 0, 1, '2026-02-20 10:28:18'),
(15, 83, '/uploads/69983741f3313_5be8435f-07ab-40fb-9a00-c8547d7a72aa_wm.jpg', NULL, 1, 2, '2026-02-20 10:28:18'),
(16, 84, '/uploads/699837b2a305c_WhatsApp Image 2026-02-12 at 00.53.10 (8)_wm.jpeg', NULL, 1, 0, '2026-02-20 10:30:10'),
(17, 84, '/uploads/699837b2a36ce_WhatsApp Image 2026-02-12 at 00.53.10 (9)_wm.jpeg', NULL, 0, 1, '2026-02-20 10:30:10'),
(18, 84, '/uploads/699837b2a3bcd_WhatsApp Image 2026-02-12 at 00.53.10_wm.jpeg', NULL, 0, 2, '2026-02-20 10:30:10'),
(19, 85, '/uploads/auctions/6998c0f66b4718.66963992_4345454545_wm.png', NULL, 1, 0, '2026-02-20 20:15:50'),
(20, 86, '/uploads/auctions/6998e7d9acd3a4.83865519_513488600_1258020112778868_7262444915472430383_n_wm.jpg', 'mansikat', 1, 0, '2026-02-20 23:01:45'),
(21, 87, '/uploads/auctions/699945bd8feed5.44767143_Capture_wm.jpg', NULL, 1, 0, '2026-02-21 05:42:21'),
(24, 88, '/uploads/auctions/69995910eef308.41014080_00i0i_90qXHucxf2L_0MM132_600x450_wm.jpg', NULL, 1, 0, '2026-02-21 07:04:48'),
(25, 89, '/uploads/auctions/6999657664a603.02759332_whatsapp_flash_snow_garage_wm.jpg', NULL, 0, 0, '2026-02-21 07:57:42'),
(28, 7, '/uploads/auctions/7/1771663361_5d17d25bb26856ad_wm.jpg', NULL, 1, 1, '2026-02-21 08:42:41'),
(29, 22, '/uploads/auctions/22/1771663507_34dd97d6885462bf_wm.jpg', NULL, 1, 1, '2026-02-21 08:45:07'),
(30, 36, '/uploads/auctions/36/1771663618_585cf862e2fe3300_wm.jpg', NULL, 1, 1, '2026-02-21 08:46:58'),
(31, 77, '/uploads/auctions/77/1771666622_68c2d4dfc233aaf3_wm.jpg', NULL, 0, 4, '2026-02-21 09:37:02'),
(33, 77, '/uploads/auctions/77/1771666655_cd383745b05775f4_wm.jpg', NULL, 1, 6, '2026-02-21 09:37:35'),
(34, 59, '/uploads/auctions/59/1771667046_24a911cf2713c39e_wm.jpg', NULL, 1, 2, '2026-02-21 09:44:06'),
(35, 63, '/uploads/auctions/63/1771667136_3efdbb84ab52a513_wm.jpg', NULL, 1, 1, '2026-02-21 09:45:37'),
(36, 90, '/uploads/auctions/6999818f718c99.95896076_10d60a49-7739-4409-b3cc-c997ffaa3ab2_wm.jpg', 'Edestä', 1, 0, '2026-02-21 09:57:35'),
(37, 91, '/uploads/auctions/699985c1832fe5.87576717_Screenshot_2026-02-21_121415_wm.png', NULL, 1, 0, '2026-02-21 10:15:30'),
(38, 92, '/uploads/auctions/69998bd986f667.39943272_WhatsApp_Image_2026-02-21_at_12.23.06_wm.jpeg', NULL, 1, 0, '2026-02-21 10:41:29'),
(39, 93, '/uploads/auctions/69998c249d5111.17491346_WhatsApp_Image_2026-02-21_at_12.23.05_wm.jpeg', 'työkalulaatikko', 1, 0, '2026-02-21 10:42:44'),
(40, 94, '/uploads/auctions/69998e79d90cd6.45653311_Heavy-Truck-Weichai-Engine-Garrett-Gt45-Turbocharger-for-Wd618-42q-612601110925-_wm.webp', NULL, 1, 0, '2026-02-21 10:52:42'),
(41, 89, '/uploads/auctions/89/1771672461_9c471a651e2673e7_wm.jpg', NULL, 1, 1, '2026-02-21 11:14:21'),
(42, 75, '/uploads/auctions/75/1771672527_89ba375efc5dbe7d_wm.jpg', NULL, 1, 1, '2026-02-21 11:15:27'),
(43, 79, '/uploads/auctions/79/1771672627_1d3be0f723846e76_wm.jpg', NULL, 1, 1, '2026-02-21 11:17:07'),
(44, 95, '/uploads/auctions/6999ac5b4b2df3.56835837_WhatsApp_Image_2026-02-21_at_13.24.55_wm.jpeg', NULL, 1, 0, '2026-02-21 13:00:13'),
(45, 95, '/uploads/auctions/6999ac5c2a04a5.70331570_WhatsApp_Image_2026-02-21_at_13.24.55_wm.jpeg', NULL, 0, 1, '2026-02-21 13:00:14'),
(46, 95, '/uploads/auctions/6999ac5c92b216.01517769_WhatsApp_Image_2026-02-21_at_13.24.56__1__wm.jpeg', NULL, 0, 2, '2026-02-21 13:00:14'),
(47, 95, '/uploads/auctions/6999ac5d051696.13643141_WhatsApp_Image_2026-02-21_at_13.24.56__2__wm.jpeg', NULL, 0, 3, '2026-02-21 13:00:14'),
(48, 95, '/uploads/auctions/6999ac5d640a58.04087523_WhatsApp_Image_2026-02-21_at_13.24.56_wm.jpeg', NULL, 0, 4, '2026-02-21 13:00:14'),
(49, 96, '/uploads/auctions/699a541ddb5530.58014791_WhatsApp_Image_2026-02-21_at_12.23.05_wm.jpeg', NULL, 1, 0, '2026-02-22 00:55:58'),
(51, 1, '/uploads/auctions/1/1771876847_9d5cdf132791b52b_wm.jpg', NULL, 1, 1, '2026-02-23 20:00:47'),
(52, 1, '/uploads/auctions/1/1771876850_9321ba9b6b106c20_wm.jpg', NULL, 0, 2, '2026-02-23 20:00:50'),
(53, 1, '/uploads/auctions/1/1771877382_2685e1f2a7739f5a_wm.jpg', NULL, 0, 3, '2026-02-23 20:09:42'),
(54, 1, '/uploads/auctions/1/1771877410_5214e8cbae2271f7_wm.jpg', NULL, 0, 4, '2026-02-23 20:10:10'),
(56, 286, '/uploads/auctions/699ccdd88564f8.14213912_BF6EEABD-CCA6-470A-9261-9D41F06F9858_wm.jpeg', NULL, 1, 0, '2026-02-23 21:59:52'),
(57, 287, '/uploads/auctions/699cd0ff593a72.93836818_Screenshot_2026-02-21_121415_wm.png', 'ylihappikones', 1, 0, '2026-02-23 22:13:19'),
(58, 236, '/uploads/auctions/236/1771884968_e24bcd65783f0597_wm.jpg', NULL, 1, 1, '2026-02-23 22:16:10'),
(59, 288, '/uploads/auctions/69d3da646243c1.25222479_qewrerqwer_wm.jpeg', NULL, 1, 0, '2026-04-06 16:08:04');

-- --------------------------------------------------------

--
-- Rakenne taululle `auction_metadata`
--

CREATE TABLE `auction_metadata` (
  `id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `auction_metadata`
--

INSERT INTO `auction_metadata` (`id`, `auction_id`, `field_name`, `field_value`, `created_at`, `updated_at`) VALUES
(1, 85, 'vehicle_brand', 'Tuntematon', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(2, 85, 'vehicle_model', 'Tuntematon', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(3, 85, 'engine', 'Tuntematon', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(4, 85, 'vehicle_defects', 'Ei', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(5, 86, 'general_type', 'Tuoreita mansikoita', '2026-02-20 23:01:45', '2026-02-20 23:01:45'),
(6, 86, 'weight', '5', '2026-02-20 23:01:45', '2026-02-20 23:01:45'),
(7, 86, 'general_dimensions', '30x40x20 cm', '2026-02-20 23:01:45', '2026-02-20 23:01:45'),
(8, 86, 'country_origin', 'Suomi', '2026-02-20 23:01:45', '2026-02-20 23:01:45'),
(9, 88, 'vehicle_brand', 'Tuntematon', '2026-02-21 07:04:48', '2026-02-21 07:04:48'),
(10, 88, 'vehicle_model', 'Tuntematon', '2026-02-21 07:04:48', '2026-02-21 07:04:48'),
(11, 88, 'engine', 'Tuntematon', '2026-02-21 07:04:48', '2026-02-21 07:04:48'),
(12, 88, 'vehicle_defects', 'Ei', '2026-02-21 07:04:48', '2026-02-21 07:04:48'),
(13, 89, 'vehicle_brand', 'Tuntematon', '2026-02-21 07:57:42', '2026-02-21 07:57:42'),
(14, 89, 'vehicle_model', 'Tuntematon', '2026-02-21 07:57:42', '2026-02-21 07:57:42'),
(15, 89, 'engine', 'Tuntematon', '2026-02-21 07:57:42', '2026-02-21 07:57:42'),
(16, 89, 'vehicle_defects', 'Ei', '2026-02-21 07:57:42', '2026-02-21 07:57:42'),
(17, 89, 'detailed_address', 'Keskuskatu 12, 00100 Helsinki', '2026-02-21 07:57:42', '2026-02-21 07:57:42'),
(18, 90, 'vehicle_brand', 'Ford', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(19, 90, 'vehicle_model', '5700', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(20, 90, 'mileage', '17500', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(21, 90, 'engine', 'hyvä', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(22, 90, 'fuel_type', 'diesel', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(23, 90, 'service_book', '1', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(24, 90, 'traffic_insurance', '1', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(25, 90, 'key_count', '1', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(26, 90, 'vehicle_defects', 'normaaleja käytön jälkiä', '2026-02-21 09:57:35', '2026-02-21 09:57:35'),
(27, 91, 'electronics_brand', 'Inogen', '2026-02-21 10:15:30', '2026-02-21 10:15:30'),
(28, 91, 'electronics_model', 'One G3', '2026-02-21 10:15:30', '2026-02-21 10:15:30'),
(29, 91, 'capacity', 'ei saatavilla', '2026-02-21 10:15:30', '2026-02-21 10:15:30'),
(30, 91, 'charger_included', '1', '2026-02-21 10:15:30', '2026-02-21 10:15:30'),
(31, 91, 'detailed_address', 'Ylistaro', '2026-02-21 10:15:30', '2026-02-21 10:15:30'),
(32, 93, 'detailed_address', 'Ylistarontie 17, Seinäjoki', '2026-02-21 10:42:44', '2026-02-21 10:42:44'),
(33, 94, 'vehicle_brand', 'Tuntematon', '2026-02-21 10:52:42', '2026-02-21 10:52:42'),
(34, 94, 'vehicle_model', 'Tuntematon', '2026-02-21 10:52:42', '2026-02-21 10:52:42'),
(35, 94, 'engine', 'Turboahdin', '2026-02-21 10:52:42', '2026-02-21 10:52:42'),
(36, 94, 'vehicle_defects', 'Ei', '2026-02-21 10:52:42', '2026-02-21 10:52:42'),
(37, 95, 'vehicle_brand', 'Hofmann', '2026-02-21 13:00:14', '2026-02-21 13:00:14'),
(38, 95, 'vehicle_model', 'Monty 3300', '2026-02-21 13:00:14', '2026-02-21 13:00:14'),
(39, 95, 'engine', 'ei saatavilla', '2026-02-21 13:00:14', '2026-02-21 13:00:14'),
(40, 95, 'vehicle_defects', 'Käytössä ollut, ei kiillotettu, mutta käyttövalmis', '2026-02-21 13:00:14', '2026-02-21 13:00:14'),
(41, 96, 'home_item_type', 'huonekalu', '2026-02-22 00:55:58', '2026-02-22 00:55:58'),
(42, 96, 'material', 'Metalli', '2026-02-22 00:55:58', '2026-02-22 00:55:58'),
(43, 96, 'dimensions', '50cm x 1 metri x 80cm', '2026-02-22 00:55:58', '2026-02-22 00:55:58'),
(44, 286, 'vehicle_brand', 'Tuntematon', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(45, 286, 'vehicle_model', 'Tuntematon', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(46, 286, 'engine', 'Tuntematon', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(47, 286, 'traffic_insurance', '1', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(48, 286, 'next_inspection', '2026-07', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(49, 286, 'key_count', '4', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(50, 286, 'vehicle_defects', 'Ei huomautuksia', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(51, 286, 'detailed_address', 'Ylistarontie 74', '2026-02-23 21:59:52', '2026-02-23 21:59:52'),
(52, 287, 'electronics_brand', 'Inogen', '2026-02-23 22:13:19', '2026-02-23 22:13:19'),
(53, 287, 'electronics_model', 'Home', '2026-02-23 22:13:19', '2026-02-23 22:13:19'),
(54, 287, 'capacity', 'Ei sovellettavissa', '2026-02-23 22:13:19', '2026-02-23 22:13:19'),
(55, 287, 'warranty_until', '2028-06-07', '2026-02-23 22:13:19', '2026-02-23 22:13:19'),
(56, 287, 'original_box', '1', '2026-02-23 22:13:19', '2026-02-23 22:13:19'),
(57, 287, 'location_notes', 'Etelä-haaga', '2026-02-23 22:13:19', '2026-02-23 22:13:19'),
(58, 236, 'sport_type', 'Voimaharjoittelu', '2026-02-23 22:16:20', '2026-02-23 22:16:20'),
(59, 236, 'size', 'Säädettävä', '2026-02-23 22:16:20', '2026-02-23 22:16:20'),
(60, 236, 'sports_brand', 'Davi', '2026-02-23 22:16:20', '2026-02-23 22:16:20'),
(61, 236, 'usage_frequency', 'Kohtuullisesti', '2026-02-23 22:16:20', '2026-02-23 22:16:20'),
(62, 288, 'detailed_address', 'Persekatu 7', '2026-04-06 16:08:04', '2026-04-06 16:08:04');

-- --------------------------------------------------------

--
-- Rakenne taululle `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `resource_type` varchar(50) NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bid_time` timestamp NULL DEFAULT current_timestamp(),
  `is_auto_bid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `bids`
--

INSERT INTO `bids` (`id`, `auction_id`, `user_id`, `amount`, `bid_time`, `is_auto_bid`) VALUES
(1, 1, 23, 50000.00, '2026-02-15 19:06:07', 0),
(2, 1, 3, 51000.00, '2026-02-16 19:06:07', 0),
(3, 1, 23, 52000.00, '2026-02-16 19:06:07', 1),
(4, 1, 22, 53000.00, '2026-02-16 19:06:07', 1),
(5, 1, 23, 54000.00, '2026-02-19 19:06:07', 0),
(6, 1, 22, 55000.00, '2026-02-20 19:06:07', 1),
(7, 1, 23, 56000.00, '2026-02-22 19:06:07', 0),
(8, 2, 3, 35000.00, '2026-02-13 19:06:07', 0),
(9, 2, 24, 35500.00, '2026-02-14 19:06:07', 0),
(10, 2, 3, 36000.00, '2026-02-14 19:06:07', 1),
(11, 2, 24, 36500.00, '2026-02-14 19:06:07', 1),
(12, 2, 3, 37000.00, '2026-02-17 19:06:07', 0),
(13, 2, 24, 37500.00, '2026-02-18 19:06:07', 1),
(14, 2, 3, 38000.00, '2026-02-18 19:06:07', 1),
(15, 2, 24, 38500.00, '2026-02-21 19:06:07', 0),
(16, 2, 3, 39000.00, '2026-02-23 07:06:07', 0),
(509, 181, 23, 50000.00, '2026-02-15 19:28:17', 0),
(510, 181, 24, 51000.00, '2026-02-16 19:28:17', 0),
(511, 181, 23, 52000.00, '2026-02-16 19:28:17', 1),
(512, 181, 24, 53000.00, '2026-02-16 19:28:17', 1),
(513, 181, 23, 54000.00, '2026-02-19 19:28:17', 0),
(514, 181, 24, 55000.00, '2026-02-20 19:28:17', 1),
(515, 181, 23, 56000.00, '2026-02-22 19:28:17', 0),
(516, 182, 24, 35000.00, '2026-02-13 19:28:17', 0),
(517, 182, 22, 35500.00, '2026-02-14 19:28:17', 0),
(518, 182, 24, 36000.00, '2026-02-14 19:28:17', 1),
(519, 182, 22, 36500.00, '2026-02-14 19:28:17', 1),
(520, 182, 24, 37000.00, '2026-02-17 19:28:17', 0),
(521, 182, 22, 37500.00, '2026-02-18 19:28:17', 1),
(522, 182, 24, 38000.00, '2026-02-18 19:28:17', 1),
(523, 182, 22, 38500.00, '2026-02-21 19:28:17', 0),
(524, 182, 24, 39000.00, '2026-02-23 07:28:17', 0),
(525, 183, 23, 12000.00, '2026-02-11 19:28:17', 0),
(526, 183, 24, 12200.00, '2026-02-12 19:28:17', 0),
(527, 183, 23, 12400.00, '2026-02-12 19:28:17', 1),
(528, 183, 24, 12600.00, '2026-02-12 19:28:17', 1),
(529, 183, 23, 12800.00, '2026-02-14 19:28:17', 0),
(530, 183, 24, 13000.00, '2026-02-15 19:28:17', 0),
(531, 183, 23, 13200.00, '2026-02-16 19:28:17', 1),
(532, 183, 24, 13400.00, '2026-02-16 19:28:17', 1),
(533, 183, 23, 13600.00, '2026-02-17 19:28:17', 0),
(534, 183, 24, 13800.00, '2026-02-18 19:28:17', 0),
(535, 183, 23, 14000.00, '2026-02-19 19:28:17', 1),
(536, 183, 24, 14200.00, '2026-02-19 19:28:17', 1),
(537, 183, 23, 14400.00, '2026-02-20 19:28:17', 0),
(538, 183, 24, 14600.00, '2026-02-21 19:28:17', 1),
(539, 183, 23, 14800.00, '2026-02-21 19:28:17', 1),
(540, 183, 24, 15000.00, '2026-02-22 19:28:17', 0),
(541, 183, 23, 15200.00, '2026-02-23 09:28:17', 1),
(542, 183, 24, 15400.00, '2026-02-23 16:28:17', 0),
(543, 184, 22, 5500.00, '2026-02-16 19:28:17', 0),
(544, 184, 24, 5600.00, '2026-02-17 19:28:17', 0),
(545, 184, 22, 5700.00, '2026-02-17 19:28:17', 1),
(546, 184, 24, 5800.00, '2026-02-17 19:28:17', 1),
(547, 184, 22, 5900.00, '2026-02-18 19:28:17', 0),
(548, 184, 24, 6000.00, '2026-02-19 19:28:17', 0),
(549, 184, 22, 6100.00, '2026-02-20 19:28:17', 1),
(550, 184, 24, 6200.00, '2026-02-20 19:28:17', 1),
(551, 184, 22, 6300.00, '2026-02-21 19:28:17', 0),
(552, 184, 24, 6400.00, '2026-02-23 01:28:17', 0),
(553, 184, 22, 6500.00, '2026-02-23 13:28:17', 0),
(554, 186, 24, 800.00, '2026-02-09 19:28:17', 0),
(555, 186, 23, 825.00, '2026-02-10 19:28:17', 0),
(556, 186, 22, 850.00, '2026-02-11 19:28:17', 0),
(557, 186, 24, 875.00, '2026-02-11 19:28:17', 1),
(558, 186, 23, 900.00, '2026-02-11 19:28:17', 1),
(559, 186, 24, 925.00, '2026-02-11 19:28:17', 1),
(560, 186, 23, 950.00, '2026-02-13 19:28:17', 0),
(561, 186, 22, 975.00, '2026-02-14 19:28:17', 0),
(562, 186, 24, 1000.00, '2026-02-14 19:28:17', 1),
(563, 186, 22, 1025.00, '2026-02-14 19:28:17', 1),
(564, 186, 23, 1050.00, '2026-02-15 19:28:17', 0),
(565, 186, 24, 1075.00, '2026-02-16 19:28:17', 1),
(566, 186, 23, 1100.00, '2026-02-16 19:28:17', 1),
(567, 186, 24, 1125.00, '2026-02-18 19:28:17', 0),
(568, 186, 22, 1150.00, '2026-02-19 19:28:17', 0),
(569, 186, 23, 1175.00, '2026-02-20 19:28:17', 1),
(570, 186, 22, 1200.00, '2026-02-20 19:28:17', 1),
(571, 186, 24, 1225.00, '2026-02-21 19:28:17', 0),
(572, 186, 23, 1250.00, '2026-02-21 19:28:17', 1),
(573, 186, 22, 1275.00, '2026-02-21 19:28:17', 1),
(574, 186, 24, 1300.00, '2026-02-23 11:28:17', 0),
(575, 186, 23, 1325.00, '2026-02-23 17:28:17', 0),
(576, 187, 22, 600.00, '2026-02-18 19:28:17', 0),
(577, 187, 24, 625.00, '2026-02-20 19:28:17', 0),
(578, 187, 22, 650.00, '2026-02-22 19:28:17', 0),
(579, 188, 23, 900.00, '2026-02-19 19:28:17', 0),
(580, 188, 22, 950.00, '2026-02-21 19:28:17', 0),
(581, 189, 24, 300.00, '2026-02-17 19:28:17', 0),
(582, 189, 22, 320.00, '2026-02-18 19:28:17', 0),
(583, 189, 24, 340.00, '2026-02-18 19:28:17', 1),
(584, 189, 22, 360.00, '2026-02-21 19:28:17', 0),
(585, 189, 24, 380.00, '2026-02-23 07:28:17', 0),
(586, 190, 23, 400.00, '2026-02-16 19:28:17', 0),
(587, 190, 22, 425.00, '2026-02-19 19:28:17', 0),
(588, 190, 23, 450.00, '2026-02-22 19:28:17', 0),
(589, 192, 23, 1500.00, '2026-02-13 19:28:17', 0),
(590, 192, 24, 1550.00, '2026-02-14 19:28:17', 0),
(591, 192, 23, 1600.00, '2026-02-14 19:28:17', 1),
(592, 192, 24, 1650.00, '2026-02-14 19:28:17', 1),
(593, 192, 23, 1700.00, '2026-02-16 19:28:17', 0),
(594, 192, 24, 1750.00, '2026-02-17 19:28:17', 0),
(595, 192, 23, 1800.00, '2026-02-18 19:28:17', 1),
(596, 192, 24, 1850.00, '2026-02-18 19:28:17', 1),
(597, 192, 23, 1900.00, '2026-02-19 19:28:17', 0),
(598, 192, 24, 1950.00, '2026-02-20 19:28:17', 0),
(599, 192, 23, 2000.00, '2026-02-21 19:28:17', 1),
(600, 192, 24, 2050.00, '2026-02-21 19:28:17', 1),
(601, 192, 23, 2100.00, '2026-02-23 01:28:17', 0),
(602, 192, 24, 2150.00, '2026-02-23 15:28:17', 0),
(603, 193, 22, 200.00, '2026-02-18 19:28:17', 0),
(604, 193, 24, 210.00, '2026-02-19 19:28:17', 0),
(605, 193, 22, 220.00, '2026-02-19 19:28:17', 1),
(606, 193, 24, 230.00, '2026-02-21 19:28:17', 0),
(607, 193, 22, 240.00, '2026-02-23 13:28:17', 0),
(608, 194, 23, 250.00, '2026-02-19 19:28:17', 0),
(609, 194, 22, 265.00, '2026-02-21 19:28:17', 0),
(610, 195, 23, 400.00, '2026-02-15 19:28:17', 0),
(611, 195, 22, 425.00, '2026-02-16 19:28:17', 0),
(612, 195, 23, 450.00, '2026-02-16 19:28:17', 1),
(613, 195, 22, 475.00, '2026-02-16 19:28:17', 1),
(614, 195, 23, 500.00, '2026-02-18 19:28:17', 0),
(615, 195, 22, 525.00, '2026-02-20 19:28:17', 0),
(616, 195, 23, 550.00, '2026-02-21 19:28:17', 1),
(617, 195, 22, 575.00, '2026-02-23 11:28:17', 0),
(618, 196, 23, 60.00, '2026-02-18 19:28:17', 0),
(619, 196, 24, 65.00, '2026-02-19 19:28:17', 0),
(620, 196, 23, 70.00, '2026-02-19 19:28:17', 1),
(621, 196, 24, 75.00, '2026-02-19 19:28:17', 1),
(622, 196, 23, 80.00, '2026-02-20 19:28:17', 0),
(623, 196, 24, 85.00, '2026-02-21 19:28:17', 0),
(624, 196, 23, 90.00, '2026-02-21 19:28:17', 1),
(625, 196, 24, 95.00, '2026-02-21 19:28:17', 1),
(626, 196, 23, 100.00, '2026-02-22 19:28:17', 0),
(627, 196, 24, 105.00, '2026-02-23 15:28:17', 0),
(628, 198, 22, 2000.00, '2026-02-08 19:28:17', 0),
(629, 198, 24, 2100.00, '2026-02-09 19:28:17', 0),
(630, 198, 23, 2200.00, '2026-02-10 19:28:17', 0),
(631, 198, 22, 2300.00, '2026-02-11 19:28:17', 1),
(632, 198, 24, 2400.00, '2026-02-11 19:28:17', 1),
(633, 198, 22, 2500.00, '2026-02-11 19:28:17', 1),
(634, 198, 23, 2600.00, '2026-02-13 19:28:17', 0),
(635, 198, 24, 2700.00, '2026-02-14 19:28:17', 1),
(636, 198, 23, 2800.00, '2026-02-14 19:28:17', 1),
(637, 198, 22, 2900.00, '2026-02-15 19:28:17', 0),
(638, 198, 24, 3000.00, '2026-02-16 19:28:17', 0),
(639, 198, 23, 3100.00, '2026-02-17 19:28:17', 1),
(640, 198, 24, 3200.00, '2026-02-17 19:28:17', 1),
(641, 198, 22, 3300.00, '2026-02-18 19:28:17', 0),
(642, 198, 24, 3400.00, '2026-02-19 19:28:17', 1),
(643, 198, 22, 3500.00, '2026-02-19 19:28:17', 1),
(644, 198, 23, 3600.00, '2026-02-20 19:28:17', 0),
(645, 198, 24, 3700.00, '2026-02-21 19:28:17', 1),
(646, 198, 22, 3800.00, '2026-02-21 19:28:17', 1),
(647, 198, 23, 3900.00, '2026-02-23 13:28:17', 0),
(648, 199, 22, 5000.00, '2026-02-11 19:28:17', 0),
(649, 199, 24, 5200.00, '2026-02-12 19:28:17', 0),
(650, 199, 22, 5400.00, '2026-02-12 19:28:17', 1),
(651, 199, 24, 5600.00, '2026-02-12 19:28:17', 1),
(652, 199, 22, 5800.00, '2026-02-14 19:28:17', 0),
(653, 199, 24, 6000.00, '2026-02-15 19:28:17', 0),
(654, 199, 22, 6200.00, '2026-02-16 19:28:17', 1),
(655, 199, 24, 6400.00, '2026-02-16 19:28:17', 1),
(656, 199, 22, 6600.00, '2026-02-18 19:28:17', 0),
(657, 199, 24, 6800.00, '2026-02-19 19:28:17', 0),
(658, 199, 22, 7000.00, '2026-02-20 19:28:17', 1),
(659, 199, 24, 7200.00, '2026-02-20 19:28:17', 1),
(660, 199, 22, 7400.00, '2026-02-21 19:28:17', 0),
(661, 199, 24, 7600.00, '2026-02-22 19:28:17', 1),
(662, 199, 22, 7800.00, '2026-02-23 15:28:17', 0),
(663, 200, 23, 400.00, '2026-02-15 19:28:17', 0),
(664, 200, 22, 425.00, '2026-02-17 19:28:17', 0),
(665, 200, 23, 450.00, '2026-02-18 19:28:17', 1),
(666, 200, 22, 475.00, '2026-02-22 19:28:17', 0),
(667, 201, 23, 50000.00, '2026-02-15 19:46:00', 0),
(668, 201, 24, 51000.00, '2026-02-16 19:46:00', 0),
(669, 201, 23, 52000.00, '2026-02-16 19:46:00', 1),
(670, 201, 24, 53000.00, '2026-02-16 19:46:00', 1),
(671, 201, 23, 54000.00, '2026-02-19 19:46:00', 0),
(672, 201, 24, 55000.00, '2026-02-20 19:46:00', 1),
(673, 201, 23, 56000.00, '2026-02-22 19:46:00', 0),
(674, 202, 24, 35000.00, '2026-02-13 19:46:00', 0),
(675, 202, 22, 35500.00, '2026-02-14 19:46:00', 0),
(676, 202, 24, 36000.00, '2026-02-14 19:46:00', 1),
(677, 202, 22, 36500.00, '2026-02-14 19:46:00', 1),
(678, 202, 24, 37000.00, '2026-02-17 19:46:00', 0),
(679, 202, 22, 37500.00, '2026-02-18 19:46:00', 1),
(680, 202, 24, 38000.00, '2026-02-18 19:46:00', 1),
(681, 202, 22, 38500.00, '2026-02-21 19:46:00', 0),
(682, 202, 24, 39000.00, '2026-02-23 07:46:00', 0),
(683, 204, 23, 12000.00, '2026-02-11 19:46:00', 0),
(684, 204, 24, 12200.00, '2026-02-12 19:46:00', 0),
(685, 204, 23, 12400.00, '2026-02-12 19:46:00', 1),
(686, 204, 24, 12600.00, '2026-02-12 19:46:00', 1),
(687, 204, 23, 12800.00, '2026-02-14 19:46:00', 0),
(688, 204, 24, 13000.00, '2026-02-15 19:46:00', 0),
(689, 204, 23, 13200.00, '2026-02-16 19:46:00', 1),
(690, 204, 24, 13400.00, '2026-02-16 19:46:00', 1),
(691, 204, 23, 13600.00, '2026-02-17 19:46:00', 0),
(692, 204, 24, 13800.00, '2026-02-18 19:46:00', 0),
(693, 204, 23, 14000.00, '2026-02-19 19:46:00', 1),
(694, 204, 24, 14200.00, '2026-02-19 19:46:00', 1),
(695, 204, 23, 14400.00, '2026-02-20 19:46:00', 0),
(696, 204, 24, 14600.00, '2026-02-21 19:46:00', 1),
(697, 204, 23, 14800.00, '2026-02-21 19:46:00', 1),
(698, 204, 24, 15000.00, '2026-02-22 19:46:00', 0),
(699, 204, 23, 15200.00, '2026-02-23 09:46:00', 1),
(700, 204, 24, 15400.00, '2026-02-23 16:46:00', 0),
(701, 205, 22, 5500.00, '2026-02-16 19:46:00', 0),
(702, 205, 24, 5600.00, '2026-02-17 19:46:00', 0),
(703, 205, 22, 5700.00, '2026-02-17 19:46:00', 1),
(704, 205, 24, 5800.00, '2026-02-17 19:46:00', 1),
(705, 205, 22, 5900.00, '2026-02-18 19:46:00', 0),
(706, 205, 24, 6000.00, '2026-02-19 19:46:00', 0),
(707, 205, 22, 6100.00, '2026-02-20 19:46:00', 1),
(708, 205, 24, 6200.00, '2026-02-20 19:46:00', 1),
(709, 205, 22, 6300.00, '2026-02-21 19:46:00', 0),
(710, 205, 24, 6400.00, '2026-02-23 01:46:00', 0),
(711, 205, 22, 6500.00, '2026-02-23 13:46:00', 0),
(712, 207, 24, 800.00, '2026-02-09 19:46:00', 0),
(713, 207, 23, 825.00, '2026-02-10 19:46:00', 0),
(714, 207, 22, 850.00, '2026-02-11 19:46:00', 0),
(715, 207, 24, 875.00, '2026-02-11 19:46:00', 1),
(716, 207, 23, 900.00, '2026-02-11 19:46:00', 1),
(717, 207, 24, 925.00, '2026-02-11 19:46:00', 1),
(718, 207, 23, 950.00, '2026-02-13 19:46:00', 0),
(719, 207, 22, 975.00, '2026-02-14 19:46:00', 0),
(720, 207, 24, 1000.00, '2026-02-14 19:46:00', 1),
(721, 207, 22, 1025.00, '2026-02-14 19:46:00', 1),
(722, 207, 23, 1050.00, '2026-02-15 19:46:00', 0),
(723, 207, 24, 1075.00, '2026-02-16 19:46:00', 1),
(724, 207, 23, 1100.00, '2026-02-16 19:46:00', 1),
(725, 207, 24, 1125.00, '2026-02-18 19:46:00', 0),
(726, 207, 22, 1150.00, '2026-02-19 19:46:00', 0),
(727, 207, 23, 1175.00, '2026-02-20 19:46:00', 1),
(728, 207, 22, 1200.00, '2026-02-20 19:46:00', 1),
(729, 207, 24, 1225.00, '2026-02-21 19:46:00', 0),
(730, 207, 23, 1250.00, '2026-02-21 19:46:00', 1),
(731, 207, 22, 1275.00, '2026-02-21 19:46:00', 1),
(732, 207, 24, 1300.00, '2026-02-23 11:46:00', 0),
(733, 207, 23, 1325.00, '2026-02-23 17:46:00', 0),
(734, 208, 22, 600.00, '2026-02-18 19:46:00', 0),
(735, 208, 24, 625.00, '2026-02-20 19:46:00', 0),
(736, 208, 22, 650.00, '2026-02-22 19:46:00', 0),
(737, 209, 23, 900.00, '2026-02-19 19:46:00', 0),
(738, 209, 22, 950.00, '2026-02-21 19:46:00', 0),
(739, 210, 24, 300.00, '2026-02-17 19:46:00', 0),
(740, 210, 22, 320.00, '2026-02-18 19:46:00', 0),
(741, 210, 24, 340.00, '2026-02-18 19:46:00', 1),
(742, 210, 22, 360.00, '2026-02-21 19:46:00', 0),
(743, 210, 24, 380.00, '2026-02-23 07:46:00', 0),
(744, 211, 23, 400.00, '2026-02-16 19:46:00', 0),
(745, 211, 22, 425.00, '2026-02-19 19:46:00', 0),
(746, 211, 23, 450.00, '2026-02-22 19:46:00', 0),
(747, 213, 23, 1500.00, '2026-02-13 19:46:00', 0),
(748, 213, 24, 1550.00, '2026-02-14 19:46:00', 0),
(749, 213, 23, 1600.00, '2026-02-14 19:46:00', 1),
(750, 213, 24, 1650.00, '2026-02-14 19:46:00', 1),
(751, 213, 23, 1700.00, '2026-02-16 19:46:00', 0),
(752, 213, 24, 1750.00, '2026-02-17 19:46:00', 0),
(753, 213, 23, 1800.00, '2026-02-18 19:46:00', 1),
(754, 213, 24, 1850.00, '2026-02-18 19:46:00', 1),
(755, 213, 23, 1900.00, '2026-02-19 19:46:00', 0),
(756, 213, 24, 1950.00, '2026-02-20 19:46:00', 0),
(757, 213, 23, 2000.00, '2026-02-21 19:46:00', 1),
(758, 213, 24, 2050.00, '2026-02-21 19:46:00', 1),
(759, 213, 23, 2100.00, '2026-02-23 01:46:00', 0),
(760, 213, 24, 2150.00, '2026-02-23 15:46:00', 0),
(761, 214, 22, 200.00, '2026-02-18 19:46:00', 0),
(762, 214, 24, 210.00, '2026-02-19 19:46:00', 0),
(763, 214, 22, 220.00, '2026-02-19 19:46:00', 1),
(764, 214, 24, 230.00, '2026-02-21 19:46:00', 0),
(765, 214, 22, 240.00, '2026-02-23 13:46:00', 0),
(766, 215, 23, 250.00, '2026-02-19 19:46:00', 0),
(767, 215, 22, 265.00, '2026-02-21 19:46:00', 0),
(768, 216, 23, 400.00, '2026-02-15 19:46:00', 0),
(769, 216, 22, 425.00, '2026-02-16 19:46:00', 0),
(770, 216, 23, 450.00, '2026-02-16 19:46:00', 1),
(771, 216, 22, 475.00, '2026-02-16 19:46:00', 1),
(772, 216, 23, 500.00, '2026-02-18 19:46:00', 0),
(773, 216, 22, 525.00, '2026-02-20 19:46:00', 0),
(774, 216, 23, 550.00, '2026-02-21 19:46:00', 1),
(775, 216, 22, 575.00, '2026-02-23 11:46:00', 0),
(776, 217, 23, 60.00, '2026-02-18 19:46:00', 0),
(777, 217, 24, 65.00, '2026-02-19 19:46:00', 0),
(778, 217, 23, 70.00, '2026-02-19 19:46:00', 1),
(779, 217, 24, 75.00, '2026-02-19 19:46:00', 1),
(780, 217, 23, 80.00, '2026-02-20 19:46:00', 0),
(781, 217, 24, 85.00, '2026-02-21 19:46:00', 0),
(782, 217, 23, 90.00, '2026-02-21 19:46:00', 1),
(783, 217, 24, 95.00, '2026-02-21 19:46:00', 1),
(784, 217, 23, 100.00, '2026-02-22 19:46:00', 0),
(785, 217, 24, 105.00, '2026-02-23 15:46:00', 0),
(786, 219, 22, 2000.00, '2026-02-08 19:46:00', 0),
(787, 219, 24, 2100.00, '2026-02-09 19:46:00', 0),
(788, 219, 23, 2200.00, '2026-02-10 19:46:00', 0),
(789, 219, 22, 2300.00, '2026-02-11 19:46:00', 1),
(790, 219, 24, 2400.00, '2026-02-11 19:46:00', 1),
(791, 219, 22, 2500.00, '2026-02-11 19:46:00', 1),
(792, 219, 23, 2600.00, '2026-02-13 19:46:00', 0),
(793, 219, 24, 2700.00, '2026-02-14 19:46:00', 1),
(794, 219, 23, 2800.00, '2026-02-14 19:46:00', 1),
(795, 219, 22, 2900.00, '2026-02-15 19:46:00', 0),
(796, 219, 24, 3000.00, '2026-02-16 19:46:00', 0),
(797, 219, 23, 3100.00, '2026-02-17 19:46:00', 1),
(798, 219, 24, 3200.00, '2026-02-17 19:46:00', 1),
(799, 219, 22, 3300.00, '2026-02-18 19:46:00', 0),
(800, 219, 24, 3400.00, '2026-02-19 19:46:00', 1),
(801, 219, 22, 3500.00, '2026-02-19 19:46:00', 1),
(802, 219, 23, 3600.00, '2026-02-20 19:46:00', 0),
(803, 219, 24, 3700.00, '2026-02-21 19:46:00', 1),
(804, 219, 22, 3800.00, '2026-02-21 19:46:00', 1),
(805, 219, 23, 3900.00, '2026-02-23 13:46:00', 0),
(806, 220, 22, 5000.00, '2026-02-11 19:46:00', 0),
(807, 220, 24, 5200.00, '2026-02-12 19:46:00', 0),
(808, 220, 22, 5400.00, '2026-02-12 19:46:00', 1),
(809, 220, 24, 5600.00, '2026-02-12 19:46:00', 1),
(810, 220, 22, 5800.00, '2026-02-14 19:46:00', 0),
(811, 220, 24, 6000.00, '2026-02-15 19:46:00', 0),
(812, 220, 22, 6200.00, '2026-02-16 19:46:00', 1),
(813, 220, 24, 6400.00, '2026-02-16 19:46:00', 1),
(814, 220, 22, 6600.00, '2026-02-18 19:46:00', 0),
(815, 220, 24, 6800.00, '2026-02-19 19:46:00', 0),
(816, 220, 22, 7000.00, '2026-02-20 19:46:00', 1),
(817, 220, 24, 7200.00, '2026-02-20 19:46:00', 1),
(818, 220, 22, 7400.00, '2026-02-21 19:46:00', 0),
(819, 220, 24, 7600.00, '2026-02-22 19:46:00', 1),
(820, 220, 22, 7800.00, '2026-02-23 15:46:00', 0),
(821, 221, 23, 400.00, '2026-02-15 19:46:00', 0),
(822, 221, 22, 425.00, '2026-02-17 19:46:00', 0),
(823, 221, 23, 450.00, '2026-02-18 19:46:00', 1),
(824, 221, 22, 475.00, '2026-02-22 19:46:00', 0),
(825, 222, 23, 50000.00, '2026-02-15 19:46:23', 0),
(826, 222, 24, 51000.00, '2026-02-16 19:46:23', 0),
(827, 222, 23, 52000.00, '2026-02-16 19:46:23', 1),
(828, 222, 24, 53000.00, '2026-02-16 19:46:23', 1),
(829, 222, 23, 54000.00, '2026-02-19 19:46:23', 0),
(830, 222, 24, 55000.00, '2026-02-20 19:46:23', 1),
(831, 222, 23, 56000.00, '2026-02-22 19:46:23', 0),
(832, 223, 24, 35000.00, '2026-02-13 19:46:23', 0),
(833, 223, 22, 35500.00, '2026-02-14 19:46:23', 0),
(834, 223, 24, 36000.00, '2026-02-14 19:46:23', 1),
(835, 223, 22, 36500.00, '2026-02-14 19:46:23', 1),
(836, 223, 24, 37000.00, '2026-02-17 19:46:23', 0),
(837, 223, 22, 37500.00, '2026-02-18 19:46:23', 1),
(838, 223, 24, 38000.00, '2026-02-18 19:46:23', 1),
(839, 223, 22, 38500.00, '2026-02-21 19:46:23', 0),
(840, 223, 24, 39000.00, '2026-02-23 07:46:23', 0),
(841, 225, 23, 12000.00, '2026-02-11 19:46:23', 0),
(842, 225, 24, 12200.00, '2026-02-12 19:46:23', 0),
(843, 225, 23, 12400.00, '2026-02-12 19:46:23', 1),
(844, 225, 24, 12600.00, '2026-02-12 19:46:23', 1),
(845, 225, 23, 12800.00, '2026-02-14 19:46:23', 0),
(846, 225, 24, 13000.00, '2026-02-15 19:46:23', 0),
(847, 225, 23, 13200.00, '2026-02-16 19:46:23', 1),
(848, 225, 24, 13400.00, '2026-02-16 19:46:23', 1),
(849, 225, 23, 13600.00, '2026-02-17 19:46:23', 0),
(850, 225, 24, 13800.00, '2026-02-18 19:46:23', 0),
(851, 225, 23, 14000.00, '2026-02-19 19:46:23', 1),
(852, 225, 24, 14200.00, '2026-02-19 19:46:23', 1),
(853, 225, 23, 14400.00, '2026-02-20 19:46:23', 0),
(854, 225, 24, 14600.00, '2026-02-21 19:46:23', 1),
(855, 225, 23, 14800.00, '2026-02-21 19:46:23', 1),
(856, 225, 24, 15000.00, '2026-02-22 19:46:23', 0),
(857, 225, 23, 15200.00, '2026-02-23 09:46:23', 1),
(858, 225, 24, 15400.00, '2026-02-23 16:46:23', 0),
(859, 226, 22, 5500.00, '2026-02-16 19:46:23', 0),
(860, 226, 24, 5600.00, '2026-02-17 19:46:23', 0),
(861, 226, 22, 5700.00, '2026-02-17 19:46:23', 1),
(862, 226, 24, 5800.00, '2026-02-17 19:46:23', 1),
(863, 226, 22, 5900.00, '2026-02-18 19:46:23', 0),
(864, 226, 24, 6000.00, '2026-02-19 19:46:23', 0),
(865, 226, 22, 6100.00, '2026-02-20 19:46:23', 1),
(866, 226, 24, 6200.00, '2026-02-20 19:46:23', 1),
(867, 226, 22, 6300.00, '2026-02-21 19:46:23', 0),
(868, 226, 24, 6400.00, '2026-02-23 01:46:23', 0),
(869, 226, 22, 6500.00, '2026-02-23 13:46:23', 0),
(870, 228, 24, 800.00, '2026-02-09 19:46:23', 0),
(871, 228, 23, 825.00, '2026-02-10 19:46:23', 0),
(872, 228, 22, 850.00, '2026-02-11 19:46:23', 0),
(873, 228, 24, 875.00, '2026-02-11 19:46:23', 1),
(874, 228, 23, 900.00, '2026-02-11 19:46:23', 1),
(875, 228, 24, 925.00, '2026-02-11 19:46:23', 1),
(876, 228, 23, 950.00, '2026-02-13 19:46:23', 0),
(877, 228, 22, 975.00, '2026-02-14 19:46:23', 0),
(878, 228, 24, 1000.00, '2026-02-14 19:46:23', 1),
(879, 228, 22, 1025.00, '2026-02-14 19:46:23', 1),
(880, 228, 23, 1050.00, '2026-02-15 19:46:23', 0),
(881, 228, 24, 1075.00, '2026-02-16 19:46:23', 1),
(882, 228, 23, 1100.00, '2026-02-16 19:46:23', 1),
(883, 228, 24, 1125.00, '2026-02-18 19:46:23', 0),
(884, 228, 22, 1150.00, '2026-02-19 19:46:23', 0),
(885, 228, 23, 1175.00, '2026-02-20 19:46:23', 1),
(886, 228, 22, 1200.00, '2026-02-20 19:46:23', 1),
(887, 228, 24, 1225.00, '2026-02-21 19:46:23', 0),
(888, 228, 23, 1250.00, '2026-02-21 19:46:23', 1),
(889, 228, 22, 1275.00, '2026-02-21 19:46:23', 1),
(890, 228, 24, 1300.00, '2026-02-23 11:46:23', 0),
(891, 228, 23, 1325.00, '2026-02-23 17:46:23', 0),
(892, 229, 22, 600.00, '2026-02-18 19:46:23', 0),
(893, 229, 24, 625.00, '2026-02-20 19:46:23', 0),
(894, 229, 22, 650.00, '2026-02-22 19:46:23', 0),
(895, 230, 23, 900.00, '2026-02-19 19:46:23', 0),
(896, 230, 22, 950.00, '2026-02-21 19:46:23', 0),
(897, 231, 24, 300.00, '2026-02-17 19:46:23', 0),
(898, 231, 22, 320.00, '2026-02-18 19:46:23', 0),
(899, 231, 24, 340.00, '2026-02-18 19:46:23', 1),
(900, 231, 22, 360.00, '2026-02-21 19:46:23', 0),
(901, 231, 24, 380.00, '2026-02-23 07:46:23', 0),
(902, 232, 23, 400.00, '2026-02-16 19:46:23', 0),
(903, 232, 22, 425.00, '2026-02-19 19:46:23', 0),
(904, 232, 23, 450.00, '2026-02-22 19:46:23', 0),
(905, 234, 23, 1500.00, '2026-02-13 19:46:23', 0),
(906, 234, 24, 1550.00, '2026-02-14 19:46:23', 0),
(907, 234, 23, 1600.00, '2026-02-14 19:46:23', 1),
(908, 234, 24, 1650.00, '2026-02-14 19:46:23', 1),
(909, 234, 23, 1700.00, '2026-02-16 19:46:23', 0),
(910, 234, 24, 1750.00, '2026-02-17 19:46:23', 0),
(911, 234, 23, 1800.00, '2026-02-18 19:46:23', 1),
(912, 234, 24, 1850.00, '2026-02-18 19:46:23', 1),
(913, 234, 23, 1900.00, '2026-02-19 19:46:23', 0),
(914, 234, 24, 1950.00, '2026-02-20 19:46:23', 0),
(915, 234, 23, 2000.00, '2026-02-21 19:46:23', 1),
(916, 234, 24, 2050.00, '2026-02-21 19:46:23', 1),
(917, 234, 23, 2100.00, '2026-02-23 01:46:23', 0),
(918, 234, 24, 2150.00, '2026-02-23 15:46:23', 0),
(919, 235, 22, 200.00, '2026-02-18 19:46:23', 0),
(920, 235, 24, 210.00, '2026-02-19 19:46:23', 0),
(921, 235, 22, 220.00, '2026-02-19 19:46:23', 1),
(922, 235, 24, 230.00, '2026-02-21 19:46:23', 0),
(923, 235, 22, 240.00, '2026-02-23 13:46:23', 0),
(924, 236, 23, 250.00, '2026-02-19 19:46:23', 0),
(925, 236, 22, 265.00, '2026-02-21 19:46:23', 0),
(926, 237, 23, 400.00, '2026-02-15 19:46:23', 0),
(927, 237, 22, 425.00, '2026-02-16 19:46:23', 0),
(928, 237, 23, 450.00, '2026-02-16 19:46:23', 1),
(929, 237, 22, 475.00, '2026-02-16 19:46:23', 1),
(930, 237, 23, 500.00, '2026-02-18 19:46:23', 0),
(931, 237, 22, 525.00, '2026-02-20 19:46:23', 0),
(932, 237, 23, 550.00, '2026-02-21 19:46:23', 1),
(933, 237, 22, 575.00, '2026-02-23 11:46:23', 0),
(934, 238, 23, 60.00, '2026-02-18 19:46:23', 0),
(935, 238, 24, 65.00, '2026-02-19 19:46:23', 0),
(936, 238, 23, 70.00, '2026-02-19 19:46:23', 1),
(937, 238, 24, 75.00, '2026-02-19 19:46:23', 1),
(938, 238, 23, 80.00, '2026-02-20 19:46:23', 0),
(939, 238, 24, 85.00, '2026-02-21 19:46:23', 0),
(940, 238, 23, 90.00, '2026-02-21 19:46:23', 1),
(941, 238, 24, 95.00, '2026-02-21 19:46:23', 1),
(942, 238, 23, 100.00, '2026-02-22 19:46:23', 0),
(943, 238, 24, 105.00, '2026-02-23 15:46:23', 0),
(944, 240, 22, 2000.00, '2026-02-08 19:46:23', 0),
(945, 240, 24, 2100.00, '2026-02-09 19:46:23', 0),
(946, 240, 23, 2200.00, '2026-02-10 19:46:23', 0),
(947, 240, 22, 2300.00, '2026-02-11 19:46:23', 1),
(948, 240, 24, 2400.00, '2026-02-11 19:46:23', 1),
(949, 240, 22, 2500.00, '2026-02-11 19:46:23', 1),
(950, 240, 23, 2600.00, '2026-02-13 19:46:23', 0),
(951, 240, 24, 2700.00, '2026-02-14 19:46:23', 1),
(952, 240, 23, 2800.00, '2026-02-14 19:46:23', 1),
(953, 240, 22, 2900.00, '2026-02-15 19:46:23', 0),
(954, 240, 24, 3000.00, '2026-02-16 19:46:23', 0),
(955, 240, 23, 3100.00, '2026-02-17 19:46:23', 1),
(956, 240, 24, 3200.00, '2026-02-17 19:46:23', 1),
(957, 240, 22, 3300.00, '2026-02-18 19:46:23', 0),
(958, 240, 24, 3400.00, '2026-02-19 19:46:23', 1),
(959, 240, 22, 3500.00, '2026-02-19 19:46:23', 1),
(960, 240, 23, 3600.00, '2026-02-20 19:46:23', 0),
(961, 240, 24, 3700.00, '2026-02-21 19:46:23', 1),
(962, 240, 22, 3800.00, '2026-02-21 19:46:23', 1),
(963, 240, 23, 3900.00, '2026-02-23 13:46:23', 0),
(964, 241, 22, 5000.00, '2026-02-11 19:46:23', 0),
(965, 241, 24, 5200.00, '2026-02-12 19:46:23', 0),
(966, 241, 22, 5400.00, '2026-02-12 19:46:23', 1),
(967, 241, 24, 5600.00, '2026-02-12 19:46:23', 1),
(968, 241, 22, 5800.00, '2026-02-14 19:46:23', 0),
(969, 241, 24, 6000.00, '2026-02-15 19:46:23', 0),
(970, 241, 22, 6200.00, '2026-02-16 19:46:23', 1),
(971, 241, 24, 6400.00, '2026-02-16 19:46:23', 1),
(972, 241, 22, 6600.00, '2026-02-18 19:46:23', 0),
(973, 241, 24, 6800.00, '2026-02-19 19:46:23', 0),
(974, 241, 22, 7000.00, '2026-02-20 19:46:23', 1),
(975, 241, 24, 7200.00, '2026-02-20 19:46:23', 1),
(976, 241, 22, 7400.00, '2026-02-21 19:46:23', 0),
(977, 241, 24, 7600.00, '2026-02-22 19:46:23', 1),
(978, 241, 22, 7800.00, '2026-02-23 15:46:23', 0),
(979, 242, 23, 400.00, '2026-02-15 19:46:23', 0),
(980, 242, 22, 425.00, '2026-02-17 19:46:23', 0),
(981, 242, 23, 450.00, '2026-02-18 19:46:23', 1),
(982, 242, 22, 475.00, '2026-02-22 19:46:23', 0),
(983, 243, 23, 50000.00, '2026-02-15 19:46:33', 0),
(984, 243, 24, 51000.00, '2026-02-16 19:46:33', 0),
(985, 243, 23, 52000.00, '2026-02-16 19:46:33', 1),
(986, 243, 24, 53000.00, '2026-02-16 19:46:33', 1),
(987, 243, 23, 54000.00, '2026-02-19 19:46:33', 0),
(988, 243, 24, 55000.00, '2026-02-20 19:46:33', 1),
(989, 243, 23, 56000.00, '2026-02-22 19:46:33', 0),
(990, 244, 24, 35000.00, '2026-02-13 19:46:33', 0),
(991, 244, 22, 35500.00, '2026-02-14 19:46:33', 0),
(992, 244, 24, 36000.00, '2026-02-14 19:46:33', 1),
(993, 244, 22, 36500.00, '2026-02-14 19:46:33', 1),
(994, 244, 24, 37000.00, '2026-02-17 19:46:33', 0),
(995, 244, 22, 37500.00, '2026-02-18 19:46:33', 1),
(996, 244, 24, 38000.00, '2026-02-18 19:46:33', 1),
(997, 244, 22, 38500.00, '2026-02-21 19:46:33', 0),
(998, 244, 24, 39000.00, '2026-02-23 07:46:33', 0),
(999, 246, 23, 12000.00, '2026-02-11 19:46:33', 0),
(1000, 246, 24, 12200.00, '2026-02-12 19:46:33', 0),
(1001, 246, 23, 12400.00, '2026-02-12 19:46:33', 1),
(1002, 246, 24, 12600.00, '2026-02-12 19:46:33', 1),
(1003, 246, 23, 12800.00, '2026-02-14 19:46:33', 0),
(1004, 246, 24, 13000.00, '2026-02-15 19:46:33', 0),
(1005, 246, 23, 13200.00, '2026-02-16 19:46:33', 1),
(1006, 246, 24, 13400.00, '2026-02-16 19:46:33', 1),
(1007, 246, 23, 13600.00, '2026-02-17 19:46:33', 0),
(1008, 246, 24, 13800.00, '2026-02-18 19:46:33', 0),
(1009, 246, 23, 14000.00, '2026-02-19 19:46:33', 1),
(1010, 246, 24, 14200.00, '2026-02-19 19:46:33', 1),
(1011, 246, 23, 14400.00, '2026-02-20 19:46:33', 0),
(1012, 246, 24, 14600.00, '2026-02-21 19:46:33', 1),
(1013, 246, 23, 14800.00, '2026-02-21 19:46:33', 1),
(1014, 246, 24, 15000.00, '2026-02-22 19:46:33', 0),
(1015, 246, 23, 15200.00, '2026-02-23 09:46:33', 1),
(1016, 246, 24, 15400.00, '2026-02-23 16:46:33', 0),
(1017, 247, 22, 5500.00, '2026-02-16 19:46:33', 0),
(1018, 247, 24, 5600.00, '2026-02-17 19:46:33', 0),
(1019, 247, 22, 5700.00, '2026-02-17 19:46:33', 1),
(1020, 247, 24, 5800.00, '2026-02-17 19:46:33', 1),
(1021, 247, 22, 5900.00, '2026-02-18 19:46:33', 0),
(1022, 247, 24, 6000.00, '2026-02-19 19:46:33', 0),
(1023, 247, 22, 6100.00, '2026-02-20 19:46:33', 1),
(1024, 247, 24, 6200.00, '2026-02-20 19:46:33', 1),
(1025, 247, 22, 6300.00, '2026-02-21 19:46:33', 0),
(1026, 247, 24, 6400.00, '2026-02-23 01:46:33', 0),
(1027, 247, 22, 6500.00, '2026-02-23 13:46:33', 0),
(1028, 249, 24, 800.00, '2026-02-09 19:46:33', 0),
(1029, 249, 23, 825.00, '2026-02-10 19:46:33', 0),
(1030, 249, 22, 850.00, '2026-02-11 19:46:33', 0),
(1031, 249, 24, 875.00, '2026-02-11 19:46:33', 1),
(1032, 249, 23, 900.00, '2026-02-11 19:46:33', 1),
(1033, 249, 24, 925.00, '2026-02-11 19:46:33', 1),
(1034, 249, 23, 950.00, '2026-02-13 19:46:33', 0),
(1035, 249, 22, 975.00, '2026-02-14 19:46:33', 0),
(1036, 249, 24, 1000.00, '2026-02-14 19:46:33', 1),
(1037, 249, 22, 1025.00, '2026-02-14 19:46:33', 1),
(1038, 249, 23, 1050.00, '2026-02-15 19:46:33', 0),
(1039, 249, 24, 1075.00, '2026-02-16 19:46:33', 1),
(1040, 249, 23, 1100.00, '2026-02-16 19:46:33', 1),
(1041, 249, 24, 1125.00, '2026-02-18 19:46:33', 0),
(1042, 249, 22, 1150.00, '2026-02-19 19:46:33', 0),
(1043, 249, 23, 1175.00, '2026-02-20 19:46:33', 1),
(1044, 249, 22, 1200.00, '2026-02-20 19:46:33', 1),
(1045, 249, 24, 1225.00, '2026-02-21 19:46:33', 0),
(1046, 249, 23, 1250.00, '2026-02-21 19:46:33', 1),
(1047, 249, 22, 1275.00, '2026-02-21 19:46:33', 1),
(1048, 249, 24, 1300.00, '2026-02-23 11:46:33', 0),
(1049, 249, 23, 1325.00, '2026-02-23 17:46:33', 0),
(1050, 250, 22, 600.00, '2026-02-18 19:46:33', 0),
(1051, 250, 24, 625.00, '2026-02-20 19:46:33', 0),
(1052, 250, 22, 650.00, '2026-02-22 19:46:33', 0),
(1053, 251, 23, 900.00, '2026-02-19 19:46:33', 0),
(1054, 251, 22, 950.00, '2026-02-21 19:46:33', 0),
(1055, 252, 24, 300.00, '2026-02-17 19:46:33', 0),
(1056, 252, 22, 320.00, '2026-02-18 19:46:33', 0),
(1057, 252, 24, 340.00, '2026-02-18 19:46:33', 1),
(1058, 252, 22, 360.00, '2026-02-21 19:46:33', 0),
(1059, 252, 24, 380.00, '2026-02-23 07:46:33', 0),
(1060, 253, 23, 400.00, '2026-02-16 19:46:33', 0),
(1061, 253, 22, 425.00, '2026-02-19 19:46:33', 0),
(1062, 253, 23, 450.00, '2026-02-22 19:46:33', 0),
(1063, 255, 23, 1500.00, '2026-02-13 19:46:33', 0),
(1064, 255, 24, 1550.00, '2026-02-14 19:46:33', 0),
(1065, 255, 23, 1600.00, '2026-02-14 19:46:33', 1),
(1066, 255, 24, 1650.00, '2026-02-14 19:46:33', 1),
(1067, 255, 23, 1700.00, '2026-02-16 19:46:33', 0),
(1068, 255, 24, 1750.00, '2026-02-17 19:46:33', 0),
(1069, 255, 23, 1800.00, '2026-02-18 19:46:33', 1),
(1070, 255, 24, 1850.00, '2026-02-18 19:46:33', 1),
(1071, 255, 23, 1900.00, '2026-02-19 19:46:33', 0),
(1072, 255, 24, 1950.00, '2026-02-20 19:46:33', 0),
(1073, 255, 23, 2000.00, '2026-02-21 19:46:33', 1),
(1074, 255, 24, 2050.00, '2026-02-21 19:46:33', 1),
(1075, 255, 23, 2100.00, '2026-02-23 01:46:33', 0),
(1076, 255, 24, 2150.00, '2026-02-23 15:46:33', 0),
(1077, 256, 22, 200.00, '2026-02-18 19:46:33', 0),
(1078, 256, 24, 210.00, '2026-02-19 19:46:33', 0),
(1079, 256, 22, 220.00, '2026-02-19 19:46:33', 1),
(1080, 256, 24, 230.00, '2026-02-21 19:46:33', 0),
(1081, 256, 22, 240.00, '2026-02-23 13:46:33', 0),
(1082, 257, 23, 250.00, '2026-02-19 19:46:33', 0),
(1083, 257, 22, 265.00, '2026-02-21 19:46:33', 0),
(1084, 258, 23, 400.00, '2026-02-15 19:46:33', 0),
(1085, 258, 22, 425.00, '2026-02-16 19:46:33', 0),
(1086, 258, 23, 450.00, '2026-02-16 19:46:33', 1),
(1087, 258, 22, 475.00, '2026-02-16 19:46:33', 1),
(1088, 258, 23, 500.00, '2026-02-18 19:46:33', 0),
(1089, 258, 22, 525.00, '2026-02-20 19:46:33', 0),
(1090, 258, 23, 550.00, '2026-02-21 19:46:33', 1),
(1091, 258, 22, 575.00, '2026-02-23 11:46:33', 0),
(1092, 259, 23, 60.00, '2026-02-18 19:46:33', 0),
(1093, 259, 24, 65.00, '2026-02-19 19:46:33', 0),
(1094, 259, 23, 70.00, '2026-02-19 19:46:33', 1),
(1095, 259, 24, 75.00, '2026-02-19 19:46:33', 1),
(1096, 259, 23, 80.00, '2026-02-20 19:46:33', 0),
(1097, 259, 24, 85.00, '2026-02-21 19:46:33', 0),
(1098, 259, 23, 90.00, '2026-02-21 19:46:33', 1),
(1099, 259, 24, 95.00, '2026-02-21 19:46:33', 1),
(1100, 259, 23, 100.00, '2026-02-22 19:46:33', 0),
(1101, 259, 24, 105.00, '2026-02-23 15:46:33', 0),
(1102, 261, 22, 2000.00, '2026-02-08 19:46:33', 0),
(1103, 261, 24, 2100.00, '2026-02-09 19:46:33', 0),
(1104, 261, 23, 2200.00, '2026-02-10 19:46:33', 0),
(1105, 261, 22, 2300.00, '2026-02-11 19:46:33', 1),
(1106, 261, 24, 2400.00, '2026-02-11 19:46:33', 1),
(1107, 261, 22, 2500.00, '2026-02-11 19:46:33', 1),
(1108, 261, 23, 2600.00, '2026-02-13 19:46:33', 0),
(1109, 261, 24, 2700.00, '2026-02-14 19:46:33', 1),
(1110, 261, 23, 2800.00, '2026-02-14 19:46:33', 1),
(1111, 261, 22, 2900.00, '2026-02-15 19:46:33', 0),
(1112, 261, 24, 3000.00, '2026-02-16 19:46:33', 0),
(1113, 261, 23, 3100.00, '2026-02-17 19:46:33', 1),
(1114, 261, 24, 3200.00, '2026-02-17 19:46:33', 1),
(1115, 261, 22, 3300.00, '2026-02-18 19:46:33', 0),
(1116, 261, 24, 3400.00, '2026-02-19 19:46:33', 1),
(1117, 261, 22, 3500.00, '2026-02-19 19:46:33', 1),
(1118, 261, 23, 3600.00, '2026-02-20 19:46:33', 0),
(1119, 261, 24, 3700.00, '2026-02-21 19:46:33', 1),
(1120, 261, 22, 3800.00, '2026-02-21 19:46:33', 1),
(1121, 261, 23, 3900.00, '2026-02-23 13:46:33', 0),
(1122, 262, 22, 5000.00, '2026-02-11 19:46:33', 0),
(1123, 262, 24, 5200.00, '2026-02-12 19:46:33', 0),
(1124, 262, 22, 5400.00, '2026-02-12 19:46:33', 1),
(1125, 262, 24, 5600.00, '2026-02-12 19:46:33', 1),
(1126, 262, 22, 5800.00, '2026-02-14 19:46:33', 0),
(1127, 262, 24, 6000.00, '2026-02-15 19:46:33', 0),
(1128, 262, 22, 6200.00, '2026-02-16 19:46:33', 1),
(1129, 262, 24, 6400.00, '2026-02-16 19:46:33', 1),
(1130, 262, 22, 6600.00, '2026-02-18 19:46:33', 0),
(1131, 262, 24, 6800.00, '2026-02-19 19:46:33', 0),
(1132, 262, 22, 7000.00, '2026-02-20 19:46:33', 1),
(1133, 262, 24, 7200.00, '2026-02-20 19:46:33', 1),
(1134, 262, 22, 7400.00, '2026-02-21 19:46:33', 0),
(1135, 262, 24, 7600.00, '2026-02-22 19:46:33', 1),
(1136, 262, 22, 7800.00, '2026-02-23 15:46:33', 0),
(1137, 263, 23, 400.00, '2026-02-15 19:46:33', 0),
(1138, 263, 22, 425.00, '2026-02-17 19:46:33', 0),
(1139, 263, 23, 450.00, '2026-02-18 19:46:33', 1),
(1140, 263, 22, 475.00, '2026-02-22 19:46:33', 0),
(1141, 264, 23, 50000.00, '2026-02-15 19:52:46', 0),
(1142, 264, 24, 51000.00, '2026-02-16 19:52:46', 0),
(1143, 264, 23, 52000.00, '2026-02-16 19:52:46', 1),
(1144, 264, 24, 53000.00, '2026-02-16 19:52:46', 1),
(1145, 264, 23, 54000.00, '2026-02-19 19:52:46', 0),
(1146, 264, 24, 55000.00, '2026-02-20 19:52:46', 1),
(1147, 264, 23, 56000.00, '2026-02-22 19:52:46', 0),
(1148, 265, 24, 35000.00, '2026-02-13 19:52:46', 0),
(1149, 265, 22, 35500.00, '2026-02-14 19:52:46', 0),
(1150, 265, 24, 36000.00, '2026-02-14 19:52:46', 1),
(1151, 265, 22, 36500.00, '2026-02-14 19:52:46', 1),
(1152, 265, 24, 37000.00, '2026-02-17 19:52:46', 0),
(1153, 265, 22, 37500.00, '2026-02-18 19:52:46', 1),
(1154, 265, 24, 38000.00, '2026-02-18 19:52:46', 1),
(1155, 265, 22, 38500.00, '2026-02-21 19:52:46', 0),
(1156, 265, 24, 39000.00, '2026-02-23 07:52:46', 0),
(1157, 267, 23, 12000.00, '2026-02-11 19:52:46', 0),
(1158, 267, 24, 12200.00, '2026-02-12 19:52:46', 0),
(1159, 267, 23, 12400.00, '2026-02-12 19:52:46', 1),
(1160, 267, 24, 12600.00, '2026-02-12 19:52:46', 1),
(1161, 267, 23, 12800.00, '2026-02-14 19:52:46', 0),
(1162, 267, 24, 13000.00, '2026-02-15 19:52:46', 0),
(1163, 267, 23, 13200.00, '2026-02-16 19:52:46', 1),
(1164, 267, 24, 13400.00, '2026-02-16 19:52:46', 1),
(1165, 267, 23, 13600.00, '2026-02-17 19:52:46', 0),
(1166, 267, 24, 13800.00, '2026-02-18 19:52:46', 0),
(1167, 267, 23, 14000.00, '2026-02-19 19:52:46', 1),
(1168, 267, 24, 14200.00, '2026-02-19 19:52:46', 1),
(1169, 267, 23, 14400.00, '2026-02-20 19:52:46', 0),
(1170, 267, 24, 14600.00, '2026-02-21 19:52:46', 1),
(1171, 267, 23, 14800.00, '2026-02-21 19:52:46', 1),
(1172, 267, 24, 15000.00, '2026-02-22 19:52:46', 0),
(1173, 267, 23, 15200.00, '2026-02-23 09:52:46', 1),
(1174, 267, 24, 15400.00, '2026-02-23 16:52:46', 0),
(1175, 268, 22, 5500.00, '2026-02-16 19:52:46', 0),
(1176, 268, 24, 5600.00, '2026-02-17 19:52:46', 0),
(1177, 268, 22, 5700.00, '2026-02-17 19:52:46', 1),
(1178, 268, 24, 5800.00, '2026-02-17 19:52:46', 1),
(1179, 268, 22, 5900.00, '2026-02-18 19:52:46', 0),
(1180, 268, 24, 6000.00, '2026-02-19 19:52:46', 0),
(1181, 268, 22, 6100.00, '2026-02-20 19:52:46', 1),
(1182, 268, 24, 6200.00, '2026-02-20 19:52:46', 1),
(1183, 268, 22, 6300.00, '2026-02-21 19:52:46', 0),
(1184, 268, 24, 6400.00, '2026-02-23 01:52:46', 0),
(1185, 268, 22, 6500.00, '2026-02-23 13:52:46', 0),
(1186, 270, 24, 800.00, '2026-02-09 19:52:46', 0),
(1187, 270, 23, 825.00, '2026-02-10 19:52:46', 0),
(1188, 270, 22, 850.00, '2026-02-11 19:52:46', 0),
(1189, 270, 24, 875.00, '2026-02-11 19:52:46', 1),
(1190, 270, 23, 900.00, '2026-02-11 19:52:46', 1),
(1191, 270, 24, 925.00, '2026-02-11 19:52:46', 1),
(1192, 270, 23, 950.00, '2026-02-13 19:52:46', 0),
(1193, 270, 22, 975.00, '2026-02-14 19:52:46', 0),
(1194, 270, 24, 1000.00, '2026-02-14 19:52:46', 1),
(1195, 270, 22, 1025.00, '2026-02-14 19:52:46', 1),
(1196, 270, 23, 1050.00, '2026-02-15 19:52:46', 0),
(1197, 270, 24, 1075.00, '2026-02-16 19:52:46', 1),
(1198, 270, 23, 1100.00, '2026-02-16 19:52:46', 1),
(1199, 270, 24, 1125.00, '2026-02-18 19:52:46', 0),
(1200, 270, 22, 1150.00, '2026-02-19 19:52:46', 0),
(1201, 270, 23, 1175.00, '2026-02-20 19:52:46', 1),
(1202, 270, 22, 1200.00, '2026-02-20 19:52:46', 1),
(1203, 270, 24, 1225.00, '2026-02-21 19:52:46', 0),
(1204, 270, 23, 1250.00, '2026-02-21 19:52:46', 1),
(1205, 270, 22, 1275.00, '2026-02-21 19:52:46', 1),
(1206, 270, 24, 1300.00, '2026-02-23 11:52:46', 0),
(1207, 270, 23, 1325.00, '2026-02-23 17:52:46', 0),
(1208, 271, 22, 600.00, '2026-02-18 19:52:46', 0),
(1209, 271, 24, 625.00, '2026-02-20 19:52:46', 0),
(1210, 271, 22, 650.00, '2026-02-22 19:52:46', 0),
(1211, 272, 23, 900.00, '2026-02-19 19:52:46', 0),
(1212, 272, 22, 950.00, '2026-02-21 19:52:46', 0),
(1213, 273, 24, 300.00, '2026-02-17 19:52:46', 0),
(1214, 273, 22, 320.00, '2026-02-18 19:52:46', 0),
(1215, 273, 24, 340.00, '2026-02-18 19:52:46', 1),
(1216, 273, 22, 360.00, '2026-02-21 19:52:46', 0),
(1217, 273, 24, 380.00, '2026-02-23 07:52:46', 0),
(1218, 274, 23, 400.00, '2026-02-16 19:52:46', 0),
(1219, 274, 22, 425.00, '2026-02-19 19:52:46', 0),
(1220, 274, 23, 450.00, '2026-02-22 19:52:46', 0),
(1221, 276, 23, 1500.00, '2026-02-13 19:52:46', 0),
(1222, 276, 24, 1550.00, '2026-02-14 19:52:46', 0),
(1223, 276, 23, 1600.00, '2026-02-14 19:52:46', 1),
(1224, 276, 24, 1650.00, '2026-02-14 19:52:46', 1),
(1225, 276, 23, 1700.00, '2026-02-16 19:52:46', 0),
(1226, 276, 24, 1750.00, '2026-02-17 19:52:46', 0),
(1227, 276, 23, 1800.00, '2026-02-18 19:52:46', 1),
(1228, 276, 24, 1850.00, '2026-02-18 19:52:46', 1),
(1229, 276, 23, 1900.00, '2026-02-19 19:52:46', 0),
(1230, 276, 24, 1950.00, '2026-02-20 19:52:46', 0),
(1231, 276, 23, 2000.00, '2026-02-21 19:52:46', 1),
(1232, 276, 24, 2050.00, '2026-02-21 19:52:46', 1),
(1233, 276, 23, 2100.00, '2026-02-23 01:52:46', 0),
(1234, 276, 24, 2150.00, '2026-02-23 15:52:46', 0),
(1235, 277, 22, 200.00, '2026-02-18 19:52:46', 0),
(1236, 277, 24, 210.00, '2026-02-19 19:52:46', 0),
(1237, 277, 22, 220.00, '2026-02-19 19:52:46', 1),
(1238, 277, 24, 230.00, '2026-02-21 19:52:46', 0),
(1239, 277, 22, 240.00, '2026-02-23 13:52:46', 0),
(1240, 278, 23, 250.00, '2026-02-19 19:52:46', 0),
(1241, 278, 22, 265.00, '2026-02-21 19:52:46', 0),
(1242, 279, 23, 400.00, '2026-02-15 19:52:46', 0),
(1243, 279, 22, 425.00, '2026-02-16 19:52:46', 0),
(1244, 279, 23, 450.00, '2026-02-16 19:52:46', 1),
(1245, 279, 22, 475.00, '2026-02-16 19:52:46', 1),
(1246, 279, 23, 500.00, '2026-02-18 19:52:46', 0),
(1247, 279, 22, 525.00, '2026-02-20 19:52:46', 0),
(1248, 279, 23, 550.00, '2026-02-21 19:52:46', 1),
(1249, 279, 22, 575.00, '2026-02-23 11:52:46', 0),
(1250, 280, 23, 60.00, '2026-02-18 19:52:46', 0),
(1251, 280, 24, 65.00, '2026-02-19 19:52:46', 0),
(1252, 280, 23, 70.00, '2026-02-19 19:52:46', 1),
(1253, 280, 24, 75.00, '2026-02-19 19:52:46', 1),
(1254, 280, 23, 80.00, '2026-02-20 19:52:46', 0),
(1255, 280, 24, 85.00, '2026-02-21 19:52:46', 0),
(1256, 280, 23, 90.00, '2026-02-21 19:52:46', 1),
(1257, 280, 24, 95.00, '2026-02-21 19:52:46', 1),
(1258, 280, 23, 100.00, '2026-02-22 19:52:46', 0),
(1259, 280, 24, 105.00, '2026-02-23 15:52:46', 0),
(1260, 282, 22, 2000.00, '2026-02-08 19:52:46', 0),
(1261, 282, 24, 2100.00, '2026-02-09 19:52:46', 0),
(1262, 282, 23, 2200.00, '2026-02-10 19:52:46', 0),
(1263, 282, 22, 2300.00, '2026-02-11 19:52:46', 1),
(1264, 282, 24, 2400.00, '2026-02-11 19:52:46', 1),
(1265, 282, 22, 2500.00, '2026-02-11 19:52:46', 1),
(1266, 282, 23, 2600.00, '2026-02-13 19:52:46', 0),
(1267, 282, 24, 2700.00, '2026-02-14 19:52:46', 1),
(1268, 282, 23, 2800.00, '2026-02-14 19:52:46', 1),
(1269, 282, 22, 2900.00, '2026-02-15 19:52:46', 0),
(1270, 282, 24, 3000.00, '2026-02-16 19:52:46', 0),
(1271, 282, 23, 3100.00, '2026-02-17 19:52:46', 1),
(1272, 282, 24, 3200.00, '2026-02-17 19:52:46', 1),
(1273, 282, 22, 3300.00, '2026-02-18 19:52:46', 0),
(1274, 282, 24, 3400.00, '2026-02-19 19:52:46', 1),
(1275, 282, 22, 3500.00, '2026-02-19 19:52:46', 1),
(1276, 282, 23, 3600.00, '2026-02-20 19:52:46', 0),
(1277, 282, 24, 3700.00, '2026-02-21 19:52:46', 1),
(1278, 282, 22, 3800.00, '2026-02-21 19:52:46', 1),
(1279, 282, 23, 3900.00, '2026-02-23 13:52:46', 0),
(1280, 283, 22, 5000.00, '2026-02-11 19:52:46', 0),
(1281, 283, 24, 5200.00, '2026-02-12 19:52:46', 0),
(1282, 283, 22, 5400.00, '2026-02-12 19:52:46', 1),
(1283, 283, 24, 5600.00, '2026-02-12 19:52:46', 1),
(1284, 283, 22, 5800.00, '2026-02-14 19:52:46', 0),
(1285, 283, 24, 6000.00, '2026-02-15 19:52:46', 0),
(1286, 283, 22, 6200.00, '2026-02-16 19:52:46', 1),
(1287, 283, 24, 6400.00, '2026-02-16 19:52:46', 1),
(1288, 283, 22, 6600.00, '2026-02-18 19:52:46', 0),
(1289, 283, 24, 6800.00, '2026-02-19 19:52:46', 0),
(1290, 283, 22, 7000.00, '2026-02-20 19:52:46', 1),
(1291, 283, 24, 7200.00, '2026-02-20 19:52:46', 1),
(1292, 283, 22, 7400.00, '2026-02-21 19:52:46', 0),
(1293, 283, 24, 7600.00, '2026-02-22 19:52:46', 1),
(1294, 283, 22, 7800.00, '2026-02-23 15:52:46', 0);

-- --------------------------------------------------------

--
-- Rakenne taululle `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active_count` int(11) DEFAULT 0,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `icon`, `sort_order`, `active_count`, `logo`) VALUES
(1, 'Kiinteistöt', 'kiinteistot', 'Tontit, maa-alueet ja kiinteistöt', NULL, '🏠', 1, 3, NULL),
(2, 'Ajoneuvot', 'ajoneuvot', 'Autot, moottoripyörät ja muut ajoneuvot', NULL, '🚗', 2, 4, NULL),
(3, 'Elektroniikka', 'elektroniikka', 'Tietokoneet, puhelimet ja elektroniikka', NULL, '💻', 3, 3, NULL),
(4, 'Kodin tavarat', 'kodin-tavarat', 'Huonekalut ja kodin sisustus', NULL, '🏡', 4, 3, NULL),
(5, 'Urheilu', 'urheilu', 'Urheiluvälineet ja -varusteet', NULL, '⚽', 5, 3, NULL),
(6, 'Vaatteet', 'vaatteet', 'Vaatteet ja asusteet', NULL, '👕', 6, 3, NULL),
(7, 'Keräily', 'keraily', 'Keräilyesineet ja antiikki', NULL, '🎨', 7, 3, NULL),
(8, 'Muut', 'muut', 'Muut tuotteet', NULL, '📦', 8, 3, NULL),
(9, 'Maakunnittain', 'maakunnittain', 'Huutokauppakohteet maakunnittain eri puolilta Suomea.', NULL, '📍', 9, 3, NULL),
(10, 'Ajoneuvojen osat ja tarvikkeet', 'ajojneuvojen-osat-ja-tarvikkeet', 'Ajoneuvojen osat ja tarvikkeet', NULL, NULL, 10, 0, NULL),
(11, 'Työkoneet ja raskas kalusto', 'tyokoneet', 'Työkoneet, kaivinkoneet, traktorit ja raskas kalusto yrityksiltä ja konkurssipesiltä.', NULL, '🚜', 11, 3, NULL),
(12, 'Asunnot, mökit, toimitilat ja tontit', 'asunnot', 'Asunnot, mökit, toimitilat ja tontit huutokaupattavina kohteina.', NULL, '🏠', 12, 3, NULL),
(13, 'Harrastusvälineet ja vapaa-aika', 'harrastus', 'Harrastusvälineet, vapaa-ajan tuotteet ja liikuntavarusteet.', NULL, '⚽', 13, 3, NULL),
(14, 'Piha ja puutarha', 'piha', 'Piha- ja puutarhatarvikkeet, koneet ja kalusteet.', NULL, '🌳', 14, 3, NULL),
(15, 'Työkalut ja työkalusarjat', 'tyokalut', 'Työkalut, koneet ja ammattikäyttöön soveltuvat laitteet.', NULL, '🔧', 15, 4, NULL),
(16, 'Rakennustarvikkeet', 'rakennus', 'Rakennustarvikkeet, materiaalit ja rakennusalan tuotteet.', NULL, '🏗️', 16, 3, NULL),
(17, 'Sisustaminen ja koti', 'sisustus', 'Sisustustuotteet, huonekalut ja kodin tarvikkeet.', NULL, '🛋️', 17, 3, NULL),
(18, 'Kirjat', 'kirjat', NULL, NULL, NULL, 0, 0, NULL),
(19, 'Lelut ja pelit', 'lelut-ja-pelit', NULL, NULL, NULL, 0, 0, NULL),
(20, 'Tukkuerät', 'tukkuerat', 'Tukkuerät, varastojen tyhjennykset ja suuremmat erämyynnit.', NULL, '📦', 20, 3, NULL),
(21, 'Kulttuuri', 'kulttuuri', NULL, NULL, NULL, 0, 0, NULL),
(22, 'Perinteiset huutokaupat', 'perinteiset', 'Perinteiset fyysiset huutokaupat ja tapahtumat.', NULL, '⚖️', 22, 3, NULL),
(23, 'Ulosotto', 'ulosotto', 'Ulosottoviranomaisten myymät kohteet.', NULL, '⚖️', 23, 3, NULL),
(24, 'Konkurssipesät', 'konkurssi', 'Konkurssipesien realisoimat omaisuuserät.', NULL, '💼', 24, 3, NULL),
(25, 'Puolustusvoimat', 'puolustusvoimat', 'Puolustusvoimien huutokauppaamat ajoneuvot ja kalusto.', NULL, '🎖️', 25, 3, NULL),
(26, 'Metsähallitus', 'metsahallitus', 'Metsähallituksen huutokauppaamat kohteet ja omaisuus.', NULL, '🌲', 26, 3, NULL),
(27, 'Rahoitusyhtiöt', 'rahoitus', 'Rahoitusyhtiöiden realisoimat kohteet ja leasing-palautukset.', NULL, '💰', 27, 3, NULL),
(28, 'Julkinen sektori', 'julkinen', 'Julkisen sektorin myymät ajoneuvot ja kalusto.', NULL, '🏛️', 28, 3, NULL),
(29, 'Päättyvät', 'paattyvat', 'Pian päättyvät huutokohteet – viimeiset mahdollisuudet tarjota.', NULL, '⏰', 29, 3, NULL);

-- --------------------------------------------------------

--
-- Rakenne taululle `email_tokens`
--

CREATE TABLE `email_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `code` varchar(6) DEFAULT NULL,
  `type` enum('verification','magic_login') NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `email_tokens`
--

INSERT INTO `email_tokens` (`id`, `user_id`, `email`, `token`, `code`, `type`, `expires_at`, `used_at`, `ip_address`, `created_at`) VALUES
(1, 3, 'kiikka@mail.com', '42074ccce45842f68f0471d0f8b81cec69d2de2560c49025d002e4cf8e1e98c1', NULL, 'verification', '2026-02-17 03:36:51', NULL, '93.106.171.167', '2026-02-16 03:36:51'),
(2, 4, 'samu.kuitunen@huuto247.fi', '48f7da5b9ab45969f4c08d10fe305584568c7696731b68d7636904e8640203ec', NULL, 'verification', '2026-02-17 05:08:31', NULL, '93.106.171.167', '2026-02-16 05:08:31'),
(6, 8, 'jussikuisma@icloud.com', 'c49eba13d2a16a75857204a9fa191a8d784c41fdd84900c92132d4f3043c890f', NULL, 'verification', '2026-02-19 13:52:44', NULL, '87.95.68.172', '2026-02-18 13:52:44'),
(7, 9, 'mikko.koivisto@mail.com', '8a5587484458f72a6068147fab954596649abf48a08627fd46af43af567d0deb', NULL, 'verification', '2026-02-21 10:00:48', NULL, '176.72.89.190', '2026-02-20 10:00:48'),
(8, 10, 'tomi.r@mail.com', '477d122ebd60dfafbf64a6024d6a110be3f00a0aafc3048f27ea671bc5b488d5', NULL, 'verification', '2026-02-22 06:37:36', '2026-02-21 06:42:39', '176.72.89.190', '2026-02-21 06:37:36'),
(10, 12, 'jussikuukka@icloud.com', '1c0a147316ee98dbceaeba7dc10352221103fac4fb324ee87b2ee073c854e71a', NULL, 'verification', '2026-02-22 11:37:59', NULL, '84.253.200.38', '2026-02-21 11:37:59'),
(12, 40, 'info@rakennusliikesuvenkari.fi', 'f3eff517b5919b67d19d0beb6ca2720c53598a4d73439ed5ff7b69762c0d09fd', NULL, 'verification', '2026-02-24 20:45:30', NULL, '85.76.101.43', '2026-02-23 20:45:30'),
(13, 41, 'aleksiluoto@icloud.com', '9a9e78aa4aa4491360a3184b3e95a90629d9f79f1b27731aba89722c0535931e', NULL, 'verification', '2026-02-24 20:48:07', '2026-02-23 20:48:24', '164.215.34.172', '2026-02-23 20:48:07'),
(15, 43, 'kiikka.jukka@gmail.com', 'b73c76a05c0b86cdbeb5592464b22342e29eef3824bee7d7eed4fc5914d67dd3', NULL, 'verification', '2026-02-24 22:10:24', '2026-02-23 22:11:25', '37.33.244.231', '2026-02-23 22:10:24'),
(16, 44, 'sisu.jaakkola2@gmail.com', 'a97814e8e1f1b118a24abfd5e4f8841bbbe1bea3968af0c5fd5a1f34137be8fc', NULL, 'verification', '2026-04-07 05:48:14', '2026-04-06 05:48:44', '91.154.1.127', '2026-04-06 05:48:14'),
(17, 45, 'exvator.oy.ab@gmail.com', 'ad5bff3d5957032a7357f1a638f4f54601b494cd604dccf773e98f7cf28c014c', NULL, 'verification', '2026-04-07 10:21:19', '2026-04-06 10:21:52', '193.160.100.62', '2026-04-06 10:21:19');

-- --------------------------------------------------------

--
-- Rakenne taululle `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip_address`, `email`, `success`, `user_agent`, `created_at`) VALUES
(1, '176.72.89.190', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 07:31:02'),
(2, '176.72.89.190', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 08:14:12'),
(3, '176.72.89.190', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 08:39:45'),
(4, '193.160.100.11', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 09:53:09'),
(5, '193.160.100.11', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 10:09:14'),
(6, '193.160.101.129', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 19:14:23'),
(7, '193.160.101.113', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 00:52:44'),
(8, '84.253.200.38', 'info@huuto247.fi', 0, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-22 09:17:18'),
(9, '84.253.200.38', 'info@huuto247.fi', 0, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-22 09:56:07'),
(10, '37.33.244.231', 'kiikka@mail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 19:59:10'),
(11, '164.215.34.172', 'aleksiluoto@icloud.com', 0, 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2026-02-23 20:45:58'),
(12, '164.215.34.172', 'aleksiluoto@icloud.com', 1, 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2026-02-23 20:48:39'),
(13, '37.33.244.231', 'kiikka@mail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 21:12:42'),
(14, '37.33.244.231', 'teboilruskeasuo@gmail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 22:02:14'),
(15, '37.33.244.231', 'kiikka.jukka@gmail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 22:10:33'),
(16, '37.33.244.231', 'kiikka@mail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 23:08:39'),
(17, '37.33.244.231', 'kiikka.jukka@gmail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 23:08:47'),
(18, '217.145.225.174', 'dallisongude83@hotmail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 CCleaner/130.0.0.0', '2026-03-19 02:01:38'),
(19, '91.154.1.127', 'kiikka.jukka@gmail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 05:47:40'),
(20, '37.136.102.17', 'sisu.jaakkola2@gmail.com', 1, 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_3_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.151 Mobile/15E148 Safari/604.1', '2026-04-06 05:49:08'),
(21, '91.154.1.127', 'sisu.jaakkola2@gmail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 05:49:50'),
(22, '91.154.1.127', 'sisu.jaakkola2@gmail.com', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 05:49:54'),
(23, '91.154.1.127', 'sisu.jaakkola2@gmail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 05:49:57'),
(24, '193.160.100.62', 'exvator.oy.ab@gmail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:22:05'),
(25, '91.154.1.127', 'info@huuto247.fi', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 16:06:45'),
(26, '91.154.1.127', 'info@huuto247.fi', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 16:06:50'),
(27, '91.154.1.127', 'info@huuto247.fi', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 16:06:55'),
(28, '91.154.1.127', 'info@huuto247.fi', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 16:06:59'),
(29, '91.154.1.127', 'sisu.jaakkola2@gmail.com', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 16:07:13');

-- --------------------------------------------------------

--
-- Rakenne taululle `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `window_start` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `identifier`, `action_type`, `attempts`, `window_start`, `created_at`) VALUES
(29, '91.154.1.127:info@huuto247.fi', 'login', 4, '2026-04-06 19:06:45', '2026-04-06 19:06:45'),
(30, '91.154.1.127:sisu.jaakkola2@gmail.com', 'login', 1, '2026-04-06 19:07:13', '2026-04-06 19:07:13');

-- --------------------------------------------------------

--
-- Rakenne taululle `seller_profiles`
--

CREATE TABLE `seller_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_sales` int(11) DEFAULT 0,
  `active_auctions` int(11) DEFAULT 0,
  `response_rate` decimal(5,2) DEFAULT 100.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_verified` tinyint(1) DEFAULT 0,
  `status` enum('pending','active','banned','suspended') DEFAULT 'pending',
  `google_id` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `created_at`, `updated_at`, `email_verified`, `status`, `google_id`, `avatar_url`, `last_login_at`) VALUES
(1, 'jussi1907', 'samu@huutdo247.fi', '$2y$10$jw3jHFTKFOKYRseor3ogYOb2j.sSA3AJzXdRK5/LKvsBaZa4gN7Di', 'Jussi', NULL, '2026-02-15 12:51:49', '2026-02-23 20:53:33', 0, 'pending', NULL, NULL, NULL),
(2, 'Ulosottolaitos', 'demo@huuto.local', '$2y$10$n.XhrnWYc9LuNBQ6mvD0fekN.JJf.jjEcsYZUn6Bx4kMt2AzT4Lxq', 'Ulosottolaitos', NULL, '2026-02-15 12:51:49', '2026-02-20 17:20:57', 0, 'pending', NULL, NULL, NULL),
(3, 'jessekuisma', 'kidikka@mail.com', '$2y$10$Oq7Y8x8sv.yk0loZUBavAuwYyD8xVHSvRNi8o6Js8aUxHvzZ6/UKO', 'Jesse Kuisma', NULL, '2026-02-16 03:36:51', '2026-02-23 20:53:29', 1, 'pending', NULL, NULL, '2026-02-23 19:59:10'),
(4, 'jessekuisma9239', 'samu.kuditunen@huuto247.fi', '$2y$10$nSsFm5PNMjTNTr61sKXLfOCQLaTuyy.nyaCtYaOmTstU2gLJR5UEi', 'Jesse Kuisma', NULL, '2026-02-16 05:08:31', '2026-02-23 20:53:26', 0, 'pending', NULL, NULL, NULL),
(8, 'jessekuisma7660', 'djussikuisma@icloud.com', '$2y$10$XnwnNdGp/4D6aJHPL0G6Yu5WjYEO4KoO9P6xYWMjWtW9v7CBvZTZW', 'Jesse Kuisma', NULL, '2026-02-18 13:52:44', '2026-02-23 20:53:21', 0, 'pending', NULL, NULL, NULL),
(9, 'jarikuisma', 'midkko.koivisto@mail.com', '$2y$10$7b.YmvfJMp09Fd/kvMAR2OCzy8FjyHAbcexik5/bNy6YBziwiQxJG', 'jari kuisma', NULL, '2026-02-20 10:00:48', '2026-02-23 20:53:15', 0, 'pending', NULL, NULL, NULL),
(10, 'kuismajussi', 'tovmi.r@mail.com', '$2y$10$WA77SELRBx/tPXdRIy1MmeS9vetJgQHzv0XKjOvUsLGGP0JCMLd8O', 'Kuisma Jussi', NULL, '2026-02-21 06:37:36', '2026-02-23 20:52:59', 1, 'active', NULL, NULL, NULL),
(11, 'jussikuukka', 'tomi.rudmmukainen@icloud.com', '$2y$10$acDsatUuUd5Q1CNQPoYjVeuuk4WrGoitbgAktJzJZYJaTWVXy5j9m', 'Jussi Kuukka', NULL, '2026-02-21 11:35:41', '2026-02-23 20:53:41', 0, 'pending', NULL, NULL, NULL),
(12, 'jussikuukka1953', 'jussddikuukka@icloud.com', '$2y$10$IWG0uXndVTdvd9pZEI4WmuknsvSsBS1akXG8zqghjI.t5rnEeqKDq', 'Jussi Kuukka', NULL, '2026-02-21 11:37:59', '2026-02-23 20:53:07', 0, 'pending', NULL, NULL, NULL),
(22, 'jari_m', 'jari.m@example.com', '$2y$10$doRst.f.4Mh2pj/YKVtPH.BYFEFJltpP7vMD2FgpdoYaaUz9VSSSW', 'Jari Mäkelä', '+358 40 123 4567', '2026-02-23 19:23:01', '2026-02-23 19:47:55', 1, 'active', NULL, NULL, '2026-02-22 00:42:44'),
(23, 'anna_k', 'anna.k@example.com', '$2y$10$4UTE4FzkmBZDtbNqUJZ/j.ppGOU.LC8OHdSC5c9QI6ZWH6TwMvpN6', 'Anna Korhonen', '+358 45 234 5678', '2026-02-23 19:23:01', '2026-02-23 19:47:43', 1, 'active', NULL, NULL, '2026-02-22 00:53:44'),
(24, 'mikko_v', 'mikko.v@example.com', '$2y$10$bDAxBiDGc0i5py9eUxwn3urN9FMaw4E/zJ6o5iV.eODjfg.6b7bVK', 'Mikko Virtanen', '+358 50 345 6789', '2026-02-23 19:23:01', '2026-02-23 19:47:49', 1, 'active', NULL, NULL, '2026-02-22 10:52:44'),
(40, 'jussikuisma', 'info@rakennusliikesuvenkari.fi', '$2y$10$VC6c.q.gXQ9UDxnWz/shH.EPFZO.bAQuZ7QSxYK05K5NEBVhVCjq.', 'Jussi kuisma', NULL, '2026-02-23 20:45:30', '2026-02-23 20:45:30', 0, 'pending', NULL, NULL, NULL),
(41, 'anttinelisantti', 'aleksiluoto@icloud.com', '$2y$10$EzQT73ZFfxJDsgrSn6j3sONgjDeMQc93DKddY7xMJywmc30I7dS2G', 'Antti Nelisantti', NULL, '2026-02-23 20:48:07', '2026-02-23 20:48:39', 1, 'active', NULL, NULL, '2026-02-23 20:48:39'),
(42, 'tomirannisto', 'tomi.rummukainen@icloud.com', '$2y$10$lAbprbzf1VtdWAqne41re.7hRcd4nT47M5SK5XHs7B6obcYBRGusu', 'Tomi Rannisto', NULL, '2026-02-23 22:02:43', '2026-02-23 22:02:43', 0, 'pending', NULL, NULL, NULL),
(43, 'kiikkamikael', 'kiikka.jukka@gmail.com', '$2y$10$8QFEtbV/wrSdh9x/.ef6bu175HZekY4OLd8SCjwRC.EQ9.R/6wPvG', 'Kiikka Mikael', NULL, '2026-02-23 22:10:24', '2026-02-23 23:08:47', 1, 'active', NULL, NULL, '2026-02-23 23:08:47'),
(44, 'kiikkajukka', 'sisu.jaakkola2@gmail.com', '$2y$10$RhLMBsOgOMfm28D19lAYv.xmAEWIPH7ekinY0S6uo3PpQy5KMg0dW', 'kiikka jukka', NULL, '2026-04-06 05:48:14', '2026-04-06 16:07:13', 1, 'active', NULL, NULL, '2026-04-06 16:07:13'),
(45, 'sisujaskala', 'exvator.oy.ab@gmail.com', '$2y$10$f2YxcV0BsQzIR5oDRE2jGOX7B421btrl2tA1plNsfMUzC86F94hK6', 'sisu jaskala', NULL, '2026-04-06 10:21:19', '2026-04-06 10:22:05', 1, 'active', NULL, NULL, '2026-04-06 10:22:05');

-- --------------------------------------------------------

--
-- Rakenne taululle `user_favourites`
--

CREATE TABLE `user_favourites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `watchlist`
--

INSERT INTO `watchlist` (`id`, `user_id`, `auction_id`, `created_at`) VALUES
(19, 22, 183, '2026-02-23 19:28:17'),
(20, 22, 186, '2026-02-23 19:28:17'),
(21, 23, 181, '2026-02-23 19:28:17'),
(22, 23, 184, '2026-02-23 19:28:17'),
(23, 24, 182, '2026-02-23 19:28:17'),
(24, 24, 186, '2026-02-23 19:28:17'),
(25, 22, 204, '2026-02-23 19:46:00'),
(26, 22, 207, '2026-02-23 19:46:00'),
(27, 23, 201, '2026-02-23 19:46:00'),
(28, 23, 205, '2026-02-23 19:46:00'),
(29, 24, 202, '2026-02-23 19:46:00'),
(30, 24, 207, '2026-02-23 19:46:00'),
(31, 22, 225, '2026-02-23 19:46:23'),
(32, 22, 228, '2026-02-23 19:46:23'),
(33, 23, 222, '2026-02-23 19:46:23'),
(34, 23, 226, '2026-02-23 19:46:23'),
(35, 24, 223, '2026-02-23 19:46:23'),
(36, 24, 228, '2026-02-23 19:46:23'),
(37, 22, 246, '2026-02-23 19:46:33'),
(38, 22, 249, '2026-02-23 19:46:33'),
(39, 23, 243, '2026-02-23 19:46:33'),
(40, 23, 247, '2026-02-23 19:46:33'),
(41, 24, 244, '2026-02-23 19:46:33'),
(42, 24, 249, '2026-02-23 19:46:33'),
(43, 22, 267, '2026-02-23 19:52:46'),
(44, 22, 270, '2026-02-23 19:52:46'),
(45, 23, 264, '2026-02-23 19:52:46'),
(46, 23, 268, '2026-02-23 19:52:46'),
(47, 24, 265, '2026-02-23 19:52:46'),
(48, 24, 270, '2026-02-23 19:52:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_end_time` (`end_time`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status_end_time` (`status`,`end_time`),
  ADD KEY `idx_category_status_end` (`category_id`,`status`,`end_time`),
  ADD KEY `idx_featured` (`featured`);

--
-- Indexes for table `auction_images`
--
ALTER TABLE `auction_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auction` (`auction_id`),
  ADD KEY `idx_auction_primary_sort` (`auction_id`,`is_primary`,`sort_order`);

--
-- Indexes for table `auction_metadata`
--
ALTER TABLE `auction_metadata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_auction_field` (`auction_id`,`field_name`),
  ADD KEY `idx_auction` (`auction_id`),
  ADD KEY `idx_field` (`field_name`),
  ADD KEY `idx_auction_field` (`auction_id`,`field_name`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_type`,`resource_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auction` (`auction_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_amount` (`amount`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_parent` (`parent_id`);

--
-- Indexes for table `email_tokens`
--
ALTER TABLE `email_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_email` (`ip_address`,`email`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_window_start` (`window_start`),
  ADD KEY `idx_identifier_action_type` (`identifier`,`action_type`);

--
-- Indexes for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_active` (`active_auctions`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `user_favourites`
--
ALTER TABLE `user_favourites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_auction` (`user_id`,`auction_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_auction` (`auction_id`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_watch` (`user_id`,`auction_id`),
  ADD KEY `auction_id` (`auction_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=289;

--
-- AUTO_INCREMENT for table `auction_images`
--
ALTER TABLE `auction_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `auction_metadata`
--
ALTER TABLE `auction_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1299;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `email_tokens`
--
ALTER TABLE `email_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `user_favourites`
--
ALTER TABLE `user_favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auctions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Rajoitteet taululle `auction_images`
--
ALTER TABLE `auction_images`
  ADD CONSTRAINT `auction_images_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `auction_metadata`
--
ALTER TABLE `auction_metadata`
  ADD CONSTRAINT `auction_metadata_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Rajoitteet taululle `email_tokens`
--
ALTER TABLE `email_tokens`
  ADD CONSTRAINT `email_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD CONSTRAINT `seller_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `user_favourites`
--
ALTER TABLE `user_favourites`
  ADD CONSTRAINT `fk_user_favourites_auction` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_favourites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Rajoitteet taululle `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
