-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 08, 2024 at 08:30 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `asset_register`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `user_role` varchar(20) NOT NULL,
  `user_password` varchar(254) NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`table_id`, `username`, `user_role`, `user_password`) VALUES
(1, 'Senior Internal Auditor', 'SIA', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda'),
(2, 'Schedule Officer', 'S/O', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda'),
(3, 'Budget Officer', 'B/O', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda'),
(4, 'Accountant', 'Accountant', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda'),
(5, 'Director Finance', 'D/F', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda'),
(6, 'DSU', 'DSU', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda');

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) NOT NULL,
  `grv_number` varchar(7) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `serial_number` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `pv_number` varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `id_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'N/A',
  `supplier_name` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `asset_class` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `sub_class` varchar(254) NOT NULL,
  `asset_type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `location` varchar(50) NOT NULL,
  `user` varchar(150) DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `current_year` int NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `additions` decimal(20,2) NOT NULL,
  `disposals` decimal(10,2) NOT NULL DEFAULT '0.00',
  `active_res_value` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dollar_rate_used` double NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_id`),
  KEY `asset_class` (`asset_class`),
  KEY `asset_type` (`asset_type`),
  KEY `location` (`location`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `assets_archive`
--

DROP TABLE IF EXISTS `assets_archive`;
CREATE TABLE IF NOT EXISTS `assets_archive` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) NOT NULL,
  `grv_number` varchar(7) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `serial_number` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `pv_number` varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `id_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'N/A',
  `supplier_name` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `asset_class` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `sub_class` varchar(254) NOT NULL,
  `asset_type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `location` varchar(50) NOT NULL,
  `user` varchar(150) DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `current_year` int NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `additions` decimal(20,2) NOT NULL,
  `disposals` decimal(10,2) NOT NULL DEFAULT '0.00',
  `active_res_value` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dollar_rate_used` double NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_id`),
  KEY `asset_class` (`asset_class`),
  KEY `asset_type` (`asset_type`),
  KEY `location` (`location`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_allocation`
--

DROP TABLE IF EXISTS `asset_allocation`;
CREATE TABLE IF NOT EXISTS `asset_allocation` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(40) NOT NULL,
  `asset_sn_number` varchar(40) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes`
--

DROP TABLE IF EXISTS `asset_classes`;
CREATE TABLE IF NOT EXISTS `asset_classes` (
  `ast_id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(50) NOT NULL,
  `account_depr_open_bal` decimal(30,2) NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `opbal_plus_additions` decimal(20,2) NOT NULL,
  `dep_rate` float NOT NULL,
  `estimated_life` int NOT NULL,
  PRIMARY KEY (`ast_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_classes`
--

INSERT INTO `asset_classes` (`ast_id`, `asset_class`, `account_depr_open_bal`, `opening_bal`, `opbal_plus_additions`, `dep_rate`, `estimated_life`) VALUES
(1, 'Computer & Accessories', '1.00', '0.00', '10512.00', 0.2, 5),
(2, 'Land And Buildings', '1.00', '0.00', '10123.00', 0.02, 50),
(3, 'Office Furniture', '0.00', '0.00', '3712.00', 0.2, 5),
(4, 'Motor Vehicles', '0.00', '0.00', '109645.00', 0.2, 5),
(5, 'Classroom Furniture', '0.00', '0.00', '30000.00', 0.2, 5),
(6, 'Test', '0.00', '2000.00', '46610.00', 0.2, 5),
(7, 'Test 1', '0.00', '0.00', '7000.00', 0.2, 5),
(8, 'Air Conditioner', '0.00', '5000.00', '8289.00', 0.3, 3),
(9, 'Test Class', '1.00', '7000.00', '12538.00', 0.2, 5),
(10, 'Furniture And Fittings ', '0.00', '10000.00', '0.00', 0.1, 10),
(11, 'Office Equipment', '0.00', '100000.00', '0.00', 0.2, 5),
(12, 'Hostel Equipment', '0.00', '1000.00', '0.00', 0.2, 5),
(13, 'Hostel Furniturre And Fittings', '0.00', '10000.00', '0.00', 0.1, 10),
(14, 'Machine And Equipment', '0.00', '500000.00', '0.00', 0.2, 5),
(15, 'Computers', '0.00', '10000.00', '0.00', 0.25, 4),
(16, 'Building', '0.00', '1000.00', '0.00', 0.02, 50),
(17, 'Teaching Equipment', '0.00', '10000.00', '0.00', 0.2, 5),
(18, 'Talif V-Sat Project', '0.00', '1000.00', '0.00', 0.2, 5),
(19, 'Library Books', '0.00', '1000.00', '0.00', 0.2, 5),
(20, 'Swimming Pool', '0.00', '20000.00', '0.00', 0.1, 10),
(21, 'Bridge Simulator', '0.00', '10000.00', '0.00', 0.2, 5),
(22, 'School Band', '0.00', '500000.00', '0.00', 0.33, 3),
(23, 'Solar Equipment(ROU)', '0.00', '10000.00', '0.00', 0.25, 4),
(24, 'Stanchion Base', '0.00', '1000.00', '0.00', 0.2, 5),
(25, 'Academic Gown', '0.00', '1000.00', '0.00', 0.2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes_archive`
--

DROP TABLE IF EXISTS `asset_classes_archive`;
CREATE TABLE IF NOT EXISTS `asset_classes_archive` (
  `ast_id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(50) NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `dep_rate` float NOT NULL,
  PRIMARY KEY (`ast_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_class_sub_classes`
--

DROP TABLE IF EXISTS `asset_class_sub_classes`;
CREATE TABLE IF NOT EXISTS `asset_class_sub_classes` (
  `T_id` int NOT NULL AUTO_INCREMENT,
  `sub_class` varchar(250) NOT NULL,
  `sub_class_code` varchar(250) NOT NULL,
  `asset_class` varchar(254) NOT NULL,
  PRIMARY KEY (`T_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_class_sub_classes`
--

INSERT INTO `asset_class_sub_classes` (`T_id`, `sub_class`, `sub_class_code`, `asset_class`) VALUES
(1, 'Monitor', 'M07', 'Computer & Accessories'),
(2, 'System Unit', 'SU10', 'Computer & Accessories'),
(3, 'Keyboard', 'KD08', 'Computer & Accessories'),
(4, 'Mouse', 'M06', 'Computer & Accessories'),
(5, 'Office Table', 'OT11', 'Office Furniture'),
(6, 'Chair', 'C05', 'Office Furniture'),
(7, 'Swivel Chair', 'SC11', 'Office Furniture'),
(8, 'Air Condition', 'AC12', 'Air Conditioner'),
(9, 'Cabinet', 'CAB08', 'Office Furniture'),
(10, 'Telephone', 'TEL09', 'Office Equipment'),
(11, 'Watch', 'W05', 'Office Equipment'),
(12, 'Fan', 'F03', 'Furniture And Fittings '),
(13, 'Laptop', 'LAP06', 'Computer & Accessories'),
(14, 'Perforator', 'P10', 'Office Equipment'),
(15, 'Stapler', 'S07', 'Office Equipment'),
(16, 'Gas Cylinder', 'GS11', 'Test'),
(17, 'Ups', 'UP03', 'Computer & Accessories'),
(18, 'Dustbin', 'D07', 'Land And Buildings');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location`
--

DROP TABLE IF EXISTS `asset_location`;
CREATE TABLE IF NOT EXISTS `asset_location` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  `loc_code` varchar(250) NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_location`
--

INSERT INTO `asset_location` (`loc_id`, `location`, `loc_code`) VALUES
(1, 'Accounts', 'ACC'),
(2, 'Pro-Vice Chancellor\'S Office', 'PRO-VC');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location_archive`
--

DROP TABLE IF EXISTS `asset_location_archive`;
CREATE TABLE IF NOT EXISTS `asset_location_archive` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  `loc_code` varchar(250) NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

DROP TABLE IF EXISTS `asset_type`;
CREATE TABLE IF NOT EXISTS `asset_type` (
  `type_id` int NOT NULL AUTO_INCREMENT,
  `asset_type` varchar(50) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_type`
--

INSERT INTO `asset_type` (`type_id`, `asset_type`) VALUES
(1, 'Owned'),
(2, 'Leased'),
(3, 'Rent'),
(4, 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `asset_users`
--

DROP TABLE IF EXISTS `asset_users`;
CREATE TABLE IF NOT EXISTS `asset_users` (
  `staff_id` varchar(40) NOT NULL,
  `staff_first_name` varchar(25) DEFAULT NULL,
  `staff_last_name` varchar(25) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `t_id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_users`
--

INSERT INTO `asset_users` (`staff_id`, `staff_first_name`, `staff_last_name`, `department`, `t_id`) VALUES
('11134123', 'John', 'Little', 'Finance', 1),
('11134123', 'John', 'Little', 'Finance', 2),
('11020323', 'John', 'Little', 'Finance', 3),
('0', 'Ismail', 'Abdulai-Saiku', '3', 4),
('1', 'Henry', 'Benjamin', 'ICT Department', 5),
('234', 'John', 'Snow', 'Nautical Science', 6),
('Rmu0997', 'Yakubu Daniels', 'Danso', 'Department of Transport', 7);

-- --------------------------------------------------------

--
-- Table structure for table `calculations`
--

DROP TABLE IF EXISTS `calculations`;
CREATE TABLE IF NOT EXISTS `calculations` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `asset_cost_opening_balance` decimal(10,2) DEFAULT NULL,
  `asset_cost_closing_balance` decimal(10,2) DEFAULT NULL,
  `depreciation_cost` decimal(10,2) DEFAULT NULL,
  `total_accumulated_depreciation` decimal(10,2) DEFAULT NULL,
  `account_depreciation_closing_balance` decimal(10,2) DEFAULT NULL,
  `closing_carrying_value` decimal(10,2) DEFAULT NULL,
  `current_lifetime` int DEFAULT NULL,
  `unexpired_lifetime` int DEFAULT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `calculations`
--

INSERT INTO `calculations` (`table_id`, `asset_cost_opening_balance`, `asset_cost_closing_balance`, `depreciation_cost`, `total_accumulated_depreciation`, `account_depreciation_closing_balance`, `closing_carrying_value`, `current_lifetime`, `unexpired_lifetime`) VALUES
(1, '-40678.00', '3370433.00', '0.00', '0.00', '-45678.00', '3416111.00', 30, -30),
(2, '4655.00', '27766.00', '0.00', '0.00', '-345.00', '28111.00', 26, -26),
(3, '498766.00', '499556.00', '9991.12', '9991.12', '8757.12', '491242.88', 10323, -10273),
(4, '4655.00', '27766.00', '0.00', '0.00', '-345.00', '5345.00', 26, -26),
(5, '4655.00', '27766.00', '0.00', '0.00', '-345.00', '5345.00', 26, -26),
(6, '500000.00', '500050.00', '10001.00', '10001.00', '10001.00', '489999.00', 2, 48),
(7, '500000.00', '500050.00', '10001.00', '10001.00', '10001.00', '489999.00', 2, 48),
(8, '500000.00', '500050.00', '10001.00', '10001.00', '10001.00', '489999.00', 2, 48),
(9, '500000.00', '500050.00', '10001.00', '10001.00', '10001.00', '489999.00', 2, 48),
(10, '500000.00', '500000.00', '10000.00', '10000.00', '10000.00', '490000.00', 2, 48),
(11, '500000.00', '550000.00', '11000.00', '11000.00', '11000.00', '539000.00', 2, 48),
(12, '498766.00', '548766.00', '10975.32', '10975.32', '9741.32', '540258.68', 10323, -10273),
(13, '500000.00', '550000.00', '11000.00', '11000.00', '11000.00', '539000.00', 2, 48),
(14, '500000.00', '550000.00', '11000.00', '11000.00', '11000.00', '539000.00', 2, 48),
(15, '499956.00', '549956.00', '10999.12', '10999.12', '10955.12', '539044.88', 1, 49),
(16, '500000.00', '550000.00', '11000.00', '11000.00', '11000.00', '539000.00', 2, 48),
(17, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(18, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(19, '-7345.00', '3452899.00', '0.00', '0.00', '-12345.00', '3477589.00', 55, -55),
(20, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(21, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(22, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(23, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(24, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(25, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(26, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(27, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(28, '0.00', '-4500.00', '-90.00', '-90.00', '-90.00', '504590.00', 1, 49),
(29, '0.00', '-4500.00', '-90.00', '-90.00', '-90.00', '504590.00', 1, 49),
(30, '0.00', '0.00', '0.00', '0.00', '0.00', '504500.00', 1, 49),
(31, '500000.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(32, '0.00', '504500.00', '10090.00', '10090.00', '10090.00', '494410.00', 1, 49),
(33, '0.00', '4500.00', '90.00', '90.00', '90.00', '504410.00', 1, 49),
(34, '0.00', '4500.00', '90.00', '90.00', '90.00', '4410.00', 1, 49),
(35, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(36, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(37, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(38, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(39, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(40, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(41, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(42, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(43, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(44, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(45, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(46, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(47, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(48, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(49, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(50, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(51, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(52, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(53, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(54, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 1, 49),
(55, '0.00', '5000.00', '100.00', '100.00', '100.00', '4900.00', 1, 0),
(56, '0.00', '5000.00', '100.00', '100.00', '100.00', '4900.00', 2, -1),
(57, '0.00', '5000.00', '100.00', '100.00', '100.00', '4900.00', 3, 1),
(58, '0.00', '5000.00', '100.00', '100.00', '100.00', '4900.00', 4, 0),
(59, '0.00', '5000.00', '100.00', '100.00', '100.00', '4900.00', 5, -1),
(60, '0.00', '3000.00', '60.00', '60.00', '60.00', '2940.00', 5, -1),
(61, '0.00', '10000.00', '0.00', '0.00', '0.00', '10000.00', 2, -2),
(62, '0.00', '10000.00', '0.00', '0.00', '0.00', '10000.00', 2, -2),
(63, '0.00', '10000.00', '2000.00', '2000.00', '2000.00', '8000.00', 2, 3),
(64, '0.00', '10000.00', '2000.00', '2000.00', '2000.00', '8000.00', 2, 3),
(65, '0.00', '10000.00', '2000.00', '2000.00', '2000.00', '8000.00', 2, 3),
(66, '0.00', '3000.00', '60.00', '60.00', '60.00', '2940.00', 5, -1),
(67, '0.00', '3000.00', '750.00', '750.00', '750.00', '2250.00', 5, -1),
(68, '0.00', '5000.00', '1250.00', '1250.00', '1250.00', '3750.00', 2, 2),
(69, '0.00', '50.00', '1.00', '1.00', '1.00', '49.00', 2, 48),
(70, '12345.00', '3469133.00', '0.00', '0.00', '-12345.00', '3481478.00', 55, -55),
(71, '12345.00', '3469133.00', '0.00', '0.00', '-12345.00', '3481478.00', 55, -55),
(72, '0.00', '500000.00', '10000.00', '10000.00', '10000.00', '490000.00', 28, 22),
(73, '0.00', '4500.00', '90.00', '90.00', '90.00', '4410.00', 28, 22),
(74, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 2, 48),
(75, '0.00', '228258.00', '4565.16', '4565.16', '4565.16', '223692.84', 2, 48),
(76, '0.00', '18738.79', '374.78', '374.78', '374.78', '18364.01', 3, 47),
(77, '0.00', '18738.79', '374.78', '374.78', '374.78', '18364.01', 3, 47),
(78, '0.00', '3742.00', '935.50', '935.50', '935.50', '2806.50', 3, 1),
(79, '0.00', '18738.79', '374.78', '374.78', '374.78', '18364.01', 3, 47),
(80, '0.00', '3000.00', '600.00', '600.00', '600.00', '2400.00', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `dep_id` varchar(37) NOT NULL,
  `dep_name` varchar(70) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`t_id`, `dep_id`, `dep_name`) VALUES
(1, 'ID001', 'Marine Engineering'),
(2, 'ID002', 'Nautical Science'),
(3, 'ID003', 'ICT Department'),
(4, 'ID004', 'Department of Transport'),
(5, 'ID005', 'Electrical Department');

-- --------------------------------------------------------

--
-- Table structure for table `dollar_rate`
--

DROP TABLE IF EXISTS `dollar_rate`;
CREATE TABLE IF NOT EXISTS `dollar_rate` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `dollar_rate` double NOT NULL,
  `rate_status` varchar(20) NOT NULL,
  `action_by` varchar(20) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dollar_rate`
--

INSERT INTO `dollar_rate` (`table_id`, `dollar_rate`, `rate_status`, `action_by`, `date_added`) VALUES
(1, 2, 'INACTIVE', 'Schedule Officer', '2024-02-01 20:06:23'),
(2, 12, 'INACTIVE', 'Schedule Officer', '2024-02-02 09:46:54'),
(3, 9, 'INACTIVE', 'Schedule Officer', '2024-02-02 11:26:20'),
(4, 7.5, 'INACTIVE', 'Schedule Officer', '2024-02-16 08:41:48'),
(5, 6.5, 'INACTIVE', 'Schedule Officer', '2024-02-16 13:35:35'),
(6, 7, 'INACTIVE', 'Schedule Officer', '2024-02-18 21:43:21'),
(7, 5.5, 'INACTIVE', 'Schedule Officer', '2024-02-19 09:36:08'),
(8, 8, 'INACTIVE', 'Schedule Officer', '2024-03-01 09:12:46'),
(9, 6, 'INACTIVE', 'Schedule Officer', '2024-03-07 09:03:56'),
(10, 10, 'INACTIVE', 'Schedule Officer', '2024-03-07 10:20:10'),
(11, 8.5, 'INACTIVE', 'Schedule Officer', '2024-03-07 11:02:40'),
(12, 9, 'INACTIVE', 'Schedule Officer', '2024-03-07 11:03:11'),
(13, 9.5, 'ACTIVE', 'Schedule Officer', '2024-03-07 11:10:23');

-- --------------------------------------------------------

--
-- Table structure for table `moved_assets`
--

DROP TABLE IF EXISTS `moved_assets`;
CREATE TABLE IF NOT EXISTS `moved_assets` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `notes` text NOT NULL,
  `old_location` varchar(100) NOT NULL,
  `old_user` varchar(100) NOT NULL,
  `New_location` varchar(100) NOT NULL,
  `new_user` varchar(100) NOT NULL,
  `date_of_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `moved_assets`
--

INSERT INTO `moved_assets` (`t_id`, `serial_number`, `notes`, `old_location`, `old_user`, `New_location`, `new_user`, `date_of_action`) VALUES
(1, '100000', 'moved on  this day', 'ICT HOD', '', 'Computer Lab 1', '', '2024-02-19 09:27:34'),
(2, '100007', 'moved on this day', 'Marketing Officer', '', 'Language Centre', '', '2024-02-19 10:02:13'),
(3, '100007', 'test run', 'Language Centre', '', 'Registry Records', '', '2024-02-19 10:03:18'),
(4, '100000', 'Moved On This Day', 'Computer Lab 1', '', 'Estate Works Supervisor', '', '2024-02-19 10:06:43'),
(5, '100005', 'Moved', 'NSD Classrooms', '', 'Nautical Science HOD', '', '2024-02-19 10:33:25'),
(6, '100006', 'Moved', 'NSD Classrooms', '', 'Accounts Clerk', '', '2024-02-19 10:33:44'),
(7, '0999997', 'Asset Has Been Moved From Point A To Point B', 'VSTC Head', '', 'M.S.F Lecture Halls', '', '2024-03-01 09:19:02'),
(8, '100006', 'Moved', 'Accounts Clerk', '', 'Computer Lab 1', '', '2024-03-01 09:19:32'),
(9, '100006', 'Moved', 'Computer Lab 1', '', 'Language Centre', '', '2024-03-01 11:13:34'),
(10, '0999997', 'Moved', 'M.S.F Lecture Halls', '', 'Nautical Science HOD', '', '2024-03-07 09:29:46'),
(11, '0999997', 'Moved', 'Nautical Science HOD', '', 'Language Centre', '', '2024-03-07 09:30:23'),
(12, '0999994', 'Moved For A While', 'Language Centre', '', 'Accounts Clerk', '', '2024-03-07 10:27:11'),
(13, '0999994', 'Moved For A While', 'Accounts Clerk', '', 'Auditorium', '', '2024-03-07 10:27:38'),
(14, '0999991', 'Moved ', 'Procurement', '', 'Computer Lab 1', '', '2024-03-07 11:16:25'),
(15, '1111111111', 'Changed', 'VSTC Head', '', 'NSD Classrooms', '', '2024-03-19 17:03:37'),
(16, '5555552', 'Working', 'Graduate School', '', 'MSF Secretariat', '', '2024-03-19 22:07:17'),
(17, '1111111111', 'Babbs Made Me Do It', 'Accounts Clerk', 'John Little', 'MSF Secretariat', 'Ismail Abdulai-Saiku', '2024-03-20 14:17:22'),
(18, '0999991', 'Burkina Vibes', 'Language Centre', '', 'Computer Lab 1', 'Henry Benjamin', '2024-03-20 14:26:13'),
(19, '1111111111', 'Changed', 'MSF Secretariat', 'Ismail Abdulai-Saiku', 'Engineering Workshop', 'Henry Benjamin', '2024-03-21 10:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `other_values`
--

DROP TABLE IF EXISTS `other_values`;
CREATE TABLE IF NOT EXISTS `other_values` (
  `acc_depr_opening_bal` decimal(20,2) NOT NULL,
  `s/n` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`s/n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `sup_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `location` varchar(250) NOT NULL,
  `number` varchar(10) NOT NULL,
  PRIMARY KEY (`sup_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `number` (`number`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`sup_id`, `name`, `location`, `number`) VALUES
(63, 'Jeff Manuel', 'Tesano', '0200934553'),
(62, 'Nii Nortey Ventures', 'Teshie-Nungua', '0201112311'),
(61, 'Yakubu\'S Furniture', 'Tema', '0201111111'),
(60, 'Hp Services', 'Tema', '0200000001'),
(11, 'Access A Properties Limited', 'teshie-nungua', '0200034433'),
(12, 'Air LIquide Ghana Limited', 'Tesano', '0200034434'),
(13, 'Aay Great Provider', 'Nima', '0200034435'),
(14, 'Sollatek Electronic Ghana Limited', 'Tema Community 2', '0200034436'),
(15, 'Akan Centre Furniture Co. Ltd.', 'Ejisu', '0200034437'),
(16, 'Prosper Furniture and Civil Works', 'kantamanto', '0200034438'),
(17, 'Feluth Enterprise', 'Achimota', '0200034439'),
(18, 'Seth Heritage Engineering and Equipment Services', 'Osu', '0200034440'),
(19, 'China Mall', 'Spintex', '0200034441'),
(20, 'Rickel', 'Ashongman', '0200034442'),
(21, 'All Grace Enterprise', 'Labadi', '0200034443'),
(22, 'KINGSDECO Limited', 'Adenta', '0200034445'),
(23, 'Ashanti Foam Factory Ltd.', 'Ejisu', '0200034446'),
(24, 'PE Star Development Ltd.', 'Dansoman', '0200034447'),
(25, 'Trademark Ghana Limited', 'Labone', '0200034449'),
(26, 'Dominion Technologies', 'Teshie', '0200034450'),
(27, 'Paasly Enterprise', 'tema', '0200034452'),
(28, 'K. Asensio Limited', 'Kasoa', '0200034456'),
(29, 'WOR-TECH Solutions', 'Mallam Junction', '0200034457'),
(30, 'COMPU-GHANA Limited', 'Freepipe', '0200034458'),
(31, 'ZIPTECH GHANA Limited', 'Estate Junction', '0200034459'),
(32, 'Felix Sosu', 'Weija', '0200034469'),
(33, 'Other Suppliers', 'Hohoe', '0200034468'),
(34, 'Electroland Ghana Limited', 'N.I.A', '0200034467'),
(35, 'Docutech Ghana Limited', 'Aburi', '0200034466'),
(36, 'Perfect Business System', 'Tamale', '0200034465'),
(37, 'Christok Industries Ltd.', 'Kojokrom', '0200034464'),
(38, 'Intelligent Building Solutions Limited', 'Tamale', '0200034462'),
(39, 'Felixco Tools Engineering Works', 'tema', '0200034470'),
(40, 'Sulas Enterprise', 'Teshie', '0200034472'),
(41, 'Bokay Ventures', 'Nungua Barrier', '0200034473'),
(42, 'Fredy\'s Audio', 'Nima', '0200034474'),
(43, 'Intercom Programming and Manufacturing CO. ltd.', 'Teshie', '0200034475'),
(44, 'G-Awutey Ent', 'Kaneshie', '0200034477'),
(45, 'Kathryn Akpabli', 'Agbogba', '0200034478'),
(46, 'Premium Technologies', 'Akwatia', '0200034479'),
(47, 'Setho Tailoring Shop', 'Estate Junction', '0200035678'),
(48, 'Devnik Ghana Limited', 'Kasoa-Mexico', '0200034481'),
(49, 'Vasco Multimedia Services', 'Tesano Total', '0200034482'),
(50, 'Multi-Art Engineering Services', 'Accra New Town', '0200034484'),
(51, 'B.A. Purple Page Enterprise', 'Tema', '0200034485'),
(52, 'Guardian Tech', 'Abeka Total', '0200034487'),
(53, 'Sun Electricals Limited', 'KojoKrom', '0200034489'),
(54, 'Forever Construction', 'Free Pipe', '0200034123'),
(57, 'Banku Services', 'Teshie-Nungua', '0200034432');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers_archive`
--

DROP TABLE IF EXISTS `suppliers_archive`;
CREATE TABLE IF NOT EXISTS `suppliers_archive` (
  `sup_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `location` varchar(250) NOT NULL,
  `number` varchar(10) NOT NULL,
  PRIMARY KEY (`sup_id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `suppliers_archive`
--

INSERT INTO `suppliers_archive` (`sup_id`, `name`, `location`, `number`) VALUES
(59, 'Hp Services', 'Tema', '0200000001'),
(4, 'ice', 'Tema', '0200000003'),
(55, 'Signage GH. Ltd', 'Nima', '0200030000'),
(56, 'Ismail Abdulai-Saiku', 'Teshie-Nungua', '0200123345');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
