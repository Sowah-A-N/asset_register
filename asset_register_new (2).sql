-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 07, 2026 at 09:09 PM
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
  `username` varchar(50) NOT NULL,
  `user_role` varchar(20) NOT NULL,
  `user_password` varchar(254) NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`table_id`, `username`, `user_role`, `user_password`) VALUES
(1, 'Senior Internal Auditor', 'SIA', '$2y$10$65JnQbqtmIwWLS1afoGHzOgsiyuLG6sS2dHJmOKPlcoaMBH.7w10K'),
(2, 'Schedule Officer', 'S/O', '$2y$10$65JnQbqtmIwWLS1afoGHzOgsiyuLG6sS2dHJmOKPlcoaMBH.7w10K'),
(3, 'Budget Officer', 'B/O', '$2y$10$65JnQbqtmIwWLS1afoGHzOgsiyuLG6sS2dHJmOKPlcoaMBH.7w10K'),
(4, 'Accountant', 'Accountant', '$2y$10$65JnQbqtmIwWLS1afoGHzOgsiyuLG6sS2dHJmOKPlcoaMBH.7w10K'),
(5, 'Director Finance', 'D/F', '$2y$10$65JnQbqtmIwWLS1afoGHzOgsiyuLG6sS2dHJmOKPlcoaMBH.7w10K'),
(6, 'DSU', 'DSU', '$2y$10$65JnQbqtmIwWLS1afoGHzOgsiyuLG6sS2dHJmOKPlcoaMBH.7w10K'),
(7, 'HOD ICT', 'ICT', '$2y$10$AMQwMfmEktyUfeZec7QZu..hSrWH6ZXfUZMwJTRCiVlTS8IxNZbsS');

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(150) NOT NULL,
  `grv_number` varchar(20) DEFAULT 'N/A',
  `serial_number` varchar(50) DEFAULT 'N/A',
  `pv_number` varchar(50) DEFAULT 'N/A',
  `id_number` varchar(100) DEFAULT 'N/A',
  `supplier_name` varchar(250) NOT NULL,
  `asset_class` varchar(50) NOT NULL,
  `sub_class` varchar(254) NOT NULL,
  `asset_type` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL DEFAULT 'on campus',
  `user` varchar(150) DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `in_service_date` date DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=388 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `in_service_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(1, 'Peugeot 3008', '00001', 'VF3M45GYVPS012514', '00001', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-01-29', NULL, 2025, '0.00', '322560.00', 0, 0, '0.00', 9, '2025-01-30 14:22:27'),
(2, 'Peugeot LandTrek', '000003', 'VR3FDAFDJN3016395', '00003', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Henry Snow', '2024-06-20', NULL, 2025, '0.00', '324000.00', 0, 0, '0.00', 9, '2025-01-30 14:29:42'),
(3, 'Peugeot LandTrek', '000005', 'VR3FDAFDJN3015640', '00005', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-06-20', NULL, 2025, '0.00', '324000.00', 0, 0, '0.00', 9, '2025-01-30 14:37:27'),
(4, 'Toyota Haice Highroof', '000007', 'JFTBB90P706057042', '00007', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-10-02', NULL, 2025, '0.00', '884041.97', 0, 0, '0.00', 10, '2025-01-30 14:44:18'),
(5, ' BEING VARIATION WORKS FOR THE OFFICE EXTENSION AT', ' qw33550 ', ' GJ: 65105 ', 'oooo14', 'RMU///0/31', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-01-31', NULL, 2025, '0.00', '68064.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(6, ' BEING ADDITIONAL WORKS -OFFICE EXTENSION IN BOTH ', ' qw33551 ', ' GJ: 65106 ', 'oooo15', 'RMU///0/32', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-01', NULL, 2025, '0.00', '197642.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(7, ' RAZOR WIRE ON THE FENCE WALLS OF RMU SAKUMONO RES', ' qw33552 ', ' GJ: 57488 ', 'oooo16', 'RMU///0/33', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-05', NULL, 2025, '0.00', '22450.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(8, ' PAYMENT FOR REPAIRS OF THE RESERVIOUR TANKS AND V', ' qw33553 ', ' GJ: 57374 ', 'oooo17', 'RMU///0/34', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-07', NULL, 2025, '0.00', '65600.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(9, ' BEING PAINTING MATERIALS, LAYING OF WASTE PIPES,C', ' qw33554 ', ' GJ: 65108 ', 'oooo18', 'RMU///0/35', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-19', NULL, 2025, '0.00', '64130.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(10, ' BEING RENOVATION OF NAUTICAL SCIENCE DEPARTMENT W', ' qw33555 ', ' GJ: 65112 ', 'oooo19', 'RMU///0/36', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-29', NULL, 2025, '0.00', '69412.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(11, ' CONSTRUCTION OF CONCRETE POLYTANK PLATFORM AT THE', ' qw33556 ', ' GJ: 65114 ', 'oooo20', 'RMU///0/37', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-29', NULL, 2025, '0.00', '36984.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(12, ' RENOVATION OF BUNGALOW R5 BY JASEKO MULTIPURPOSE ', ' qw33557 ', ' GJ: 65113 ', 'oooo21', 'RMU///0/38', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-02-29', NULL, 2025, '0.00', '190577.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(13, ' BEING RENOVATION OF THE ECDIS LAB BY FOREVER CONS', ' qw33558 ', ' GJ: 65110 ', 'oooo22', 'RMU///0/39', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-03-08', NULL, 2025, '0.00', '54065.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(14, ' BEING EXTERNAL WORKS AT THE OFFICE EXTENSION BY A', ' qw33559 ', ' GJ: 65107 ', 'oooo23', 'RMU///0/40', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-01', NULL, 2025, '0.00', '26840.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(15, ' PURCHASE OF 24PCS OF 17FT LONG AND 11PCS OF 22FT ', ' qw33560 ', ' P: 122376 ', 'oooo24', 'RMU///0/41', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-04', NULL, 2025, '0.00', '36994.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(16, ' PURCHASE OF MATERIALS FOR ALL THE HANDRAILS FOR V', ' qw33561 ', ' P: 122457 ', 'oooo25', 'RMU///0/42', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-04-12', NULL, 2025, '0.00', '61010.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(18, ' PAYMENT FOR THE REPLACING OF 2NO. FIRE HYDRANT AT', ' qw33563 ', ' P: 126623 ', 'oooo27', 'RMU///0/44', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-05-02', NULL, 2025, '0.00', '19630.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(19, ' RE-ISSUE OF CANCELLED CHEQUE FOR PURCHASE OF POLY', ' qw33564 ', ' P: 129869 ', 'oooo28', 'RMU///0/45', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-05-27', NULL, 2025, '0.00', '11640.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(20, ' PAYMENT FOR CEILING WORKS AT THE OFFICE COMPLEX-M', ' qw33565 ', ' P: 135195 ', 'oooo29', 'RMU///0/46', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-06-21', NULL, 2025, '0.00', '30170.00', 0, 0, '0.00', 9, '2025-03-12 00:00:00'),
(21, ' WORKS ON THE LEAKAGES IN THE SLAB AT THE 1ST AND ', ' qw33566 ', ' P: 135326 ', 'oooo30', 'RMU///0/47', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-07-01', NULL, 2025, '0.00', '37490.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(22, ' BEING CONTRACT FOR THE RE- ROOFING OF THE  BRIDGE', ' qw33567 ', ' GJ: 78642 ', 'oooo31', 'RMU///0/48', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-07-31', NULL, 2025, '0.00', '69785.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(23, ' ASSESSMENT OF ROOF AND RECTIFICATION OF PERSISTEN', ' qw33568 ', ' P: 137618 ', 'oooo32', 'RMU///0/49', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-08-06', NULL, 2025, '0.00', '73975.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(24, ' BEING CONTRACT FOR THE RENOVATION OF SAKUMO NO RE', ' qw33569 ', ' GJ: 78643 ', 'oooo33', 'RMU///0/50', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-10-14', NULL, 2025, '0.00', '116871.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(26, ' COST OF CEILING ACHIVES CORRIDOR AND EXTERNAL WOR', ' qw33571 ', ' GJ: 84999 ', 'oooo35', 'RMU///0/52', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-10-31', NULL, 2025, '0.00', '92400.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(27, ' FABRICATION, INSTALLATION AND PAINTING OF 13NR HO', ' qw33572 ', ' P: 153787 ', 'oooo36', 'RMU///0/53', 'Yakubu Furnitures', 'Buildings', 'Test Sub Class', 'Owned', 'on campus', 'Henry Snow', '2024-12-09', NULL, 2025, '0.00', '93970.00', 0, 0, '0.00', 10, '2025-03-12 00:00:00'),
(28, 'PURCHASE OF REFURBISHED SYSTEM UNIT LABEL CORE 158', '2020396', '66325501', '8859701', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-15', NULL, 2025, '0.00', '7800.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(29, 'PURCHASE OF HP Z4 WORKSTATION MOTHERBOARD & DDR4, ', '2020397', '66325502', '8859702', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-19', NULL, 2025, '0.00', '12500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(30, 'PURCHASE OF INTERNET SWITCH FOR THE STORES UNIT', '2020398', '66325503', '8859703', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-22', NULL, 2025, '0.00', '16975.89', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(31, 'PURCHASE OF TWO SOLLATEK UPS- 850VA FOR THE IT UNI', '2020399', '66325504', '8859704', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-12', NULL, 2025, '0.00', '4900.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(32, 'PURCHASE OF AUDIO-VISUAL DESKTOP FOR THE AUDITORIU', '2020400', '66325505', '8859705', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-12', NULL, 2025, '0.00', '10712.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(33, '50% PAYMENT FOR SUBSCRIPTION AND EXTERNAL TECHNICA', '2020401', '66325506', '8859706', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-14', NULL, 2025, '0.00', '11000.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(34, 'PURCHASE OF DELL OPTIPLEX 3050 CPU-DAMAGED POWER-P', '2020402', '66325507', '8859707', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', NULL, 2025, '0.00', '18420.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(35, 'PURCHASE OF 4NO. AVS 30 (SOLLATEK) TO BE USED IN C', '2020403', '66325508', '8859708', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', NULL, 2025, '0.00', '3508.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(36, 'PURCHASE OF NEW PROJECTORS-6PCS-EPSON EB-E10 (OVER', '2020404', '66325509', '8859709', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', NULL, 2025, '0.00', '53700.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(37, 'PAYMENT OF 60% FOR THE COMMENCEMENT OF THE ELECTRO', '2020405', '66325510', '8859710', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', NULL, 2025, '0.00', '39150.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(38, 'PURCHASE OF DELL 3050 DESKTOP COMPUTER FOR MSSC DE', '2020406', '66325511', '8859711', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-12', NULL, 2025, '0.00', '12825.28', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(39, 'PURCHASE OF 6 NEW UBIQUITI M2 FOR THE NAUTICAL STU', '2020407', '66325512', '8859712', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', NULL, 2025, '0.00', '22200.03', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(40, 'PURCHASE OF AVS 30, ULTIMA UPS 850VA & MG2  FOR TH', '2020408', '66325513', '8859713', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', NULL, 2025, '0.00', '5856.97', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(41, 'PURCHASE OF SIGNAL FIRE OPTICAL FUSION SPLICER, 8S', '2020409', '66325514', '8859714', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', NULL, 2025, '0.00', '40714.60', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(42, 'PURCHASE OF NEW 850VA UPS FOR THE RESEARCH DEPT.', '2020410', '66325515', '8859715', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', NULL, 2025, '0.00', '1225.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(43, 'PURCHASE OF 2NR PROJECTORS FOR THE WEEKEND SCHOOL', '2020411', '66325516', '8859716', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', NULL, 2025, '0.00', '17900.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(44, 'PURCHASE OF CHARGER AND EXTERNAL HARD DRIVE FOR PU', '2020412', '66325517', '8859717', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', NULL, 2025, '0.00', '2035.73', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(45, 'PURCHASE OF NEW NETWORK SWITCH FOR THE DSU BLOCK', '2020413', '66325518', '8859718', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-04', NULL, 2025, '0.00', '16975.89', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(46, 'PURCHASE OF ONE DESKTOP COMPUTER FOR USE AT THE AR', '2020414', '66325519', '8859719', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-18', NULL, 2025, '0.00', '12825.28', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(47, 'PURCHASE OF HP LASERJET M402 DN PRINTER FOR THE AD', '2020415', '66325520', '8859720', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-18', NULL, 2025, '0.00', '9800.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(48, 'PURCHASE OF 2NO DESKTOP COMPUTER FOR THE NEW IT TE', '2020416', '66325521', '8859721', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-26', NULL, 2025, '0.00', '25967.01', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(49, 'PURCHASE OF 4 PCS OF EXTERNAL HDD TOSHIBA 4TB USB ', '2020417', '66325522', '8859722', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-27', NULL, 2025, '0.00', '6731.42', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(50, 'PURCHASE OF GTX 1070 GRAPHIC CARD FOR NS DEPT.', '2020418', '66325523', '8859723', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-28', NULL, 2025, '0.00', '3650.40', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(51, 'PURCHASE OF LAPTOPS FOR THE FINANCE DEPT., MSSC, D', '2020419', '66325524', '8859724', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-28', NULL, 2025, '0.00', '50710.40', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(52, 'PURCHASE OF SOLLATEK EXTENSION BOARDS FOR MSSC AND', '2020420', '66325525', '8859725', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-04', NULL, 2025, '0.00', '22930.07', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(53, 'PURCHASE OF 6 PCS OF DELL OPTIPLEX 7040 MT SYSTEM ', '2020421', '66325526', '8859726', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-04', NULL, 2025, '0.00', '65637.81', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(54, 'PAYMENT OF INCENTIVES FOR THE SOFTWARE DEVELOPMENT', '2020422', '66325527', '8859727', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-10', NULL, 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(55, 'PAYMENT OF INCENTIVES FOR THE SOFTWARE DEVELOPMENT', '2020423', '66325528', '8859728', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-10', NULL, 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(56, 'PAYMENT OF INCENTIVES FOR THE SOFTWARE DEVELOPMENT', '2020424', '66325529', '8859729', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-10', NULL, 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(57, 'PURCHASE OF MULTIFUNCTIONAL HP LASER JET PRINTER F', '2020425', '66325530', '8859730', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-11', NULL, 2025, '0.00', '2683.96', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(58, 'PURCHASE OF 2PCS OF 24\'\' MONITOR WITH ID-PORT/ HDM', '2020426', '66325531', '8859731', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '3536.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(59, 'PURCHASE OF 2NO HP LASERJECT 400 3DN PRINTER FOR T', '2020427', '66325532', '8859732', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '10039.68', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(60, 'PURCHASE OF DELL OPTIPLEX 7000, INTEL CORE 8GB RAM', '2020428', '66325533', '8859733', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-10', NULL, 2025, '0.00', '64272.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(61, 'PURCHASE OF CCTV CAMERA SYSTEM FOR RMU\'S MAIN WEST', '2020429', '66325534', '8859734', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-15', NULL, 2025, '0.00', '44537.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(62, 'PURCHASE OF 2 SOLLATEK UPS 1,500 VA TO REPLACE BLW', '2020430', '66325535', '8859735', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-16', NULL, 2025, '0.00', '7034.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(63, 'PURCHASE OF 1NR LAPTOP FOR THE OFFICE OF HEAD, DSU', '2020431', '66325536', '8859736', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-19', NULL, 2025, '0.00', '12677.60', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(64, 'PAYMENT FOR GARNET MICROTIK ROUTER CCR2116-12G-45+', '2020432', '66325537', '8859737', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-23', NULL, 2025, '0.00', '22500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(65, 'PURCHASE OF DESKTOP COMPUTER FOR NEW ADMIN. ASSIST', '2020433', '66325538', '8859738', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '81246.35', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(66, 'PURCHASE OF 2PCS OF NEW SOLLATEK UPS 850VA FOR AG.', '2020434', '66325539', '8859739', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '23254.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(67, 'PURCHASE OF UBIQUITI UNIFI M2 STATION FOR NSD', '2020435', '66325540', '8859740', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '16450.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(68, 'PURCHASE OF FOUR BACK-UP HARD DISK 8TB FOR IT UNIT', '2020436', '66325541', '8859741', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '60525.79', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(69, 'PURCHASE OF IT ITEMS FOR THE BUSINESS DEVELOPMENT ', '2020437', '66325542', '8859742', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-27', NULL, 2025, '0.00', '29987.40', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(70, 'PURCHASE AND INSTALLATION OF 1NR PROJECTOR WITH DR', '2020438', '66325543', '8859743', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-30', NULL, 2025, '0.00', '52112.48', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(71, 'PURCHASE OF 4PCS OF 8TB EXTERNAL HARD DRIVE TO BE ', '2020439', '66325544', '8859744', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-11', NULL, 2025, '0.00', '20233.80', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(72, 'PURCHASE OF 2 SURVEILANCE HARD DISK DRIVES FOR CCT', '2020440', '66325545', '8859745', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', NULL, 2025, '0.00', '2917.66', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(73, 'PURCHASE OF HP LASERJET COLOUR PRINTER FOR THE DES', '2020441', '66325546', '8859746', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-24', NULL, 2025, '0.00', '13500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(74, 'PURCHASE OF TWO NEW UPS FOR STUDENTS AFFAIRS DEPT.', '2020442', '66325547', '8859747', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-04', NULL, 2025, '0.00', '4758.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(75, 'PURCHASE OF TWO GIGABIT ROUTERS FOR IT UNIT SERVER', '2020443', '66325548', '8859748', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-04', NULL, 2025, '0.00', '116899.36', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(76, 'PURCHASE OF A NEW COMPUTER SYSTEM UNIT TO REPLACE ', '2020444', '66325549', '8859749', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-16', NULL, 2025, '0.00', '25499.99', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(77, 'PURCHASE OF 10PCS OF UPS 1000 VA FOR ICT DEPARTMEN', '2020445', '66325550', '8859750', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-24', NULL, 2025, '0.00', '6600.03', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(78, 'PAYMENT FOR THE PURCHASE OF NEW 19 INCH MONITOR FO', '2020446', '66325551', '8859751', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-15', NULL, 2025, '0.00', '5400.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(79, 'PURCHASE OF UPS FOR THE INTERNAL SWITCH', '2020447', '66325552', '8859752', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-15', NULL, 2025, '0.00', '3751.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(80, 'PURCHASE OF COMPUTER FOR ADMINSTRATIVE ASSISTANTS ', '2020448', '66325553', '8859753', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-15', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(81, 'PAYMENT FOR ONE STM-I 155MBPS LIMIT FOR THE MONTH ', '2020449', '66325554', '8859754', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-18', NULL, 2025, '0.00', '22500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(82, 'PAYMENT TO KNUST-UITS MAINTENANCE FEES FOR SCHOOL ', '2020450', '66325555', '8859755', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-18', NULL, 2025, '0.00', '50000.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(83, 'PROCUREMENT OF A WIFI ROUTER FOR THE TRANSPORT UNI', '2020451', '66325556', '8859756', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-19', NULL, 2025, '0.00', '3717.95', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(84, 'PURCHASE OF A COLOURED PRINTER WITH SCANNER FOR TH', '2020452', '66325557', '8859757', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-22', NULL, 2025, '0.00', '13900.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(85, 'PURCHASE OF THREE COMPUTERS (HP-COMPACT ELITE 8300', '2020453', '66325558', '8859758', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-22', NULL, 2025, '0.00', '32136.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(86, 'PURCHASE OF 1000VA SOLLATEK UPS FOR THE ROUTER AT ', '2020454', '66325559', '8859759', 'RMU/////', 'Vendor Name', 'Computer & Accessories', ' Computer And Accessory ', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-12-23', NULL, 2025, '0.00', '8617.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(87, 'PURCHASE OF BASIC WORKING EQUIPMENT FOR N.S DEPART', '161', '225712', '118992', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'Leased', 'Nautical Studies Department - Classroom-Up 202', '', '2024-02-21', NULL, 2025, '0.00', '13980.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(88, 'PURCHASE OF BASIC WORKING EQUIPMENT FOR THE HOSPIT', '162', '225713', '119048', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-27', NULL, 2025, '0.00', '3229.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(89, 'PURCHASE OF 1NR 2HP STAINLESS STEEL SUBMERSIBLE PU', '163', '225714', '119090', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-01', NULL, 2025, '0.00', '7950.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(90, 'PURCHASE OF PUBLIC ADDRESS SYSTEM FOR ORIENTATION ', '164', '225715', '119119', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-05', NULL, 2025, '0.00', '18650.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(91, 'PURCHASE OF NEW MOISTURE EXTRACTOR X 1PCS WITH TIM', '165', '225716', '122304', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-22', NULL, 2025, '0.00', '6600.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(92, 'PURCHASE AND INSTALLATION OF 1NR PUMPING MACHINE F', '166', '225717', '122399', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', '', '', '', '2024-04-08', NULL, 2025, '0.00', '132150.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(93, 'PURCHASE OF 2NO. PULSE OXIMETER FOR THE SICK BAY U', '167', '225718', '129866', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-27', NULL, 2025, '0.00', '1500.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(94, 'PROVISION OF 2NR HOME -USED MOWERS FOR THE UNIVERS', '168', '225719', '130945', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-31', NULL, 2025, '0.00', '4000.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(95, 'PURCHASE OF 6 PCS OF OVERALL JACKETS FOR THE IT UN', '169', '225720', '130982', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-07', NULL, 2025, '0.00', '3400.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(96, 'PURCHASE OF 2 NEW SMOKE GENERATOR FOR FIRE FIGHTIN', '170', '225721', '137666', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-12', NULL, 2025, '0.00', '23161.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(97, 'PURCHASE OF 1NR CHAINSAW MACHINE- BRUSHLESS 16 LIT', '171', '225722', '142012', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', NULL, 2025, '0.00', '8000.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(98, 'REPAIR OF THREE PCS OF OPTIMUM B30 BS-VARIO PILLAR', '172', '225723', '146425', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-22', NULL, 2025, '0.00', '11650.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(99, 'PAYMENT FOR THE PURCHASE OF ELECTRONIC TOUCH SCREE', '173', '225724', '153752', 'RMU/////', ' VENDOR NAME ', 'Machine And Equipment', 'Machine And Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-29', NULL, 2025, '0.00', '17500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(100, 'BEING THE COST OF 3.0 TONS LPG FORKLIFT PURCHASED ', 'rmu///', 'C1129M00015-HT30TS', 'GJ: 84989', 'rmu///', 'supplier', 'Forklift', 'test', 'OWNED', 'on campus', 'USERNAME', '2024-04-29', NULL, 2025, '0.00', '438840.00', 0, 0, '0.00', 9, '2025-03-17 19:20:10'),
(101, 'PAYMENT FOR PHAROS GMDSS SIMULATORS FOR THE GMDSS LABORATORY FROM POSEIDON SIMULATION AS', '11122345', '1112234', 'GJ: 84997', '900001', 'SUPPLIER NAME', 'GMDSS Simulator', '', 'OWNED', 'on campus', 'USER', '2024-10-02', NULL, 2025, '0.00', '2247600.00', 0, 0, '0.00', 10, '2025-03-18 10:07:13'),
(104, 'THE COST OF TWO DESKTOP BRIDGE SIMULATORS PURCHASED FROM WARTSILLA VOYAGE OY', 'N/A', 'N/A', 'GJ: 84996', 'N/A', 'SUPPLIER NAME', 'Bridge Simulator', 'TEST', 'OWNED', 'on campus', 'USERNAME', '2024-10-01', NULL, 2025, '0.00', '727283.40', 0, 0, '0.00', 10, '2025-03-18 10:22:48'),
(105, 'PURCHASE OF 2-WAY MAIN SWITCH CIRCUIT BREAKER FOR MSSC CLASSROOM, EXTENSION CABLE AND INDUSTRIAL FAN FOR STUDENT AFFAIRS DEPT.', '44', '112100', '112018', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '0000-00-00', NULL, 2025, '0.00', '5824.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(106, 'PURCHASE OF 2PCS OF FUSE ASSEMBLY FOR XEROX WORK CENTRE 5325 PHOTOCOPIER MACHINES', '48', '112104', '118631', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-01-15', NULL, 2025, '0.00', '25151.02', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(107, 'PURCHASE OF INR 3-IN-1 WAITING SOFA FOR UR\'S OFFICE & INR 42\" DIGITAL SATELLITE TV FOR USE AT THE PORTERS\' LODGE-BACK ACCOMODATION', '42', '112098', '118841', 'RMU/UR/OE/B2/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'UNIVERSITY REGISTRAR ', 'USER NAME', '2024-02-06', NULL, 2025, '0.00', '1149.50', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(108, 'PURCHASE OF INR 3-IN-1 WAITING SOFA FOR UR\'S OFFICE & INR 42\" DIGITAL SATELLITE TV FOR USE AT THE PORTERS\' LODGE-BACK ACCOMODATION', '19', '112075', '118841', 'RMU/CSA/OE/TV42/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'BACK ACCOMMODATION - CSA', 'USER NAME', '2024-02-06', NULL, 2025, '0.00', '1149.50', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(109, 'PURCHASE OF 3NR OUTDOOR UNITS 2.0HP AC FOR THE TRANSPORT OFFICER\'S OFFICE, DSU INNER OFFICE & IT TECHNICIANS OFFICE', '26', '112082', '118932', 'RMU/DSU/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'DSU INNER OFFICE ', 'USER NAME', '2024-02-14', NULL, 2025, '0.00', '2300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(110, 'PURCHASE OF 3NR OUTDOOR UNITS 2.0HP AC FOR THE TRANSPORT OFFICER\'S OFFICE, DSU INNER OFFICE & IT TECHNICIANS OFFICE', '31', '112087', '118932', 'RMU/ITT/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'IT TECHNICIANS OFFICE ', 'USER NAME', '2024-02-14', NULL, 2025, '0.00', '2300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(111, 'PURCHASE OF 3NR OUTDOOR UNITS 2.0HP AC FOR THE TRANSPORT OFFICER\'S OFFICE, DSU INNER OFFICE & IT TECHNICIANS OFFICE', '40', '112096', '118932', 'RMU/TO/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'TRANSPORT OFFICER', 'USER NAME', '2024-02-14', NULL, 2025, '0.00', '2300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(112, 'PURCHASE OF 2.0 HP AIR CONDITIONER FOR THE OFFICE OF THE DEPUTY REGISTRAR-ACADEMIC', '23', '112079', '119042', 'RMU/DRA/OE/01/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'DEPUTY REGISTRAR - ADMIN.', 'USER NAME', '2024-02-27', NULL, 2025, '0.00', '6116.15', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(113, 'PURCHASE OF AC FOR ACCOUNTS OFFICE EXTENSION', '15', '112071', '119060', 'RMU/BO/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ACCOUNTS OFFICE EXTENSION - BO', 'USER NAME', '2024-02-28', NULL, 2025, '0.00', '6116.14', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(114, 'PURCHASE OF SOLIT 2.5HP AIR-CONDITIONER FOR GRADUATE SCHOOL', '30', '112086', '119124', 'RMU/SGS/OE/017/017/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'GRADUATE SCHOOL ', 'USER NAME', '2024-03-08', NULL, 2025, '0.00', '8255.20', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(115, 'PURCHASE OF TELEPHONE FOR ACCOUNTS OFFICER ONE (1), CASHIER AND ADM ASSISTANT', '50', '112106', '122283', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-20', NULL, 2025, '0.00', '3300.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(116, 'PAYMENT FOR FIRE EXTINGUISHER & FIRE ACTION  STICKER AT MSSC, ', '49', '112105', '126628', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-08', NULL, 2025, '0.00', '670.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(117, 'PURCHASE OF 7 PCS EXTENTION BOARDS FOR LIBRARY, 2 PCS OF SVS O4 AND 5 PCS 850VA UPS FOR IT UNIT', '51', '112107', '126700', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-08', NULL, 2025, '0.00', '12891.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(118, 'PURCHASE OF NEW AIR CONDITIONING FOR THE MARINE ENGINEERING STAFF COMMON ROOM', '33', '112089', '129860', 'RMU/ME/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', ' MARINE ENGINEERING  ', 'USER NAME', '2024-05-27', NULL, 2025, '0.00', '84876.43', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(119, 'PURCHASE OF  A NEW ROBUST MONEY COUNTING MACHINE FOR THE RMU CASHIER', '14', '112070', '129872', 'RMU/ACC/OE/MCM/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ACCOUNTS - CASHIER', 'USER NAME', '2024-05-27', NULL, 2025, '0.00', '21996.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(120, 'PURCHASE OF SOLLATEK SVS-04 (1) FOR ADMINISTRATION', '52', '112108', '130954', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-04', NULL, 2025, '0.00', '1848.00', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(121, 'PURCHASE OF 3 IN 1 CABINET FOR DEP. REGISTRAR-ADMIN\'S OFFICE, 3NR DOUBLE DOOR METAL CABINET FOR THE MARINE HOSPITALITY DEPT. AND GMDSS LAB', '24', '112080', '130983', 'RMU/DRA/OE/026/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'DEPUTY REGISTRAR - ADMIN.', 'USER NAME', '2024-06-07', NULL, 2025, '0.00', '6155.95', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(122, 'PURCHASE OF 3 IN 1 CABINET FOR DEP. REGISTRAR-ADMIN\'S OFFICE, 3NR DOUBLE DOOR METAL CABINET FOR THE MARINE HOSPITALITY DEPT. AND GMDSS LAB', '34', '112090', '130983', 'RMU/DSU/OE/026/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'MARINE HOSPITALITY /DSU', 'USER NAME', '2024-06-07', NULL, 2025, '0.00', '6155.95', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(123, 'PURCHASE OF 3 IN 1 CABINET FOR DEP. REGISTRAR-ADMIN\'S OFFICE, 3NR DOUBLE DOOR METAL CABINET FOR THE MARINE HOSPITALITY DEPT. AND GMDSS LAB', '29', '112085', '130983', 'RMU/GMDSS/OE/D026/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'GMDSS', 'USER NAME', '2024-06-07', NULL, 2025, '0.00', '6155.95', 0, 0, '0.00', 9, '2025-03-17 00:00:00'),
(124, 'PURCHASE OF INR 42\'\' SMART TELEVISION FOR THE CONFERENCE ROOM IN THE NEW OFFICE EXTENSION', '16', '112072', '135328', 'RMU/AAB/OE/TV42/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ADMINISTRATION ANNEX', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '3299.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(125, 'PURCHASE OF 2-WAY MAIN SWITCH CIRCUIT BREAKER FOR MSSC CLASSROOM, EXTENSION CABLE AND INDUSTRIAL FAN FOR STUDENT AFFAIRS DEPT.', '22', '112078', '135490', 'RMU/CSA/OE/SFAN/01 - 2/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'CSA', 'USER NAME', '2024-07-16', NULL, 2025, '0.00', '5824.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(127, 'PURCHASE OF 1NR 55\'\' DIGITAL TV WITH BRACKET FOR THE STAFF CANTEEN AND 2NR AIR CONDITIONERS FOR THE RECOVERY ROOM AT SICK BAY', '17', '112073', '140876', 'RMU/AAB/OE/TV55/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ADMINISTRATION ANNEX CANTEEN', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '31232.61', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(128, 'PURCHASE OF 1NR 55\'\' DIGITAL TV WITH BRACKET FOR THE STAFF CANTEEN AND 2NR AIR CONDITIONERS FOR THE RECOVERY ROOM AT SICK BAY', '54', '112110', '140876', 'RMU/SBU/OE/017/01 - 2/22', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '31232.61', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(129, 'PAYMENT FOR ONE PC SVS AND ONE PC UPS FOR INTERNAL AUDIT UNIT', '55', '112111', '143057', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-12', NULL, 2025, '0.00', '5666.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(130, 'PURCHASE OF 1PC OF UPS FOR THE GAMBIA HOSTEL', '56', '112112', '143071', 'RMU/////', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', NULL, 2025, '0.00', '6923.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(131, 'PAYMENT FOR THE PURCHASE OF 1.5HP AIR CONDITIONER FOR THE CASHIER\'S OFFICE', '13', '112069', '146507', 'RMU/ACC/OE/017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'ACCONTS - CASHIER ', 'USER NAME', '2024-10-29', NULL, 2025, '0.00', '4500.00', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(132, 'PURCHASE OF NASCO TABLE TOP REFRIDGERATOR FOR VSTC', '43', '112099', '151674', 'RMU/VSTC/OE/F017/01/24', 'SUPPLIER NAME', 'Office Equipment', 'Office Equipment', 'OWNED', 'VSTC ', 'USER NAME', '2024-11-22', NULL, 2025, '0.00', '4500.01', 0, 0, '0.00', 10, '2025-03-17 00:00:00'),
(133, 'PURCHASE OF INR 3-IN-1 WAITING SOFA FOR UR\'S OFFICE & INR 42\" DIGITAL SATELLITE TV FOR USE AT THE PORTERS\' LODGE-BACK ACCOMODATION', '34527', '457146', 'P:118841', 'tg23494', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-06', NULL, 2025, '0.00', '9500.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(134, 'PURCHASE OF 2NO SINGLE WAITING CHAIRS FOR THE OFFICE OF THE UNIVERSITY REGISTRAR', '34528', '457147', 'P:118854', 'tg23495', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-07', NULL, 2025, '0.00', '25480.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(135, 'SEWING AND INSTALLATION OD 100PCS OF CURTAINS FOR FEMALES AT BACK ACCOMODATION', '34529', '457148', 'P:118875', 'tg23496', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-02-08', NULL, 2025, '0.00', '25180.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(137, 'PURCHASE OF 3-IN-ONE SOFAR AND CENTRE TABLE FOR DF\'S OFFICE', '34531', '457150', 'P:122348', 'tg23498', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-03-28', NULL, 2025, '0.00', '9500.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(138, 'PURCHASE OF TWO PCS OF COFFEE TABLE FOR DIRECTOR OF FINANCE & UR\'S OFFICES', '34532', '457151', 'P:125526', 'tg23499', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-25', NULL, 2025, '0.00', '6620.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(139, 'REFURBISHMENT OF 160 MATTRESSES FOR THE STUDENTS ACCOMODATION (CADET & BACK)', '34533', '457152', 'P:125529', 'tg23500', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-04-26', NULL, 2025, '0.00', '144000.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(140, 'PURCHASE OF NINE PCS OF EXEC. SWIVEL CHAIRS FOR IT, LIBRARY AND REGISTRY', '34534', '457153', 'P:126682', 'tg23501', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-07', NULL, 2025, '0.00', '28080.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(143, 'PROVISION OF CURTAINS FOR THE NEW OFFICE EXTENSION AT THE MARKETING UNIT & IT TECHNICIAN OFFICE', '34537', '457156', 'P:129875', 'tg23504', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-05-28', NULL, 2025, '0.00', '14240.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(144, 'PURCHASE OF SWIVEL CHAIR FOR THE SNR. STORES OFFICER AND LECTURE HALL CHAIRS FOR GMDSS LAB', '34538', '457157', 'P:130980', 'tg23505', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-07', NULL, 2025, '0.00', '48048.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(145, 'PURCHASE OF EIGHT EXECUTIVE CHAIRS FOR THE 18TH CONGREGATION  DIFFERENCE ON EXCHANGE (USD) FROM 9.000000000 TO 10.000000000', '34539', '457158', 'P:135145', 'tg23506', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-06-20', NULL, 2025, '0.00', '17600.00', 0, 0, '0.00', 9, '2025-03-24 00:00:00'),
(146, 'PURCHACE OF SWIVEL CHAIRS, L-SHAPE DESK AND VISITORS CHAIR FOR THE BUSINESS DEV. CENTRE OFFICES ', '34540', '457159', 'P:135314', 'tg23507', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '13416.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(147, 'PURCHASE OF LECTURE ROOM TABLES FOR GMDSS LABORATORY ', '34541', '457160', 'P:135315', 'tg23508', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '30840.70', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(148, 'PURCHASE OF CONFERENCE TABLE FOR THE CONFERENCE ROOM AT THE NEW OFFICE EXTENSION AT THE MARKETING UNIT', '34542', '457161', 'P:135318', 'tg23509', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '69680.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(149, 'REFUND FOR THE PURCHASE OF CURTAINS FOR UR\'S AND DEPUTY REGISTRAR-ADMIN\'S OFFICE', '34543', '457162', 'P:135297', 'tg23510', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-01', NULL, 2025, '0.00', '1056.40', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(150, 'REPLACEMENT AND REPAIR OF BROKEN AUDITORIUM FIXED CHAIRS', '34544', '457163', 'P:135417', 'tg23511', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-12', NULL, 2025, '0.00', '17200.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(151, 'PURCHASE OF 1NR EXECUTIVE SWIVEL CHAIR FOR QMS OFFICE', '34545', '457164', 'P:137586', 'tg23512', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-07-30', NULL, 2025, '0.00', '4160.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(152, 'PURCHASE OF SWIVEL CHAIR FOR PROVOST', '34546', '457165', 'P:140875', 'tg23513', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '7800.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(153, 'PURCHASE OF 1NO 3-IN-1 WAITING CHAIR (OUTSIDE) FOR ADMINISTRATION', '34547', '457166', 'P:140878', 'tg23514', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-22', NULL, 2025, '0.00', '3328.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(154, 'PURCHASE OF 2NR EACH OF 1/2 10\'\' HIGH DENSITY MATTRESSES AND 1/2 WOODEN BEDS TO BE USED AT THE GAMBIA HOSTEL', '34548', '457167', 'P:140918', 'tg23515', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-08-27', NULL, 2025, '0.00', '12800.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(155, 'PURCHASE OF DESK AND VISITORS WAITING CHAIR FOR PROVOST\'S OFFICE', '34549', '457168', 'P:142009', 'tg23516', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', NULL, 2025, '0.00', '11128.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(156, 'PURCHASE OF 4NR STAINLESS STEEL 4-IN-1 WAITING CHAIRS FOR REGISTRY AND ACCOUNTS FRONTAGE', '34550', '457169', 'P:142010', 'tg23517', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', NULL, 2025, '0.00', '14000.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(157, 'PURCHASE OF CONFERENCE CHAIR, SWIVEL CHAIR AND DESK FOR MARINE HOSPITALITY DEPT.', '34551', '457170', 'P:142011', 'tg23518', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-04', NULL, 2025, '0.00', '8492.36', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(158, 'PURCHASE OF 1NR OFFICE DESK AND 1NR CHAIR (SWIVEL) FOR QMS OFFICE', '34552', '457171', 'P:143067', 'tg23519', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', NULL, 2025, '0.00', '4437.16', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(159, 'PURCHASE OF SWIVEL CHAIR FOR CASHIER', '34553', '457172', 'P:143066', 'tg23520', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-09-13', NULL, 2025, '0.00', '3120.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(160, 'PURCHASE OF 3-IN-1 SOFA, ORTHOPEDIC SWIVEL CHAIR FOR PROVOST\'S OFFICE', '34554', '457173', 'P:144299', 'tg23521', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-09', NULL, 2025, '0.00', '15700.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(161, 'PURCHASE OF 2NR. 18 INCHES WALL FANS FOR CSA OFFICE BY DSU ', '34555', '457174', 'P:146343', 'tg23522', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-10-16', NULL, 2025, '0.00', '2600.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(162, 'PURCHASE OF 40 CHAIRS(SAME TYPE AS THE ONES AT TRANSPORT DEPT.) FOR 00W ENGINE (PART A) PROGRAMME', '34556', '457175', 'P:153717', 'tg23523', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-11-28', NULL, 2025, '0.00', '56074.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(163, 'PURCHASE OF DUST PROOF COVER FOR SMART BOARD TV 65\'\' FOR TRANSPORT DEPT.', '34557', '457176', 'P:153819', 'tg23524', 'SUPPLIER NAME', 'Furnitures & Fixtures', 'Furnitures & Fixtures', 'OWNED', 'ON CAMPUS', 'USER NAME', '2024-12-10', NULL, 2025, '0.00', '11832.00', 0, 0, '0.00', 10, '2025-03-24 00:00:00'),
(164, 'PAYMENT OF 20% OF TOTAL CONTRACT VALUE AS MOBILISATION FOR THE CONSTRUCTION  OF DORMITORY AT RMU', 'N/A', 'N/A', 'P: 148573', 'N/A', 'SUPPLIER', 'Building Works in Progress', 'Building Works in Progress', 'OWNED', 'on campus', 'USERNAME', '2024-11-13', NULL, 2025, '0.00', '1944000.00', 0, 0, '0.00', 10, '2025-03-25 09:35:27'),
(166, 'PURCHASE OF DEFIBRILLATOR AED 2001-CHARGEABLE FOR THE AMBULANCE PAYABLE TO JAKE ADAMZ CO. LTD', 'N/A', 'N/A', 'N/A', 'N/A', 'JAKE ADAMZ CO. LTD', 'Machine And Equipment', 'test sub class', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '19500.00', 0, 0, '0.00', 10, '2025-04-04 09:51:57'),
(167, 'PAYMENT FOR ONE VENTILATOR AMBULANCE PAYABLE TO PAAYIE ENTERPRISE', 'N/A', 'N/A', 'N/A', 'N/A', 'PAAYIE ENTERPRISE', 'Machine And Equipment', 'Test sub class', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '104000.00', 0, 0, '0.00', 10, '2025-04-04 09:51:57'),
(168, 'PAYMENT FOR 70KG ANVIL WEIGHT PAYABLE TO B.A. PURPLE', 'N/A', 'N/A', 'N/A', 'N/A', ' B.A. PURPLE', 'Machine And Equipment', 'Test', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '10000.00', 0, 0, '0.00', 10, '2025-04-04 09:54:10'),
(170, 'PAYMENT FOR SMART TELEVISION SET TO BE USED AT OFFICE COMPLEX PAYABLE TO THE SPOT FURNITURE', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Teaching Equipment', 'test', 'owned', 'on campus', 'USER', '2024-12-31', NULL, 0, '0.00', '39662.68', 0, 0, '0.00', 10, '2025-04-07 08:43:17'),
(171, 'PAYMENT FOR EXECUTIVE ONE EXECUTIVE DESK FOR THE BUSINESS DEVELOPEMT CENTRE', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'BDC', 'USER', '2024-06-21', NULL, 2025, '0.00', '3943.47', 0, 0, '0.00', 9, '2025-04-07 08:53:37'),
(172, 'PURCHASE OF 1NR SWIVEL CHAIR FOR THE BUSINESS DEVELOPMENT CENTRE', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'BDC', 'USER', '2024-06-21', NULL, 2025, '0.00', '3500.00', 0, 0, '0.00', 9, '2025-04-07 08:53:37'),
(173, 'TWO SWIVEL CHAIRS FOR PROCUREMENT & AG. VC', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'PROC. & VC', 'USER', '2024-12-31', NULL, 2025, '0.00', '6068.18', 0, 0, '0.00', 10, '2025-04-07 08:57:18'),
(174, 'PAYMENT FOR CARPENTRY ITEMS TO BE USED AT THE DRAWING ROOM 202', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Furnitures & Fixtures', 'TEST', 'OWNED', 'DRAWING ROOM', 'USER', '2024-12-31', NULL, 2025, '0.00', '95056.00', 0, 0, '0.00', 10, '2025-04-07 08:57:18'),
(175, 'PAYMENT FOR CURTAINS, ACCESSORIES AND INSTALLATION AT THE DRAWING ROOM', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Furnitures & Fixtures', 'Test', 'OWNED', ' DRAWING ROOM', 'USER', '2024-12-31', NULL, 2025, '0.00', '5100.00', 0, 0, '0.00', 10, '2025-04-07 09:01:21');
INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `in_service_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(177, 'INVENTOR BATTERY SYSTEM FOR THE SERVER CONTROL ROOM PAYABLE TO DATA VOICE NETWORK SYSTEM', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'TEST', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '55500.00', 0, 0, '0.00', 10, '2025-04-07 23:15:45'),
(178, 'PAYMENT FOR PRINTER FOR THE OFFICE OF THE ASSISTANT REGISTRAR', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'Test', 'owned', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '10354.92', 0, 0, '0.00', 10, '2025-04-07 23:15:45'),
(179, 'PAYMENT FOR SOLLATEX VOLTAGE STABILIZER', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'Test', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '2040.00', 0, 0, '0.00', 10, '2025-04-07 23:22:38'),
(180, 'PAYMENT FOR D-LINK 1510-28P', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'TEST', 'owned', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '99008.00', 0, 0, '0.00', 10, '2025-04-07 23:22:38'),
(181, 'PAYMENT FOR DESKTOP COMPUTER FOR AG VC;S ADMIN. ASS.', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'Test', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 10, '2025-04-07 23:28:24'),
(182, 'PAYMENT FOR TWO (2TB) EXTERNAL HARD DRIVE PAYABLE TO TRADEMART', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'TEST', 'owned', 'on campus', 'USER', '2024-12-31', NULL, 2025, '0.00', '3800.00', 0, 0, '0.00', 10, '2025-04-07 23:28:24'),
(183, 'PAYMENT FOR DRILLING ANTI-PLAGIARISM SOFTWARE', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Computer & Accessories', 'Test', 'OWNED', 'on campus', 'USERNAME', '2024-12-31', NULL, 2025, '0.00', '34650.00', 0, 0, '0.00', 10, '2025-04-07 23:30:00'),
(184, 'BEING DONATION OF TWO AIR-CONDITIONERS BY PAST STUDENTS', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Office Equipment', 'TEST', 'OWNED', 'on campus', 'USER', '2024-12-19', NULL, 2025, '0.00', '9099.00', 0, 0, '0.00', 10, '2025-04-09 12:40:50'),
(186, 'PAYMENT FOR 2NO STANDING ACS FOR THE BRIDGE SIMULATOR LAB. PAYABLE TO SPOT FURNITURE\r\n', 'N/A', 'N/A', 'N/A', 'N/A', 'SUPPLIER', 'Office Equipment', 'SUBclass', 'OWNED', 'BRIDGE SIMULATOR', 'USER', '2024-12-31', NULL, 2025, '0.00', '51998.00', 0, 0, '0.00', 10, '2025-04-09 12:43:21'),
(188, 'PAYMENT FOR TWO 2 PCS OF AC UNIT SET (2.5HP) FOR THE MSSC LECTURERS OFFICE PAYABLE TO COMPU-GHANA\r\n', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Office Equipment', 'SUBCLASS', 'OWNED', 'on campus', 'USER', '2024-12-31', NULL, 2025, '0.00', '43600.00', 0, 0, '0.00', 10, '2025-04-09 12:47:19'),
(190, 'PAYMENT FOR 1NO WATER DISPENSER FOR THE DRAWING ROOM AT THE OFFICE COMPLEX PAYABLE TO SPOT FURNITURE\r\n', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Office Equipment', 'SUBClass', 'OWNED', 'DRAWING ROOM', 'USER', '2024-12-31', NULL, 2025, '0.00', '2499.00', 0, 0, '0.00', 10, '2025-04-09 12:50:13'),
(193, 'BEING CONTRACT FOR THE CONSRUCTION OF WATERPROOFING WORKS (CONCRESETE CEILING) AT THE CADET MESS BY MIKOBIE CO. LTD', 'N/A', 'N/A', 'N/A', 'N/A', 'MIKOBIE CO. LTD', 'Buildings', 'Test', 'OWNED', 'on campus', 'Username', '2024-08-31', NULL, 2025, '0.00', '114068.00', 0, 0, '0.00', 10, '2025-04-11 10:31:07'),
(194, 'BEING THE CONSTRUCTION OF ULTRA MODERN MOSQUE COMPLEX AND MECHANISED BOREHOLE BY DIRECTAID SOCIETY GHANA FOR THE UNIVERSITY', 'N/A', 'N/A', 'N/A', 'N/A', 'DIRECTAID SOCIETY GHANA', 'Buildings', 'Test', 'owned', 'on campus', 'USERNAME', '2024-12-24', NULL, 2025, '0.00', '840000.00', 0, 0, '0.00', 10, '2025-04-11 10:31:07'),
(197, 'Extention Cable & Sollatek Voltage', 'RMU/IT/UPS/C&A/02/25', 'RMU/IT/UPS/C&A/02/25', 'P: 160457', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Henry Snow', '2025-02-12', NULL, 2025, '0.00', '4976.00', 0, 0, '0.00', 11, '2025-07-30 11:33:28'),
(202, 'DECKTOP', 'P: 161493', '4CE426BWXR', 'P: 161493', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 11:31:00'),
(203, 'DECKTOP', 'P: 161493-1', '4CE426BT89', 'P: 161493-1', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:02:05'),
(204, 'DECKTOP', 'P: 161493-2', '4CE426BX18', 'P: 161493-2', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:03:41'),
(205, 'DECKTOP', 'P: 161493-3', '4CE426BX00', 'P: 161493-3', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:05:38'),
(206, 'DECKTOP', 'P: 161493-4', '4CE426BT70', 'P: 161493-4', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:07:43'),
(207, 'DECKTOP', 'P: 161493-5', '4CE426BWV3', 'P: 161493-5', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:10:32'),
(208, 'DECKTOP', 'P: 161493-6', '4CER426BTSI', 'P: 161493-6', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:13:08'),
(209, 'DECKTOP', 'P: 161493-7', '4CE426BXIG', 'P: 161493-7', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:15:45'),
(210, 'DECKTOP', 'P: 161493-8', '4CE426BX0W', 'P: 161493-8', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:17:11'),
(211, 'DECKTOP', 'P: 161493-9', '4CE426BF7M', 'P: 161493-9', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:18:33'),
(212, 'DECKTOP', 'P: 161493-10', '4CE426BX49', 'P: 161493-10', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:20:08'),
(213, 'DECKTOP', 'P: 161493-12', '4CE426BT4X', 'P: 161493-12', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:24:13'),
(214, 'DECKTOP', 'P: 161493-13', '4CE426BWS4', 'P: 161493-13', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:25:40'),
(215, 'DECKTOP', 'P: 161493-14', '4CE426BT43', 'P: 161493-14', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:32:01'),
(216, 'DECKTOP', 'P: 161493-15', '4CE426BT71', 'P: 161493-15', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:34:53'),
(217, 'DECKTOP', 'P: 161493-16', '4CE426BX39', 'P: 161493-16', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:36:04'),
(218, 'DECKTOP', 'P: 161493-17', '4CE426BT3H', 'P: 161493-17', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:38:01'),
(219, 'DECKTOP', 'P: 161493-18', '4CE426BX2X', 'P: 161493-18', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:39:51'),
(220, 'DECKTOP', 'P: 161493-19', '4CE426BTBS', 'P: 161493-19', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:41:13'),
(221, 'DECKTOP', 'P: 161493-20', '4CE426BWX7', 'P: 161493-20', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:42:32'),
(222, 'DECKTOP', 'P: 161493-21', '4CE426BT5K', 'P: 161493-21', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:43:41'),
(223, 'DECKTOP', 'P: 161493-22', '4CE426BT75', 'P: 161493-22', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:45:10'),
(224, 'DECKTOP', 'P: 161493-23', '4CE426BX0M', 'P: 161493-23', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:46:31'),
(225, 'DECKTOP', 'P: 161493-24', '4CE426BTFN', 'P: 161493-24', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:48:06'),
(226, 'DECKTOP', 'P: 161493-25', '4CE426BT19', 'P: 161493-25', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:49:56'),
(227, 'DECKTOP', 'P: 161493-26', '4CE426BT55', 'P: 161493-26', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:53:11'),
(228, 'DECKTOP', 'P: 161493-27', '4CE426BWNP', 'P: 161493-27', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:55:09'),
(229, 'DECKTOP', 'P: 161493-28', '4CE426BT30', 'P: 161493-28', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 12:57:28'),
(230, 'DECKTOP', 'P: 161493-29', '4CE426BX41', 'P: 161493-29', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '13407.17', 0, 0, '0.00', 12, '2025-08-18 13:00:49'),
(232, 'PRINTER', 'P: 160912=1', 'PHM5P26189', 'P: 160912=1', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2025, '0.00', '4693.15', 0, 0, '0.00', 12, '2025-08-18 13:16:35'),
(234, 'CONSTRUCTION OF DORMITORY', 'P: 159110', 'P: 159110', 'P: 159110', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-01-17', NULL, 2025, '0.00', '2555000.00', 0, 0, '0.00', 11, '2025-08-25 10:50:06'),
(235, '30% CONTRACT SUM FOR CONSTRUCTION OF WASH ROOMS', 'P: 160259', 'P: 160259', 'P: 160259', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'RMU CAMPUS', 'Ismail Abdulai-Saiku', '2025-01-30', NULL, 2025, '0.00', '69900.00', 0, 0, '0.00', 11, '2025-08-25 11:02:52'),
(236, 'BALANCE ON 2ND INSTALLMENT FOR DORMITORY', 'P: 160544', 'P: 160544', 'P: 160544', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-02-25', NULL, 2025, '0.00', '532000.00', 0, 0, '0.00', 11, '2025-08-25 11:05:51'),
(237, '3RD INSTALLMENT FOR CONTRUCTION OF DORMITORY', 'P: 160559', 'P: 160559', 'P: 160559', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-02-26', NULL, 2025, '0.00', '1848000.00', 0, 0, '0.00', 11, '2025-08-25 11:08:29'),
(238, '4TH PAYMENT FOR CONSTRUCTION OF DORMITORY', 'P: 161233', 'P: 161233', 'P: 161233', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-05-08', NULL, 2025, '0.00', '1290000.00', 0, 0, '0.00', 12, '2025-08-25 11:11:03'),
(239, 'CONSTRUCTION OF WASH ROOM', 'P: 161641', 'P: 161641', 'P: 161641', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-06-11', NULL, 2025, '0.00', '169100.00', 0, 0, '0.00', 12, '2025-08-25 11:13:48'),
(240, 'FINAL INSTALLMENT FOR THE CONTRUCTION OF DORMITORY', 'P: 161853', 'P: 161853', 'P: 161853', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-06-23', NULL, 2025, '0.00', '169000.00', 0, 0, '0.00', 12, '2025-08-25 11:15:40'),
(241, 'PROJECTOR', 'P: 160402', 'P: 160402', 'P: 160402', 'RMU///0/25', 'Others', 'Teaching Equipment', 'Test Sub Class', 'Owned', 'Lab Complex', 'Ismail Abdulai-Saiku', '2025-02-11', NULL, 2025, '0.00', '46280.00', 0, 0, '0.00', 11, '2025-08-25 11:25:52'),
(242, 'AIR CONDITION', 'P: 160272', 'P: 160272', 'P: 160272', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - HOD\'s office', 'Ismail Abdulai-Saiku', '2025-02-05', NULL, 2025, '0.00', '15054.65', 0, 0, '0.00', 11, '2025-08-25 11:44:40'),
(243, 'AIR CONDITIONER', 'P: 160365', 'P: 160365', 'P: 160365', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Transport Department - Classroom 101', 'Ismail Abdulai-Saiku', '2025-02-10', NULL, 2025, '0.00', '54500.00', 0, 0, '0.00', 11, '2025-08-25 11:48:05'),
(244, 'STEEL CABINET AND PRINTER', 'P: 160369', 'P: 160369', 'P: 160369', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Administration Block Annex - Quality Management Co', 'Ismail Abdulai-Saiku', '2025-02-10', NULL, 2025, '0.00', '4022.70', 0, 0, '0.00', 11, '2025-08-25 11:57:11'),
(245, 'AIR CONDITIONER', 'P: 160494-1', 'P: 160494-1', 'P: 160494-1', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Academic Research Unit - Head of Research Office', 'Ismail Abdulai-Saiku', '2025-02-14', NULL, 2025, '0.00', '19620.00', 0, 0, '0.00', 11, '2025-08-25 12:07:59'),
(246, 'AIR CONDITIONER', 'P: 160494', 'P: 160494', 'P: 160494', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'ICT - HOD\'s office', 'Ismail Abdulai-Saiku', '2025-02-14', NULL, 2025, '0.00', '19620.00', 0, 0, '0.00', 11, '2025-08-25 12:10:24'),
(247, 'AIR CONDITIONER', 'P: 160494-3', 'P: 160494-3', 'P: 160494-3', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Chartroom', 'Ismail Abdulai-Saiku', '2025-02-14', NULL, 2025, '0.00', '19600.00', 0, 0, '0.00', 11, '2025-08-25 12:12:33'),
(248, 'AIR CONDITIONER', 'P: 160494-4', 'P: 160494-4', 'P: 160494-4', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Pro Vice Chancellor - General office', 'Ismail Abdulai-Saiku', '2025-02-14', NULL, 2025, '0.00', '19620.00', 0, 0, '0.00', 11, '2025-08-25 12:14:21'),
(249, 'AIR CONDITIONER', 'P: 160494-5', 'P: 160494-5', 'P: 160494-5', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Transport Department - Classroom 102', 'Ismail Abdulai-Saiku', '2025-02-14', NULL, 2025, '0.00', '19620.00', 0, 0, '0.00', 11, '2025-08-25 12:16:53'),
(251, 'AIR CONDITIONER', 'P: 160591', 'P: 160591', 'P: 160591', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Finance Directorate - Account officer\'s office', 'Ismail Abdulai-Saiku', '2025-03-04', NULL, 2025, '0.00', '4699.99', 0, 0, '0.00', 12, '2025-08-25 12:22:44'),
(252, 'FUSER REPLACEMENT FOR LIBRARY PHOTOCOPIER MACHINE', 'P: 160960', 'P: 160960', 'P: 160960', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Library - Main Library', 'Ismail Abdulai-Saiku', '2025-04-08', NULL, 2025, '0.00', '12575.51', 0, 0, '0.00', 12, '2025-08-25 12:25:21'),
(253, 'SHREDDER MACHINE', 'P: 161205', 'P: 161205', 'P: 161205', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Students Accommodation 1 - Co-ordinator\'s office', 'Ismail Abdulai-Saiku', '2025-05-13', NULL, 2025, '0.00', '6704.50', 0, 0, '0.00', 12, '2025-08-25 12:31:31'),
(255, 'FILE CABINET', 'P: 161445', 'P: 161445', 'P: 161445', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Administration Block Annex - Head BDC', 'Ismail Abdulai-Saiku', '2025-05-20', NULL, 2025, '0.00', '4571.25', 0, 0, '0.00', 12, '2025-08-25 12:53:19'),
(256, 'CONSTRUCTION OF L-SHAPED CANOPY', 'P: 161022', 'P: 161022', 'P: 161022', 'RMU///0/25', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - Old office', 'Ismail Abdulai-Saiku', '2025-04-10', NULL, 2025, '0.00', '44852.00', 0, 0, '0.00', 12, '2025-08-25 13:02:17'),
(258, 'FABRICATION AND INSTALLATION OF SLIDING WINDOWS AND DOOR ', 'P: 161595', 'P: 161595', 'P: 161595', 'RMU///0/25', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Classroo', 'Ismail Abdulai-Saiku', '2025-06-05', NULL, 2025, '0.00', '60224.69', 0, 0, '0.00', 12, '2025-08-25 13:12:28'),
(259, 'CONSTRUCTION OF ANTI-RUST METAL STAIRCASE FOR THE OVERHEAD TANK ', 'P: 161642', 'P: 161642', 'P: 161642', 'RMU///0/25', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'RMU CAMPUS', 'Ismail Abdulai-Saiku', '2025-06-11', NULL, 2025, '0.00', '98637.00', 0, 0, '0.00', 12, '2025-08-25 13:14:44'),
(260, 'SUPPLY AND FIXING OF FLOOR WOOLEN CARPET, CURTAINS AND ACCESSORIES ', 'P: 160278', 'P: 160278', 'P: 160278', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Global M', 'Ismail Abdulai-Saiku', '2025-02-18', NULL, 2025, '0.00', '18620.00', 0, 0, '0.00', 11, '2025-08-25 13:31:53'),
(261, 'MANUFACTURING OF SPECIALIZED TABLES (3NR) FOR THE TABLE TOP BRIDGE SIMULATOR', 'P: 160370', 'P: 160370', 'P: 160370', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'BRIDGE SIMULATOR', 'Ismail Abdulai-Saiku', '2025-02-10', NULL, 2025, '0.00', '10500.00', 0, 0, '0.00', 11, '2025-08-25 13:39:54'),
(262, ' 2 SWIVEL CHAIRS FOR LECTURERS IN THE ICT DEPARTMENT AND 1NR SWIVEL CHAIR FOR INSTRUCTOR FOR THE TABLE-TOP BRIDGE SIMULATOR', 'P: 160437', 'P: 160437', 'P: 160437', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'BRIDGE SIMULATOR', 'Ismail Abdulai-Saiku', '2025-02-13', NULL, 2025, '0.00', '21238.63', 0, 0, '0.00', 11, '2025-08-25 13:42:47'),
(263, ' 3PCS OF OFFICE WRITING DESKS ', 'P: 160515', 'P: 160515', 'P: 160515', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Development Services Unit - Head of DSU\'s office', 'Ismail Abdulai-Saiku', '2025-02-17', NULL, 2025, '0.00', '11544.00', 0, 0, '0.00', 12, '2025-08-25 13:45:31'),
(265, ' 100PCS OF LECTURE HALL DESK ', 'P: 160908', 'RMU/MEE/LHC/FF/01/25 TO RMU/MEE/LHC/FF/100/25', 'P: 160908', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Classroo', 'Ismail Abdulai-Saiku', '2025-04-02', NULL, 2025, '0.00', '131652.00', 0, 0, '0.00', 12, '2025-08-25 13:54:30'),
(266, ' SWIVEL CHAIR FOR THE STUDENTS COORDINATOR AND 30PCS OF  SWIVEL CHAIRS FOR ICT DEPT.', 'P: 160944', 'P: 160944', 'P: 160944', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'ICT - Computer Laboratory 1', 'Ismail Abdulai-Saiku', '2025-04-07', NULL, 2025, '0.00', '79500.00', 0, 0, '0.00', 12, '2025-08-25 13:57:31'),
(267, '. OFFICE CHAIR', 'P: 160966', 'P: 160966', 'P: 160966', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Staff Common room', 'Ismail Abdulai-Saiku', '2025-04-08', NULL, 2025, '0.00', '6068.18', 0, 0, '0.00', 12, '2025-08-25 14:00:56'),
(268, 'SUPPLY & FIXING OF SECURITY (METAL) DOOR AND PANEL ', 'P: 161041', 'P: 161041', 'P: 161041', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Pro Vice Chancellor - Pro-Vice Chancellor\'s office', 'Ismail Abdulai-Saiku', '2025-04-14', NULL, 2025, '0.00', '7010.00', 0, 0, '0.00', 12, '2025-08-25 15:22:43'),
(269, 'PROVISION AND INSTALLATION OF CURTAINS ND ACCESSORIES ', 'P: 161118', 'P: 161118', 'P: 161118', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Transport Department - Classroom 101', 'Ismail Abdulai-Saiku', '2025-04-23', NULL, 2025, '0.00', '11680.00', 0, 0, '0.00', 12, '2025-08-25 15:39:02'),
(270, 'CURTAINS FOR FEMALE ROOMS AT BACK/CADET ACCOMODATION BUILDINGS', 'P: 161137', 'P: 161137', 'P: 161137', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'BACK ACCOMMODATION', 'Ismail Abdulai-Saiku', '2025-04-29', NULL, 2025, '0.00', '35010.00', 0, 0, '0.00', 12, '2025-08-28 09:09:59'),
(271, 'INSTALLATION OF CURTAINS AND ACCESSORIES AT NSD 101 AND 102', 'P: 161119', 'P: 161119', 'P: 161119', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Classroom-Down 101', 'Ismail Abdulai-Saiku', '2025-04-29', NULL, 2025, '0.00', '12800.00', 0, 0, '0.00', 12, '2025-08-28 09:12:55'),
(272, ' 2 ADDITIONAL CHAIRS FOR THE GMDSS LAB', 'P: 161207', 'RMU/MSSC/LHC2/FF/02/25 ,RMU/MSSC/LHC2/FF/01/25', 'P: 161207', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Global M', 'Ismail Abdulai-Saiku', '2025-05-07', NULL, 2025, '0.00', '4160.00', 0, 0, '0.00', 12, '2025-08-28 09:15:56'),
(273, 'PURCHASE OF 3 ADDITIONAL TABLES FOR GMDSS, AN EXECUTIVE DESK FOR MARKETING & OFFICE DESK FOR STORES', 'P: 161201', 'P: 161201', 'P: 161201', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marketing Unit - Marketing Officers\' office', 'Ismail Abdulai-Saiku', '2025-05-07', NULL, 2025, '0.00', '22566.03', 0, 0, '0.00', 12, '2025-08-28 10:12:25'),
(274, 'PAYMENT FOR 100 PCS OF LECTURE HALL DESK (SIV-002481)', 'P: 161484', 'RMU/MEE/LHC/FF/101/25  TO RMU/MEE/LHC/FF/200/25', 'P: 161484', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Tools Store Room', 'Ismail Abdulai-Saiku', '2025-05-26', NULL, 2025, '0.00', '131652.00', 0, 0, '0.00', 12, '2025-08-28 10:19:00'),
(275, 'PURCHASE OF AN OFFICE DESK TO REPLACE THE INFESTED DESK FOR THE CONSULTING ROOM AT SICKBAY             (SIV-002479)', 'P: 161492', 'RMU/SB/DSK/FF/02/25', 'P: 161492', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Sick Bay - Consulting room 1', 'Ismail Abdulai-Saiku', '2025-05-26', NULL, 2025, '0.00', '4000.00', 0, 0, '0.00', 12, '2025-08-28 10:21:34'),
(276, 'PURCHASE OF 2 LOCALLY MANUFACTURED DOUBLE DOOR STEEL CABINETS FOR THE STORES UNIT (GRV-002502)', 'P: 161580', 'P: 161580', 'P: 161580', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Stores Unit - Stores officer', 'Ismail Abdulai-Saiku', '2025-06-03', NULL, 2025, '0.00', '9142.50', 0, 0, '0.00', 12, '2025-08-28 10:23:37'),
(277, 'PROVISION OF CURTAINS, ACCESSORIES AND INSTALLATION IN THE VC\'S LOUNGE', 'P: 161615', 'P: 161615', 'P: 161615', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Vice Chancellor - Conference Room', 'Ismail Abdulai-Saiku', '2025-06-05', NULL, 2025, '0.00', '10660.00', 0, 0, '0.00', 12, '2025-08-28 10:26:11'),
(278, 'PURCHASE OF SWIVEL CHAIR FOR THE HOD, ICT (PURCHASE OF SWIVEL CHAIR FOR THE HOD, ICT (GRV-002518)', 'PURCHASE OF SWIVEL C', 'P: 161709', 'P: 161709', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'ICT - HOD\'s office', 'Ismail Abdulai-Saiku', '2025-06-13', NULL, 2025, '0.00', '4500.00', 0, 0, '0.00', 12, '2025-08-28 10:29:51'),
(279, 'SUPPLY AND INSTALLATION OF CURTAINS & ACCESSORIES FOR THE MODERN VIRTUAL CONFERENCE HALL AT MEE', 'P: 161884', 'P: 161884', 'P: 161884', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Classroo', 'Ismail Abdulai-Saiku', '2025-06-25', NULL, 2025, '0.00', '9200.00', 0, 0, '0.00', 12, '2025-08-28 10:33:18'),
(281, 'LAPTOP FOR THE MARKETING UNIT', 'P: 160647', 'P: 160647', 'P: 160647', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Marketing Unit - Head of Marketing', 'Ismail Abdulai-Saiku', '2025-03-11', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 12, '2025-08-28 10:41:15'),
(282, ' LAPTOP FOR THE MARKETING UNIT', 'P: 160647-1', 'RMU/IT/LPt/C&A/01/25', 'P: 160647-1', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Administration - IT Technician\'s office 1', 'Ismail Abdulai-Saiku', '2025-03-11', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 12, '2025-08-28 10:45:33'),
(283, 'PROCUREMENT OF LAPTOP FOR MSSC', 'P: 160647-3', 'RMU/IT/LPt/C&A/02/25', 'P: 160647-3', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - HOD\'s office', 'Ismail Abdulai-Saiku', '2025-03-11', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 12, '2025-08-28 10:50:16'),
(284, 'F UNIT ELECTRONIC MOTORISED PROJECTION SCREEN WITH REMOTE CONTROL FOR MEE DEPT.', 'P: 160912', 'P: 160912', 'P: 160912', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Master\'s', 'Ismail Abdulai-Saiku', '2025-03-27', NULL, 2025, '0.00', '5270.00', 0, 0, '0.00', 12, '2025-08-28 10:54:48'),
(285, ' COLOUR PRINTER FOR BDC OFFICE', 'P: 160913', 'P: 160913', 'P: 160913', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Administration Block Annex - Head BDC', 'Ismail Abdulai-Saiku', '2025-04-04', NULL, 2025, '0.00', '13981.58', 0, 0, '0.00', 12, '2025-08-28 11:00:55'),
(286, ' TWO ADDITIONAL DEEP CYCLE BATTERY FOR SERVER ROOM INVERTER SYSTEM', 'P: 160963', 'P: 160963', 'P: 160963', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'SERVER ROOM', 'Ismail Abdulai-Saiku', '2025-04-08', NULL, 2025, '0.00', '9974.00', 0, 0, '0.00', 12, '2025-08-28 11:06:11'),
(287, 'PURCHASE OF VARIOUS SOLLATEK PRODUCTS FOR MSSC, IT & ICT AND UPS & EXTENSION BOARD FOR MARKETING & MSSC DESKTOPS', 'P: 160967', 'P: 160967', 'P: 160967', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-04-09', NULL, 2025, '0.00', '21485.89', 0, 0, '0.00', 12, '2025-08-28 11:09:04'),
(288, 'TWO CORE I5 LENOVO LAPTOPS AND LAPTOP BAG FOR IT UNIT', 'P: 161135', 'P: 161135', 'P: 161135', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Administration - IT Manger\'s office', 'Ismail Abdulai-Saiku', '2025-04-24', NULL, 2025, '0.00', '62047.10', 0, 0, '0.00', 12, '2025-08-28 11:16:57'),
(289, ' NEWLY LENOVO LAPTOP FOR DEPUTY REGISTRAR -ACADEMIC   (SIV-002447)', ' (SIV-002447)', 'RMU/DpReg/LPt/C&A/03/25', 'P: 161489', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Registry - Deputy Registrar Academic', 'Ismail Abdulai-Saiku', '2025-05-26', NULL, 2025, '0.00', '27122.75', 0, 0, '0.00', 12, '2025-08-28 11:22:22'),
(290, ' SOLLATEK SVS FOR GCU, MEE, MARKETING AND UPS FOR STUDENT AFFAIRS UNIT', 'P: 161883', 'P: 161883', 'P: 161883', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Students Accommodation 1 - Co-ordinator\'s office', 'Ismail Abdulai-Saiku', '2025-06-25', NULL, 2025, '0.00', '7706.00', 0, 0, '0.00', 12, '2025-08-28 11:25:01'),
(294, '2HP AIR CONDITION', '160513', ' 160513', '160513', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Administration - IT Manger\'s office', 'Ismail Abdulai-Saiku', '2025-02-17', NULL, 2025, '0.00', '7545.61', 0, 0, '0.00', 11, '2025-10-02 15:50:47'),
(295, 'CURTAINS', 'P:160211', 'P:160211', 'P:160211', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Provost - Provost\'s office', 'Ismail Abdulai-Saiku', '2025-01-24', NULL, 2025, '0.00', '4280.00', 0, 0, '0.00', 11, '2025-10-06 09:19:54'),
(296, 'CURTAINS', 'P:160260', 'P:160260', 'P:160260', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'VSTC', 'Ismail Abdulai-Saiku', '2025-01-30', NULL, 2025, '0.00', '3065.00', 0, 0, '0.00', 11, '2025-10-06 09:22:40'),
(297, 'CURTAINS', 'P:160344', 'P:160344', 'P:160344', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'BRIDGE SIMULATOR', 'Ismail Abdulai-Saiku', '2025-02-05', NULL, 2025, '0.00', '4360.00', 0, 0, '0.00', 11, '2025-10-06 09:25:11'),
(298, 'PURCHASE OF 2NO. UPS 2200VA AND 1NO SVS FOR LOT 1 ', ' (GRV-002541)', 'P: 162100', 'P: 162100', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', '', 'Ismail Abdulai-Saiku', '2025-07-18', NULL, 2025, '0.00', '10374.00', 0, 0, '0.00', 12, '2025-10-20 08:15:00'),
(300, ' 1NR LENOVO THINKPAD LAPTOP ', ' (GRV-002533)', 'P: 162157', 'P: 162157', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Master\'s', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2025, '0.00', '25719.00', 0, 0, '0.00', 12, '2025-10-20 08:32:36'),
(301, 'DISTRIBUTION OF FUNDS TO THE SOFTWARE PROJECT DEVELOPMENT TEAM', 'P: 162216', 'P: 162216', 'P: 162216', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Administration - IT Manger\'s office', 'Ismail Abdulai-Saiku', '2025-08-01', NULL, 2025, '0.00', '50000.00', 0, 0, '0.00', 12, '2025-10-20 08:38:17'),
(303, ' 1NR HP LASERJET PRINTER', '(GRV-002552)', 'P: 162242', 'P: 162242', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Development Services Unit - Head of DSU\'s office', 'Ismail Abdulai-Saiku', '2025-08-06', NULL, 2025, '0.00', '2781.15', 0, 0, '0.00', 12, '2025-10-20 08:50:40'),
(304, ' DESKTOP COMPUTER ', '(GRV-002569)', 'P: 162308', 'P: 162308', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Stores Unit - Stores officer', 'Ismail Abdulai-Saiku', '2025-08-07', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 12, '2025-10-20 08:53:06'),
(305, ' SOFTWARE, CONNECTIVITY, AND TECHNOLOGY INSTALLATION UNDER LOT 2 ', '(GRV-002617)', 'P: 163355', 'P: 163355', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Conference Room', 'Ismail Abdulai-Saiku', '2025-08-15', NULL, 2025, '0.00', '481419.70', 0, 0, '0.00', 12, '2025-10-20 08:56:50'),
(306, 'HP COLOR PRINTER ', '(GRV-002624)', 'P: 163402', 'P: 163402', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Registry - Registration/Certification office', 'Ismail Abdulai-Saiku', '2025-08-19', NULL, 2025, '0.00', '5950.00', 0, 0, '0.00', 12, '2025-10-20 09:02:58'),
(308, 'NEW REBUILT DESKTOP COMPUTER ', '(GRV-002542)', 'P: 163526', 'P: 163526', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Global M', 'Ismail Abdulai-Saiku', '2025-08-28', NULL, 2025, '0.00', '15420.35', 0, 0, '0.00', 12, '2025-10-20 09:12:30'),
(309, ' ORDINARY OFFICE SWIVEL CHAIR ', ' (GRV-002537)', 'P: 161952', 'P: 161952', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Technip ', 'Ismail Abdulai-Saiku', '2025-07-03', NULL, 2025, '0.00', '3034.09', 0, 0, '0.00', 12, '2025-10-20 09:22:45'),
(310, 'SINGLE BED C/W MATTRESS ', '(GRV-002540)', 'P: 161992', 'P: 161992', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-09', NULL, 2025, '0.00', '32048.00', 0, 0, '0.00', 12, '2025-10-20 09:29:09'),
(311, ' 48PCS OF STUDENT MATTRESS WITH MAKINTOSH COVER ', '(GRV-002543)', 'P: 161990', 'P: 161990', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-09', NULL, 2025, '0.00', '54000.00', 0, 0, '0.00', 12, '2025-10-20 09:32:57'),
(312, ' WINDOW BLINDS ', 'P: 161991', 'P: 161991', 'P: 161991', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-09', NULL, 2025, '0.00', '15665.00', 0, 0, '0.00', 12, '2025-11-04 12:19:23'),
(314, ' 2 TABLES AND 4 CHAIRS FOR THE STUDENTS EXECUTIVE ROOMS ', '(GRV-002576)', 'P: 162101', 'P: 162101', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-18', NULL, 2025, '0.00', '9984.00', 0, 0, '0.00', 12, '2025-11-04 12:23:48'),
(315, ' 3-IN-1 VISITOR\'S CHAIR ', '(GRV-002579)', 'P: 162115', 'P: 162115', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Registry - Registration/Certification office', 'Ismail Abdulai-Saiku', '2025-07-30', NULL, 2025, '0.00', '3500.00', 0, 0, '0.00', 12, '2025-11-04 12:27:01'),
(316, ' 1PC CONFRERNCE STEDY TABLE & 10 CHAIRS  AND 21NO. OF CONFERENCE ROOM CHAIRS ', ' (GRV-002574) &(GRV-', 'P: 162132', 'P: 162132', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2025, '0.00', '94120.00', 0, 0, '0.00', 12, '2025-11-04 12:31:35'),
(317, ' 31PCS EACH OF MOVABLE TABLES & CHAIRS (', '(GRV-002568)', 'P: 162131', 'P: 162131', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - Old office', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2025, '0.00', '63800.00', 0, 0, '0.00', 12, '2025-11-04 12:41:10'),
(319, ' SWIVEL CHAIR  (GRV-002595)', '(GRV-002595)', 'P: 163424', 'P: 163424', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Transport Unit - Transport Officer\'s office', 'Ismail Abdulai-Saiku', '2025-08-21', NULL, 2025, '0.00', '4500.00', 0, 0, '0.00', 12, '2025-11-04 12:49:46'),
(320, 'ASSESSMENT AND MAINTENANCE WORKS ON THE AUDITORIUM FIXED CHAIRS FOR THE BOG MEETING', 'P: 163575', 'P: 163575', 'P: 163575', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Auditorium', 'Ismail Abdulai-Saiku', '2025-09-03', NULL, 2025, '0.00', '65000.00', 0, 0, '0.00', 12, '2025-11-04 12:52:01'),
(321, 'CURTAINS TO REPLACE THE VERY OLD AND WORN-OUT ONES ', '(SIV 002654)', 'P: 163672', 'P: 163672', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Stores Unit - Stores officer', 'Ismail Abdulai-Saiku', '2025-09-15', NULL, 2025, '0.00', '3295.00', 0, 0, '0.00', 12, '2025-11-04 12:54:07'),
(322, 'BEING THE COST OF THE LIBERIAN HOSTEL NOW CAPITALISED', 'GJ: 86434', 'GJ: 86434', 'GJ: 86434', 'RMU///0/25', 'Others', 'Land', 'Test Sub Class', 'Owned', 'RMU CAMPUS', 'Ismail Abdulai-Saiku', '2025-07-01', NULL, 2025, '0.00', '7199760.00', 0, 0, '0.00', 12, '2025-11-04 13:04:54'),
(323, 'FACELIFT OF THE LIBRARY STAFF WASHROOM', 'P: 162023', 'P: 162023', 'P: 162023', 'RMU///0/25', 'Others', 'Land', 'Test Sub Class', 'Owned', 'RMU CAMPUS', 'Ismail Abdulai-Saiku', '2025-07-10', NULL, 2025, '0.00', '16722.00', 0, 0, '0.00', 12, '2025-11-04 13:06:34'),
(324, ' REPLACEMENT OF EXISTING WOODEN WINDOW WITH ALUMINIUM GLAZING WINDLW ', 'GJ: 86289', 'GJ: 86289', 'GJ: 86289', 'RMU///0/25', 'Others', 'Land', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - Old office', 'Ismail Abdulai-Saiku', '2025-07-11', NULL, 2025, '0.00', '159565.00', 0, 0, '0.00', 12, '2025-11-04 13:10:47'),
(325, 'PROVISION OF DEDICATION PLAQUE ', 'P: 162156', 'P: 162156', 'P: 162156', 'RMU///0/25', 'Others', 'Land', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2025, '0.00', '77981.25', 0, 0, '0.00', 12, '2025-11-04 13:12:44'),
(326, 'CONSTRUCTION OF COVER SLABS TO DRAINS FOR THE LIBERIA HOSTEL', 'P: 162250', 'P: 162250', 'P: 162250', 'RMU///0/25', 'Others', 'Land', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-08-05', NULL, 2025, '0.00', '10042.34', 0, 0, '0.00', 12, '2025-11-04 13:14:25'),
(327, ' REFRIDGERATOR ', '(GRV-002532)', 'P: 161950', 'P: 161950', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Marketing Unit - Marketing Officers\' office', 'Ismail Abdulai-Saiku', '2025-07-03', NULL, 2025, '0.00', '2250.00', 0, 0, '0.00', 12, '2025-11-07 14:49:48'),
(328, 'PAPER SHREDDER ', '(GRV-002534)', 'P: 162009', 'P: 162009', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'GUIDANCE & COUNSELING', 'Ismail Abdulai-Saiku', '2025-07-10', NULL, 2025, '0.00', '6704.50', 0, 0, '0.00', 12, '2025-11-07 14:51:51'),
(329, ' 1NO. SOLLATEK SVS-04 FOR LOT 1 ', 'GRV-002565)', 'P: 162158', 'P: 162158', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Global M', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2025, '0.00', '2040.00', 0, 0, '0.00', 12, '2025-11-07 14:54:40'),
(330, ' AC AND ACCESSORIES-5.0 HP STANDING ', '002577)', 'P: 162147', 'P: 162147', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Global M', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2025, '0.00', '26500.00', 0, 0, '0.00', 12, '2025-11-07 14:56:40'),
(332, ' 1PC-2.0HP AIR CONDITIONER', '(GRV-002614)', 'P: 163446', 'P: 163446', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'GRADUATE SCHOOL', 'Ismail Abdulai-Saiku', '2025-08-22', NULL, 2025, '0.00', '7289.62', 0, 0, '0.00', 12, '2025-11-07 15:05:35'),
(333, ' 5PCS OF 30\'\' INDUSTRIAL FAN (STANDING TYPE) & 1PC OF CEILING FAN ', ' (GRV-002593)', 'P: 163448', 'P: 163448', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Refrigeration/Air ', 'Ismail Abdulai-Saiku', '2025-08-22', NULL, 2025, '0.00', '13577.00', 0, 0, '0.00', 12, '2025-11-07 15:07:26'),
(334, '1PC-2.0HP AIR CONDITIONER ', 'GRV-002613)', 'P: 163451', 'P: 163451', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Mr. Mantey\'s Offic', 'Ismail Abdulai-Saiku', '2025-08-22', NULL, 2025, '0.00', '7289.62', 0, 0, '0.00', 12, '2025-11-07 15:09:21'),
(335, 'PAYMENT FOR FUSER ASSEMBLY ', 'P: 163633', 'P: 163633', 'P: 163633', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Exams Unit - Coordinator\'s office', 'Ismail Abdulai-Saiku', '2025-09-10', NULL, 2025, '0.00', '27188.58', 0, 0, '0.00', 12, '2025-11-07 15:12:29'),
(336, ' AIR CONDITIONERS ', '(GRV-002682)', 'P: 163743', 'P: 163743', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Chartroom', 'Ismail Abdulai-Saiku', '2025-10-29', NULL, 2025, '0.00', '19798.00', 0, 0, '0.00', 12, '2025-11-07 15:14:22'),
(337, ' TWO PROJECTORS FOR ICT DEPARTMENT & ONE PROJECTOR FOR GRAD. SCHOOL', '(GRV-002558)', 'P: 162344', 'P: 162344', 'RMU///0/25', 'Others', 'Teaching Equipment', 'Test Sub Class', 'Owned', 'GRADUATE SCHOOL', 'Ismail Abdulai-Saiku', '2025-08-13', NULL, 2025, '0.00', '41040.00', 0, 0, '0.00', 12, '2025-11-07 15:17:05'),
(338, ' INSTALLATION OF WEICHAI ENGINE ', 'P: 162318', 'P: 162318', 'P: 162318', 'RMU///0/25', 'Others', 'Machine And Equipment', 'Test Sub Class', 'Owned', 'Development Services Unit - Head of DSU\'s office', 'Ismail Abdulai-Saiku', '2025-08-11', NULL, 2025, '0.00', '6500.00', 0, 0, '0.00', 12, '2025-11-07 15:20:57'),
(339, ' ORIGINAL PRUGA MK45 3D PRINTER ', '(GRV NO. 002705)', 'P: 164389', 'P: 164389', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Laboratory Complex Building - Mechanical Engineeri', 'Ismail Abdulai-Saiku', '2025-11-24', NULL, 2025, '0.00', '25731.87', 0, 0, '0.00', 12, '2025-12-09 13:15:58'),
(340, ' 3-IN-1 WAITING CHAIR, VISITORS CHAIR & OFFICE DESK ', '(GRV-002673)', 'P: 163802', 'P: 163802', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Conference Room', 'Ismail Abdulai-Saiku', '2025-10-03', NULL, 2025, '0.00', '10192.00', 0, 0, '0.00', 12, '2025-12-09 13:49:35'),
(341, ' WAITING CHAIRS ', 'GRV-002721)', 'P: 164072', 'P: 164072', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Nautical Studies Department - HOD\'s office', 'Ismail Abdulai-Saiku', '2025-10-29', NULL, 2025, '0.00', '3499.99', 0, 0, '2.00', 12, '2025-12-09 13:54:08'),
(343, ' TWO LECTURE CHAIRS ', '(GRV-002744)', 'P: 164110', 'P: 164110', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Lecturer\'s office-Up', 'Ismail Abdulai-Saiku', '2025-10-30', NULL, 2025, '0.00', '15500.00', 0, 0, '0.00', 12, '2025-12-09 14:08:30'),
(344, 'CURTAINS AND ITS ACCESSORIES ', 'P: 164386', 'P: 164386', 'P: 164386', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Chartroom', 'Ismail Abdulai-Saiku', '2025-11-24', NULL, 2025, '0.00', '8690.00', 0, 0, '0.00', 12, '2025-12-09 14:49:00'),
(345, 'CONTRUCTION OF ALUMINIUM CUBUCLE FOR THE PORTER', 'P: 164212', 'P: 164212', 'P: 164212', 'RMU///0/25', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-11-10', NULL, 2025, '0.00', '41400.00', 0, 0, '0.00', 12, '2025-12-09 15:09:27'),
(347, '2PCS OF AIR CONDITIONERS ', '(GRV-002718)', '(GRV-002718)', 'P: 163841', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Laboratory Complex Building - Mechanical Engineeri', 'Ismail Abdulai-Saiku', '2025-10-08', NULL, 2025, '0.00', '27359.24', 0, 0, '0.00', 12, '2025-12-09 15:24:21'),
(348, 'PAYMENT FOR SPLIT AIR -CONDITIONER 2.5HP FOR THE UNIVERSITY USE (SIV NO. 002368)', 'SIV NO. 002368)', 'P: 164129', 'P: 164129', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'RMU CAMPUS', 'Ismail Abdulai-Saiku', '2025-10-30', NULL, 2025, '0.00', '10360.28', 0, 0, '0.00', 12, '2025-12-09 15:27:07'),
(349, ' THREE 2.5 HP A/C ', '(GRV-002751)', 'P: 164216', 'P: 164216', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Technip ', 'Ismail Abdulai-Saiku', '2025-11-10', NULL, 2025, '0.00', '26550.03', 0, 0, '0.00', 12, '2025-12-09 15:30:00'),
(350, 'REPLACEMENT OF TV SET ', '(GRV NO. 002771)', 'P: 164391', 'P: 164391', 'RMU///0/25', 'Others', 'Teaching Equipment', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - Classrooms 1', 'Ismail Abdulai-Saiku', '2025-11-24', NULL, 2025, '0.00', '20540.00', 0, 0, '0.00', 12, '2025-12-09 15:41:32'),
(352, 'CERTIFICATE NO. 7 FOR CONSTRUCTION OF AUDITORIUM COMPLEX', 'GJ:87163', 'GJ:87163', 'GJ:87163', 'RMU///0/26', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'Auditorium', 'Ismail Abdulai-Saiku', '2025-12-30', NULL, 2026, '0.00', '1000000.00', 0, 0, '0.00', 12, '2026-03-11 12:24:07'),
(353, '2 PCS OF PRINTERS', ' 002777', 'P:164616', 'P:164616', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Finance Directorate - General office', 'Ismail Abdulai-Saiku', '2025-12-16', NULL, 2026, '0.00', '14800.01', 0, 0, '0.00', 12, '2026-03-11 12:32:11'),
(354, 'DESKTOP COMPUTER AND ACCESSORIES ', '002773', 'P:164656', 'P:164656', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'GRADUATE SCHOOL', 'Ismail Abdulai-Saiku', '2025-12-17', NULL, 2026, '0.00', '57766.60', 0, 0, '0.00', 12, '2026-03-11 12:39:05'),
(355, 'MIRRORLESS DIGITAL CAMERA', 'P:164703', 'P:164703', 'P:164703', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', '', 'Ismail Abdulai-Saiku', '2025-12-19', NULL, 2026, '0.00', '78520.00', 0, 0, '0.00', 12, '2026-03-11 12:49:49'),
(356, '2 PCS OF PROJECTORS', '002804', 'P:164617', 'P:164617', 'RMU///0/26', 'Others', 'Teaching Equipment', 'Test Sub Class', 'Owned', 'GRADUATE SCHOOL', 'Ismail Abdulai-Saiku', '2025-12-16', NULL, 2026, '0.00', '41040.00', 0, 0, '0.00', 12, '2026-03-11 12:57:31'),
(357, '1 NO. AIR CONDITIONER ', '002823', 'P:164657', 'P:164657', 'RMU///0/26', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'SERVER ROOM', 'Ismail Abdulai-Saiku', '2025-12-17', NULL, 2026, '0.00', '9000.00', 0, 0, '0.00', 12, '2026-03-11 13:07:53'),
(358, 'TABLE TOP FRIDGE', 'AP:53924', 'AP:53924', 'AP:53924', 'RMU///0/26', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Administration - Deputy Registrar-Admin\'s office', 'Ismail Abdulai-Saiku', '2025-12-31', NULL, 2026, '0.00', '2250.00', 0, 0, '0.00', 12, '2026-03-11 13:15:20'),
(359, 'TWO CHAIRS AND OFFICE DESKS FOR FIRE INSTRUCTOR\'S OFFICE', 'AP:54117', 'AP:54117', 'AP:54117', 'RMU///0/26', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Maritime Safety and Security Center - Fire Instruc', 'Ismail Abdulai-Saiku', '2025-12-31', NULL, 2026, '0.00', '13000.00', 0, 0, '0.00', 12, '2026-03-11 13:19:06'),
(360, '60 PIECES OF CONFERENCE ROOM CHAIRS ', 'AP:54116', 'AP:54116', 'AP:54116', 'RMU///0/26', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Transport Department - Master\'s Classroom', 'Ismail Abdulai-Saiku', '2025-12-31', NULL, 2026, '0.00', '180000.00', 0, 0, '0.00', 12, '2026-03-11 13:24:13'),
(361, '1 PIECE SWIVEL CHAIR ', 'AP:54115', 'AP:54115', 'AP:54115', 'RMU///0/26', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Transport Department - Mr. Gohoho\'s office', 'Ismail Abdulai-Saiku', '2025-12-31', NULL, 2026, '0.00', '2998.84', 0, 0, '0.00', 12, '2026-03-11 13:35:13'),
(362, ' 1.NO. ELECTRIC DRUM PUMP AND 1.NO. DIESEL DRUM PUMP FOR THE TRANSFER OF FUEL FROM DRUMS TO THE GENERATOR SET ', '002800', 'P:164452', 'P:164452', 'RMU///0/26', 'Others', 'Machine And Equipment', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Tools Store Room', 'Ismail Abdulai-Saiku', '2025-12-02', NULL, 2026, '0.00', '8320.00', 0, 0, '0.00', 12, '2026-03-11 13:42:02'),
(363, 'TABLETOP FRIDGE', '002554', 'RMU/31/15(1298)', 'GJ:87018', 'RMU///0/26', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Liberia Hostel - Executive Room 007', 'Ismail Abdulai-Saiku', '2025-07-18', NULL, 2026, '0.00', '4500.00', 0, 0, '0.00', 12, '2026-03-24 09:22:50'),
(364, 'PROVISION AND FIXING OF CURTAINS', 'P:160650', 'P:160650', 'P:160650', 'RMU///0/26', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Transport Department - Chinese Lecturer\'s office', 'Ismail Abdulai-Saiku', '2025-03-11', NULL, 2026, '0.00', '2200.00', 0, 0, '0.00', 12, '2026-03-24 09:29:54'),
(365, 'UPGRADING OF WASHROOMS', 'GJ:86973', 'GJ:86973', 'GJ:86973', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'MARITIME SAFETY AND SECURITY CENTRE ', 'Ismail Abdulai-Saiku', '2025-01-16', NULL, 2026, '0.00', '180988.00', 0, 0, '0.00', 12, '2026-03-24 10:07:07'),
(366, 'CONSTRUCTION, ROOFING AND CEILING WORKS OF CONCRETE PLATFORM FOR 40 FOOTER CONTAINER ', 'GJ:86972', 'GJ:86972', 'GJ:86972', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Stores Unit - Stores officer', 'Ismail Abdulai-Saiku', '2025-01-30', NULL, 2026, '0.00', '119933.20', 0, 0, '0.00', 12, '2026-03-24 10:25:55'),
(369, 'REPLACEMENT OF WOODEN WINDOW WITH ALUMINIUM GLAZING WINDOW', 'GJ:86289', 'GJ:86289', 'GJ:86289', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'MARITIME SAFETY AND SECURITY CENTRE ', 'Ismail Abdulai-Saiku', '2025-07-11', NULL, 2026, '0.00', '194509.74', 0, 0, '0.00', 12, '2026-03-24 10:51:27'),
(370, 'DEDICATION PLAQUE FOR LIBERIA HOSTEL', 'P:162156', 'P:162156', 'P:162156', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Liberia Hostel - LiMA Room 001', 'Ismail Abdulai-Saiku', '2025-07-31', NULL, 2026, '0.00', '77981.25', 0, 0, '0.00', 12, '2026-03-24 10:55:12'),
(371, 'RENOVATION OF BUNGALOW R4', 'AP:54102', 'AP:54102', 'AP:54102', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Bungalows - R4', 'Ismail Abdulai-Saiku', '2025-11-10', NULL, 2026, '0.00', '252892.50', 0, 0, '0.00', 12, '2026-03-24 10:58:39'),
(372, '2 (TWO) HARD DRIVE DOCKING STATION', 'P:160661', 'P:160661', 'P:160661', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Administration - IT Manger\'s office', 'Ismail Abdulai-Saiku', '2025-03-11', NULL, 2026, '0.00', '4228.32', 0, 0, '0.00', 12, '2026-03-24 11:07:50'),
(374, 'DESKTOP', '002383', 'P:160912 (2)', 'P:160912 (2)', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Stores Unit - Stores officer', 'Ismail Abdulai-Saiku', '2025-04-02', NULL, 2026, '0.00', '15420.35', 0, 0, '0.00', 12, '2026-03-24 11:34:27');
INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `in_service_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(375, 'DESKTOP', '002475', 'P:161493', 'P:161493', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT - Computer Laboratory 1', 'Ismail Abdulai-Saiku', '2025-05-23', NULL, 2026, '0.00', '13407.17', 0, 0, '0.00', 12, '2026-03-24 11:53:49'),
(376, 'LASERJET PRINTER', '002386', 'P:160912', '027757', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'GUIDANCE & COUNSELING', 'Ismail Abdulai-Saiku', '2025-04-02', NULL, 2026, '0.00', '9386.30', 0, 0, '0.00', 12, '2026-03-24 12:01:09'),
(377, 'LAPTOP', '002460', 'P:161535', 'P:161535', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'GMDSS LAB', 'Ismail Abdulai-Saiku', '2025-05-28', NULL, 2026, '0.00', '20988.00', 0, 0, '0.00', 12, '2026-03-24 12:12:45'),
(378, 'LAPTOP', '002460 (2)', 'P:161535 (2)', 'P:161535 (2)', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Sick Bay - Consulting room 1', 'Ismail Abdulai-Saiku', '2025-05-28', NULL, 2026, '0.00', '20988.00', 0, 0, '0.00', 12, '2026-03-24 12:16:10'),
(379, '\" FOLLOW THE KING, A TRUE STORY \" BY RAYMOND LAWRENCE', 'P:161887', 'P:161887', 'P:161887', 'RMU///0/26', 'Others', 'Library Books', 'Test Sub Class', 'Owned', 'Library - Main Library', 'Ismail Abdulai-Saiku', '2025-06-25', NULL, 2026, '0.00', '5000.00', 0, 0, '0.00', 12, '2026-03-24 12:46:52'),
(380, 'SHIP MACHINERY AND EQUIPMENT', 'GJ:87210', 'GJ:87210', 'GJ:87210', 'RMU///0/26', 'Others', 'Machine And Equipment', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Main Workshop', 'Ismail Abdulai-Saiku', '2025-04-16', NULL, 2026, '0.00', '734400.00', 0, 0, '0.00', 12, '2026-03-25 11:46:13'),
(381, 'TABLE TOP FRIDGE', '002436', '002436', '161376', 'RMU///0/26', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Transport Department - Chinese Lecturer\'s office', 'Ismail Abdulai-Saiku', '2025-05-15', NULL, 2026, '0.00', '2250.00', 0, 0, '0.00', 12, '2026-03-25 13:23:55'),
(382, 'TABLE TOP FRIDGE', '002439', '002439', '161376 (2)', 'RMU///0/26', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Development Services Unit - General office', 'Ismail Abdulai-Saiku', '2025-05-15', NULL, 2026, '0.00', '2250.00', 0, 0, '0.00', 12, '2026-03-25 13:25:38'),
(383, 'TABLETOP FRIDGE', '002439(2)', '002439 (2)', '161376(3)', 'RMU///0/26', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'ICT - ICT Lecturer\'s office', 'Ismail Abdulai-Saiku', '2025-05-15', NULL, 2026, '0.00', '2250.00', 0, 0, '0.00', 12, '2026-03-25 13:27:49'),
(384, 'MATERIALS FOR STORAGE TANK FOR SCANJET', 'P:164211', 'P:164211', 'P:164211', 'RMU///0/26', 'Others', 'Teaching Equipment', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Classroom-Up 203', 'Ismail Abdulai-Saiku', '2025-11-10', NULL, 2026, '0.00', '122940.00', 0, 0, '0.00', 12, '2026-03-25 15:37:10'),
(385, 'AMBULANCE', 'GJ:87126', 'GJ:87126', 'GJ:87126', 'RMU///0/26', 'Others', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'RMU VEHICLE FLEET', 'Ismail Abdulai-Saiku', '2025-03-31', NULL, 2026, '0.00', '70000.00', 0, 0, '0.00', 12, '2026-03-27 08:40:59'),
(386, 'MATERIALS FOR INSTALLATION OF WEICHAI ENGINE ', '002789', '002789', '164495', 'RMU///0/26', 'Others', 'Machine And Equipment', 'Test Sub Class', 'Owned', 'Marine Engineering Department - Main Workshop', 'Ismail Abdulai-Saiku', '2025-12-09', NULL, 2026, '0.00', '52760.00', 0, 0, '0.00', 12, '2026-04-01 09:59:35');

--
-- Triggers `assets`
--
DROP TRIGGER IF EXISTS `trg_assets_insert`;
DELIMITER $$
CREATE TRIGGER `trg_assets_insert` AFTER INSERT ON `assets` FOR EACH ROW BEGIN
  DECLARE assetYear INT;
  DECLARE existing_id INT;

  SET assetYear = YEAR(NEW.acquisition_date);

  SELECT id INTO existing_id
  FROM asset_additions_year WHERE asset_class 
  COLLATE utf8mb4_general_ci = NEW.asset_class
  AND year = assetYear
  LIMIT 1;

  IF existing_id IS NOT NULL THEN
    UPDATE asset_additions_year
    SET 
      total_additions_cedi = total_additions_cedi + NEW.additions,
      total_additions_dollar = total_additions_dollar + NEW.additions_dollar
    WHERE id = existing_id;
  ELSE
    INSERT INTO asset_additions_year (
      asset_class, year,
      total_additions_cedi, total_additions_dollar,
      total_disposals_cedi, total_disposals_dollar
    )
    VALUES (
      NEW.asset_class, assetYear,
      NEW.additions, NEW.additions_dollar,
      0.00, 0.00
    );
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_disposals_summary`;
DELIMITER $$
CREATE TRIGGER `update_disposals_summary` BEFORE UPDATE ON `assets` FOR EACH ROW BEGIN
  DECLARE existing_id INT;

  -- Only continue if the 'disposals' value is being changed
  IF NEW.disposals <> OLD.disposals THEN
    -- Try to find an existing summary row
    SELECT id INTO existing_id
    FROM asset_additions_year
    WHERE asset_class COLLATE utf8mb4_general_ci = NEW.asset_class 
      AND year = YEAR(NEW.acquisition_date)
    LIMIT 1;

    -- Update existing record
    IF existing_id IS NOT NULL THEN
      UPDATE asset_additions_year
      SET total_disposals_cedi = NEW.disposals
      WHERE id = existing_id;

    -- Or insert a new one if it doesn't exist
    ELSE
      INSERT INTO asset_additions_year (
        asset_class, year, total_additions_cedi, total_additions_dollar, total_disposals_cedi
      ) VALUES (
        NEW.asset_class, YEAR(NEW.acquisition_date), 0.00, 0.00, NEW.disposals
      );
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `assets_archive`
--

DROP TABLE IF EXISTS `assets_archive`;
CREATE TABLE IF NOT EXISTS `assets_archive` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(150) NOT NULL,
  `grv_number` varchar(20) DEFAULT 'N/A',
  `serial_number` varchar(50) DEFAULT 'N/A',
  `pv_number` varchar(50) DEFAULT 'N/A',
  `id_number` varchar(100) DEFAULT 'N/A',
  `supplier_name` varchar(250) NOT NULL,
  `asset_class` varchar(50) NOT NULL,
  `sub_class` varchar(254) NOT NULL,
  `asset_type` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL DEFAULT 'on campus',
  `user` varchar(150) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `assets_archive`
--

INSERT INTO `assets_archive` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(102, 'PURCHASE OF TWENTY PCS OF LIFE JACKETS ', 'N/A', 'N/A', 'P: 112171', 'N/A', 'SUPPLIER NAME', 'Teaching Equipment', 'N/A', 'OWNED', 'MSSC DEPT', 'USERNAME', '2025-05-09', 2025, '0.00', '58240.00', 0, 0, '0.00', 8, '2025-03-18 10:15:47'),
(103, 'REPAIR OF TWO ELECTRODE OVENS', 'N/A', 'N/A', 'P: 135447', 'N/A', 'SUPPLIER NAME', 'Teaching Equipment', 'TEST', 'OWNED', 'MODEC WELDING CENTER', 'USERNAME', '2025-07-15', 2025, '0.00', '34863.40', 0, 0, '0.00', 10, '2025-03-18 10:18:17'),
(169, 'REPAIR OF TWO ELECTRODE OVENS AT THE MODEC WELDING CENTER', 'N/A', 'N/A', 'N/A', 'N/A', 'Supplier', 'Teaching Equipment', 'TEST', 'OWNED', 'on campus', 'User', '2024-07-15', 2025, '0.00', '34863.40', 0, 0, '0.00', 10, '2025-04-07 08:43:17'),
(195, 'Toyota Yaris', 'J54574k', 'J7rtg', 'K532', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-02-03', 2025, '0.00', '30000.00', 0, 0, '0.00', 10.5, '2025-07-28 19:36:04'),
(196, 'Peaugot LE', 'J54574L', 'J7rtH', 'K530', 'RMU///0/25', 'Azar Group', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'Lab Complex', 'Sowah Ako-Nai', '2025-03-10', 2025, '0.00', '300000.00', 0, 0, '0.00', 12, '2025-07-28 20:08:59'),
(198, 'PURCHASE OF 30 DESKTOP FOR ICT DEPARTMENT', 'P: 161493', 'RMU/ICT/CC/C&A/02/25', 'P: 161493', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', 2025, '0.00', '13.00', 0, 0, '0.00', 12, '2025-08-07 09:43:50'),
(199, 'PURCHASE OF DESKTOP FOR ICT DEPARTMENT', 'P: 161493-2', '4CE426BT89', 'P: 161493-2', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', 2025, '0.00', '0.00', 0, 0, '13.00', 12, '2025-08-18 10:13:40'),
(200, 'PURCHASE OF DESKTOP FOR ICT DEPARTMENT', 'P: 161493-3', '4CE426BX18', 'P: 161493-3', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', 2025, '0.00', '13.00', 0, 0, '0.00', 12, '2025-08-18 10:29:12'),
(201, 'PURCHASE OF DESKTOP FOR ICT DEPARTMENT', 'P: 161493-4', '4CE426BX00', 'P: 161493-4', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', 2025, '0.00', '13.00', 0, 0, '0.00', 12, '2025-08-18 10:33:14'),
(231, 'PRINTER', 'P: 160912', 'PHM5P26192', 'P: 160912', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-05-23', 2025, '0.00', '4.00', 0, 0, '0.00', 12, '2025-08-18 13:12:55'),
(233, 'DECKTOP', 'P: 160912-2', '3CM4031JSJ', 'P: 160912-2', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Stores Unit - Stores officer', 'Ismail Abdulai-Saiku', '2025-05-23', 2025, '0.00', '15.00', 0, 0, '0.00', 12, '2025-08-20 08:24:58'),
(250, 'AIR CONDITIONER', 'P: 160513', 'P: 160513', 'P: 160513', 'RMU///0/25', 'Others', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'Administration - IT Technician\'s office 1', 'Ismail Abdulai-Saiku', '2025-02-17', 2025, '0.00', '7545.61', 0, 0, '0.00', 11, '2025-08-25 12:20:07'),
(254, 'TABLE TOP FRIDGE', 'P: 161376', 'P: 161376', 'P: 161376', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'Development Services Unit - Head of DSU\'s office', 'Ismail Abdulai-Saiku', '2025-05-15', 2025, '0.00', '4175.00', 0, 0, '0.00', 12, '2025-08-25 12:48:59'),
(257, 'FLOOR TILING', 'P: 161596', 'P: 161596', 'P: 161596', 'RMU///0/25', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Classroo', 'Ismail Abdulai-Saiku', '2025-06-05', 2025, '0.00', '14300.00', 0, 0, '0.00', 12, '2025-08-25 13:09:14'),
(264, ' MATERIALS FOR MANUFACTURING OF 50 BUNKBEDS ', 'P: 160911', 'P: 160911', 'P: 160911', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Gambia Hostel - Gambia GF 1', 'Ismail Abdulai-Saiku', '2025-04-02', 2025, '0.00', '20580.00', 0, 0, '0.00', 12, '2025-08-25 13:48:30'),
(280, ' DESKTOP BRIDGE SIMULATOR', 'P: 160646', 'P: 160646', 'P: 160646', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'BRIDGE SIMULATOR', 'Ismail Abdulai-Saiku', '2025-04-10', 2025, '0.00', '30363.35', 0, 0, '0.00', 12, '2025-08-28 10:36:23'),
(291, 'CURTAINS', 'P: 160211', 'P: 160211', 'P: 160211', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Provost - Provost\'s office', 'Ismail Abdulai-Saiku', '2025-06-24', 2025, '0.00', '4280.00', 0, 0, '0.00', 11, '2025-10-02 15:24:10'),
(292, 'CURTAINS', 'P: 160260', 'P: 160260', 'P: 160260', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'VSTC', 'Ismail Abdulai-Saiku', '2025-06-30', 2025, '0.00', '3065.00', 0, 0, '0.00', 11, '2025-10-02 15:35:01'),
(293, 'CURTAINS', 'P: 160344', 'P: 160344', 'P: 160344', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'BRIDGE SIMULATOR', 'Ismail Abdulai-Saiku', '2025-02-05', 2025, '0.00', '4360.00', 0, 0, '0.00', 11, '2025-10-02 15:38:35'),
(299, 'SOLLATEK UPS 850VTS ', '(GRV-002562)', 'P: 162155', 'P: 162155', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'RMU CAMPUS', 'Ismail Abdulai-Saiku', '2025-07-30', 2025, '0.00', '1457.00', 0, 0, '0.00', 12, '2025-10-20 08:18:35'),
(302, ' TELEPHONE', 'GRV-002551)', 'P: 162209', 'P: 162209', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'GUIDANCE & COUNSELING', 'Ismail Abdulai-Saiku', '2025-08-04', 2025, '0.00', '796.01', 0, 0, '0.00', 12, '2025-10-20 08:46:52'),
(307, ' SCANNER', '(GRV- 002602)', 'P: 163440', 'P: 163440', 'RMU///0/25', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'Registry - Registration/Certification office', 'Ismail Abdulai-Saiku', '2025-08-22', 2025, '0.00', '1650.00', 0, 0, '0.00', 12, '2025-10-20 09:05:18'),
(313, ' TABLE TOP FRIDGE FOR THE TWO EXECUTIVE ROOMS ', ' (GRV-002554)', 'P: 162098', 'P: 162098', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'LIMA HOSTEL', 'Ismail Abdulai-Saiku', '2025-07-18', 2025, '0.00', '4500.00', 0, 0, '0.00', 12, '2025-11-04 12:21:46'),
(318, ' ADDITIONAL 100PCS OF 40MMX40MM PLASTIC PADS FOR STUDENTS BUNK BEDS', '(GRV-002573)', 'P: 162195', 'P: 162195', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'VSTC', 'Ismail Abdulai-Saiku', '2025-08-01', 2025, '0.00', '5000.00', 0, 0, '0.00', 12, '2025-11-04 12:46:48'),
(331, ' TELEPHONE HANDSET F', '(GRV-002525)', 'P: 162235', 'P: 162235', 'RMU///0/25', 'Others', 'Office Equipment', 'Test Sub Class', 'Owned', 'GRADUATE SCHOOL', 'Ismail Abdulai-Saiku', '2025-08-06', 2025, '0.00', '796.01', 0, 0, '0.00', 12, '2025-11-07 15:03:19'),
(342, 'SUPPLY AND INSTALLATION OF RUBBER CARPET AND WOOLEN CARPET', 'P: 164107', 'P: 164107', 'P: 164107', 'RMU///0/25', 'Others', 'Furnitures & Fixtures', 'Test Sub Class', 'Owned', 'Archives - OIC Archives office', 'Ismail Abdulai-Saiku', '2025-10-30', 2025, '0.00', '6440.00', 0, 0, '0.00', 12, '2025-12-09 13:59:02'),
(346, 'PURCHASE OF MATERIALS FOR STORAGE TANK FOR SCANJET FOR NSD', 'P: 164211', 'P: 164211', 'P: 164211', 'RMU///0/25', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Nautical Studies Department - Classroom-Up 203', 'Ismail Abdulai-Saiku', '2025-11-10', 2025, '0.00', '122940.00', 0, 0, '0.00', 12, '2025-12-09 15:12:10'),
(351, 'CONSTRUCTION OF INSPECTION CHAMBER ', 'P: 164103', 'P: 164103', 'P: 164103', 'RMU///0/25', 'Others', 'Building Works in Progress', 'Test Sub Class', 'Owned', 'Marine Electrical and Electronics Block - Global M', 'Ismail Abdulai-Saiku', '2025-10-30', 2025, '0.00', '5220.00', 0, 0, '0.00', 12, '2025-12-11 12:03:11'),
(367, 'CONSTRUCTION OF LIBERIAN HOSTEL', 'GJ:86434', 'GJ:86434', 'GJ:86434', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Liberia Hostel - LiMA Room 001', 'Ismail Abdulai-Saiku', '2025-07-01', 2026, '0.00', '7199760.00', 0, 0, '0.00', 12, '2026-03-24 10:32:33'),
(368, 'CONSTRUCTION OF WASHROOM ADJACENT TO AUDITORIUM', 'GJ:86634', 'GJ:86634', 'GJ:86634', 'RMU///0/26', 'Others', 'Buildings', 'Test Sub Class', 'Owned', 'Auditorium', 'Ismail Abdulai-Saiku', '2025-07-01', 2026, '0.00', '239000.00', 0, 0, '0.00', 12, '2026-03-24 10:38:42'),
(373, 'LASERJET PRINTER', '002386', 'P:160912', 'P:160912', 'RMU///0/26', 'Others', 'Computer & Accessories', 'Test Sub Class', 'Owned', 'GUIDANCE & COUNSELING', 'Ismail Abdulai-Saiku', '2025-01-23', 2026, '0.00', '9386.30', 0, 0, '0.00', 12, '2026-03-24 11:14:55'),
(374, 'Asset 1', 'Rrtt44', 'Yyh44', '445y2', 'undefined///undefined/undefined', 'Others', 'Academic Gown', 'Test Sub Class', 'Leased', 'Laboratory Complex Building - HOD ME', 'Henry Snow', '2025-06-18', 2026, '0.00', '4000.00', 0, 0, '0.00', 12, '2026-06-06 03:00:11');

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
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_additions_year`
--

INSERT INTO `asset_additions_year` (`id`, `asset_class`, `year`, `total_additions_cedi`, `total_additions_dollar`, `total_disposals_cedi`, `total_disposals_dollar`) VALUES
(53, 'GMDSS Simulator', 2025, '0.00', '0.00', '0.00', '0.00'),
(42, 'Building Works in Progress', 2025, '7638220.00', '674434.25', '0.00', '0.00'),
(43, 'Buildings', 2025, '8647418.38', '720618.20', '0.00', '0.00'),
(4, 'Buildings', 2024, '3474406.00', '358161.78', '0.00', '0.00'),
(52, 'School Band', 2025, '0.00', '0.00', '0.00', '0.00'),
(47, 'Bridge Simulator', 2025, '0.00', '0.00', '0.00', '0.00'),
(48, 'Academic Gown', 2025, '4000.00', '333.33', '0.00', '0.00'),
(44, 'Machine And Equipment', 2025, '801980.00', '66831.67', '0.00', '0.00'),
(45, 'Office Equipment', 2025, '412132.09', '35701.97', '0.00', '0.00'),
(11, 'GMDSS Simulator', 2024, '2247600.00', '224760.00', '0.00', '0.00'),
(12, 'Teaching Equipment', 2025, '364943.40', '33770.28', '0.00', '0.00'),
(49, 'Swimming Pool', 2025, '0.00', '0.00', '0.00', '0.00'),
(50, 'Stanchion Base', 2025, '0.00', '0.00', '0.00', '0.00'),
(51, 'Solar Equipment', 2025, '0.00', '0.00', '0.00', '0.00'),
(38, 'Furnitures & Fixtures', 2025, '1186460.26', '99430.56', '0.00', '0.00'),
(39, 'Forklift', 2025, '0.00', '0.00', '0.00', '0.00'),
(40, 'Refurbished Road', 2025, '0.00', '0.00', '0.00', '0.00'),
(41, 'Motor Vehicles', 2025, '407545.61', '34376.43', '0.00', '0.00'),
(22, 'Talif V-Sat Project', 2025, '7726138.00', '858459.79', '0.00', '0.00'),
(23, 'Motor Vehicles', 2024, '1854601.97', '196244.20', '0.00', '0.00'),
(46, 'Library Books', 2025, '5000.00', '416.67', '0.00', '0.00'),
(25, 'Computer & Accessories', 2024, '1504146.45', '156118.80', '0.00', '0.00'),
(26, 'Machine And Equipment', 2024, '385270.00', '40654.31', '0.00', '0.00'),
(27, 'Forklift', 2024, '438840.00', '48760.00', '0.00', '0.00'),
(37, 'Computer & Accessories', 2025, '1415551.28', '118000.16', '0.00', '0.00'),
(30, 'Bridge Simulator', 2024, '727283.40', '72728.34', '0.00', '0.00'),
(32, 'Office Equipment', 2024, '399661.02', '42175.93', '0.00', '0.00'),
(33, 'Furnitures & Fixtures', 2024, '729980.27', '75762.05', '0.00', '0.00'),
(34, 'Building Works in Progress', 2024, '1944000.00', '194400.00', '0.00', '0.00'),
(54, 'Refurbished Road', 2024, '0.00', '0.00', '0.00', '0.00'),
(36, 'Teaching Equipment', 2024, '81526.08', '8230.39', '0.00', '0.00'),
(55, 'Library Books', 2024, '0.00', '0.00', '0.00', '0.00'),
(56, 'Academic Gown', 2024, '0.00', '0.00', '0.00', '0.00'),
(57, 'Swimming Pool', 2024, '0.00', '0.00', '0.00', '0.00'),
(58, 'Stanchion Base', 2024, '0.00', '0.00', '0.00', '0.00'),
(59, 'Solar Equipment', 2024, '0.00', '0.00', '0.00', '0.00'),
(60, 'School Band', 2024, '0.00', '0.00', '0.00', '0.00'),
(61, 'Talif V-Sat Project', 2024, '0.00', '0.00', '0.00', '0.00'),
(62, 'Land', 2024, '0.00', '0.00', '0.00', '0.00'),
(63, 'Land', 2025, '7464070.59', '622005.88', '0.00', '0.00');

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
  `estimated_life_months` int GENERATED ALWAYS AS ((`estimated_life` * 12)) VIRTUAL,
  `depreciation` int NOT NULL DEFAULT '0',
  `depreciated` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ast_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_classes`
--

INSERT INTO `asset_classes` (`ast_id`, `asset_class`, `account_depr_open_bal`, `opening_bal`, `opbal_plus_additions`, `dep_rate`, `estimated_life`, `depreciation`, `depreciated`) VALUES
(1, 'Computer & Accessories', '1.00', '0.00', '1485551.28', 0.1, 10, 0, 1),
(2, 'Buildings', '1.00', '0.00', '9601486.38', 0.02, 50, 0, 1),
(3, 'Motor Vehicles', '0.00', '149932.00', '4682405.58', 0.2, 5, 0, 1),
(4, 'Furnitures & Fixtures', '0.00', '0.00', '1186460.26', 0.1, 10, 0, 1),
(5, 'Office Equipment', '0.00', '0.00', '412132.09', 0.2, 5, 0, 1),
(8, 'Machine And Equipment', '0.00', '0.00', '801980.00', 0.2, 5, 0, 1),
(9, 'Building Works in Progress', '0.00', '0.00', '7638220.02', 0, 50, 1, 0),
(10, 'Library Books', '0.00', '0.00', '5000.00', 0.2, 5, 0, 1),
(11, 'Bridge Simulator', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(12, 'Academic Gown', '0.00', '0.00', '4000.00', 0.2, 5, 0, 1),
(13, 'Teaching Equipment', '0.00', '0.00', '278840.00', 0.2, 5, 0, 1),
(15, 'Swimming Pool', '0.00', '0.00', '0.00', 0.1, 10, 0, 1),
(16, 'Stanchion Base', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(17, 'School Band', '0.00', '0.00', '0.00', 0.3, 3, 0, 1),
(18, 'Solar Equipment', '0.00', '0.00', '0.00', 0.25, 4, 0, 1),
(19, 'Refurbished Road', '0.00', '0.00', '0.00', 0.065, 15, 0, 1),
(20, 'Forklift', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(21, 'GMDSS Simulator', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(22, 'Talif V-Sat Project', '0.00', '0.00', '0.00', 0.2, 5, 0, 1),
(23, 'Land', '0.00', '0.00', '7464070.59', 0, 50, 1, 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_class_opbal_year`
--

DROP TABLE IF EXISTS `asset_class_opbal_year`;
CREATE TABLE IF NOT EXISTS `asset_class_opbal_year` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(255) DEFAULT NULL,
  `opening_balance` decimal(12,2) DEFAULT NULL,
  `open_bal_usd` decimal(20,2) NOT NULL,
  `total_accum_depr_start` decimal(12,2) DEFAULT '0.00',
  `total_accum_start_usd` decimal(20,0) NOT NULL,
  `total_depr_year_charge` decimal(12,2) DEFAULT '0.00',
  `total_depr_year_charge_usd` decimal(20,2) NOT NULL,
  `disposals_depr` decimal(12,2) DEFAULT '0.00',
  `disposals_depr_usd` decimal(20,2) NOT NULL,
  `total_accum_depr_end` decimal(12,2) GENERATED ALWAYS AS (((`total_depr_year_charge` + `total_accum_depr_start`) - `disposals_depr`)) VIRTUAL,
  `total_accum_end_usd` decimal(20,2) GENERATED ALWAYS AS (((`total_depr_year_charge_usd` + `total_accum_start_usd`) - `disposals_depr_usd`)) VIRTUAL,
  `net_book_value` decimal(12,2) GENERATED ALWAYS AS ((`opening_balance` - ((`total_depr_year_charge` + `total_accum_depr_start`) - `disposals_depr`))) VIRTUAL,
  `net_book_value_usd` decimal(20,2) GENERATED ALWAYS AS ((`open_bal_usd` - ((`total_depr_year_charge_usd` + `total_accum_start_usd`) - `disposals_depr_usd`))) VIRTUAL,
  `year` year DEFAULT NULL,
  `expected_life_months` int DEFAULT '0',
  `rate` decimal(6,2) DEFAULT '0.00',
  `depreciated` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_class_opbal_year`
--

INSERT INTO `asset_class_opbal_year` (`id`, `asset_class`, `opening_balance`, `open_bal_usd`, `total_accum_depr_start`, `total_accum_start_usd`, `total_depr_year_charge`, `total_depr_year_charge_usd`, `disposals_depr`, `disposals_depr_usd`, `year`, `expected_life_months`, `rate`, `depreciated`) VALUES
(19, 'Computer & Accessories', '2880372.94', '320042.00', '1252157.01', '125216', '288037.29', '32004.20', '0.00', '0.00', 2024, 120, '8.50', 1),
(20, 'Furnitures & Fixtures', '2412266.90', '268030.00', '465590.00', '46560', '241226.69', '26803.00', '0.00', '0.00', 2024, 120, '8.50', 1),
(23, 'Forklift', '0.00', '0.00', '0.00', '0', '0.00', '0.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(25, 'Refurbished Road', '8695741.18', '869574.00', '580010.00', '58001', '579716.08', '57971.60', '0.00', '0.00', 2024, 180, '8.50', 1),
(26, 'Teaching Equipment', '3425218.38', '342522.00', '1123570.00', '112357', '685043.68', '68504.40', '0.00', '0.00', 2024, 60, '8.50', 1),
(27, 'Motor Vehicles', '4216818.85', '468535.00', '1023290.47', '102322', '843363.77', '93707.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(30, 'Building Works in Progress', '42591366.68', '4259136.00', '0.00', '0', '0.00', '0.00', '0.00', '0.00', 2024, 600, '8.50', 0),
(33, 'Buildings', '1529584262.44', '6498874.23', '2560510.00', '256051', '30591685.25', '129977.48', '0.00', '0.00', 2024, 600, '8.50', 1),
(35, 'Machine And Equipment', '5222308.16', '580257.00', '1427030.60', '142703', '1044461.63', '116051.40', '0.00', '0.00', 2024, 60, '8.50', 1),
(36, 'Office Equipment', '970850.19', '107873.00', '324515.60', '32452', '194170.04', '21574.60', '0.00', '0.00', 2024, 60, '8.50', 1),
(38, 'Library Books', '19074258.32', '1907426.00', '7629702.90', '762970', '3814851.66', '381485.20', '0.00', '0.00', 2024, 60, '8.50', 1),
(39, 'Bridge Simulator', '10848083.94', '1084808.00', '10848080.00', '1084808', '0.00', '0.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(40, 'Academic Gown', '98381.25', '9838.00', '39360.00', '3936', '19676.25', '1967.60', '0.00', '0.00', 2024, 60, '8.50', 1),
(41, 'Swimming Pool', '591860.00', '59186.00', '71030.00', '7103', '59186.00', '5918.60', '0.00', '0.00', 2024, 120, '8.50', 1),
(42, 'Stanchion Base', '637170.00', '63717.00', '172960.00', '17294', '127434.00', '12743.40', '0.00', '0.00', 2024, 60, '8.50', 1),
(43, 'Solar Equipment', '11652309.50', '1165231.00', '2625952.20', '262596', '2913077.38', '46609.24', '0.00', '0.00', 2024, 300, '8.50', 1),
(44, 'School Band', '87896.08', '8789.00', '87893.30', '8789', '0.00', '0.00', '0.00', '0.00', 2024, 36, '8.50', 1),
(46, 'GMDSS Simulator', '0.00', '0.00', '0.00', '0', '0.00', '0.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(47, 'Talif V-Sat Project', '2160100.00', '216010.00', '2160100.00', '216010', '0.00', '0.00', '0.00', '0.00', 2024, 60, '8.50', 1),
(49, 'Land', '1634549320.00', '163454932.00', '0.00', '0', '0.00', '0.00', '0.00', '0.00', 2024, 600, '8.50', 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_class_sub_classes`
--

INSERT INTO `asset_class_sub_classes` (`T_id`, `sub_class`, `sub_class_code`, `asset_class`) VALUES
(1, 'Test Sub Class', '', 'Buildings');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location`
--

DROP TABLE IF EXISTS `asset_location`;
CREATE TABLE IF NOT EXISTS `asset_location` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(200) NOT NULL,
  `loc_code` varchar(250) NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=636 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_location`
--

INSERT INTO `asset_location` (`loc_id`, `location`, `loc_code`) VALUES
(1, 'ICT LAB', ''),
(2, 'Registry Records', ''),
(3, 'TRANSPORT DEPT', ''),
(4, 'Lab Complex', ''),
(417, 'Finance Directorate - Director\'s office', ''),
(418, 'Finance Directorate - Snr. Accountant\'s office', ''),
(419, 'Finance Directorate - Budget officer\'s office', ''),
(420, 'Finance Directorate - Secretary\'s office', ''),
(421, 'Finance Directorate - Account officer\'s office', ''),
(422, 'Finance Directorate - General office', ''),
(423, 'Finance Directorate - Cashier\'s office', ''),
(424, 'Pro Vice Chancellor - Pro-Vice Chancellor\'s office', ''),
(425, 'Pro Vice Chancellor - Secretary\'s office', ''),
(426, 'Pro Vice Chancellor - General office', ''),
(427, 'Vice Chancellor - Vice Chancellor\'s office', ''),
(428, 'Vice Chancellor - Secretary\'s office', ''),
(429, 'Vice Chancellor - Conference Room', ''),
(430, 'Auditorium', ''),
(431, 'Library - Librarian\'s office', ''),
(432, 'Library - Main Library', ''),
(433, 'Marine Electrical and Electronics Block - Global Maritime Distress and Safety System Laboratory', ''),
(434, 'Marine Electrical and Electronics Block - Technip Laboratory', ''),
(435, 'Marine Electrical and Electronics Block - Language Center', ''),
(436, 'Marine Electrical and Electronics Block - Master\'s Classroom', ''),
(437, 'Marine Electrical and Electronics Block - Classroom 101(Down)', ''),
(438, 'Marine Electrical and Electronics Block - Classroom 102(Down)', ''),
(439, 'Marine Electrical and Electronics Block - Classroom 201(Up)', ''),
(440, 'Marine Electrical and Electronics Block - Classroom 202(Up)', ''),
(441, 'Administration - Reprography\'s office', ''),
(442, 'Administration - Deputy Registrar-Admin\'s office', ''),
(443, 'Administration - IT Manger\'s office', ''),
(444, 'Administration - IT Technician\'s office 1', ''),
(445, 'Administration - Assistant Registrar\'s office', ''),
(446, 'Administration - Human Resource officer\'s office', ''),
(447, 'Administration - Secretary\'s office', ''),
(448, 'Administration - University Registrar\'s office', ''),
(449, 'Administration - Procurement Unit', ''),
(450, 'Maritime Safety and Security Center - HOD\'s office', ''),
(451, 'Maritime Safety and Security Center - Secretary\'s office', ''),
(452, 'Maritime Safety and Security Center - Lecturer\'s office', ''),
(453, 'Maritime Safety and Security Center - Fire Instructor\'s office', ''),
(454, 'Maritime Safety and Security Center - Bosun\'s office', ''),
(455, 'Maritime Safety and Security Center - Swimming Pool', ''),
(456, 'Maritime Safety and Security Center - Old office', ''),
(457, 'Maritime Safety and Security Center - Classrooms 1', ''),
(458, 'Maritime Safety and Security Center - Classrooms 2', ''),
(459, 'Maritime Safety and Security Center - Classrooms 3', ''),
(460, 'Maritime Safety and Security Center - Classrooms 4', ''),
(461, 'Maritime Safety and Security Center - Classrooms 5', ''),
(462, 'Sick Bay - Consulting room 1', ''),
(463, 'Sick Bay - Consulting room 2', ''),
(464, 'Sick Bay - Consulting room 3', ''),
(465, 'Sick Bay - Treatment room', ''),
(466, 'Sick Bay - Drug store', ''),
(467, 'Sick Bay - Laboratory', ''),
(468, 'Sick Bay - Demonstration room', ''),
(469, 'Sick Bay - Ward', ''),
(470, 'Registry - Academic Registrar', ''),
(471, 'Registry - Deputy Registrar Academic', ''),
(472, 'Registry - Secretary\'s office', ''),
(473, 'Registry - Records office', ''),
(474, 'Registry - Registration/Certification office', ''),
(475, 'Registry - Admissions office', ''),
(476, 'Transport Unit - Transport Officer\'s office', ''),
(477, 'Transport Unit - Driver\'s office', ''),
(478, 'Transport Unit - Driver\'s room', ''),
(479, 'Stores Unit - Senior Stores office', ''),
(480, 'Stores Unit - Stores officer', ''),
(481, 'Archives - OIC Archives office', ''),
(482, 'Development Services Unit - Head of DSU\'s office', ''),
(483, 'Development Services Unit - General office', ''),
(484, 'Development Services Unit - IT Technician office 2', ''),
(485, 'Students Accommodation 1 - Co-ordinator\'s office', ''),
(486, 'Students Accommodation 1 - Secretary\'s office', ''),
(487, 'Students Accommodation 1 - Leadership Instructor\'s office', ''),
(488, 'Students Accommodation 1 - Porter\'s Lounge', ''),
(489, 'Students Accommodation 2 - Porter\'s Lounge', ''),
(490, 'Gambia Hostel - Gambia GF 1', ''),
(491, 'Gambia Hostel - Gambia GF 2', ''),
(492, 'Gambia Hostel - Gambia GF 3', ''),
(493, 'Gambia Hostel - Gambia GF 4', ''),
(494, 'Gambia Hostel - Gambia GF 5', ''),
(495, 'Gambia Hostel - Gambia GF 6', ''),
(496, 'Gambia Hostel - Gambia FF 1', ''),
(497, 'Gambia Hostel - Gambia FF 2', ''),
(498, 'Gambia Hostel - Gambia FF 3', ''),
(499, 'Gambia Hostel - Gambia FF 4', ''),
(500, 'Gambia Hostel - Gambia FF 5', ''),
(501, 'Gambia Hostel - Gambia FF 6', ''),
(502, 'Gambia Hostel - Gambia SF 1', ''),
(503, 'Gambia Hostel - Gambia SF 2', ''),
(504, 'Gambia Hostel - Gambia SF 3', ''),
(505, 'Gambia Hostel - Gambia SF 4', ''),
(506, 'Gambia Hostel - Gambia SF 5', ''),
(507, 'Gambia Hostel - Gambia SF 6', ''),
(508, 'Gambia Hostel - Gambia TF 1', ''),
(509, 'Gambia Hostel - Gambia TF 2', ''),
(510, 'Gambia Hostel - Gambia TF 3', ''),
(511, 'Gambia Hostel - Gambia TF 4', ''),
(512, 'Gambia Hostel - Gambia TF 5', ''),
(513, 'Gambia Hostel - Gambia TF 6', ''),
(514, 'Liberia Hostel - LiMA Room 001', ''),
(515, 'Liberia Hostel - LiMA Room 002', ''),
(516, 'Liberia Hostel - LiMA Room 003', ''),
(517, 'Liberia Hostel - LiMA Room 004', ''),
(518, 'Liberia Hostel - LiMA Room 005', ''),
(519, 'Liberia Hostel - LiMA Room 006', ''),
(520, 'Liberia Hostel - LiMA Room 009', ''),
(521, 'Liberia Hostel - LiMA Room 010', ''),
(522, 'Liberia Hostel - LiMA Room 011', ''),
(523, 'Liberia Hostel - LiMA Room 012', ''),
(524, 'Liberia Hostel - LiMA Room 013', ''),
(525, 'Liberia Hostel - LiMA Room 014', ''),
(526, 'Liberia Hostel - Executive Room 007', ''),
(527, 'Liberia Hostel - Executive Room 008', ''),
(528, 'VC Chancellor\'s Lounge - VC Chancellor\'s Lounge', ''),
(529, 'Bungalows - VC\'s Bungalow', ''),
(530, 'Bungalows - R1', ''),
(531, 'Bungalows - R2', ''),
(532, 'Bungalows - R3', ''),
(533, 'Bungalows - R4', ''),
(534, 'Bungalows - R5', ''),
(535, 'ICT - ICT Lecturer\'s office', ''),
(536, 'ICT - HOD\'s office', ''),
(537, 'ICT - Computer Laboratory 1', ''),
(538, 'ICT - Computer Laboratory 2', ''),
(539, 'Administration Block Annex - Conference Room', ''),
(540, 'Administration Block Annex - Quality Management Co-ordinator 1', ''),
(541, 'Administration Block Annex - Quality Management Co-ordinator 2', ''),
(542, 'Administration Block Annex - Head BDC', ''),
(543, 'Administration Block Annex - BDC Assistant', ''),
(544, 'Administration Block Annex - Head Public Relations', ''),
(545, 'Administration Block Annex - Public Relations Assistant', ''),
(546, 'Administration Block Annex - RMU Alumni office', ''),
(547, 'Administration Block Annex - Reception', ''),
(548, 'Marketing Unit - Front Desk', ''),
(549, 'Marketing Unit - Marketing Officers\' office', ''),
(550, 'Marketing Unit - Head of Marketing', ''),
(551, 'Academic Research Unit - Head of Research Office', ''),
(552, 'Academic Research Unit - Research Officer\'s Secretary Office', ''),
(553, 'Academic Research Unit - Research Library', ''),
(554, 'Nautical Studies Department - HOD\'s office', ''),
(555, 'Nautical Studies Department - Secretary\'s office', ''),
(556, 'Nautical Studies Department - Lecturer\'s office-Down', ''),
(557, 'Nautical Studies Department - National Service Personnel Office-Down', ''),
(558, 'Nautical Studies Department - Lecturer\'s office-Up', ''),
(559, 'Nautical Studies Department - Classroom-Down 101', ''),
(560, 'Nautical Studies Department - Classroom-Down 102', ''),
(561, 'Nautical Studies Department - Classroom-Down 103', ''),
(562, 'Nautical Studies Department - Classroom-Down 104', ''),
(563, 'Nautical Studies Department - Classroom-Down 105', ''),
(564, 'Nautical Studies Department - Classroom-Up 202', ''),
(565, 'Nautical Studies Department - Classroom-Up 203', ''),
(566, 'Nautical Studies Department - Classroom-Up 204', ''),
(567, 'Nautical Studies Department - Chartroom', ''),
(568, 'Nautical Studies Department - Conference Room', ''),
(569, 'Marine Engineering Department - Refrigeration/Air Conditioners Office', ''),
(570, 'Marine Engineering Department - Electrical Shop', ''),
(571, 'Marine Engineering Department - Refrigeration/Air conditioners Classroom/Workshop', ''),
(572, 'Marine Engineering Department - HOD\'s office', ''),
(573, 'Marine Engineering Department - Secretary\'s office', ''),
(574, 'Marine Engineering Department - Lecture\'s office', ''),
(575, 'Marine Engineering Department - Staff Common room', ''),
(576, 'Marine Engineering Department - Mr. Mantey\'s Office', ''),
(577, 'Marine Engineering Department - Eng. Larkai\'s office', ''),
(578, 'Marine Engineering Department - Main Workshop', ''),
(579, 'Marine Engineering Department - MODEC workshop', ''),
(580, 'Marine Engineering Department - Tools Store Room', ''),
(581, 'Laboratory Complex Building - Mechanical Engineering office', ''),
(582, 'Laboratory Complex Building - ME-Staff office (2)-Two rooms', ''),
(583, 'Laboratory Complex Building - Power room(1)', ''),
(584, 'Laboratory Complex Building - HOD PMAS', ''),
(585, 'Laboratory Complex Building - Common Room', ''),
(586, 'Laboratory Complex Building - Drawing Room', ''),
(587, 'Laboratory Complex Building - Power room(2)', ''),
(588, 'Laboratory Complex Building - Secretariat', ''),
(589, 'Laboratory Complex Building - Dean MS', ''),
(590, 'Laboratory Complex Building - Dean Eng', ''),
(591, 'Laboratory Complex Building - HOD EEE', ''),
(592, 'Laboratory Complex Building - HOD ME', ''),
(593, 'Laboratory Complex Building - Weekend Coordinator\'s office', ''),
(594, 'Laboratory Complex Building - MEE Staff room(1)', ''),
(595, 'Laboratory Complex Building - MEE Staff room(2)', ''),
(596, 'Laboratory Complex Building - ICT Staff room', ''),
(597, 'Laboratory Complex Building - HOD ICT', ''),
(598, 'Exams Unit - Coordinator\'s office', ''),
(599, 'Exams Unit - Secretary\'s office', ''),
(600, 'Provost - Provost\'s office', ''),
(601, 'Provost - Secretary\'s office', ''),
(602, 'Transport Department - HOD\'s office', ''),
(603, 'Transport Department - Secretary\'s office', ''),
(604, 'Transport Department - National Service office', ''),
(605, 'Transport Department - Mr. Gohoho\'s office', ''),
(606, 'Transport Department - Mr Alex\'s office', ''),
(607, 'Transport Department - Ms Christable\'s Office', ''),
(608, 'Transport Department - Dr. Evans Office', ''),
(609, 'Transport Department - Mr. Dzikunu\'s Office', ''),
(610, 'Transport Department - Chinese Lecturer\'s office', ''),
(611, 'Transport Department - Classroom 101', ''),
(612, 'Transport Department - Classroom 102', ''),
(613, 'Transport Department - Classroom 103', ''),
(614, 'Transport Department - Classroom 104', ''),
(615, 'Transport Department - Classroom 105', ''),
(616, 'Transport Department - Classroom 202', ''),
(617, 'Transport Department - Classroom 203', ''),
(618, 'Transport Department - Classroom 204', ''),
(619, 'Transport Department - Master\'s Classroom', ''),
(620, 'Internal Audit Unit - Snr. Internal Auditor\'s office', ''),
(621, 'Internal Audit Unit - Internal Auditor\'s office', ''),
(622, 'Internal Audit Unit - Audit Assistant\'s office', ''),
(623, 'LIMA HOSTEL', ''),
(624, 'RMU CAMPUS', ''),
(625, 'SMU', ''),
(626, 'BRIDGE SIMULATOR', ''),
(627, 'BACK ACCOMMODATION', ''),
(628, 'SERVER ROOM', ''),
(629, 'VSTC', ''),
(630, 'GUIDANCE & COUNSELING', ''),
(631, 'GRADUATE SCHOOL', ''),
(632, 'MARITIME SAFETY AND SECURITY CENTRE ', ''),
(633, 'GMDSS LAB', ''),
(634, 'RMU VEHICLE FLEET', '');

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
) ENGINE=InnoDB AUTO_INCREMENT=636 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_reclass_log`
--

DROP TABLE IF EXISTS `asset_reclass_log`;
CREATE TABLE IF NOT EXISTS `asset_reclass_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `asset_id` int NOT NULL,
  `serial_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asset_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `from_class` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `from_sub_class` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `to_class` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `to_sub_class` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reclass_date` date DEFAULT NULL,
  `action_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logged_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_asset` (`asset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

DROP TABLE IF EXISTS `asset_type`;
CREATE TABLE IF NOT EXISTS `asset_type` (
  `type_id` int NOT NULL AUTO_INCREMENT,
  `asset_type` varchar(50) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `staff_id` varchar(40) NOT NULL,
  `staff_first_name` varchar(25) DEFAULT NULL,
  `staff_last_name` varchar(25) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `t_id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `asset_users`
--

INSERT INTO `asset_users` (`staff_id`, `staff_first_name`, `staff_last_name`, `department`, `t_id`) VALUES
('Rmu001', 'Ismail', 'Abdulai-Saiku', 'ICT', 1),
('Rmu002', 'Henry', 'Snow', 'ICT', 2),
('Rmu0099', 'Sowah', 'Ako-Nai', 'ICT', 3);

-- --------------------------------------------------------

--
-- Table structure for table `calculations`
--

DROP TABLE IF EXISTS `calculations`;
CREATE TABLE IF NOT EXISTS `calculations` (
  `table_id` int NOT NULL AUTO_INCREMENT,
  `asset_id` int NOT NULL,
  `asset_name` varchar(254) NOT NULL,
  `asset_class` varchar(100) NOT NULL,
  `asset_type` varchar(20) NOT NULL,
  `location` varchar(100) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `dep_id` varchar(37) NOT NULL,
  `dep_name` varchar(70) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`t_id`, `dep_id`, `dep_name`) VALUES
(1, 'DEP001', 'ICT'),
(2, 'DEP002', 'DOT'),
(3, 'DEP003', 'MEE');

-- --------------------------------------------------------

--
-- Table structure for table `disposals`
--

DROP TABLE IF EXISTS `disposals`;
CREATE TABLE IF NOT EXISTS `disposals` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) NOT NULL,
  `grv_number` varchar(20) DEFAULT 'N/A',
  `serial_number` varchar(50) DEFAULT 'N/A',
  `pv_number` varchar(50) DEFAULT 'N/A',
  `id_number` varchar(100) DEFAULT 'N/A',
  `supplier_name` varchar(250) NOT NULL,
  `asset_class` varchar(50) NOT NULL,
  `sub_class` varchar(254) NOT NULL,
  `asset_type` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `user` varchar(150) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `disposals`
--
DROP TRIGGER IF EXISTS `trg_disposals_insert`;
DELIMITER $$
CREATE TRIGGER `trg_disposals_insert` AFTER INSERT ON `disposals` FOR EACH ROW BEGIN
  DECLARE disposalYear INT;
  DECLARE existing_id INT;
  DECLARE disposalDollar DECIMAL(20,2);

  SET disposalYear = YEAR(NEW.date_of_disposal);
  SET disposalDollar = 
    CASE 
      WHEN NEW.dollar_rate_used > 0 THEN NEW.disposal_value / NEW.dollar_rate_used
      ELSE 0.00
    END;

  SELECT id INTO existing_id
  FROM asset_additions_year
  WHERE asset_class = NEW.asset_class AND year = disposalYear
  LIMIT 1;

  IF existing_id IS NOT NULL THEN
    UPDATE asset_additions_year
    SET 
      total_disposals_cedi = total_disposals_cedi + NEW.disposal_value,
      total_disposals_dollar = total_disposals_dollar + disposalDollar
    WHERE id = existing_id;
  ELSE
    INSERT INTO asset_additions_year (
      asset_class, year,
      total_additions_cedi, total_additions_dollar,
      total_disposals_cedi, total_disposals_dollar
    )
    VALUES (
      NEW.asset_class, disposalYear,
      0.00, 0.00,
      NEW.disposal_value, disposalDollar
    );
  END IF;
END
$$
DELIMITER ;

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dollar_rate`
--

INSERT INTO `dollar_rate` (`table_id`, `dollar_rate`, `rate_status`, `action_by`, `date_added`) VALUES
(1, 9.5, 'INACTIVE', 'schedule', '2024-05-01 17:44:22'),
(2, 8, 'INACTIVE', 'Schedule Officer', '2025-01-14 14:46:33'),
(3, 8.5, 'INACTIVE', 'Schedule Officer', '2025-01-30 14:10:27'),
(4, 9, 'INACTIVE', 'Schedule Officer', '2025-01-30 14:30:01'),
(5, 10, 'INACTIVE', 'Schedule Officer', '2025-01-30 14:37:44'),
(6, 9, 'INACTIVE', 'Schedule Officer', '2025-02-04 11:37:06'),
(7, 10, 'INACTIVE', 'Schedule Officer', '2025-04-09 15:30:18'),
(8, 11, 'INACTIVE', 'Schedule Officer', '2025-05-05 13:44:56'),
(9, 11, 'INACTIVE', 'Schedule Officer', '2025-05-05 13:44:56'),
(10, 10.5, 'INACTIVE', 'Schedule Officer', '2025-07-28 19:17:27'),
(11, 12, 'INACTIVE', 'Schedule Officer', '2025-07-28 19:43:22'),
(12, 12, 'INACTIVE', 'Schedule Officer', '2025-07-28 19:44:24'),
(13, 12, 'INACTIVE', 'Schedule Officer', '2025-07-28 19:44:36'),
(14, 10.5, 'INACTIVE', 'Schedule Officer 3', '2025-07-30 11:24:34'),
(15, 11, 'INACTIVE', 'Schedule Officer 3', '2025-07-30 11:25:16'),
(16, 12, 'INACTIVE', 'Schedule Officer', '2025-08-07 09:04:13'),
(17, 12, 'INACTIVE', 'Schedule Officer', '2025-08-18 10:08:35'),
(18, 12, 'INACTIVE', 'Schedule Officer', '2025-08-18 11:28:41'),
(19, 11, 'INACTIVE', 'Schedule Officer', '2025-08-25 09:10:32'),
(20, 12, 'INACTIVE', 'Schedule Officer', '2025-08-25 11:09:07'),
(21, 11, 'INACTIVE', 'Schedule Officer', '2025-08-25 11:21:45'),
(22, 11, 'INACTIVE', 'Schedule Officer', '2025-08-25 11:27:02'),
(23, 11, 'INACTIVE', 'Schedule Officer', '2025-08-25 11:41:45'),
(24, 12, 'INACTIVE', 'Schedule Officer', '2025-08-25 12:20:36'),
(25, 12, 'INACTIVE', 'Schedule Officer', '2025-08-25 12:57:55'),
(26, 11, 'INACTIVE', 'Schedule Officer', '2025-08-25 13:27:09'),
(27, 12, 'INACTIVE', 'Schedule Officer', '2025-08-25 13:43:52'),
(28, 12, 'INACTIVE', 'Schedule Officer', '2025-08-28 09:04:01'),
(29, 11, 'INACTIVE', 'Schedule Officer', '2025-10-02 13:32:30'),
(30, 11, 'INACTIVE', 'Schedule Officer', '2025-10-02 15:18:38'),
(31, 11, 'INACTIVE', 'Schedule Officer', '2025-10-06 09:17:46'),
(32, 12, 'INACTIVE', 'Schedule Officer', '2025-10-20 08:09:56'),
(33, 12, 'INACTIVE', 'Schedule Officer', '2025-11-04 12:16:26'),
(34, 12, 'INACTIVE', 'Schedule Officer', '2025-11-07 14:47:25'),
(35, 12, 'INACTIVE', 'Schedule Officer', '2025-12-09 13:10:09'),
(36, 12, 'INACTIVE', 'Schedule Officer', '2025-12-11 12:00:26'),
(37, 12, 'ACTIVE', 'Schedule Officer', '2026-03-11 12:09:32');

-- --------------------------------------------------------

--
-- Table structure for table `moved_assets`
--

DROP TABLE IF EXISTS `moved_assets`;
CREATE TABLE IF NOT EXISTS `moved_assets` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(100) NOT NULL,
  `notes` text NOT NULL,
  `old_location` varchar(100) NOT NULL,
  `old_user` varchar(100) NOT NULL,
  `New_location` varchar(100) NOT NULL,
  `new_user` varchar(100) NOT NULL,
  `date_of_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `moved_assets`
--

INSERT INTO `moved_assets` (`t_id`, `serial_number`, `notes`, `old_location`, `old_user`, `New_location`, `new_user`, `date_of_action`) VALUES
(1, '0', 'Asset Has Been Moved From Qualiity Control To ICt Lab', 'gambian hostel', 'null', 'ICT LAB', 'Ismail Abdulai-Saiku', '2025-01-14 14:23:57'),
(2, '0', 'Move', 'ICT LAB', 'Ismail Abdulai-Saiku', 'Registry Records', 'Henry Snow', '2025-01-27 09:05:50'),
(3, '2345678', 'Trial', 'ICT LAB', 'Ismail Abdulai-Saiku', 'Registry Records', 'Henry Snow', '2025-01-27 10:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uq_permission_key` (`permission_key`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_key`, `module`, `description`) VALUES
(1, 'asset.view', 'assets', 'View assets'),
(2, 'asset.create', 'assets', 'Register new assets'),
(3, 'asset.edit', 'assets', 'Edit asset details'),
(4, 'asset.move', 'assets', 'Transfer asset location/user'),
(5, 'asset.archive', 'assets', 'Archive assets'),
(6, 'asset.dispose', 'assets', 'Dispose / write off assets'),
(7, 'catalog.view', 'catalog', 'View classes, sub-classes, locations, types'),
(8, 'catalog.manage', 'catalog', 'Manage classes, sub-classes, locations, types'),
(9, 'supplier.view', 'suppliers', 'View suppliers'),
(10, 'supplier.manage', 'suppliers', 'Manage suppliers'),
(11, 'assetuser.view', 'people', 'View asset users'),
(12, 'assetuser.manage', 'people', 'Manage asset users'),
(13, 'rate.view', 'rate', 'View dollar rate'),
(14, 'rate.set', 'rate', 'Set / update dollar rate'),
(15, 'report.view', 'reports', 'View reports'),
(16, 'report.export', 'reports', 'Export / print reports'),
(17, 'user.manage', 'admin', 'Manage system user accounts'),
(18, 'role.manage', 'admin', 'Manage roles and permissions');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_key` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uq_role_key` (`role_key`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_key`, `role_name`, `description`, `created_at`) VALUES
(1, 'schedule_officer', 'Schedule Officer', 'Full operational control of the asset register', '2026-06-06 02:09:36'),
(2, 'dsu', 'DSU', 'Departmental view of assets, locations and reports', '2026-06-06 02:09:36'),
(3, 'accountant', 'Accountant', 'Financial reporting and asset valuation', '2026-06-06 02:09:36'),
(4, 'budget_officer', 'Budget Officer', 'Budget oversight and asset reporting', '2026-06-06 02:09:36'),
(5, 'sia', 'Senior Internal Auditor', 'Audit-level read access and report export', '2026-06-06 02:09:36'),
(6, 'director_finance', 'Director of Finance', 'Executive oversight and reporting', '2026-06-06 02:09:36'),
(7, 'system_admin', 'System Administrator', 'Manages users, roles and permissions', '2026-06-06 02:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_rp_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(1, 2),
(7, 2),
(1, 3),
(7, 3),
(1, 4),
(7, 4),
(1, 5),
(7, 5),
(1, 6),
(7, 6),
(1, 7),
(2, 7),
(7, 7),
(1, 8),
(7, 8),
(1, 9),
(7, 9),
(1, 10),
(7, 10),
(1, 11),
(7, 11),
(1, 12),
(7, 12),
(1, 13),
(2, 13),
(3, 13),
(4, 13),
(6, 13),
(7, 13),
(1, 14),
(7, 14),
(1, 15),
(2, 15),
(3, 15),
(4, 15),
(5, 15),
(6, 15),
(7, 15),
(1, 16),
(2, 16),
(3, 16),
(5, 16),
(6, 16),
(7, 16),
(7, 17),
(7, 18);

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
  UNIQUE KEY `number` (`number`),
  UNIQUE KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`sup_id`, `name`, `location`, `number`) VALUES
(1, 'Yakubu Furnitures', 'Tema', '0200034432'),
(2, 'Azar Group', 'Prampram', '0200034430'),
(3, 'Others', 'Tema', '0200034439');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `untracked_asset_disposals`
--

DROP TABLE IF EXISTS `untracked_asset_disposals`;
CREATE TABLE IF NOT EXISTS `untracked_asset_disposals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `acquisition_date` date DEFAULT '2023-01-01',
  `date_of_disposal` date NOT NULL,
  `dollar_rate` decimal(6,2) DEFAULT '8.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_ur_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 5, '2026-06-06 02:10:28'),
(2, 1, '2026-06-06 02:10:28'),
(3, 4, '2026-06-06 02:10:28'),
(4, 3, '2026-06-07 14:14:55'),
(5, 6, '2026-06-06 02:10:28'),
(6, 2, '2026-06-06 02:10:28'),
(7, 7, '2026-06-06 02:22:28');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
