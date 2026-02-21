-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 21, 2026 at 12:49 AM
-- Server version: 10.11.15-MariaDB-cll-lve
-- PHP Version: 8.4.17

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
-- Table structure for table `auctions`
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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `map_coordinates` varchar(50) DEFAULT NULL,
  `seller_commitment` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`id`, `user_id`, `category_id`, `title`, `description`, `starting_price`, `current_price`, `reserve_price`, `buy_now_price`, `bid_increment`, `start_time`, `end_time`, `status`, `views`, `location`, `condition_description`, `created_at`, `updated_at`, `map_coordinates`, `seller_commitment`) VALUES
(1, 2, 1, 'Vanha rahapussi - keräilijän kohde #5417', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 172.00, 172.00, NULL, 344.00, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 8, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 15:32:39', NULL, 0),
(2, 2, 1, 'Polkupyörä 28\" - toimiva kunto #9478', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 19.00, 49.00, NULL, 98.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:03:31', NULL, 0),
(3, 2, 1, 'Laadukas sohva - mukava ja hyväkuntoinen #1153', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 447.00, 457.00, NULL, 914.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:16:51', NULL, 0),
(5, 2, 2, 'Polkupyörä 28\" - toimiva kunto #3029', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 43.00, 53.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:14:06', NULL, 0),
(6, 2, 2, 'Talonrakennustarvikkeita - iso erä #1900', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 455.00, 495.00, NULL, 990.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:53:01', NULL, 0),
(7, 2, 3, 'Laadukas sohva - mukava ja hyväkuntoinen #7942', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 298.00, 298.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:46:37', NULL, 0),
(8, 2, 3, 'Polkupyörä 28\" - toimiva kunto #4707', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 494.00, 514.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(9, 2, 3, 'Puutarhatyökalut 15 kpl - käytetty #4816', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 429.00, 469.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(10, 2, 4, 'Polkupyörä 28\" - toimiva kunto #5201', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 129.00, 159.00, NULL, 318.00, 1.00, '2026-02-15 12:51:49', '2026-03-08 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(11, 2, 4, 'Polkupyörä 28\" - toimiva kunto #9122', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 147.00, 197.00, NULL, 394.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 21:50:59', NULL, 0),
(12, 2, 4, 'Puutarhatyökalut 15 kpl - käytetty #3489', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 134.00, 184.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 4, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 05:18:13', NULL, 0),
(13, 2, 5, 'Talonrakennustarvikkeita - iso erä #3909', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 228.00, 248.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:35:38', NULL, 0),
(14, 2, 5, 'Talonrakennustarvikkeita - iso erä #2260', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 84.00, 94.00, NULL, 188.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(15, 2, 5, 'Vanha rahapussi - keräilijän kohde #8909', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 428.00, 448.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-27 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:57:14', NULL, 0),
(16, 2, 6, 'Puutarhatyökalut 15 kpl - käytetty #2822', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 339.00, 369.00, NULL, 738.00, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:47:35', NULL, 0),
(17, 2, 6, 'Vanha rahapussi - keräilijän kohde #7381', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 216.00, 266.00, NULL, 532.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(18, 2, 6, 'Polkupyörä 28\" - toimiva kunto #6692', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 158.00, 158.00, NULL, 316.00, 1.00, '2026-02-15 12:51:49', '2026-03-14 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(19, 2, 7, 'Talonrakennustarvikkeita - iso erä #1014', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 126.00, 126.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:33:30', NULL, 0),
(20, 2, 7, 'Vanha rahapussi - keräilijän kohde #3164', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 433.00, 453.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:40:59', NULL, 0),
(21, 2, 7, 'Vanha rahapussi - keräilijän kohde #7228', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 498.00, 538.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(22, 2, 8, 'Talonrakennustarvikkeita - iso erä #1272', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 428.00, 448.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 4, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 14:37:47', NULL, 0),
(24, 2, 8, 'Puutarhatyökalut 15 kpl - käytetty #4430', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 281.00, 291.00, NULL, 582.00, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:28:47', NULL, 0),
(25, 2, 9, 'Talonrakennustarvikkeita - iso erä #7271', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 260.00, 310.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(26, 2, 9, 'Laadukas sohva - mukava ja hyväkuntoinen #9546', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 31.00, 61.00, NULL, 122.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 00:42:55', NULL, 0),
(27, 2, 9, 'Vanha rahapussi - keräilijän kohde #1911', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 493.00, 513.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:36:14', NULL, 0),
(28, 2, 11, 'Talonrakennustarvikkeita - iso erä #1584', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 194.00, 214.00, NULL, 428.00, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 17:04:25', NULL, 0),
(29, 2, 11, 'Vanha rahapussi - keräilijän kohde #4643', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 288.00, 328.00, NULL, 656.00, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:39:09', NULL, 0),
(30, 2, 11, 'Laadukas sohva - mukava ja hyväkuntoinen #1453', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 334.00, 334.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 3, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 15:10:03', NULL, 0),
(31, 2, 12, 'Polkupyörä 28\" - toimiva kunto #7440', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 448.00, 498.00, NULL, 996.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:34:08', NULL, 0),
(32, 2, 12, 'Puutarhatyökalut 15 kpl - käytetty #6028', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 239.00, 289.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-22 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:37:39', NULL, 0),
(33, 2, 12, 'Polkupyörä 28\" - toimiva kunto #4031', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 385.00, 395.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-02 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:49:49', NULL, 0),
(34, 2, 13, 'Talonrakennustarvikkeita - iso erä #9234', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 144.00, 144.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-22 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:40:49', NULL, 0),
(35, 2, 13, 'Puutarhatyökalut 15 kpl - käytetty #1820', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 354.00, 394.00, NULL, 788.00, 1.00, '2026-02-15 12:51:49', '2026-03-07 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(36, 2, 13, 'Puutarhatyökalut 15 kpl - käytetty #9896', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 426.00, 426.00, NULL, 852.00, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:39:50', NULL, 0),
(37, 2, 14, 'Talonrakennustarvikkeita - iso erä #1431', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 50.00, 70.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:34:43', NULL, 0),
(38, 2, 14, 'Vanha rahapussi - keräilijän kohde #7479', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 42.00, 62.00, NULL, 124.00, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', NULL, 0),
(39, 2, 14, 'Laadukas sohva - mukava ja hyväkuntoinen #9079', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 40.00, 60.00, NULL, 120.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 02:20:48', NULL, 0),
(40, 2, 15, 'Puutarhatyökalut 15 kpl - käytetty #1283', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 362.00, 392.00, NULL, 784.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:49:50', NULL, 0),
(41, 2, 15, 'Vanha rahapussi - keräilijän kohde #1965', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 17.00, 47.00, NULL, 94.00, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:42:41', NULL, 0),
(42, 2, 15, 'Polkupyörä 28\" - toimiva kunto #5997', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 392.00, 422.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-10 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 17:10:20', NULL, 0),
(43, 2, 16, 'Polkupyörä 28\" - toimiva kunto #7968', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 145.00, 195.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:02', NULL, 0),
(44, 2, 16, 'Puutarhatyökalut 15 kpl - käytetty #1004', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 70.00, 70.00, NULL, 140.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 16:13:11', NULL, 0),
(45, 2, 16, 'Polkupyörä 28\" - toimiva kunto #1876', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 16.00, 16.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:04:48', NULL, 0),
(46, 2, 17, 'Talonrakennustarvikkeita - iso erä #3389', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 59.00, 99.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:09:46', NULL, 0),
(47, 2, 17, 'Talonrakennustarvikkeita - iso erä #9343', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 491.00, 521.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:37:59', NULL, 0),
(48, 2, 17, 'Vanha rahapussi - keräilijän kohde #1835', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 416.00, 436.00, NULL, 872.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:31:41', NULL, 0),
(49, 2, 20, 'Laadukas sohva - mukava ja hyväkuntoinen #6821', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 128.00, 138.00, NULL, 276.00, 1.00, '2026-02-15 12:51:49', '2026-03-06 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:35:20', NULL, 0),
(50, 2, 20, 'Laadukas sohva - mukava ja hyväkuntoinen #2268', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 317.00, 337.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-19 12:51:49', 'active', 13, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 15:05:22', NULL, 0),
(51, 2, 20, 'Talonrakennustarvikkeita - iso erä #7169', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 79.00, 89.00, NULL, 178.00, 1.00, '2026-02-15 12:51:49', '2026-03-17 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:41:24', NULL, 0),
(52, 2, 22, 'Vanha rahapussi - keräilijän kohde #4851', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 267.00, 287.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:38', NULL, 0),
(53, 2, 22, 'Talonrakennustarvikkeita - iso erä #6353', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 255.00, 285.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:04:29', NULL, 0),
(54, 2, 22, 'Vanha rahapussi - keräilijän kohde #1313', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 205.00, 255.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:56', NULL, 0),
(55, 2, 23, 'Laadukas sohva - mukava ja hyväkuntoinen #4829', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 481.00, 521.00, NULL, 1042.00, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:26:27', NULL, 0),
(56, 2, 23, 'Laadukas sohva - mukava ja hyväkuntoinen #7022', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 318.00, 318.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-02 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:20', NULL, 0),
(57, 2, 23, 'Vanha rahapussi - keräilijän kohde #1669', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 374.00, 414.00, NULL, 828.00, 1.00, '2026-02-15 12:51:49', '2026-03-04 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:40:02', NULL, 0),
(58, 2, 24, 'Polkupyörä 28\" - toimiva kunto #8481', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 143.00, 163.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:40:37', NULL, 0),
(59, 2, 24, 'Talonrakennustarvikkeita - iso erä #3308', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 45.00, 85.00, NULL, 170.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 11, NULL, NULL, '2026-02-15 12:51:49', '2026-02-20 17:00:17', NULL, 0),
(60, 2, 24, 'Talonrakennustarvikkeita - iso erä #8269', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 311.00, 321.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-26 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:48:13', NULL, 0),
(61, 2, 25, 'Vanha rahapussi - keräilijän kohde #2747', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 157.00, 207.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:35:02', NULL, 0),
(62, 2, 25, 'Puutarhatyökalut 15 kpl - käytetty #2171', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 39.00, 79.00, NULL, 158.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:42:04', NULL, 0),
(63, 2, 25, 'Talonrakennustarvikkeita - iso erä #4808', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 80.00, 130.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:45:14', NULL, 0),
(64, 2, 26, 'Polkupyörä 28\" - toimiva kunto #6213', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 386.00, 406.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-08 12:51:49', 'active', 3, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 17:09:19', NULL, 0),
(65, 2, 26, 'Polkupyörä 28\" - toimiva kunto #2746', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 499.00, 499.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 16:45:17', NULL, 0),
(66, 2, 26, 'Vanha rahapussi - keräilijän kohde #7265', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 301.00, 351.00, NULL, 702.00, 1.00, '2026-02-15 12:51:49', '2026-02-27 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:41:30', NULL, 0),
(67, 2, 27, 'Polkupyörä 28\" - toimiva kunto #5658', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 348.00, 398.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-04 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:42:17', NULL, 0),
(68, 2, 27, 'Vanha rahapussi - keräilijän kohde #9560', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 336.00, 366.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-26 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:46:12', NULL, 0),
(69, 2, 27, 'Vanha rahapussi - keräilijän kohde #3019', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 47.00, 87.00, NULL, 174.00, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:55:17', NULL, 0),
(70, 2, 28, 'Laadukas sohva - mukava ja hyväkuntoinen #1971', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 67.00, 77.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:36:32', NULL, 0),
(71, 2, 28, 'Polkupyörä 28\" - toimiva kunto #4417', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 472.00, 492.00, NULL, 984.00, 1.00, '2026-02-15 12:51:49', '2026-03-07 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:38:55', NULL, 0),
(72, 2, 28, 'Laadukas sohva - mukava ja hyväkuntoinen #8805', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 361.00, 401.00, NULL, 802.00, 1.00, '2026-02-15 12:51:49', '2026-03-06 12:51:49', 'active', 3, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:55:57', NULL, 0),
(73, 2, 29, 'Talonrakennustarvikkeita - iso erä #9805', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 480.00, 480.00, NULL, 960.00, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:44:33', NULL, 0),
(74, 2, 29, 'Laadukas sohva - mukava ja hyväkuntoinen #4203', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 469.00, 509.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 04:34:31', NULL, 0),
(75, 2, 29, 'Talonrakennustarvikkeita - iso erä #4566', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: fair\r\nSijainti: Pohjois-Pohjanmaa\r\nLisätietoja: Ota yhteyttä myyjään.', 248.00, 268.00, NULL, 536.00, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 6, '', '', '2026-02-15 12:51:49', '2026-02-20 22:33:59', NULL, 0),
(76, 2, 2, 'Harley Davidson- custom moottoripyörä', 'Myyntiin hyväkuntoinen harrikka.', 4000.00, 4000.00, 6000.00, 5500.00, 100.00, '2026-02-15 13:54:02', '2026-02-16 13:54:02', 'active', 17, 'Harjavalta', 'Hyvä', '2026-02-15 13:54:02', '2026-02-18 14:37:01', NULL, 0),
(77, 2, 15, 'Makita VC2512L (2023) + Kärcher VC 3 (ERP), 2 imuria', 'Myydään\r\nMakita VC2512L (2023) + Kärcher VC 3 (ERP) – 2 imuria. Toimivuus testattu Kone Korpelan korjaamolla.\r\n\r\n\r\n\r\n1) Makita VC2512L – L-luokan ammattimärkä-/kuivaimuri:\r\n\r\n● Malli: VC2512L\r\n● Valmistettu: 06/2023\r\n● Teho: 1000 W\r\n● Työkalupistorasia: max. 3600 W\r\n● Ilmavirta: 3600 l/min\r\n● Alipaine: 210 mbar\r\n● IP24\r\n● Paino: 8,0 kg\r\n● Pölyluokka: L\r\n\r\n\r\n\r\nOminaisuudet:\r\n\r\n● Automaattikäynnistys työkalulle\r\n● Märkä- ja kuivaimuri\r\n● Pyörällinen säiliörakenne\r\n● Ammattikäyttöön\r\n\r\n\r\n\r\n2) Kärcher VC 3 (ERP):\r\n\r\n● Malli: VC 3 (ERP)\r\n● 700 W (max. 750 W)\r\n● 220–240 V\r\n● 50/60 Hz\r\n\r\n\r\n\r\nMukaan:\r\n\r\n● Letku\r\n● Putki\r\n● Kärcher lattiasuulake\r\n\r\n\r\n\r\n\r\n\r\nKohde voidaan toimittaa Kiitolinjan, autonkuljetusauton tai matkahuollon jne. kanssa riippuen kohteesta, ostajan maksaessa kaikki kuljetus, lähetys ja pakkaus kulut. Voidaan toimittaa myös Manner-Suomen ulkopuolelle lisäkustannusta vastaan. Lähettämästämme noutamatta jätetystä kohteesta / kohteista veloitamme saman suuruisen toimituskulun kuin kohteessa on ollut sekä palautuksesta meille että uudelleen lähetyksestä. Kohde tulee noutaa 3 arkivuorokauden sisällä kohteen päättymisestä tai ilmoituksessa kerrottuna ajankohtana (niissä kohteissa joissa nouto on mahdollinen). Tämän jälkeen veloitamme säilytyksestä 10 €/vrk.\r\n\r\n\r\n\r\nToimituskuluun sisältyy:\r\n\r\n\r\n\r\nPakkausmateriaalit\r\n\r\nPakkaus (henkilöstökulut)\r\n\r\nLähetyslappujen teko\r\n\r\nMatkahuolto noutaa paketit meiltä\r\n\r\nItse kuljetus\r\n\r\njne.\r\n\r\nToimituskulut sisältävät myös valtiolle menevän arvonlisäveron 25,5 %\r\n\r\nNäitä kuluja ei saa sisällytettyä tuotteen hintaa huutokaupassa niin kuin verkkokaupat tekevät, koska hinta ei ole kiinteä. Huutohinta ja toimituskulut ovat tuotteen kokonaishinta toimitettuna arvonlisäveroineen.\r\n\r\nUseampi tuote voidaan pakata samaan pakettiin\r\n\r\n\r\n\r\nAA Realisointi Oy\r\n\r\nInkereentie 1021\r\n\r\n25190 Pertteli (Salo)\r\n\r\nViat ja muut havainnot\r\nTuotteen huutokauppailmoitus on tehty myyjän havaintoihin perustuen, tuotteessa saattaa olla piileviä vikoja tai virheitä, mitä myyjä ei ole huutokauppailmoitusta laatiessa havainnut.', 80.00, 90.00, 60.00, 100.00, 1.00, '2026-02-15 15:35:37', '2026-02-22 15:35:37', 'active', 9, 'Saloa', 'Hyvää', '2026-02-15 15:35:37', '2026-02-18 14:53:27', NULL, 0),
(78, 2, 8, 'Vannesetti', 'Myynnissä neljä alumiinivannetta, joissa on hyväkuntoiset renkaat. Vanteet ovat tyylikkäät ja modernit, ja niissä on musta/hopea väriyhdistelmä. Renkaissa on riittävästi kulutuspintaa ja ne sopivat useisiin automalleihin. Tämä on loistava tilaisuus päivittää autosi ulkonäkö.', 50.00, 50.00, NULL, NULL, 1.00, '2026-02-18 14:28:38', '2026-02-25 14:28:51', 'active', 3, '', 'Uusi', '2026-02-18 14:28:38', '2026-02-18 15:07:43', NULL, 0),
(79, 2, 17, 'Ilmeikäs valokuvaaja', 'Kuva, jossa henkilö ilmeilee ja pitää kädessään jotain, mikä näyttää olevan valokuvausväline. Taustalla näkyy kodin sisustus ja muita esineitä. Valo tulee huoneeseen, mikä luo mielenkiintoisen tunnelman.', 100.00, 100.00, 120.00, 150.00, 3.00, '2026-02-19 17:18:37', '2026-02-26 17:19:19', 'active', 5, 'Salo', 'Erinomainen', '2026-02-19 17:18:37', '2026-02-20 10:00:23', NULL, 0),
(80, 2, 16, 'Custom Chopper Motorcycle', 'Tyylikäs ja voimakas custom chopper -moottoripyörä, jossa on musta viimeistely ja kiiltävät kromiosat. Moottoripyörässä on laaja takarengas, joka lisää vakautta ja ajomukavuutta. Pyörä on varustettu tehokkaalla moottorilla ja sporttisella pakoputkella, joka tuottaa syvän ja voimakkaan äänen.', 15000.00, 15000.00, NULL, NULL, 1.00, '2026-02-19 19:46:21', '2026-02-26 19:46:47', 'active', 3, '', 'Erinomainen', '2026-02-19 19:46:21', '2026-02-20 14:41:51', NULL, 0),
(81, 2, 15, 'Custom Cruiser Moottoripyörä', 'Tyylikäs ja voimakas custom cruiser moottoripyörä, jossa on näyttävä muotoilu ja vahva moottori. Moottoripyörässä on kromiset yksityiskohdat ja leveä takarengas, joka takaa erinomaisen pidon ja ajokokemuksen. Sopii erinomaisesti sekä kaupunkiin että pidemmille matkoille.', 15000.00, 15000.00, NULL, NULL, 1.00, '2026-02-19 19:48:05', '2026-02-26 19:48:09', 'active', 2, 'Suomi', 'Erinomainen', '2026-02-19 19:48:05', '2026-02-20 09:32:43', NULL, 0),
(82, 2, 11, 'Kaivinkone  Liebherr R924 Compact Litronic', 'Vuosimallin 2014 kaivuri, 10 200 käyttötuntia. 26,300 kg, 120 Kw. Moottori D934S', 10000.00, 10000.00, NULL, NULL, 100.00, '2026-02-20 00:16:08', '2026-02-27 00:16:32', 'active', 1, 'Espoo', 'Hyvä', '2026-02-20 00:16:08', '2026-02-20 00:16:11', NULL, 0),
(83, 2, 2, 'Harley Davidsson', 'Tyylikäs ja yksilöllinen chopper-tyylinen moottoripyörä, jossa on voimakas moottori ja erikoisrakenteinen runko. Musta väri yhdistettynä kiiltävään kromiin luo näyttävän kokonaisuuden. Renkaat ovat leveät ja sopivat hyvin moottorin tehoon, mikä takaa erinomaisen ajokokemuksen. Sopii niin katuajoon kuin näyttelyihin.', 15000.00, 15000.00, 16500.00, 18000.00, 100.00, '2026-02-20 10:28:17', '2026-02-27 10:29:15', 'active', 27, 'Oulu', 'Erinomainen', '2026-02-20 10:28:17', '2026-02-20 22:33:38', NULL, 0),
(84, 2, 2, 'Viper', 'Tyylikäs musta Viper-moottorivene, varustettu mukavilla istuimilla ja modernilla ohjaamolla. Veneessä on tilava avotila sekä laadukas sisustus. Varustettu suojapeitteellä ja trailerilla, mikä helpottaa kuljetusta. Vene on hyvässä kunnossa ja valmis vesille.', 125000.00, 125000.00, 180000.00, 150000.00, 1.00, '2026-02-20 10:30:10', '2026-02-27 10:30:37', 'active', 6, 'Puola', 'Erinomainen', '2026-02-20 10:30:10', '2026-02-20 17:17:57', NULL, 0),
(85, 9, 2, 'Talvirenkaat alumiinivanteilla', 'Neljä talvirengasta alumiinivanteilla, hyvässä kunnossa. Renkaat tarjoavat erinomaisen pidon talvisissa olosuhteissa ja ovat valmiita asennettavaksi. Vanteet ovat tyylikkäät ja hyvässä kunnossa.', 200.00, 200.00, 250.00, 350.00, 1.00, '2026-02-20 20:15:50', '2026-02-27 20:16:35', 'active', 3, 'Helsinki', 'Hyvä, käytetty mutta ei kulunut', '2026-02-20 20:15:50', '2026-02-20 20:16:08', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `auction_images`
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
-- Dumping data for table `auction_images`
--

INSERT INTO `auction_images` (`id`, `auction_id`, `image_path`, `caption`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 76, '/uploads/6991cffa5de4a_1f3eb519-654a-4b10-9219-22e03de46a57.jpg', NULL, 0, 0, '2026-02-15 13:54:02'),
(2, 59, '/uploads/auctions/6991cffa5e4ef_4caaaaaa-297a-4168-9512-a93e0d40b55b.jpg', NULL, 1, 1, '2026-02-15 13:54:02'),
(3, 76, '/uploads/6991cffa5ead6_5be8435f-07ab-40fb-9a00-c8547d7a72aa.jpg', NULL, 1, 2, '2026-02-15 13:54:02'),
(4, 76, '/uploads/6991cffa5ef77_6fd85647-1f45-42a5-bd14-55d78115689f.jpg', NULL, 0, 3, '2026-02-15 13:54:02'),
(5, 76, '/uploads/6991cffa5f3f9_045cc395-9e30-4aa7-b73e-070b4ec68a33.jpg', NULL, 0, 4, '2026-02-15 13:54:02'),
(6, 75, '/uploads/6991d4d3d62d9_20807932-01.jpg', NULL, 1, 0, '2026-02-15 14:14:43'),
(7, 77, '/uploads/6991e7c93ab97_Capture.PNG', NULL, 1, 0, '2026-02-15 15:35:37'),
(8, 78, '/uploads/6995cc963b6be_WhatsApp Image 2026-02-13 at 14.02.24.jpeg', NULL, 1, 0, '2026-02-18 14:28:38'),
(9, 79, '/uploads/699745ede2e8f_dsadsaas.jpg', NULL, 1, 0, '2026-02-19 17:18:37'),
(10, 80, '/uploads/6997688d7cff7_6fd85647-1f45-42a5-bd14-55d78115689f.jpg', NULL, 1, 0, '2026-02-19 19:46:21'),
(11, 81, '/uploads/699768f5b8077_5be8435f-07ab-40fb-9a00-c8547d7a72aa.jpg', NULL, 1, 0, '2026-02-19 19:48:05'),
(12, 82, '/uploads/6997a7c86e00a_lieb1.jpg', NULL, 1, 0, '2026-02-20 00:16:08'),
(13, 83, '/uploads/69983741f2c29_1f3eb519-654a-4b10-9219-22e03de46a57.jpg', NULL, 0, 0, '2026-02-20 10:28:18'),
(14, 83, '/uploads/69983741f300b_4caaaaaa-297a-4168-9512-a93e0d40b55b.jpg', NULL, 0, 1, '2026-02-20 10:28:18'),
(15, 83, '/uploads/69983741f3313_5be8435f-07ab-40fb-9a00-c8547d7a72aa.jpg', NULL, 1, 2, '2026-02-20 10:28:18'),
(16, 84, '/uploads/699837b2a305c_WhatsApp Image 2026-02-12 at 00.53.10 (8).jpeg', NULL, 1, 0, '2026-02-20 10:30:10'),
(17, 84, '/uploads/699837b2a36ce_WhatsApp Image 2026-02-12 at 00.53.10 (9).jpeg', NULL, 0, 1, '2026-02-20 10:30:10'),
(18, 84, '/uploads/699837b2a3bcd_WhatsApp Image 2026-02-12 at 00.53.10.jpeg', NULL, 0, 2, '2026-02-20 10:30:10'),
(19, 85, '/uploads/auctions/6998c0f66b4718.66963992_4345454545.png', NULL, 1, 0, '2026-02-20 20:15:50');

-- --------------------------------------------------------

--
-- Table structure for table `auction_metadata`
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
-- Dumping data for table `auction_metadata`
--

INSERT INTO `auction_metadata` (`id`, `auction_id`, `field_name`, `field_value`, `created_at`, `updated_at`) VALUES
(1, 85, 'vehicle_brand', 'Tuntematon', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(2, 85, 'vehicle_model', 'Tuntematon', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(3, 85, 'engine', 'Tuntematon', '2026-02-20 20:15:50', '2026-02-20 20:15:50'),
(4, 85, 'vehicle_defects', 'Ei', '2026-02-20 20:15:50', '2026-02-20 20:15:50');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
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
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bid_time` timestamp NULL DEFAULT current_timestamp(),
  `is_auto_bid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `icon`, `sort_order`, `active_count`) VALUES
(1, 'Kiinteistöt', 'kiinteistot', 'Tontit, maa-alueet ja kiinteistöt', NULL, '🏠', 1, 3),
(2, 'Ajoneuvot', 'ajoneuvot', 'Autot, moottoripyörät ja muut ajoneuvot', NULL, '🚗', 2, 4),
(3, 'Elektroniikka', 'elektroniikka', 'Tietokoneet, puhelimet ja elektroniikka', NULL, '💻', 3, 3),
(4, 'Kodin tavarat', 'kodin-tavarat', 'Huonekalut ja kodin sisustus', NULL, '🏡', 4, 3),
(5, 'Urheilu', 'urheilu', 'Urheiluvälineet ja -varusteet', NULL, '⚽', 5, 3),
(6, 'Vaatteet', 'vaatteet', 'Vaatteet ja asusteet', NULL, '👕', 6, 3),
(7, 'Keräily', 'keraily', 'Keräilyesineet ja antiikki', NULL, '🎨', 7, 3),
(8, 'Muut', 'muut', 'Muut tuotteet', NULL, '📦', 8, 3),
(9, 'Maakunnittain', 'maakunnittain', 'Huutokauppakohteet maakunnittain eri puolilta Suomea.', NULL, '📍', 9, 3),
(11, 'Työkoneet ja raskas kalusto', 'tyokoneet', 'Työkoneet, kaivinkoneet, traktorit ja raskas kalusto yrityksiltä ja konkurssipesiltä.', NULL, '🚜', 11, 3),
(12, 'Asunnot, mökit, toimitilat ja tontit', 'asunnot', 'Asunnot, mökit, toimitilat ja tontit huutokaupattavina kohteina.', NULL, '🏠', 12, 3),
(13, 'Harrastusvälineet ja vapaa-aika', 'harrastus', 'Harrastusvälineet, vapaa-ajan tuotteet ja liikuntavarusteet.', NULL, '⚽', 13, 3),
(14, 'Piha ja puutarha', 'piha', 'Piha- ja puutarhatarvikkeet, koneet ja kalusteet.', NULL, '🌳', 14, 3),
(15, 'Työkalut ja työkalusarjat', 'tyokalut', 'Työkalut, koneet ja ammattikäyttöön soveltuvat laitteet.', NULL, '🔧', 15, 4),
(16, 'Rakennustarvikkeet', 'rakennus', 'Rakennustarvikkeet, materiaalit ja rakennusalan tuotteet.', NULL, '🏗️', 16, 3),
(17, 'Sisustaminen ja koti', 'sisustus', 'Sisustustuotteet, huonekalut ja kodin tarvikkeet.', NULL, '🛋️', 17, 3),
(18, 'Kirjat', 'kirjat', NULL, NULL, NULL, 0, 0),
(19, 'Lelut ja pelit', 'lelut-ja-pelit', NULL, NULL, NULL, 0, 0),
(20, 'Tukkuerät', 'tukkuerat', 'Tukkuerät, varastojen tyhjennykset ja suuremmat erämyynnit.', NULL, '📦', 20, 3),
(21, 'Kulttuuri', 'kulttuuri', NULL, NULL, NULL, 0, 0),
(22, 'Perinteiset huutokaupat', 'perinteiset', 'Perinteiset fyysiset huutokaupat ja tapahtumat.', NULL, '⚖️', 22, 3),
(23, 'Ulosotto', 'ulosotto', 'Ulosottoviranomaisten myymät kohteet.', NULL, '⚖️', 23, 3),
(24, 'Konkurssipesät', 'konkurssi', 'Konkurssipesien realisoimat omaisuuserät.', NULL, '💼', 24, 3),
(25, 'Puolustusvoimat', 'puolustusvoimat', 'Puolustusvoimien huutokauppaamat ajoneuvot ja kalusto.', NULL, '🎖️', 25, 3),
(26, 'Metsähallitus', 'metsahallitus', 'Metsähallituksen huutokauppaamat kohteet ja omaisuus.', NULL, '🌲', 26, 3),
(27, 'Rahoitusyhtiöt', 'rahoitus', 'Rahoitusyhtiöiden realisoimat kohteet ja leasing-palautukset.', NULL, '💰', 27, 3),
(28, 'Julkinen sektori', 'julkinen', 'Julkisen sektorin myymät ajoneuvot ja kalusto.', NULL, '🏛️', 28, 3),
(29, 'Päättyvät', 'paattyvat', 'Pian päättyvät huutokohteet – viimeiset mahdollisuudet tarjota.', NULL, '⏰', 29, 3);

-- --------------------------------------------------------

--
-- Table structure for table `email_tokens`
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
-- Dumping data for table `email_tokens`
--

INSERT INTO `email_tokens` (`id`, `user_id`, `email`, `token`, `code`, `type`, `expires_at`, `used_at`, `ip_address`, `created_at`) VALUES
(1, 3, 'kiikka@mail.com', '42074ccce45842f68f0471d0f8b81cec69d2de2560c49025d002e4cf8e1e98c1', NULL, 'verification', '2026-02-17 03:36:51', NULL, '93.106.171.167', '2026-02-16 03:36:51'),
(2, 4, 'samu.kuitunen@huuto247.fi', '48f7da5b9ab45969f4c08d10fe305584568c7696731b68d7636904e8640203ec', NULL, 'verification', '2026-02-17 05:08:31', NULL, '93.106.171.167', '2026-02-16 05:08:31'),
(3, 5, 'kiikka.jukka@gmail.com', '3bb1788b263535f108fc74b201da04eaa78ca0b33f02c322346c766fc658bbee', NULL, 'verification', '2026-02-17 05:56:05', NULL, '93.106.171.167', '2026-02-16 05:56:05'),
(4, 6, 'samu.kuitunen@huuto2d47.fi', '6f43d57d91f56b601f79f2e2b4d179cb1e20d1adb012f90fa2100668b92f37d3', NULL, 'verification', '2026-02-17 08:24:37', NULL, '93.106.171.167', '2026-02-16 08:24:37'),
(5, 7, 'teboilruskeasuo@gmail.com', '92ff1dbb868ee844bea697cd264eb5ea41fab57f750a55a617b97d4b92be5400', NULL, 'verification', '2026-02-17 10:53:59', NULL, '93.106.171.167', '2026-02-16 10:53:59'),
(6, 8, 'jussikuisma@icloud.com', 'c49eba13d2a16a75857204a9fa191a8d784c41fdd84900c92132d4f3043c890f', NULL, 'verification', '2026-02-19 13:52:44', NULL, '87.95.68.172', '2026-02-18 13:52:44'),
(7, 9, 'mikko.koivisto@mail.com', '8a5587484458f72a6068147fab954596649abf48a08627fd46af43af567d0deb', NULL, 'verification', '2026-02-21 10:00:48', NULL, '176.72.89.190', '2026-02-20 10:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
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
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `action` varchar(64) NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `first_hit_at` datetime NOT NULL,
  `last_hit_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `created_at`, `updated_at`, `email_verified`, `status`, `google_id`, `avatar_url`, `last_login_at`) VALUES
(1, 'jussi1907', 'samu@huuto247.fi', '$2y$10$jw3jHFTKFOKYRseor3ogYOb2j.sSA3AJzXdRK5/LKvsBaZa4gN7Di', 'Jussi', NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49', 0, 'pending', NULL, NULL, NULL),
(2, 'Ulosottolaitos', 'demo@huuto.local', '$2y$10$n.XhrnWYc9LuNBQ6mvD0fekN.JJf.jjEcsYZUn6Bx4kMt2AzT4Lxq', 'Ulosottolaitos', NULL, '2026-02-15 12:51:49', '2026-02-20 17:20:57', 0, 'pending', NULL, NULL, NULL),
(3, 'jessekuisma', 'kiikka@mail.com', '$2y$10$Oq7Y8x8sv.yk0loZUBavAuwYyD8xVHSvRNi8o6Js8aUxHvzZ6/UKO', 'Jesse Kuisma', NULL, '2026-02-16 03:36:51', '2026-02-16 03:36:51', 0, 'pending', NULL, NULL, NULL),
(4, 'jessekuisma9239', 'samu.kuitunen@huuto247.fi', '$2y$10$nSsFm5PNMjTNTr61sKXLfOCQLaTuyy.nyaCtYaOmTstU2gLJR5UEi', 'Jesse Kuisma', NULL, '2026-02-16 05:08:31', '2026-02-16 05:08:31', 0, 'pending', NULL, NULL, NULL),
(5, 'jessekuisma6171', 'kiikka.jukka@gmail.com', '$2y$10$Awd9MdcJuegId.WzHXIPL.kK4MZilDwxXZSqDL33XAQg9F8rp1hde', 'Jesse Kuisma', NULL, '2026-02-16 05:56:05', '2026-02-16 05:56:05', 0, 'pending', NULL, NULL, NULL),
(6, 'jessekuisma5810', 'samu.kuitunen@huuto2d47.fi', '$2y$10$dk8xQBXYY6bPFvcdi3Z41.wC/4TRz1eOyZpiqJmaSAVqgxMx02BBm', 'Jesse Kuisma', NULL, '2026-02-16 08:24:37', '2026-02-16 08:24:37', 0, 'pending', NULL, NULL, NULL),
(7, 'jessekuisma7505', 'teboilruskeasuo@gmail.com', '$2y$10$FadZA6.ZAeRqzTWrGnKmqO/UeSKNvK0Ip5nETAuKkjlcGeQkcZMLa', 'Jesse Kuisma', NULL, '2026-02-16 10:53:59', '2026-02-16 10:53:59', 0, 'pending', NULL, NULL, NULL),
(8, 'jessekuisma7660', 'jussikuisma@icloud.com', '$2y$10$XnwnNdGp/4D6aJHPL0G6Yu5WjYEO4KoO9P6xYWMjWtW9v7CBvZTZW', 'Jesse Kuisma', NULL, '2026-02-18 13:52:44', '2026-02-18 13:52:44', 0, 'pending', NULL, NULL, NULL),
(9, 'jarikuisma', 'mikko.koivisto@mail.com', '$2y$10$7b.YmvfJMp09Fd/kvMAR2OCzy8FjyHAbcexik5/bNy6YBziwiQxJG', 'jari kuisma', NULL, '2026-02-20 10:00:48', '2026-02-20 10:00:48', 0, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `auction_images`
--
ALTER TABLE `auction_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auction` (`auction_id`);

--
-- Indexes for table `auction_metadata`
--
ALTER TABLE `auction_metadata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_auction_field` (`auction_id`,`field_name`),
  ADD KEY `idx_auction` (`auction_id`),
  ADD KEY `idx_field` (`field_name`);

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
  ADD UNIQUE KEY `uniq_key_action` (`key`,`action`),
  ADD KEY `idx_expires` (`expires_at`),
  ADD KEY `idx_action_expires` (`action`,`expires_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `auction_images`
--
ALTER TABLE `auction_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `auction_metadata`
--
ALTER TABLE `auction_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `email_tokens`
--
ALTER TABLE `email_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auctions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `auction_images`
--
ALTER TABLE `auction_images`
  ADD CONSTRAINT `auction_images_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auction_metadata`
--
ALTER TABLE `auction_metadata`
  ADD CONSTRAINT `auction_metadata_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `email_tokens`
--
ALTER TABLE `email_tokens`
  ADD CONSTRAINT `email_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
