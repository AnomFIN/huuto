-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 20, 2026 at 04:44 PM
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`id`, `user_id`, `category_id`, `title`, `description`, `starting_price`, `current_price`, `reserve_price`, `buy_now_price`, `bid_increment`, `start_time`, `end_time`, `status`, `views`, `location`, `condition_description`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Vanha rahapussi - keräilijän kohde #5417', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 172.00, 172.00, NULL, 344.00, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 8, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 15:32:39'),
(2, 2, 1, 'Polkupyörä 28\" - toimiva kunto #9478', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 19.00, 49.00, NULL, 98.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:03:31'),
(3, 2, 1, 'Laadukas sohva - mukava ja hyväkuntoinen #1153', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 447.00, 457.00, NULL, 914.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:16:51'),
(5, 2, 2, 'Polkupyörä 28\" - toimiva kunto #3029', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 43.00, 53.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:14:06'),
(6, 2, 2, 'Talonrakennustarvikkeita - iso erä #1900', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 455.00, 495.00, NULL, 990.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:53:01'),
(7, 2, 3, 'Laadukas sohva - mukava ja hyväkuntoinen #7942', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 298.00, 298.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:46:37'),
(8, 2, 3, 'Polkupyörä 28\" - toimiva kunto #4707', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 494.00, 514.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(9, 2, 3, 'Puutarhatyökalut 15 kpl - käytetty #4816', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 429.00, 469.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(10, 2, 4, 'Polkupyörä 28\" - toimiva kunto #5201', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 129.00, 159.00, NULL, 318.00, 1.00, '2026-02-15 12:51:49', '2026-03-08 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(11, 2, 4, 'Polkupyörä 28\" - toimiva kunto #9122', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 147.00, 197.00, NULL, 394.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 21:50:59'),
(12, 2, 4, 'Puutarhatyökalut 15 kpl - käytetty #3489', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 134.00, 184.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 4, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 05:18:13'),
(13, 2, 5, 'Talonrakennustarvikkeita - iso erä #3909', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 228.00, 248.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:35:38'),
(14, 2, 5, 'Talonrakennustarvikkeita - iso erä #2260', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 84.00, 94.00, NULL, 188.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(15, 2, 5, 'Vanha rahapussi - keräilijän kohde #8909', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 428.00, 448.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-27 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:57:14'),
(16, 2, 6, 'Puutarhatyökalut 15 kpl - käytetty #2822', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 339.00, 369.00, NULL, 738.00, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:47:35'),
(17, 2, 6, 'Vanha rahapussi - keräilijän kohde #7381', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 216.00, 266.00, NULL, 532.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(18, 2, 6, 'Polkupyörä 28\" - toimiva kunto #6692', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 158.00, 158.00, NULL, 316.00, 1.00, '2026-02-15 12:51:49', '2026-03-14 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(19, 2, 7, 'Talonrakennustarvikkeita - iso erä #1014', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 126.00, 126.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:33:30'),
(20, 2, 7, 'Vanha rahapussi - keräilijän kohde #3164', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 433.00, 453.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-17 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:40:59'),
(21, 2, 7, 'Vanha rahapussi - keräilijän kohde #7228', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 498.00, 538.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(22, 2, 8, 'Talonrakennustarvikkeita - iso erä #1272', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 428.00, 448.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 4, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 14:37:47'),
(24, 2, 8, 'Puutarhatyökalut 15 kpl - käytetty #4430', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 281.00, 291.00, NULL, 582.00, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:28:47'),
(25, 2, 9, 'Talonrakennustarvikkeita - iso erä #7271', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 260.00, 310.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(26, 2, 9, 'Laadukas sohva - mukava ja hyväkuntoinen #9546', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 31.00, 61.00, NULL, 122.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 00:42:55'),
(27, 2, 9, 'Vanha rahapussi - keräilijän kohde #1911', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 493.00, 513.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:36:14'),
(28, 2, 11, 'Talonrakennustarvikkeita - iso erä #1584', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 194.00, 214.00, NULL, 428.00, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 17:04:25'),
(29, 2, 11, 'Vanha rahapussi - keräilijän kohde #4643', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 288.00, 328.00, NULL, 656.00, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:39:09'),
(30, 2, 11, 'Laadukas sohva - mukava ja hyväkuntoinen #1453', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 334.00, 334.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-15 12:51:49', 'active', 3, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 15:10:03'),
(31, 2, 12, 'Polkupyörä 28\" - toimiva kunto #7440', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 448.00, 498.00, NULL, 996.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:34:08'),
(32, 2, 12, 'Puutarhatyökalut 15 kpl - käytetty #6028', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 239.00, 289.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-22 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:37:39'),
(33, 2, 12, 'Polkupyörä 28\" - toimiva kunto #4031', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 385.00, 395.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-02 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:49:49'),
(34, 2, 13, 'Talonrakennustarvikkeita - iso erä #9234', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 144.00, 144.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-22 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:40:49'),
(35, 2, 13, 'Puutarhatyökalut 15 kpl - käytetty #1820', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 354.00, 394.00, NULL, 788.00, 1.00, '2026-02-15 12:51:49', '2026-03-07 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(36, 2, 13, 'Puutarhatyökalut 15 kpl - käytetty #9896', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 426.00, 426.00, NULL, 852.00, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:39:50'),
(37, 2, 14, 'Talonrakennustarvikkeita - iso erä #1431', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 50.00, 70.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:34:43'),
(38, 2, 14, 'Vanha rahapussi - keräilijän kohde #7479', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 42.00, 62.00, NULL, 124.00, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 0, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 12:51:49'),
(39, 2, 14, 'Laadukas sohva - mukava ja hyväkuntoinen #9079', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 40.00, 60.00, NULL, 120.00, 1.00, '2026-02-15 12:51:49', '2026-02-18 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 02:20:48'),
(40, 2, 15, 'Puutarhatyökalut 15 kpl - käytetty #1283', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 362.00, 392.00, NULL, 784.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:49:50'),
(41, 2, 15, 'Vanha rahapussi - keräilijän kohde #1965', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 17.00, 47.00, NULL, 94.00, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:42:41'),
(42, 2, 15, 'Polkupyörä 28\" - toimiva kunto #5997', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 392.00, 422.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-10 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 17:10:20'),
(43, 2, 16, 'Polkupyörä 28\" - toimiva kunto #7968', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 145.00, 195.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:02'),
(44, 2, 16, 'Puutarhatyökalut 15 kpl - käytetty #1004', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 70.00, 70.00, NULL, 140.00, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 16:13:11'),
(45, 2, 16, 'Polkupyörä 28\" - toimiva kunto #1876', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 16.00, 16.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:04:48'),
(46, 2, 17, 'Talonrakennustarvikkeita - iso erä #3389', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 59.00, 99.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-03 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:09:46'),
(47, 2, 17, 'Talonrakennustarvikkeita - iso erä #9343', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 491.00, 521.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:37:59'),
(48, 2, 17, 'Vanha rahapussi - keräilijän kohde #1835', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 416.00, 436.00, NULL, 872.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:31:41'),
(49, 2, 20, 'Laadukas sohva - mukava ja hyväkuntoinen #6821', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 128.00, 138.00, NULL, 276.00, 1.00, '2026-02-15 12:51:49', '2026-03-06 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:35:20'),
(50, 2, 20, 'Laadukas sohva - mukava ja hyväkuntoinen #2268', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 317.00, 337.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-19 12:51:49', 'active', 13, NULL, NULL, '2026-02-15 12:51:49', '2026-02-18 15:05:22'),
(51, 2, 20, 'Talonrakennustarvikkeita - iso erä #7169', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 79.00, 89.00, NULL, 178.00, 1.00, '2026-02-15 12:51:49', '2026-03-17 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:41:24'),
(52, 2, 22, 'Vanha rahapussi - keräilijän kohde #4851', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 267.00, 287.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:38'),
(53, 2, 22, 'Talonrakennustarvikkeita - iso erä #6353', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: good\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 255.00, 285.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:04:29'),
(54, 2, 22, 'Vanha rahapussi - keräilijän kohde #1313', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 205.00, 255.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:56'),
(55, 2, 23, 'Laadukas sohva - mukava ja hyväkuntoinen #4829', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 481.00, 521.00, NULL, 1042.00, 1.00, '2026-02-15 12:51:49', '2026-02-28 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:26:27'),
(56, 2, 23, 'Laadukas sohva - mukava ja hyväkuntoinen #7022', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 318.00, 318.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-02 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:44:20'),
(57, 2, 23, 'Vanha rahapussi - keräilijän kohde #1669', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 374.00, 414.00, NULL, 828.00, 1.00, '2026-02-15 12:51:49', '2026-03-04 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:40:02'),
(58, 2, 24, 'Polkupyörä 28\" - toimiva kunto #8481', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 143.00, 163.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:40:37'),
(59, 2, 24, 'Talonrakennustarvikkeita - iso erä #3308', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 45.00, 85.00, NULL, 170.00, 1.00, '2026-02-15 12:51:49', '2026-03-16 12:51:49', 'active', 8, NULL, NULL, '2026-02-15 12:51:49', '2026-02-19 00:48:19'),
(60, 2, 24, 'Talonrakennustarvikkeita - iso erä #8269', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 311.00, 321.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-26 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:48:13'),
(61, 2, 25, 'Vanha rahapussi - keräilijän kohde #2747', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 157.00, 207.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-20 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:35:02'),
(62, 2, 25, 'Puutarhatyökalut 15 kpl - käytetty #2171', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 39.00, 79.00, NULL, 158.00, 1.00, '2026-02-15 12:51:49', '2026-03-09 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:42:04'),
(63, 2, 25, 'Talonrakennustarvikkeita - iso erä #4808', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 80.00, 130.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-21 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:45:14'),
(64, 2, 26, 'Polkupyörä 28\" - toimiva kunto #6213', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: excellent\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 386.00, 406.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-08 12:51:49', 'active', 3, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 17:09:19'),
(65, 2, 26, 'Polkupyörä 28\" - toimiva kunto #2746', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Uusimaa\nLisätietoja: Ota yhteyttä myyjään.', 499.00, 499.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-16 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 16:45:17'),
(66, 2, 26, 'Vanha rahapussi - keräilijän kohde #7265', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 301.00, 351.00, NULL, 702.00, 1.00, '2026-02-15 12:51:49', '2026-02-27 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:41:30'),
(67, 2, 27, 'Polkupyörä 28\" - toimiva kunto #5658', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 348.00, 398.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-04 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:42:17'),
(68, 2, 27, 'Vanha rahapussi - keräilijän kohde #9560', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 336.00, 366.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-26 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:46:12'),
(69, 2, 27, 'Vanha rahapussi - keräilijän kohde #3019', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 47.00, 87.00, NULL, 174.00, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:55:17'),
(70, 2, 28, 'Laadukas sohva - mukava ja hyväkuntoinen #1971', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: poor\nSijainti: Pirkanmaa\nLisätietoja: Ota yhteyttä myyjään.', 67.00, 77.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-02-25 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 14:36:32'),
(71, 2, 28, 'Polkupyörä 28\" - toimiva kunto #4417', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Pohjois-Pohjanmaa\nLisätietoja: Ota yhteyttä myyjään.', 472.00, 492.00, NULL, 984.00, 1.00, '2026-02-15 12:51:49', '2026-03-07 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:38:55'),
(72, 2, 28, 'Laadukas sohva - mukava ja hyväkuntoinen #8805', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Satakunta\nLisätietoja: Ota yhteyttä myyjään.', 361.00, 401.00, NULL, 802.00, 1.00, '2026-02-15 12:51:49', '2026-03-06 12:51:49', 'active', 3, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:55:57'),
(73, 2, 29, 'Talonrakennustarvikkeita - iso erä #9805', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: fair\nSijainti: Kanta-Häme\nLisätietoja: Ota yhteyttä myyjään.', 480.00, 480.00, NULL, 960.00, 1.00, '2026-02-15 12:51:49', '2026-03-12 12:51:49', 'active', 1, NULL, NULL, '2026-02-15 12:51:49', '2026-02-15 15:44:33'),
(74, 2, 29, 'Laadukas sohva - mukava ja hyväkuntoinen #4203', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\n\nKunto: new\nSijainti: Varsinais-Suomi\nLisätietoja: Ota yhteyttä myyjään.', 469.00, 509.00, NULL, NULL, 1.00, '2026-02-15 12:51:49', '2026-03-11 12:51:49', 'active', 2, NULL, NULL, '2026-02-15 12:51:49', '2026-02-16 04:34:31'),
(75, 2, 29, 'Talonrakennustarvikkeita - iso erä #4566', 'Myydään tämä kohde huutokauppaan. Noudettavissa sovitusti.\r\n\r\nKunto: fair\r\nSijainti: Pohjois-Pohjanmaa\r\nLisätietoja: Ota yhteyttä myyjään.', 248.00, 268.00, NULL, 536.00, 1.00, '2026-02-15 12:51:49', '2026-02-24 12:51:49', 'active', 5, '', '', '2026-02-15 12:51:49', '2026-02-16 03:36:39'),
(76, 2, 2, 'Harley Davidson- custom moottoripyörä', 'Myyntiin hyväkuntoinen harrikka.', 4000.00, 4000.00, 6000.00, 5500.00, 100.00, '2026-02-15 13:54:02', '2026-02-16 13:54:02', 'active', 17, 'Harjavalta', 'Hyvä', '2026-02-15 13:54:02', '2026-02-18 14:37:01'),
(77, 2, 15, 'Makita VC2512L (2023) + Kärcher VC 3 (ERP), 2 imuria', 'Myydään\r\nMakita VC2512L (2023) + Kärcher VC 3 (ERP) – 2 imuria. Toimivuus testattu Kone Korpelan korjaamolla.\r\n\r\n\r\n\r\n1) Makita VC2512L – L-luokan ammattimärkä-/kuivaimuri:\r\n\r\n● Malli: VC2512L\r\n● Valmistettu: 06/2023\r\n● Teho: 1000 W\r\n● Työkalupistorasia: max. 3600 W\r\n● Ilmavirta: 3600 l/min\r\n● Alipaine: 210 mbar\r\n● IP24\r\n● Paino: 8,0 kg\r\n● Pölyluokka: L\r\n\r\n\r\n\r\nOminaisuudet:\r\n\r\n● Automaattikäynnistys työkalulle\r\n● Märkä- ja kuivaimuri\r\n● Pyörällinen säiliörakenne\r\n● Ammattikäyttöön\r\n\r\n\r\n\r\n2) Kärcher VC 3 (ERP):\r\n\r\n● Malli: VC 3 (ERP)\r\n● 700 W (max. 750 W)\r\n● 220–240 V\r\n● 50/60 Hz\r\n\r\n\r\n\r\nMukaan:\r\n\r\n● Letku\r\n● Putki\r\n● Kärcher lattiasuulake\r\n\r\n\r\n\r\n\r\n\r\nKohde voidaan toimittaa Kiitolinjan, autonkuljetusauton tai matkahuollon jne. kanssa riippuen kohteesta, ostajan maksaessa kaikki kuljetus, lähetys ja pakkaus kulut. Voidaan toimittaa myös Manner-Suomen ulkopuolelle lisäkustannusta vastaan. Lähettämästämme noutamatta jätetystä kohteesta / kohteista veloitamme saman suuruisen toimituskulun kuin kohteessa on ollut sekä palautuksesta meille että uudelleen lähetyksestä. Kohde tulee noutaa 3 arkivuorokauden sisällä kohteen päättymisestä tai ilmoituksessa kerrottuna ajankohtana (niissä kohteissa joissa nouto on mahdollinen). Tämän jälkeen veloitamme säilytyksestä 10 €/vrk.\r\n\r\n\r\n\r\nToimituskuluun sisältyy:\r\n\r\n\r\n\r\nPakkausmateriaalit\r\n\r\nPakkaus (henkilöstökulut)\r\n\r\nLähetyslappujen teko\r\n\r\nMatkahuolto noutaa paketit meiltä\r\n\r\nItse kuljetus\r\n\r\njne.\r\n\r\nToimituskulut sisältävät myös valtiolle menevän arvonlisäveron 25,5 %\r\n\r\nNäitä kuluja ei saa sisällytettyä tuotteen hintaa huutokaupassa niin kuin verkkokaupat tekevät, koska hinta ei ole kiinteä. Huutohinta ja toimituskulut ovat tuotteen kokonaishinta toimitettuna arvonlisäveroineen.\r\n\r\nUseampi tuote voidaan pakata samaan pakettiin\r\n\r\n\r\n\r\nAA Realisointi Oy\r\n\r\nInkereentie 1021\r\n\r\n25190 Pertteli (Salo)\r\n\r\nViat ja muut havainnot\r\nTuotteen huutokauppailmoitus on tehty myyjän havaintoihin perustuen, tuotteessa saattaa olla piileviä vikoja tai virheitä, mitä myyjä ei ole huutokauppailmoitusta laatiessa havainnut.', 80.00, 90.00, 60.00, 100.00, 1.00, '2026-02-15 15:35:37', '2026-02-22 15:35:37', 'active', 9, 'Saloa', 'Hyvää', '2026-02-15 15:35:37', '2026-02-18 14:53:27'),
(78, 2, 8, 'Vannesetti', 'Myynnissä neljä alumiinivannetta, joissa on hyväkuntoiset renkaat. Vanteet ovat tyylikkäät ja modernit, ja niissä on musta/hopea väriyhdistelmä. Renkaissa on riittävästi kulutuspintaa ja ne sopivat useisiin automalleihin. Tämä on loistava tilaisuus päivittää autosi ulkonäkö.', 50.00, 50.00, NULL, NULL, 1.00, '2026-02-18 14:28:38', '2026-02-25 14:28:51', 'active', 3, '', 'Uusi', '2026-02-18 14:28:38', '2026-02-18 15:07:43'),
(79, 2, 17, 'Ilmeikäs valokuvaaja', 'Kuva, jossa henkilö ilmeilee ja pitää kädessään jotain, mikä näyttää olevan valokuvausväline. Taustalla näkyy kodin sisustus ja muita esineitä. Valo tulee huoneeseen, mikä luo mielenkiintoisen tunnelman.', 100.00, 100.00, 120.00, 150.00, 3.00, '2026-02-19 17:18:37', '2026-02-26 17:19:19', 'active', 5, 'Salo', 'Erinomainen', '2026-02-19 17:18:37', '2026-02-20 10:00:23'),
(80, 2, 16, 'Custom Chopper Motorcycle', 'Tyylikäs ja voimakas custom chopper -moottoripyörä, jossa on musta viimeistely ja kiiltävät kromiosat. Moottoripyörässä on laaja takarengas, joka lisää vakautta ja ajomukavuutta. Pyörä on varustettu tehokkaalla moottorilla ja sporttisella pakoputkella, joka tuottaa syvän ja voimakkaan äänen.', 15000.00, 15000.00, NULL, NULL, 1.00, '2026-02-19 19:46:21', '2026-02-26 19:46:47', 'active', 3, '', 'Erinomainen', '2026-02-19 19:46:21', '2026-02-20 14:41:51'),
(81, 2, 15, 'Custom Cruiser Moottoripyörä', 'Tyylikäs ja voimakas custom cruiser moottoripyörä, jossa on näyttävä muotoilu ja vahva moottori. Moottoripyörässä on kromiset yksityiskohdat ja leveä takarengas, joka takaa erinomaisen pidon ja ajokokemuksen. Sopii erinomaisesti sekä kaupunkiin että pidemmille matkoille.', 15000.00, 15000.00, NULL, NULL, 1.00, '2026-02-19 19:48:05', '2026-02-26 19:48:09', 'active', 2, 'Suomi', 'Erinomainen', '2026-02-19 19:48:05', '2026-02-20 09:32:43'),
(82, 2, 11, 'Kaivinkone  Liebherr R924 Compact Litronic', 'Vuosimallin 2014 kaivuri, 10 200 käyttötuntia. 26,300 kg, 120 Kw. Moottori D934S', 10000.00, 10000.00, NULL, NULL, 100.00, '2026-02-20 00:16:08', '2026-02-27 00:16:32', 'active', 1, 'Espoo', 'Hyvä', '2026-02-20 00:16:08', '2026-02-20 00:16:11'),
(83, 2, 2, 'Harley Davidsson', 'Tyylikäs ja yksilöllinen chopper-tyylinen moottoripyörä, jossa on voimakas moottori ja erikoisrakenteinen runko. Musta väri yhdistettynä kiiltävään kromiin luo näyttävän kokonaisuuden. Renkaat ovat leveät ja sopivat hyvin moottorin tehoon, mikä takaa erinomaisen ajokokemuksen. Sopii niin katuajoon kuin näyttelyihin.', 15000.00, 15000.00, 16500.00, 18000.00, 100.00, '2026-02-20 10:28:17', '2026-02-27 10:29:15', 'active', 24, 'Oulu', 'Erinomainen', '2026-02-20 10:28:17', '2026-02-20 14:43:27'),
(84, 2, 2, 'Viper', 'Tyylikäs musta Viper-moottorivene, varustettu mukavilla istuimilla ja modernilla ohjaamolla. Veneessä on tilava avotila sekä laadukas sisustus. Varustettu suojapeitteellä ja trailerilla, mikä helpottaa kuljetusta. Vene on hyvässä kunnossa ja valmis vesille.', 125000.00, 125000.00, 180000.00, 150000.00, 1.00, '2026-02-20 10:30:10', '2026-02-27 10:30:37', 'active', 5, 'Puola', 'Erinomainen', '2026-02-20 10:30:10', '2026-02-20 14:43:58');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auctions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
