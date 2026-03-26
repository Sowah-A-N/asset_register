-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 07, 2025 at 01:34 PM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

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
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `user_password` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `asset_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `grv_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `serial_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `pv_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `id_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `supplier_name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_class` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_class` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'on campus',
  `user` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `current_year` int NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `additions` decimal(20,2) NOT NULL,
  `additions_dollar` decimal(20,2) GENERATED ALWAYS AS ((`additions` / `dollar_rate_used`)) VIRTUAL,
  `disposals` int NOT NULL DEFAULT '0',
  `disposed` int NOT NULL DEFAULT '0',
  `active_res_value` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dollar_rate_used` double NOT NULL,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_id`),
  KEY `asset_class` (`asset_class`),
  KEY `asset_type` (`asset_type`),
  KEY `location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(1, 'Peugeot 3008', '00001', 'VF3M45GYVPS012514', '00001', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-01-29', 2025, '0.00', '322560.00', 0, 0, '0.00', 9, '2025-01-30 14:22:27'),
(2, 'Peugeot LandTrek', '000003', 'VR3FDAFDJN3016395', '00003', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Henry Snow', '2024-06-20', 2025, '0.00', '324000.00', 0, 0, '0.00', 9, '2025-01-30 14:29:42'),
(3, 'Peugeot LandTrek', '000005', 'VR3FDAFDJN3015640', '00005', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-06-20', 2025, '0.00', '324000.00', 0, 0, '0.00', 9, '2025-01-30 14:37:27'),
(4, 'Toyota Haice Highroof', '000007', 'JFTBB90P706057042', '00007', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-10-02', 2025, '0.00', '884041.97', 0, 0, '0.00', 10, '2025-01-30 14:44:18'),
(5, ' BEING VARIATION WORKS FOR THE OFFICE EXTENSION AT', ' qw33550 ', ' GJ: 65105 ', 'oooo14', 'RMU///0/31', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-01-31', 2025, '0.00', '68064.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(6, ' BEING ADDITIONAL WORKS -OFFICE EXTENSION IN BOTH ', ' qw33551 ', ' GJ: 65106 ', 'oooo15', 'RMU///0/32', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-01', 2025, '0.00', '197642.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(7, ' RAZOR WIRE ON THE FENCE WALLS OF RMU SAKUMONO RES', ' qw33552 ', ' GJ: 57488 ', 'oooo16', 'RMU///0/33', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-05', 2025, '0.00', '22450.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(8, ' PAYMENT FOR REPAIRS OF THE RESERVIOUR TANKS AND V', ' qw33553 ', ' GJ: 57374 ', 'oooo17', 'RMU///0/34', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-07', 2025, '0.00', '65600.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(9, ' BEING PAINTING MATERIALS, LAYING OF WASTE PIPES,C', ' qw33554 ', ' GJ: 65108 ', 'oooo18', 'RMU///0/35', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-19', 2025, '0.00', '64130.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(10, ' BEING RENOVATION OF NAUTICAL SCIENCE DEPARTMENT W', ' qw33555 ', ' GJ: 65112 ', 'oooo19', 'RMU///0/36', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-29', 2025, '0.00', '69412.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(11, ' CONSTRUCTION OF CONCRETE POLYTANK PLATFORM AT THE', ' qw33556 ', ' GJ: 65114 ', 'oooo20', 'RMU///0/37', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-29', 2025, '0.00', '36984.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(12, ' RENOVATION OF BUNGALOW R5 BY JASEKO MULTIPURPOSE ', ' qw33557 ', ' GJ: 65113 ', 'oooo21', 'RMU///0/38', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-29', 2025, '0.00', '190577.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(13, ' BEING RENOVATION OF THE ECDIS LAB BY FOREVER CONS', ' qw33558 ', ' GJ: 65110 ', 'oooo22', 'RMU///0/39', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-03-08', 2025, '0.00', '54065.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(14, ' BEING EXTERNAL WORKS AT THE OFFICE EXTENSION BY A', ' qw33559 ', ' GJ: 65107 ', 'oooo23', 'RMU///0/40', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-01', 2025, '0.00', '26840.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(15, ' PURCHASE OF 24PCS OF 17FT LONG AND 11PCS OF 22FT ', ' qw33560 ', ' P: 122376 ', 'oooo24', 'RMU///0/41', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-04', 2025, '0.00', '36994.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(16, ' PURCHASE OF MATERIALS FOR ALL THE HANDRAILS FOR V', ' qw33561 ', ' P: 122457 ', 'oooo25', 'RMU///0/42', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-12', 2025, '0.00', '61010.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(17, ' PURCHASE OF 1NR POLYTANK-RAMBO 1000 TO REPLACE TH', ' qw33562 ', ' P: 124478 ', 'oooo26', 'RMU///0/43', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-18', 2025, '0.00', '9700.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(18, ' PAYMENT FOR THE REPLACING OF 2NO. FIRE HYDRANT AT', ' qw33563 ', ' P: 126623 ', 'oooo27', 'RMU///0/44', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-05-02', 2025, '0.00', '19630.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(19, ' RE-ISSUE OF CANCELLED CHEQUE FOR PURCHASE OF POLY', ' qw33564 ', ' P: 129869 ', 'oooo28', 'RMU///0/45', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-05-27', 2025, '0.00', '11640.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(20, ' PAYMENT FOR CEILING WORKS AT THE OFFICE COMPLEX-M', ' qw33565 ', ' P: 135195 ', 'oooo29', 'RMU///0/46', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-06-21', 2025, '0.00', '30170.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(21, ' WORKS ON THE LEAKAGES IN THE SLAB AT THE 1ST AND ', ' qw33566 ', ' P: 135326 ', 'oooo30', 'RMU///0/47', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-07-01', 2025, '0.00', '37490.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(22, ' BEING CONTRACT FOR THE RE- ROOFING OF THE  BRIDGE', ' qw33567 ', ' GJ: 78642 ', 'oooo31', 'RMU///0/48', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-07-31', 2025, '0.00', '69785.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(23, ' ASSESSMENT OF ROOF AND RECTIFICATION OF PERSISTEN', ' qw33568 ', ' P: 137618 ', 'oooo32', 'RMU///0/49', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-08-06', 2025, '0.00', '73975.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(24, ' BEING CONTRACT FOR THE RENOVATION OF SAKUMO NO RE', ' qw33569 ', ' GJ: 78643 ', 'oooo33', 'RMU///0/50', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-10-14', 2025, '0.00', '116871.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(25, ' BEING THE COST FOR THE RENOVATION OF RMU SAKUMONO', ' qw33570 ', ' GJ: 84998 ', 'oooo34', 'RMU///0/51', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-10-14', 2025, '0.00', '116871.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(26, ' COST OF CEILING ACHIVES CORRIDOR AND EXTERNAL WOR', ' qw33571 ', ' GJ: 84999 ', 'oooo35', 'RMU///0/52', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-10-31', 2025, '0.00', '92400.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(27, ' FABRICATION, INSTALLATION AND PAINTING OF 13NR HO', ' qw33572 ', ' P: 153787 ', 'oooo36', 'RMU///0/53', 'Yakubu Furnitures', 'Land And Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-12-09', 2025, '0.00', '93970.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(28, 'PURCHASE OF REFURBISHED SYSTEM UNIT LABEL CORE 158', '2020396', '66325501', '8859701', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-15', 2025, '0.00', '7800.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(29, 'PURCHASE OF HP Z4 WORKSTATION MOTHERBOARD & DDR4, ', '2020397', '66325502', '8859702', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-19', 2025, '0.00', '12500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(30, 'PURCHASE OF INTERNET SWITCH FOR THE STORES UNIT', '2020398', '66325503', '8859703', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-22', 2025, '0.00', '16975.89', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(31, 'PURCHASE OF TWO SOLLATEK UPS- 850VA FOR THE IT UNI', '2020399', '66325504', '8859704', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-12', 2025, '0.00', '4900.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(32, 'PURCHASE OF AUDIO-VISUAL DESKTOP FOR THE AUDITORIU', '2020400', '66325505', '8859705', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-12', 2025, '0.00', '10712.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(33, '50% PAYMENT FOR SUBSCRIPTION AND EXTERNAL TECHNICA', '2020401', '66325506', '8859706', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-14', 2025, '0.00', '11000.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(34, 'PURCHASE OF DELL OPTIPLEX 3050 CPU-DAMAGED POWER-P', '2020402', '66325507', '8859707', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', 2025, '0.00', '18420.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(35, 'PURCHASE OF 4NO. AVS 30 (SOLLATEK) TO BE USED IN C', '2020403', '66325508', '8859708', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', 2025, '0.00', '3508.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(36, 'PURCHASE OF NEW PROJECTORS-6PCS-EPSON EB-E10 (OVER', '2020404', '66325509', '8859709', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', 2025, '0.00', '53700.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(37, 'PAYMENT OF 60% FOR THE COMMENCEMENT OF THE ELECTRO', '2020405', '66325510', '8859710', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', 2025, '0.00', '39150.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(38, 'PURCHASE OF DELL 3050 DESKTOP COMPUTER FOR MSSC DE', '2020406', '66325511', '8859711', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-12', 2025, '0.00', '12825.28', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(39, 'PURCHASE OF 6 NEW UBIQUITI M2 FOR THE NAUTICAL STU', '2020407', '66325512', '8859712', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', 2025, '0.00', '22200.03', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(40, 'PURCHASE OF AVS 30, ULTIMA UPS 850VA & MG2  FOR TH', '2020408', '66325513', '8859713', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', 2025, '0.00', '5856.97', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(41, 'PURCHASE OF SIGNAL FIRE OPTICAL FUSION SPLICER, 8S', '2020409', '66325514', '8859714', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', 2025, '0.00', '40714.60', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(42, 'PURCHASE OF NEW 850VA UPS FOR THE RESEARCH DEPT.', '2020410', '66325515', '8859715', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', 2025, '0.00', '1225.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(43, 'PURCHASE OF 2NR PROJECTORS FOR THE WEEKEND SCHOOL', '2020411', '66325516', '8859716', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', 2025, '0.00', '17900.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(44, 'PURCHASE OF CHARGER AND EXTERNAL HARD DRIVE FOR PU', '2020412', '66325517', '8859717', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', 2025, '0.00', '2035.73', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(45, 'PURCHASE OF NEW NETWORK SWITCH FOR THE DSU BLOCK', '2020413', '66325518', '8859718', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-04', 2025, '0.00', '16975.89', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(46, 'PURCHASE OF ONE DESKTOP COMPUTER FOR USE AT THE AR', '2020414', '66325519', '8859719', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-18', 2025, '0.00', '12825.28', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(47, 'PURCHASE OF HP LASERJET M402 DN PRINTER FOR THE AD', '2020415', '66325520', '8859720', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-18', 2025, '0.00', '9800.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(48, 'PURCHASE OF 2NO DESKTOP COMPUTER FOR THE NEW IT TE', '2020416', '66325521', '8859721', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-26', 2025, '0.00', '25967.01', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(49, 'PURCHASE OF 4 PCS OF EXTERNAL HDD TOSHIBA 4TB USB ', '2020417', '66325522', '8859722', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-27', 2025, '0.00', '6731.42', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(50, 'PURCHASE OF GTX 1070 GRAPHIC CARD FOR NS DEPT.', '2020418', '66325523', '8859723', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-28', 2025, '0.00', '3650.40', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(51, 'PURCHASE OF LAPTOPS FOR THE FINANCE DEPT., MSSC, D', '2020419', '66325524', '8859724', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-28', 2025, '0.00', '50710.40', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(52, 'PURCHASE OF SOLLATEK EXTENSION BOARDS FOR MSSC AND', '2020420', '66325525', '8859725', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-04', 2025, '0.00', '22930.07', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(53, 'PURCHASE OF 6 PCS OF DELL OPTIPLEX 7040 MT SYSTEM ', '2020421', '66325526', '8859726', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-04', 2025, '0.00', '65637.81', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(54, 'PAYMENT OF INCENTIVES FOR THE SOFTWARE DEVELOPMENT', '2020422', '66325527', '8859727', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-10', 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(55, 'PAYMENT OF INCENTIVES FOR THE SOFTWARE DEVELOPMENT', '2020423', '66325528', '8859728', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-10', 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(56, 'PAYMENT OF INCENTIVES FOR THE SOFTWARE DEVELOPMENT', '2020424', '66325529', '8859729', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-10', 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(57, 'PURCHASE OF MULTIFUNCTIONAL HP LASER JET PRINTER F', '2020425', '66325530', '8859730', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-11', 2025, '0.00', '2683.96', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(58, 'PURCHASE OF 2PCS OF 24\'\' MONITOR WITH ID-PORT/ HDM', '2020426', '66325531', '8859731', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', 2025, '0.00', '3536.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(59, 'PURCHASE OF 2NO HP LASERJECT 400 3DN PRINTER FOR T', '2020427', '66325532', '8859732', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', 2025, '0.00', '10039.68', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(60, 'PURCHASE OF DELL OPTIPLEX 7000, INTEL CORE 8GB RAM', '2020428', '66325533', '8859733', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-10', 2025, '0.00', '64272.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(61, 'PURCHASE OF CCTV CAMERA SYSTEM FOR RMU\'S MAIN WEST', '2020429', '66325534', '8859734', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-15', 2025, '0.00', '44537.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(62, 'PURCHASE OF 2 SOLLATEK UPS 1,500 VA TO REPLACE BLW', '2020430', '66325535', '8859735', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-16', 2025, '0.00', '7034.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(63, 'PURCHASE OF 1NR LAPTOP FOR THE OFFICE OF HEAD, DSU', '2020431', '66325536', '8859736', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-19', 2025, '0.00', '12677.60', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(64, 'PAYMENT FOR GARNET MICROTIK ROUTER CCR2116-12G-45+', '2020432', '66325537', '8859737', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-23', 2025, '0.00', '22500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(65, 'PURCHASE OF DESKTOP COMPUTER FOR NEW ADMIN. ASSIST', '2020433', '66325538', '8859738', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '81246.35', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(66, 'PURCHASE OF 2PCS OF NEW SOLLATEK UPS 850VA FOR AG.', '2020434', '66325539', '8859739', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '23254.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(67, 'PURCHASE OF UBIQUITI UNIFI M2 STATION FOR NSD', '2020435', '66325540', '8859740', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '16450.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(68, 'PURCHASE OF FOUR BACK-UP HARD DISK 8TB FOR IT UNIT', '2020436', '66325541', '8859741', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '60525.79', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(69, 'PURCHASE OF IT ITEMS FOR THE BUSINESS DEVELOPMENT ', '2020437', '66325542', '8859742', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-27', 2025, '0.00', '29987.40', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(70, 'PURCHASE AND INSTALLATION OF 1NR PROJECTOR WITH DR', '2020438', '66325543', '8859743', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-30', 2025, '0.00', '52112.48', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(71, 'PURCHASE OF 4PCS OF 8TB EXTERNAL HARD DRIVE TO BE ', '2020439', '66325544', '8859744', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-11', 2025, '0.00', '19747.80', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(72, 'PURCHASE OF 2 SURVEILANCE HARD DISK DRIVES FOR CCT', '2020440', '66325545', '8859745', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', 2025, '0.00', '2917.66', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(73, 'PURCHASE OF HP LASERJET COLOUR PRINTER FOR THE DES', '2020441', '66325546', '8859746', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-24', 2025, '0.00', '13500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(74, 'PURCHASE OF TWO NEW UPS FOR STUDENTS AFFAIRS DEPT.', '2020442', '66325547', '8859747', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-04', 2025, '0.00', '4758.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(75, 'PURCHASE OF TWO GIGABIT ROUTERS FOR IT UNIT SERVER', '2020443', '66325548', '8859748', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-04', 2025, '0.00', '116899.36', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(76, 'PURCHASE OF A NEW COMPUTER SYSTEM UNIT TO REPLACE ', '2020444', '66325549', '8859749', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-16', 2025, '0.00', '25499.99', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(77, 'PURCHASE OF 10PCS OF UPS 1000 VA FOR ICT DEPARTMEN', '2020445', '66325550', '8859750', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-24', 2025, '0.00', '6600.03', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(78, 'PAYMENT FOR THE PURCHASE OF NEW 19 INCH MONITOR FO', '2020446', '66325551', '8859751', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-15', 2025, '0.00', '5400.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(79, 'PURCHASE OF UPS FOR THE INTERNAL SWITCH', '2020447', '66325552', '8859752', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-15', 2025, '0.00', '3751.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(80, 'PURCHASE OF COMPUTER FOR ADMINSTRATIVE ASSISTANTS ', '2020448', '66325553', '8859753', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-15', 2025, '0.00', '15420.35', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(81, 'PAYMENT FOR ONE STM-I 155MBPS LIMIT FOR THE MONTH ', '2020449', '66325554', '8859754', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-18', 2025, '0.00', '22500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(82, 'PAYMENT TO KNUST-UITS MAINTENANCE FEES FOR SCHOOL ', '2020450', '66325555', '8859755', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-18', 2025, '0.00', '50000.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(83, 'PROCUREMENT OF A WIFI ROUTER FOR THE TRANSPORT UNI', '2020451', '66325556', '8859756', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-19', 2025, '0.00', '3717.95', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(84, 'PURCHASE OF A COLOURED PRINTER WITH SCANNER FOR TH', '2020452', '66325557', '8859757', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-22', 2025, '0.00', '13900.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(85, 'PURCHASE OF THREE COMPUTERS (HP-COMPACT ELITE 8300', '2020453', '66325558', '8859758', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-22', 2025, '0.00', '32136.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(86, 'PURCHASE OF 1000VA SOLLATEK UPS FOR THE ROUTER AT ', '2020454', '66325559', '8859759', 'RMU/////', 'Vendor Name', 'Computer & Accessories ', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-12-23', 2025, '0.00', '8617.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(87, 'PURCHASE OF BASIC WORKING EQUIPMENT FOR N.S DEPART', '161', '225712', '118992', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-21', 2025, '0.00', '13980.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(88, 'PURCHASE OF BASIC WORKING EQUIPMENT FOR THE HOSPIT', '162', '225713', '119048', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', 2025, '0.00', '3229.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(89, 'PURCHASE OF 1NR 2HP STAINLESS STEEL SUBMERSIBLE PU', '163', '225714', '119090', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-01', 2025, '0.00', '7950.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(90, 'PURCHASE OF PUBLIC ADDRESS SYSTEM FOR ORIENTATION ', '164', '225715', '119119', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-05', 2025, '0.00', '18650.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(91, 'PURCHASE OF NEW MOISTURE EXTRACTOR X 1PCS WITH TIM', '165', '225716', '122304', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-22', 2025, '0.00', '6600.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(92, 'PURCHASE AND INSTALLATION OF 1NR PUMPING MACHINE F', '166', '225717', '122399', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-08', 2025, '0.00', '132150.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(93, 'PURCHASE OF 2NO. PULSE OXIMETER FOR THE SICK BAY U', '167', '225718', '129866', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-27', 2025, '0.00', '1500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(94, 'PROVISION OF 2NR HOME -USED MOWERS FOR THE UNIVERS', '168', '225719', '130945', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-31', 2025, '0.00', '4000.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(95, 'PURCHASE OF 6 PCS OF OVERALL JACKETS FOR THE IT UN', '169', '225720', '130982', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-07', 2025, '0.00', '3400.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(96, 'PURCHASE OF 2 NEW SMOKE GENERATOR FOR FIRE FIGHTIN', '170', '225721', '137666', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-12', 2025, '0.00', '23161.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(97, 'PURCHASE OF 1NR CHAINSAW MACHINE- BRUSHLESS 16 LIT', '171', '225722', '142012', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', 2025, '0.00', '8000.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(98, 'REPAIR OF THREE PCS OF OPTIMUM B30 BS-VARIO PILLAR', '172', '225723', '146425', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-22', 2025, '0.00', '11650.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(99, 'PAYMENT FOR THE PURCHASE OF ELECTRONIC TOUCH SCREE', '173', '225724', '153752', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-29', 2025, '0.00', '17500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(100, 'BEING THE COST OF 3.0 TONS LPG FORKLIFT PURCHASED ', 'rmu///', 'C1129M00015-HT30TS', 'GJ: 84989', 'rmu///', 'supplier', 'Forklift', 'test', 'OWNED', 'on campus', 'USERNAME', '2024-04-29', 2025, '0.00', '438840.00', 0, 0, '0.00', 9, '2025-03-17 19:20:10'),
(101, 'PAYMENT FOR PHAROS GMDSS SIMULATORS FOR THE GMDSS LABORATORY FROM POSEIDON SIMULATION AS', '11122345', '1112234', 'GJ: 84997', '900001', 'SUPPLIER NAME', 'GMDSS Simulator', '', 'OWNED', 'on campus', 'USER', '2024-10-02', 2025, '0.00', '2247600.00', 0, 0, '0.00', 10, '2025-03-18 10:07:13'),
(102, 'PURCHASE OF TWENTY PCS OF LIFE JACKETS ', 'N/A', 'N/A', 'P: 112171', 'N/A', 'SUPPLIER NAME', 'Teaching Equipment', 'N/A', 'OWNED', 'MSSC DEPT', 'USERNAME', '2025-05-09', 2025, '0.00', '58240.00', 0, 0, '0.00', 8, '2025-03-18 10:15:47'),
(103, 'REPAIR OF TWO ELECTRODE OVENS', 'N/A', 'N/A', 'P: 135447', 'N/A', 'SUPPLIER NAME', 'Teaching Equipment', 'TEST', 'OWNED', 'MODEC WELDING CENTER', 'USERNAME', '2025-07-15', 2025, '0.00', '34863.40', 0, 0, '0.00', 10, '2025-03-18 10:18:17'),
(104, 'THE COST OF TWO DESKTOP BRIDGE SIMULATORS PURCHASED FROM WARTSILLA VOYAGE OY', 'N/A', 'N/A', 'GJ: 84996', 'N/A', 'SUPPLIER NAME', 'Bridge Simulator', 'TEST', 'OWNED', 'on campus', 'USERNAME', '2024-10-01', 2025, '0.00', '727283.40', 0, 0, '0.00', 10, '2025-03-18 10:22:48'),
(105, 'PURCHASE OF 2-WAY MAIN SWITCH CIRCUIT BREAKER FOR MSSC CLASSROOM, EXTENSION CABLE AND INDUSTRIAL FAN FOR STUDENT AFFAIRS DEPT.', '44', '112100', '112018', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '0000-00-00', 2025, '0.00', '5824.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(106, 'PURCHASE OF 2PCS OF FUSE ASSEMBLY FOR XEROX WORK CENTRE 5325 PHOTOCOPIER MACHINES', '48', '112104', '118631', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-15', 2025, '0.00', '25151.02', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(107, 'PURCHASE OF INR 3-IN-1 WAITING SOFA FOR UR\'S OFFICE & INR 42\" DIGITAL SATELLITE TV FOR USE AT THE PORTERS\' LODGE-BACK ACCOMODATION', '42', '112098', '118841', 'RMU/UR/OE/B2/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'UNIVERSITY REGISTRAR ', 'USER NAME', '2024-02-06', 2025, '0.00', '1149.50', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(108, 'PURCHASE OF INR 3-IN-1 WAITING SOFA FOR UR\'S OFFICE & INR 42\" DIGITAL SATELLITE TV FOR USE AT THE PORTERS\' LODGE-BACK ACCOMODATION', '19', '112075', '118841', 'RMU/CSA/OE/TV42/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'BACK ACCOMMODATION - CSA', 'USER NAME', '2024-02-06', 2025, '0.00', '1149.50', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(109, 'PURCHASE OF 3NR OUTDOOR UNITS 2.0HP AC FOR THE TRANSPORT OFFICER\'S OFFICE, DSU INNER OFFICE & IT TECHNICIANS OFFICE', '26', '112082', '118932', 'RMU/DSU/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'DSU INNER OFFICE ', 'USER NAME', '2024-02-14', 2025, '0.00', '2300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(110, 'PURCHASE OF 3NR OUTDOOR UNITS 2.0HP AC FOR THE TRANSPORT OFFICER\'S OFFICE, DSU INNER OFFICE & IT TECHNICIANS OFFICE', '31', '112087', '118932', 'RMU/ITT/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'IT TECHNICIANS OFFICE ', 'USER NAME', '2024-02-14', 2025, '0.00', '2300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(111, 'PURCHASE OF 3NR OUTDOOR UNITS 2.0HP AC FOR THE TRANSPORT OFFICER\'S OFFICE, DSU INNER OFFICE & IT TECHNICIANS OFFICE', '40', '112096', '118932', 'RMU/TO/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'TRANSPORT OFFICER', 'USER NAME', '2024-02-14', 2025, '0.00', '2300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(112, 'PURCHASE OF 2.0 HP AIR CONDITIONER FOR THE OFFICE OF THE DEPUTY REGISTRAR-ACADEMIC', '23', '112079', '119042', 'RMU/DRA/OE/01/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'DEPUTY REGISTRAR - ADMIN.', 'USER NAME', '2024-02-27', 2025, '0.00', '6116.15', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(113, 'PURCHASE OF AC FOR ACCOUNTS OFFICE EXTENSION', '15', '112071', '119060', 'RMU/BO/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ACCOUNTS OFFICE EXTENSION - BO', 'USER NAME', '2024-02-28', 2025, '0.00', '6116.14', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(114, 'PURCHASE OF SOLIT 2.5HP AIR-CONDITIONER FOR GRADUATE SCHOOL', '30', '112086', '119124', 'RMU/SGS/OE/017/017/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'GRADUATE SCHOOL ', 'USER NAME', '2024-03-08', 2025, '0.00', '8255.20', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(115, 'PURCHASE OF TELEPHONE FOR ACCOUNTS OFFICER ONE (1), CASHIER AND ADM ASSISTANT', '50', '112106', '122283', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', 2025, '0.00', '3300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(116, 'PAYMENT FOR FIRE EXTINGUISHER & FIRE ACTION  STICKER AT MSSC, ', '49', '112105', '126628', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-08', 2025, '0.00', '670.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(117, 'PURCHASE OF 7 PCS EXTENTION BOARDS FOR LIBRARY, 2 PCS OF SVS O4 AND 5 PCS 850VA UPS FOR IT UNIT', '51', '112107', '126700', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-08', 2025, '0.00', '12891.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(118, 'PURCHASE OF NEW AIR CONDITIONING FOR THE MARINE ENGINEERING STAFF COMMON ROOM', '33', '112089', '129860', 'RMU/ME/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', ' MARINE ENGINEERING  ', 'USER NAME', '2024-05-27', 2025, '0.00', '84876.43', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(119, 'PURCHASE OF  A NEW ROBUST MONEY COUNTING MACHINE FOR THE RMU CASHIER', '14', '112070', '129872', 'RMU/ACC/OE/MCM/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ACCOUNTS - CASHIER', 'USER NAME', '2024-05-27', 2025, '0.00', '21996.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(120, 'PURCHASE OF SOLLATEK SVS-04 (1) FOR ADMINISTRATION', '52', '112108', '130954', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-04', 2025, '0.00', '1848.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(121, 'PURCHASE OF 3 IN 1 CABINET FOR DEP. REGISTRAR-ADMIN\'S OFFICE, 3NR DOUBLE DOOR METAL CABINET FOR THE MARINE HOSPITALITY DEPT. AND GMDSS LAB', '24', '112080', '130983', 'RMU/DRA/OE/026/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'DEPUTY REGISTRAR - ADMIN.', 'USER NAME', '2024-06-07', 2025, '0.00', '6155.95', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(122, 'PURCHASE OF 3 IN 1 CABINET FOR DEP. REGISTRAR-ADMIN\'S OFFICE, 3NR DOUBLE DOOR METAL CABINET FOR THE MARINE HOSPITALITY DEPT. AND GMDSS LAB', '34', '112090', '130983', 'RMU/DSU/OE/026/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'MARINE HOSPITALITY /DSU', 'USER NAME', '2024-06-07', 2025, '0.00', '6155.95', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(123, 'PURCHASE OF 3 IN 1 CABINET FOR DEP. REGISTRAR-ADMIN\'S OFFICE, 3NR DOUBLE DOOR METAL CABINET FOR THE MARINE HOSPITALITY DEPT. AND GMDSS LAB', '29', '112085', '130983', 'RMU/GMDSS/OE/D026/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'GMDSS', 'USER NAME', '2024-06-07', 2025, '0.00', '6155.95', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(124, 'PURCHASE OF INR 42\'\' SMART TELEVISION FOR THE CONFERENCE ROOM IN THE NEW OFFICE EXTENSION', '16', '112072', '135328', 'RMU/AAB/OE/TV42/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ADMINISTRATION ANNEX', 'USER NAME', '2024-07-01', 2025, '0.00', '3299.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(125, 'PURCHASE OF 2-WAY MAIN SWITCH CIRCUIT BREAKER FOR MSSC CLASSROOM, EXTENSION CABLE AND INDUSTRIAL FAN FOR STUDENT AFFAIRS DEPT.', '22', '112078', '135490', 'RMU/CSA/OE/SFAN/01 - 2/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'CSA', 'USER NAME', '2024-07-16', 2025, '0.00', '5824.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(126, 'PC: KETTLE USED AT ADMINISTRATION, UR, HR\'S OFFICE  (PV O25512),', '53', '112109', '137773', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-04', 2025, '0.00', '400.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(127, 'PURCHASE OF 1NR 55\'\' DIGITAL TV WITH BRACKET FOR THE STAFF CANTEEN AND 2NR AIR CONDITIONERS FOR THE RECOVERY ROOM AT SICK BAY', '17', '112073', '140876', 'RMU/AAB/OE/TV55/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ADMINISTRATION ANNEX CANTEEN', 'USER NAME', '2024-08-22', 2025, '0.00', '31232.61', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(128, 'PURCHASE OF 1NR 55\'\' DIGITAL TV WITH BRACKET FOR THE STAFF CANTEEN AND 2NR AIR CONDITIONERS FOR THE RECOVERY ROOM AT SICK BAY', '54', '112110', '140876', 'RMU/SBU/OE/017/01 - 2/22', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '31232.61', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(129, 'PAYMENT FOR ONE PC SVS AND ONE PC UPS FOR INTERNAL AUDIT UNIT', '55', '112111', '143057', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-12', 2025, '0.00', '5666.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(130, 'PURCHASE OF 1PC OF UPS FOR THE GAMBIA HOSTEL', '56', '112112', '143071', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', 2025, '0.00', '6923.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(131, 'PAYMENT FOR THE PURCHASE OF 1.5HP AIR CONDITIONER FOR THE CASHIER\'S OFFICE', '13', '112069', '146507', 'RMU/ACC/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ACCONTS - CASHIER ', 'USER NAME', '2024-10-29', 2025, '0.00', '4500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(132, 'PURCHASE OF NASCO TABLE TOP REFRIDGERATOR FOR VSTC', '43', '112099', '151674', 'RMU/VSTC/OE/F017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'VSTC ', 'USER NAME', '2024-11-22', 2025, '0.00', '4500.01', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(133, 'PURCHASE OF INR 3-IN-1 WAITING SOFA FOR UR\'S OFFICE & INR 42\" DIGITAL SATELLITE TV FOR USE AT THE PORTERS\' LODGE-BACK ACCOMODATION', '34527', '457146', 'P:118841', 'tg23494', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-06', 2025, '0.00', '9500.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(134, 'PURCHASE OF 2NO SINGLE WAITING CHAIRS FOR THE OFFICE OF THE UNIVERSITY REGISTRAR', '34528', '457147', 'P:118854', 'tg23495', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-07', 2025, '0.00', '25480.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(135, 'SEWING AND INSTALLATION OD 100PCS OF CURTAINS FOR FEMALES AT BACK ACCOMODATION', '34529', '457148', 'P:118875', 'tg23496', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-08', 2025, '0.00', '25180.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(136, 'PC: PURCHASE OF FOLDABLE TABLE TO DISPLAY MARKETING ITEMS (PV O23529),', '34530', '457149', 'P:119161', 'tg23497', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-09', 2025, '0.00', '400.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(137, 'PURCHASE OF 3-IN-ONE SOFAR AND CENTRE TABLE FOR DF\'S OFFICE', '34531', '457150', 'P:122348', 'tg23498', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', 2025, '0.00', '9500.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(138, 'PURCHASE OF TWO PCS OF COFFEE TABLE FOR DIRECTOR OF FINANCE & UR\'S OFFICES', '34532', '457151', 'P:125526', 'tg23499', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-25', 2025, '0.00', '6620.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(139, 'REFURBISHMENT OF 160 MATTRESSES FOR THE STUDENTS ACCOMODATION (CADET & BACK)', '34533', '457152', 'P:125529', 'tg23500', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-26', 2025, '0.00', '144000.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(140, 'PURCHASE OF NINE PCS OF EXEC. SWIVEL CHAIRS FOR IT, LIBRARY AND REGISTRY', '34534', '457153', 'P:126682', 'tg23501', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-07', 2025, '0.00', '28080.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(143, 'PROVISION OF CURTAINS FOR THE NEW OFFICE EXTENSION AT THE MARKETING UNIT & IT TECHNICIAN OFFICE', '34537', '457156', 'P:129875', 'tg23504', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-28', 2025, '0.00', '14240.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(144, 'PURCHASE OF SWIVEL CHAIR FOR THE SNR. STORES OFFICER AND LECTURE HALL CHAIRS FOR GMDSS LAB', '34538', '457157', 'P:130980', 'tg23505', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-07', 2025, '0.00', '48048.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(145, 'PURCHASE OF EIGHT EXECUTIVE CHAIRS FOR THE 18TH CONGREGATION  DIFFERENCE ON EXCHANGE (USD) FROM 9.000000000 TO 10.000000000', '34539', '457158', 'P:135145', 'tg23506', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-20', 2025, '0.00', '17600.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(146, 'PURCHACE OF SWIVEL CHAIRS, L-SHAPE DESK AND VISITORS CHAIR FOR THE BUSINESS DEV. CENTRE OFFICES ', '34540', '457159', 'P:135314', 'tg23507', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', 2025, '0.00', '13416.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(147, 'PURCHASE OF LECTURE ROOM TABLES FOR GMDSS LABORATORY ', '34541', '457160', 'P:135315', 'tg23508', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', 2025, '0.00', '30840.70', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(148, 'PURCHASE OF CONFERENCE TABLE FOR THE CONFERENCE ROOM AT THE NEW OFFICE EXTENSION AT THE MARKETING UNIT', '34542', '457161', 'P:135318', 'tg23509', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', 2025, '0.00', '69680.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(149, 'REFUND FOR THE PURCHASE OF CURTAINS FOR UR\'S AND DEPUTY REGISTRAR-ADMIN\'S OFFICE', '34543', '457162', 'P:135297', 'tg23510', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', 2025, '0.00', '1056.40', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(150, 'REPLACEMENT AND REPAIR OF BROKEN AUDITORIUM FIXED CHAIRS', '34544', '457163', 'P:135417', 'tg23511', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-12', 2025, '0.00', '17200.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(151, 'PURCHASE OF 1NR EXECUTIVE SWIVEL CHAIR FOR QMS OFFICE', '34545', '457164', 'P:137586', 'tg23512', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-30', 2025, '0.00', '4160.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(152, 'PURCHASE OF SWIVEL CHAIR FOR PROVOST', '34546', '457165', 'P:140875', 'tg23513', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '7800.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(153, 'PURCHASE OF 1NO 3-IN-1 WAITING CHAIR (OUTSIDE) FOR ADMINISTRATION', '34547', '457166', 'P:140878', 'tg23514', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', 2025, '0.00', '3328.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(154, 'PURCHASE OF 2NR EACH OF 1/2 10\'\' HIGH DENSITY MATTRESSES AND 1/2 WOODEN BEDS TO BE USED AT THE GAMBIA HOSTEL', '34548', '457167', 'P:140918', 'tg23515', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-27', 2025, '0.00', '12800.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(155, 'PURCHASE OF DESK AND VISITORS WAITING CHAIR FOR PROVOST\'S OFFICE', '34549', '457168', 'P:142009', 'tg23516', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', 2025, '0.00', '11128.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(156, 'PURCHASE OF 4NR STAINLESS STEEL 4-IN-1 WAITING CHAIRS FOR REGISTRY AND ACCOUNTS FRONTAGE', '34550', '457169', 'P:142010', 'tg23517', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', 2025, '0.00', '14000.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(157, 'PURCHASE OF CONFERENCE CHAIR, SWIVEL CHAIR AND DESK FOR MARINE HOSPITALITY DEPT.', '34551', '457170', 'P:142011', 'tg23518', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', 2025, '0.00', '8492.36', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(158, 'PURCHASE OF 1NR OFFICE DESK AND 1NR CHAIR (SWIVEL) FOR QMS OFFICE', '34552', '457171', 'P:143067', 'tg23519', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', 2025, '0.00', '4437.16', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(159, 'PURCHASE OF SWIVEL CHAIR FOR CASHIER', '34553', '457172', 'P:143066', 'tg23520', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', 2025, '0.00', '3120.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(160, 'PURCHASE OF 3-IN-1 SOFA, ORTHOPEDIC SWIVEL CHAIR FOR PROVOST\'S OFFICE', '34554', '457173', 'P:144299', 'tg23521', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-09', 2025, '0.00', '15700.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(161, 'PURCHASE OF 2NR. 18 INCHES WALL FANS FOR CSA OFFICE BY DSU ', '34555', '457174', 'P:146343', 'tg23522', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-16', 2025, '0.00', '2600.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(162, 'PURCHASE OF 40 CHAIRS(SAME TYPE AS THE ONES AT TRANSPORT DEPT.) FOR 00W ENGINE (PART A) PROGRAMME', '34556', '457175', 'P:153717', 'tg23523', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-28', 2025, '0.00', '56074.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(163, 'PURCHASE OF DUST PROOF COVER FOR SMART BOARD TV 65\'\' FOR TRANSPORT DEPT.', '34557', '457176', 'P:153819', 'tg23524', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-12-10', 2025, '0.00', '11832.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(164, 'PAYMENT OF 20% OF TOTAL CONTRACT VALUE AS MOBILISATION FOR THE CONSTRUCTION  OF DORMITORY AT RMU', 'N/A', 'N/A', 'P: 148573', 'N/A', 'SUPPLIER', 'Building Works in Progress', 'Building Works in Progress', 'OWNED', 'on campus', 'USERNAME', '2024-11-13', 2025, '0.00', '1944000.00', 0, 0, '0.00', 10, '2025-03-25 09:35:27'),
(165, '', 'N/A', 'N/A', 'N/A', 'N/A', '', '', '', '', 'on campus', NULL, '0000-00-00', 0, '0.00', '0.00', 0, 0, '0.00', 0, '2025-04-04 09:45:18'),
(166, 'PURCHASE OF DEFIBRILLATOR AED 2001-CHARGEABLE FOR THE AMBULANCE PAYABLE TO JAKE ADAMZ CO. LTD', 'N/A', 'N/A', 'N/A', 'N/A', 'JAKE ADAMZ CO. LTD', 'Machine And Equipment', 'test sub class', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', 2025, '0.00', '19500.00', 0, 0, '0.00', 10, '2025-04-04 09:51:57'),
(167, 'PAYMENT FOR ONE VENTILATOR AMBULANCE PAYABLE TO PAAYIE ENTERPRISE', 'N/A', 'N/A', 'N/A', 'N/A', 'PAAYIE ENTERPRISE', 'Machine And Equipment', 'Test sub class', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', 2025, '0.00', '104000.00', 0, 0, '0.00', 10, '2025-04-04 09:51:57'),
(168, 'PAYMENT FOR 70KG ANVIL WEIGHT PAYABLE TO B.A. PURPLE', 'N/A', 'N/A', 'N/A', 'N/A', ' B.A. PURPLE', 'Machine And Equipment', 'Test', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', 2025, '0.00', '10000.00', 0, 0, '0.00', 10, '2025-04-04 09:54:10'),
(169, 'REPAIR OF TWO ELECTRODE OVENS AT THE MODEC WELDING CENTER', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Teaching Equipment', 'TEST', 'OWNED', 'on campus', 'User', '2024-07-15', 2025, '0.00', '34863.40', 0, 0, '0.00', 10, '2025-04-07 08:43:17'),
(170, 'PAYMENT FOR SMART TELEVISION SET TO BE USED AT OFFICE COMPLEX PAYABLE TO THE SPOT FURNITURE', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Teaching Equipment', 'test', 'owned', 'on campus', 'USER', '2024-12-31', 0, '0.00', '39662.68', 0, 0, '0.00', 10, '2025-04-07 08:43:17');
INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(171, 'PAYMENT FOR EXECUTIVE ONE EXECUTIVE DESK FOR THE BUSINESS DEVELOPEMT CENTRE', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'BDC', 'USER', '2024-06-21', 2025, '0.00', '3943.47', 0, 0, '0.00', 10, '2025-04-07 08:53:37'),
(172, 'PURCHASE OF 1NR SWIVEL CHAIR FOR THE BUSINESS DEVELOPMENT CENTRE', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'BDC', 'USER', '2024-06-21', 2025, '0.00', '3500.00', 0, 0, '0.00', 10, '2025-04-07 08:53:37'),
(173, 'TWO SWIVEL CHAIRS FOR PROCUREMENT & AG. VC', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'PROC. & VC', 'USER', '2024-12-31', 2025, '0.00', '6068.18', 0, 0, '0.00', 10, '2025-04-07 08:57:18'),
(174, 'PAYMENT FOR CARPENTRY ITEMS TO BE USED AT THE DRAWING ROOM 202', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'DRAWING ROOM', 'USER', '2024-12-31', 2025, '0.00', '95056.00', 0, 0, '0.00', 10, '2025-04-07 08:57:18'),
(175, 'PAYMENT FOR CURTAINS, ACCESSORIES AND INSTALLATION AT THE DRAWING ROOM', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Furnitures & Fixtures', 'Test', 'OWNED', ' DRAWING ROOM', 'USER', '2024-12-31', 2025, '0.00', '5100.00', 0, 0, '0.00', 10, '2025-04-07 09:01:21'),
(176, 'Test Asset', '99', '88', '77', 'RMU///0/25', 'Yakubu Furnitures', 'Teaching Equipment', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2024-02-04', 2025, '0.00', '7000.00', 0, 0, '0.00', 9, '2025-04-07 13:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `assets_archive`
--

DROP TABLE IF EXISTS `assets_archive`;
CREATE TABLE IF NOT EXISTS `assets_archive` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `grv_number` varchar(7) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `serial_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `pv_number` varchar(9) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `id_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `supplier_name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_class` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_class` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_additions_year`
--

DROP TABLE IF EXISTS `asset_additions_year`;
CREATE TABLE IF NOT EXISTS `asset_additions_year` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(255) NOT NULL,
  `year` int NOT NULL,
  `total_additions_cedi` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_additions_dollar` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_disposals_cedi` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_disposals_dollar` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_additions_year`
--

INSERT INTO `asset_additions_year` (`id`, `asset_class`, `year`, `total_additions_cedi`, `total_additions_dollar`, `total_disposals_cedi`, `total_disposals_dollar`) VALUES
(1, 'Motor Vehicles', 2023, '1642159.98', '198707.50', '0.00', '0.00'),
(2, 'Motor Vehicles', 2024, '1854601.97', '198352.44', '0.00', '0.00'),
(3, 'Land And Buildings', 2023, '735621.00', '87637.29', '0.00', '0.00'),
(4, 'Land And Buildings', 2024, '1566270.00', '167348.18', '0.00', '0.00'),
(5, 'Computer & Accessories ', 2023, '936275.68', '113693.09', '0.00', '0.00'),
(6, 'Computer & Accessories ', 2024, '1283373.18', '134041.47', '0.00', '0.00'),
(7, 'Machine And Equipment', 2023, '248329.72', '29602.72', '0.00', '0.00'),
(8, 'Machine And Equipment', 2024, '251770.00', '27304.31', '0.00', '0.00'),
(9, 'Forklift', 2024, '438840.00', '48760.00', '0.00', '0.00'),
(10, 'Refurbished Road', 2023, '7391380.00', '869574.12', '0.00', '0.00'),
(11, 'GMDSS Simulator', 2024, '2247600.00', '224760.00', '0.00', '0.00'),
(12, 'Teaching Equipment', 2025, '93103.40', '10766.34', '0.00', '0.00'),
(13, 'Teaching Equipment', 2023, '11242.00', '1322.59', '0.00', '0.00'),
(14, 'Bridge Simulator', 2024, '727283.40', '72728.34', '0.00', '0.00'),
(15, 'Office Equipment', 2023, '212107.51', '25629.86', '0.00', '0.00'),
(16, 'Office Equipment', 0, '4623.65', '536.44', '0.00', '0.00'),
(17, 'Office Equipment', 2024, '291314.52', '31328.51', '0.00', '0.00'),
(18, 'Furnitures & Fixtures', 2023, '589071.64', '70791.85', '0.00', '0.00'),
(19, 'Furnitures & Fixtures', 2024, '624812.62', '65339.72', '0.00', '0.00'),
(20, 'Building Works in Progress', 2024, '1944000.00', '194400.00', '0.00', '0.00'),
(21, 'Talif V-Sat Project', 2023, '1223445.00', '135938.33', '0.00', '0.00'),
(22, 'Talif V-Sat Project', 2025, '7726138.00', '858459.79', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `asset_allocation`
--

DROP TABLE IF EXISTS `asset_allocation`;
CREATE TABLE IF NOT EXISTS `asset_allocation` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_sn_number` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes`
--

DROP TABLE IF EXISTS `asset_classes`;
CREATE TABLE IF NOT EXISTS `asset_classes` (
  `ast_id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `account_depr_open_bal` decimal(30,2) NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `opbal_plus_additions` decimal(20,2) NOT NULL,
  `dep_rate` float NOT NULL,
  `estimated_life` int NOT NULL,
  `estimated_life_months` int GENERATED ALWAYS AS ((`estimated_life` * 12)) VIRTUAL,
  `depreciation` int NOT NULL DEFAULT '0',
  `depreciated` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ast_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_classes`
--

INSERT INTO `asset_classes` (`ast_id`, `asset_class`, `account_depr_open_bal`, `opening_bal`, `opbal_plus_additions`, `dep_rate`, `estimated_life`, `depreciation`, `depreciated`) VALUES
(1, 'Computer & Accessories ', '1.00', '0.00', '0.00', 0.1, 10, 0, 1),
(2, 'Land And Buildings', '1.00', '0.00', '0.00', 0.02, 50, 0, 1),
(3, 'Motor Vehicles', '0.00', '149932.00', '4274859.97', 0.2, 5, 0, 1),
(4, 'Furnitures & Fixtures', '0.00', '0.00', '0.00', 0.1, 10, 0, 1),
(5, 'Office Equipment', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(8, 'Machine And Equipment', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(9, 'Building Works in Progress', '0.00', '0.00', '0.02', 0, 50, 1, 0),
(10, 'Library Books', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(11, 'Bridge Simulator', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(12, 'Academic Gown', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(13, 'Teaching Equipment', '0.00', '0.00', '7000.00', 0.2, 5, 0, 1),
(15, 'Swimming Pool', '0.00', '0.00', '0.00', 0.1, 10, 0, 1),
(16, 'Stanchion Base', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(17, 'School Band', '0.00', '0.00', '0.00', 0.3, 3, 0, 1),
(18, 'Solar Equipment', '0.00', '0.00', '0.00', 0.25, 4, 0, 1),
(19, 'Refurbished Road', '0.00', '0.00', '0.00', 0.065, 15, 0, 1),
(20, 'Forklift', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(21, 'GMDSS Simulator', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(22, 'Talif V-Sat Project', '0.00', '0.00', '0.00', 0.2, 5, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes_archive`
--

