-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 03, 2025 at 01:31 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `asset_register_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset_additions_year`
--

DROP TABLE IF EXISTS `asset_additions_year`;
CREATE TABLE IF NOT EXISTS `asset_additions_year` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(255) NOT NULL,
  `year` int NOT NULL,
  `total_additions` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_additions_year`
--

INSERT INTO `asset_additions_year` (`id`, `asset_class`, `year`, `total_additions`) VALUES
(1, 'Motor Vehicles', 2023, 1642159.98),
(2, 'Motor Vehicles', 2024, 1854601.97),
(3, 'Land And Buildings', 2023, 735621.00),
(4, 'Land And Buildings', 2024, 1566270.00),
(5, 'Computer & Accessories ', 2023, 936275.68),
(6, 'Computer & Accessories ', 2024, 1283373.18),
(7, 'Machine And Equipment', 2023, 248329.72),
(8, 'Machine And Equipment', 2024, 251770.00),
(9, 'Forklift', 2024, 438840.00),
(10, 'Refurbished Road', 2023, 7391380.00),
(11, 'GMDSS Simulator', 2024, 2247600.00),
(12, 'Teaching Equipment', 2025, 93103.40),
(13, 'Teaching Equipment', 2023, 11242.00),
(14, 'Bridge Simulator', 2024, 727283.40),
(15, 'Office Equipment', 2023, 212107.51),
(16, 'Office Equipment', 0, 4623.65),
(17, 'Office Equipment', 2024, 291314.52),
(18, 'Furnitures & Fixtures', 2023, 589071.64),
(19, 'Furnitures & Fixtures', 2024, 624812.62),
(20, 'Building Works in Progress', 2024, 1944000.00);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