DROP TABLE IF EXISTS `asset_classes_archive`;
CREATE TABLE IF NOT EXISTS `asset_classes_archive` (
  `ast_id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `dep_rate` float NOT NULL,
  PRIMARY KEY (`ast_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_class_opbal_year`
--

DROP TABLE IF EXISTS `asset_class_opbal_year`;
CREATE TABLE IF NOT EXISTS `asset_class_opbal_year` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `opening_balance` decimal(12,2) DEFAULT NULL,
  `total_accum_depr_start` decimal(12,2) DEFAULT '0.00',
  `total_depr_year_charge` decimal(12,2) DEFAULT '0.00',
  `total_accum_depr_end` decimal(12,2) GENERATED ALWAYS AS (((`total_depr_year_charge` + `total_accum_depr_start`) - `disposals_depr`)) VIRTUAL,
  `disposals_depr` decimal(12,2) DEFAULT '0.00',
  `net_book_value` decimal(12,2) GENERATED ALWAYS AS ((`opening_balance` - `total_accum_depr_end`)) VIRTUAL,
  `year` year DEFAULT NULL,
  `expected_life_months` int DEFAULT '0',
  `rate` decimal(6,2) DEFAULT '0.00',
  `depreciated` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_class_opbal_year`
--

INSERT INTO `asset_class_opbal_year` (`id`, `asset_class`, `opening_balance`, `total_accum_depr_start`, `total_depr_year_charge`, `disposals_depr`, `year`, `expected_life_months`, `rate`, `depreciated`) VALUES
(19, 'Computer & Accessories ', '2720357.00', '1064333.46', '140627.40', '0.00', 2024, 120, '8.50', 1),
(20, 'Furnitures & Fixtures', '2278255.00', '395751.50', '158056.52', '0.00', 2024, 120, '8.50', 1),
(23, 'Forklift', '0.00', '0.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(25, 'Refurbished Road', '7391379.00', '493008.50', '0.00', '0.00', 2024, 180, '8.50', 1),
(26, 'Teaching Equipment', '2911437.00', '955034.50', '534270.80', '0.00', 2024, 60, '8.50', 1),
(27, 'Motor Vehicles', '3982547.50', '869737.00', '792914.90', '25162.20', 2024, 60, '8.50', 1),
(30, 'Building Works in Progress', '36202.66', '766644.48', '766644.48', '0.00', 2024, 600, '8.50', 1),
(33, 'Land And Buildings', '1444607359.50', '2176433.50', '28892147.19', '0.00', 2024, 600, '8.50', 1),
(35, 'Machine And Equipment', '4932184.50', '1212976.01', '986436.90', '0.00', 2024, 60, '8.50', 1),
(36, 'Office Equipment', '916920.50', '275838.26', '114074.40', '0.00', 2024, 60, '8.50', 1),
(38, 'Library Books', '16213121.00', '6485247.47', '3814851.66', '0.00', 2024, 60, '8.50', 1),
(39, 'Bridge Simulator', '9220868.00', '9220868.00', '976327.55', '0.00', 2024, 60, '8.50', 1),
(40, 'Academic Gown', '83623.00', '33456.00', '11805.75', '0.00', 2024, 60, '8.50', 1),
(41, 'Swimming Pool', '503081.00', '60375.50', '35511.60', '0.00', 2024, 120, '8.50', 1),
(42, 'Stanchion Base', '541594.50', '147016.00', '76460.40', '0.00', 2024, 60, '8.50', 1),
(43, 'Solar Equipment', '9904463.50', '2232059.37', '1310884.82', '0.00', 2024, 48, '8.50', 1),
(44, 'School Band', '74706.50', '74709.31', '14532.90', '0.00', 2024, 36, '8.50', 1),
(46, 'GMDSS Simulator', '0.00', '0.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(47, 'Talif V-Sat Project', '1836085.00', '1836085.00', '0.00', '0.00', 2024, 0, '8.50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `asset_class_sub_classes`
--

DROP TABLE IF EXISTS `asset_class_sub_classes`;
CREATE TABLE IF NOT EXISTS `asset_class_sub_classes` (
  `T_id` int NOT NULL AUTO_INCREMENT,
  `sub_class` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_class_code` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_class` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`T_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_class_sub_classes`
--

INSERT INTO `asset_class_sub_classes` (`T_id`, `sub_class`, `sub_class_code`, `asset_class`) VALUES
(1, 'Test Sub Class', '', 'Land And Buildings');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location`
--

DROP TABLE IF EXISTS `asset_location`;
CREATE TABLE IF NOT EXISTS `asset_location` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `loc_code` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_location`
--

INSERT INTO `asset_location` (`loc_id`, `location`, `loc_code`) VALUES
(1, 'ICT LAB', ''),
(2, 'Registry Records', ''),
(3, 'TRANSPORT DEPT', '');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location_archive`
--

DROP TABLE IF EXISTS `asset_location_archive`;
CREATE TABLE IF NOT EXISTS `asset_location_archive` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `loc_code` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

DROP TABLE IF EXISTS `asset_type`;
CREATE TABLE IF NOT EXISTS `asset_type` (
  `type_id` int NOT NULL AUTO_INCREMENT,
  `asset_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_type`
--

INSERT INTO `asset_type` (`type_id`, `asset_type`) VALUES
(1, 'Owned'),
(2, 'Leased');

-- --------------------------------------------------------

--
-- Table structure for table `asset_users`
--

DROP TABLE IF EXISTS `asset_users`;
CREATE TABLE IF NOT EXISTS `asset_users` (
  `staff_id` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `staff_first_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `staff_last_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `t_id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_users`
--

INSERT INTO `asset_users` (`staff_id`, `staff_first_name`, `staff_last_name`, `department`, `t_id`) VALUES
('Rmu001', 'Ismail', 'Abdulai-Saiku', 'ICT', 1),
('Rmu002', 'Henry', 'Snow', 'ICT', 2);

-- --------------------------------------------------------

--
-- Table structure for table `calculations`
--

DROP TABLE IF EXISTS `calculations`;
CREATE TABLE IF NOT EXISTS `calculations` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `asset_id` int NOT NULL,
  `asset_name` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_class` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_type` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `acquisition_date` date NOT NULL,
  `active_res_value` float(9,2) NOT NULL,
  `dollar_rate_used` float(4,2) NOT NULL,
  `date_added` date NOT NULL,
  `asset_cost_opening_balance` decimal(10,2) DEFAULT NULL,
  `asset_cost_closing_balance` decimal(10,2) DEFAULT NULL,
  `accumulated_depr_opening_balance` float(15,2) NOT NULL,
  `depreciation_cost` decimal(10,2) DEFAULT NULL,
  `total_accumulated_depreciation` decimal(10,2) DEFAULT NULL,
  `account_depreciation_closing_balance` decimal(10,2) DEFAULT NULL,
  `closing_carrying_value` decimal(10,2) DEFAULT NULL,
  `current_lifetime` int DEFAULT NULL,
  `unexpired_lifetime` int DEFAULT NULL,
  `year` int NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calculations`
--

INSERT INTO `calculations` (`table_id`, `asset_id`, `asset_name`, `asset_class`, `asset_type`, `location`, `acquisition_date`, `active_res_value`, `dollar_rate_used`, `date_added`, `asset_cost_opening_balance`, `asset_cost_closing_balance`, `accumulated_depr_opening_balance`, `depreciation_cost`, `total_accumulated_depreciation`, `account_depreciation_closing_balance`, `closing_carrying_value`, `current_lifetime`, `unexpired_lifetime`, `year`) VALUES
(293, 1, 'fire Training Works', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '91.00', 0.00, '1.00', '1.00', '1.00', '89.00', 3, 47, 2010),
(294, 2, 'overhead tank', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '19.00', 0.00, '398.80', '398.80', '398.80', '19.00', 3, 47, 2010),
(295, 3, 'RENOVATION', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '86.00', 0.00, '1.00', '1.00', '1.00', '84.00', 3, 47, 2010),
(296, 4, 'RENOVATION', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '91.00', 0.00, '1.00', '1.00', '1.00', '89.00', 3, 47, 2010),
(297, 5, 'RENOVATION', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '305.00', 0.00, '6.00', '6.00', '6.00', '299.00', 3, 47, 2010),
(298, 6, 'tiling Works', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '28.00', 0.00, '568.56', '568.56', '568.56', '27.00', 3, 47, 2010),
(299, 7, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '17.00', 0.00, '3.00', '3.00', '3.00', '13.00', 3, 2, 2010),
(300, 8, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '1.00', 0.00, '280.00', '280.00', '280.00', '1.00', 1, 4, 2010),
(301, 9, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '4.00', 0.00, '900.00', '900.00', '900.00', '3.00', 1, 4, 2010),
(302, 10, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '2.00', 0.00, '599.80', '599.80', '599.80', '2.00', 3, 2, 2010),
(303, 11, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '5.00', 0.00, '1.00', '1.00', '1.00', '4.00', 3, 2, 2010),
(304, 12, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '1.00', 0.00, '248.28', '248.28', '248.28', '993.11', 3, 2, 2010),
(305, 13, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '1.00', 0.00, '370.00', '370.00', '370.00', '1.00', 3, 2, 2010),
(306, 14, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '1.00', 0.00, '200.00', '200.00', '200.00', '800.00', 3, 2, 2010),
(307, 15, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '2.00', 0.00, '400.00', '400.00', '400.00', '1.00', 3, 2, 2010),
(308, 16, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '4.00', 0.00, '900.00', '900.00', '900.00', '3.00', 3, 2, 2010),
(309, 17, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '12.00', 0.00, '2.00', '2.00', '2.00', '9.00', 3, 2, 2010),
(310, 18, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '6.00', 0.00, '1.00', '1.00', '1.00', '4.00', 3, 2, 2010),
(311, 19, 'electrical pump', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '5.00', 0.00, '0.00', '0.00', '0.00', '5.00', 3, -3, 2010),
(312, 20, 'electrical pump', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '39.00', 0.00, '0.00', '0.00', '0.00', '39.00', 3, -3, 2010),
(313, 21, 'generator', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '50.00', 0.00, '0.00', '0.00', '0.00', '50.00', 3, -3, 2010),
(314, 22, 'generator', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '17.00', 0.00, '0.00', '0.00', '0.00', '17.00', 3, -3, 2010),
(315, 23, 'mower', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '10.00', 0.00, '0.00', '0.00', '0.00', '10.00', 3, -3, 2010),
(316, 24, 'mower', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '18.00', 0.00, '0.00', '0.00', '0.00', '18.00', 3, -3, 2010),
(317, 25, 'pumping machine motor', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '34.00', 0.00, '0.00', '0.00', '0.00', '34.00', 3, -3, 2010),
(318, 26, 'shear wire cutter', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '5.00', 0.00, '0.00', '0.00', '0.00', '5.00', 3, -3, 2010),
(319, 27, 'universal water pump', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '10.00', 0.00, '0.00', '0.00', '0.00', '10.00', 3, -3, 2010),
(320, 28, '16GB RAM', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '1.00', 0.00, '360.00', '360.00', '360.00', '1.00', 3, 2, 2010),
(321, 29, '8 PORT D LINK SWITCH', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '33.00', 0.00, '6.00', '6.00', '6.00', '26.00', 3, 2, 2010),
(322, 30, '9 PORT D LINK SWITCH', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '14.00', 0.00, '2.00', '2.00', '2.00', '11.00', 3, 2, 2010),
(323, 31, 'bridge simulator software', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '85.00', 0.00, '17.00', '17.00', '17.00', '68.00', 3, 2, 2010),
(324, 32, 'certificate', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '3.00', 0.00, '655.00', '655.00', '655.00', '2.00', 3, 2, 2010),
(325, 33, 'complete desktop computer', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '4.00', 0.00, '960.00', '960.00', '960.00', '3.00', 3, 2, 2010),
(326, 34, 'computer set', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '11.00', 0.00, '2.00', '2.00', '2.00', '8.00', 3, 2, 2010),
(327, 35, 'dell projector 1610HD', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '850.00', 0.00, '170.00', '170.00', '170.00', '680.00', 3, 2, 2010),
(328, 36, 'hp laserjet Pro 401DN', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '5.00', 0.00, '1.00', '1.00', '1.00', '4.00', 3, 2, 2010),
(329, 37, 'HP Scanner', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '3.00', 0.00, '683.06', '683.06', '683.06', '2.00', 3, 2, 2010),
(330, 38, 'HP Scanner', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '5.00', 0.00, '1.00', '1.00', '1.00', '4.00', 3, 2, 2010),
(331, 39, 'internet switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '3.00', 0.00, '727.90', '727.90', '727.90', '2.00', 3, 2, 2010),
(332, 40, 'internet switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '14.00', 0.00, '2.00', '2.00', '2.00', '11.00', 3, 2, 2010),
(333, 41, 'laptop computer', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '15.00', 0.00, '3.00', '3.00', '3.00', '12.00', 3, 2, 2010),
(334, 42, 'laptop computer', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '6.00', 0.00, '1.00', '1.00', '1.00', '5.00', 3, 2, 2010),
(335, 43, 'LCD EPSON 2800 PROJECTOR', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '50.00', 0.00, '10.00', '10.00', '10.00', '40.00', 3, 2, 2010),
(336, 44, 'network housing', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '4.00', 0.00, '981.76', '981.76', '981.76', '3.00', 3, 2, 2010),
(337, 45, 'projector', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '7.00', 0.00, '1.00', '1.00', '1.00', '5.00', 3, 2, 2010),
(338, 46, 'projector', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '9.00', 0.00, '1.00', '1.00', '1.00', '7.00', 3, 2, 2010),
(339, 47, 'switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '43.00', 0.00, '8.00', '8.00', '8.00', '34.00', 3, 2, 2010),
(340, 48, 'switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '35.00', 0.00, '7.00', '7.00', '7.00', '28.00', 3, 2, 2010),
(341, 49, 'system unit', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '1.00', 0.00, '360.00', '360.00', '360.00', '1.00', 3, 2, 2010),
(342, 50, 'system unit', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '5.00', 0.00, '1.00', '1.00', '1.00', '4.00', 3, 2, 2010),
(343, 51, 'ups', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '24.00', 0.00, '4.00', '4.00', '4.00', '19.00', 3, 2, 2010),
(344, 52, 'ups', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '9.00', 0.00, '1.00', '1.00', '1.00', '7.00', 3, 2, 2010),
(345, 53, 'ups 1000VA', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '1.00', 0.00, '275.00', '275.00', '275.00', '1.00', 3, 2, 2010),
(346, 54, 'ups 1000VA', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '3.00', 0.00, '680.00', '680.00', '680.00', '2.00', 3, 2, 2010),
(347, 55, 'visual Fault Locator', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '15.00', 0.00, '3.00', '3.00', '3.00', '12.00', 3, 2, 2010),
(348, 56, 'Windows Pro Software 20 User License', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '7.00', 0.00, '1.00', '1.00', '1.00', '6.00', 3, 2, 2010),
(349, 57, 'Windows Pro Software 20 User License', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', '0.00', '38.00', 0.00, '7.00', '7.00', '7.00', '30.00', 3, 2, 2010),
(350, 58, '3 IN 1 VISITORS CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '1.00', 0.00, '171.72', '171.72', '171.72', '1.00', 3, 7, 2010),
(351, 59, '6 DRAWER DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '2.00', 0.00, '240.00', '240.00', '240.00', '2.00', 3, 7, 2010),
(352, 60, 'CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '8.00', 0.00, '833.56', '833.56', '833.56', '7.00', 3, 7, 2010),
(353, 61, 'CONFERENCE TABLE & CHAIRS', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '3.00', 0.00, '320.00', '320.00', '320.00', '2.00', 3, 7, 2010),
(354, 62, 'CURTAIN', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '5.00', 0.00, '509.40', '509.40', '509.40', '4.00', 3, 7, 2010),
(355, 63, 'CURTAIN', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '7.00', 0.00, '710.00', '710.00', '710.00', '6.00', 3, 7, 2010),
(356, 64, 'CURTAIN', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '3.00', 0.00, '349.40', '349.40', '349.40', '3.00', 3, 7, 2010),
(357, 65, 'EXECUTIVE SWIVEL CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '6.00', 0.00, '600.00', '600.00', '600.00', '5.00', 3, 7, 2010),
(358, 66, 'EXECUTIVE SWIVEL CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '4.00', 0.00, '416.00', '416.00', '416.00', '3.00', 3, 7, 2010),
(359, 67, 'EXECUTIVE WRITING DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '9.00', 0.00, '975.00', '975.00', '975.00', '8.00', 3, 7, 2010),
(360, 68, 'EXECUTIVE WRITING DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '8.00', 0.00, '884.00', '884.00', '884.00', '7.00', 3, 7, 2010),
(361, 69, 'L SHAPED DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '6.00', 0.00, '650.00', '650.00', '650.00', '5.00', 3, 7, 2010),
(362, 70, 'LECTURE HALL METAL DESKS', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '60.00', 0.00, '6.00', '6.00', '6.00', '54.00', 3, 7, 2010),
(363, 71, 'SECRETARY SWIVEL CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '4.00', 0.00, '457.60', '457.60', '457.60', '4.00', 3, 7, 2010),
(364, 72, 'SUGGESTION BOX', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '1.00', 0.00, '140.00', '140.00', '140.00', '1.00', 3, 7, 2010),
(365, 73, 'TABLE', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '15.00', 0.00, '1.00', '1.00', '1.00', '13.00', 3, 7, 2010),
(366, 74, 'WAITING CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '1.00', 0.00, '171.72', '171.72', '171.72', '1.00', 3, 7, 2010),
(367, 75, 'GOWNS', 'Academic Gowns', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '59.00', 0.00, '11.00', '11.00', '11.00', '47.00', 3, 2, 2010),
(368, 76, 'HIGH EXPANSION FOAM GENERATIOR', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '63.00', 0.00, '12.00', '12.00', '12.00', '50.00', 3, 2, 2010),
(369, 77, 'LIFEJACKET', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '116.00', 0.00, '23.00', '23.00', '23.00', '93.00', 3, 2, 2010),
(370, 78, 'STAINLESS STEEL TRAY', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '41.00', 0.00, '8.00', '8.00', '8.00', '32.00', 3, 2, 2010),
(371, 79, 'TECQUIPMENT', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', '0.00', '71.00', 0.00, '14.00', '14.00', '14.00', '57.00', 3, 2, 2010),
(372, 84, 'Peaugot 3008', 'Motor Vehicles', 'Owned', 'ICT LAB', '0000-00-00', 0.00, 8.00, '0000-00-00', '0.00', '350.00', 0.00, '70.00', '70.00', '70.00', '280.00', 3, 2, 2010),
(373, 83, 'TOYOTA HILUX', 'Motor Vehicles', 'Owned', 'Registry Records', '0000-00-00', 0.00, 8.00, '0000-00-00', '0.00', '749.00', 0.00, '149.00', '149.00', '149.00', '599.00', 4, 1, 2010),
(374, 85, 'Peugeot 3008 1', 'Motor Vehicles', 'Owned', 'Registry Records', '0000-00-00', 0.00, 8.00, '0000-00-00', '0.00', '535.00', 0.00, '107.00', '107.00', '107.00', '428.00', 2, 3, 2010);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `dep_id` varchar(37) COLLATE utf8mb4_general_ci NOT NULL,
  `dep_name` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`t_id`, `dep_id`, `dep_name`) VALUES
(1, 'DEP001', 'ICT');

-- --------------------------------------------------------

--
-- Table structure for table `disposals`
--

DROP TABLE IF EXISTS `disposals`;
CREATE TABLE IF NOT EXISTS `disposals` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `grv_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `serial_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `pv_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `id_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'N/A',
  `supplier_name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_class` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_class` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `asset_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `current_year` int NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `additions` decimal(20,2) NOT NULL,
  `disposal_value` decimal(20,2) NOT NULL DEFAULT '0.00',
  `active_res_value` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dollar_rate_used` double NOT NULL,
  `date_of_disposal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `disposal_month` int DEFAULT '0',
  `estimated_life_months` int DEFAULT '0',
  `disposal_depreciation` decimal(12,2) GENERATED ALWAYS AS (((`additions` / `estimated_life_months`) * `disposal_month`)) VIRTUAL,
  PRIMARY KEY (`asset_id`),
  KEY `asset_class` (`asset_class`),
  KEY `asset_type` (`asset_type`),
  KEY `location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dollar_rate`
--

DROP TABLE IF EXISTS `dollar_rate`;
CREATE TABLE IF NOT EXISTS `dollar_rate` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `dollar_rate` double NOT NULL,
  `rate_status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `action_by` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dollar_rate`
--

INSERT INTO `dollar_rate` (`table_id`, `dollar_rate`, `rate_status`, `action_by`, `date_added`) VALUES
(1, 9.5, 'INACTIVE', 'schedule', '2024-05-01 17:44:22'),
(2, 8, 'INACTIVE', 'Schedule Officer', '2025-01-14 14:46:33'),
(3, 8.5, 'INACTIVE', 'Schedule Officer', '2025-01-30 14:10:27'),
(4, 9, 'INACTIVE', 'Schedule Officer', '2025-01-30 14:30:01'),
(5, 10, 'INACTIVE', 'Schedule Officer', '2025-01-30 14:37:44'),
(6, 9, 'ACTIVE', 'Schedule Officer', '2025-02-04 11:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `moved_assets`
--

DROP TABLE IF EXISTS `moved_assets`;
CREATE TABLE IF NOT EXISTS `moved_assets` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci NOT NULL,
  `old_location` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `old_user` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `New_location` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `new_user` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_of_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moved_assets`
--

INSERT INTO `moved_assets` (`t_id`, `serial_number`, `notes`, `old_location`, `old_user`, `New_location`, `new_user`, `date_of_action`) VALUES
(1, '0', 'Asset Has Been Moved From Qualiity Control To ICt Lab', 'gambian hostel', 'null', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-01-14 14:23:57'),
(2, '0', 'Move', 'ICT LAB', 'Ismail Abdulai-Saiku', 'Registry Records', 'Henry Snow', '2025-01-27 09:05:50'),
(3, '2345678', 'Trial', 'ICT LAB', 'Ismail Abdulai-Saiku', 'Registry Records', 'Henry Snow', '2025-01-27 10:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `other_values`
--

DROP TABLE IF EXISTS `other_values`;
CREATE TABLE IF NOT EXISTS `other_values` (
  `acc_depr_opening_bal` decimal(20,2) NOT NULL,
  `s/n` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`s/n`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `sup_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `number` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`sup_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `number` (`number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`sup_id`, `name`, `location`, `number`) VALUES
(1, 'Yakubu Furnitures', 'Tema', '0200034432');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers_archive`
--

DROP TABLE IF EXISTS `suppliers_archive`;
CREATE TABLE IF NOT EXISTS `suppliers_archive` (
  `sup_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `number` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`sup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `untracked_asset_disposals`
--

DROP TABLE IF EXISTS `untracked_asset_disposals`;
CREATE TABLE IF NOT EXISTS `untracked_asset_disposals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `acquisition_date` date DEFAULT '2023-01-01',
  `date_of_disposal` date NOT NULL,
  `dollar_rate` decimal(6,2) DEFAULT '8.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `untracked_asset_disposals`
--

INSERT INTO `untracked_asset_disposals` (`id`, `class`, `name`, `value`, `acquisition_date`, `date_of_disposal`, `dollar_rate`) VALUES
(1, 'Motor Vehicles', 'Toyota Haice Highroof', '35220.00', '2023-01-01', '2023-12-16', '8.00'),
(2, 'Motor Vehicles', 'Peugeot LandTrek', '35946.00', '2023-01-01', '2024-09-17', '8.00'),
(3, 'Motor Vehicles', 'Peugeot LandTrek', '35946.00', '2023-01-01', '2024-09-17', '8.00'),
(4, 'Motor Vehicles', 'Track 1', '40000.00', '2023-03-01', '2025-03-01', '8.50'),
(5, 'Motor Vehicles', 'Track 2', '40000.00', '2023-01-01', '2025-03-10', '8.50');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
