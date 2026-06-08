-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 04, 2024 at 07:23 PM
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
  `id_number` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `pv_number` varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `supplier_name` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `asset_class` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `asset_type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `location` varchar(50) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `id_number`, `pv_number`, `supplier_name`, `asset_class`, `asset_type`, `location`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(2, 'CO2 Extinguisher 5KG', 'GRV002', 'ID00000002', ' PV000002', '', 'Teaching Equipment', 'Leased', 'MSF Secretariat', '2022-01-17', 2024, '0.00', '17163.52', '0.00', '0.00', 7, '2024-01-29 19:19:15'),
(4, 'UPS', 'GRV004', 'ID00000004', 'PV000004', '', 'Computer & Accessories', 'Owned', 'Nautical Science HOD\'s Office', '2022-01-23', 2024, '0.00', '13000.00', '0.00', '0.00', 7, '2024-01-29 19:38:07'),
(5, 'White Board ', 'GRV005', 'ID00000005', 'PV000005', '', 'Classroom Furniture', 'Owned', 'NSD Classrooms', '2022-01-23', 2024, '0.00', '16718.00', '0.00', '0.00', 7, '2024-01-29 19:47:07'),
(6, 'Lecture Hall Chair', 'GRV006', 'ID00000006', 'PV000006', '', 'Classroom Furniture', 'Owned', 'NSD Classrooms', '2022-01-24', 2024, '0.00', '60000.00', '0.00', '0.00', 7, '2024-01-29 19:48:59'),
(7, 'Mower', 'GRV007', 'ID00000007', 'PV000007', '', 'Machines, Equipment & Tools', 'Owned', 'University Campus', '2022-01-27', 2024, '0.00', '5000.00', '0.00', '0.00', 7, '2024-01-29 19:56:39'),
(8, 'Mobile Crane', 'GRV008', 'ID00000008', 'PV000008', '', 'Teaching Equipment', 'Owned', 'Engineering Workshop', '2022-02-07', 2024, '0.00', '25800.00', '0.00', '0.00', 7, '2024-01-29 19:59:40'),
(9, 'Mobile Crane', 'GRV009', 'ID00000009', 'PV000009', '', 'Teaching Equipment', 'Owned', 'Engineering Workshop', '2022-02-07', 2024, '0.00', '3050.00', '0.00', '0.00', 7, '2024-01-29 20:00:47'),
(10, 'Fire Items', 'GRV010', 'ID00000010', 'PV000010', '', 'Teaching Equipment', 'Owned', 'MSF Secretariat', '2022-02-07', 2024, '0.00', '31200.00', '0.00', '0.00', 7, '2024-01-29 20:01:55'),
(12, 'Sec. Swivel Chair', 'GRV012', 'ID00000012', 'PV000012', '', 'Classroom Furniture', 'Owned', 'Lectures/ Marine Electrical/Electronic', '2022-02-17', 2024, '0.00', '2024.87', '0.00', '0.00', 7, '2024-01-29 20:09:16'),
(13, 'Mattress', 'GRV013', 'ID00000013', 'PV000013', '', 'Student Furniture', 'Owned', 'Back Accommodation', '2022-02-17', 2024, '0.00', '51600.00', '0.00', '0.00', 7, '2024-01-29 20:13:41'),
(14, 'Forklift Training', 'GRV014', 'ID00000014', 'PV000014', '', 'Teaching Equipment', 'Owned', 'Engineering Workshop', '2022-02-23', 2024, '0.00', '6000.00', '0.00', '0.00', 7, '2024-01-29 20:16:12'),
(15, 'Split Air Conditioner 2.0 HP', 'GRV015', 'ID00000015', 'PV000015', '', 'Office Equipment', 'Owned', 'VSTC Head', '2022-02-28', 2024, '0.00', '30999.99', '0.00', '0.00', 7, '2024-01-29 20:17:26'),
(16, 'Desktop Computer', 'GRV016', 'ID00000016', 'PV000016', '', 'Computer & Accessories', 'Owned', 'Account\'s Clerk', '2022-02-03', 2024, '0.00', '2132.00', '0.00', '0.00', 7, '2024-01-29 20:22:18'),
(17, 'Lecture Hall Metal Desk', 'GRV017', 'ID00000017', 'PV000017', '', 'Office Furniture', 'Owned', 'Language Centre', '2022-03-02', 2024, '0.00', '14900.00', '0.00', '0.00', 7, '2024-01-29 20:25:47'),
(18, 'Conference Table and Chairs', 'GRV018', 'ID00000018', 'PV000018', '', 'Office Furniture', 'Owned', 'Auditorium', '2022-03-03', 2024, '0.00', '91417.70', '0.00', '0.00', 7, '2024-01-29 20:27:13'),
(19, 'Executive Swivel Chair', 'GRV019', 'ID00000019', 'PV000019', '', 'Office Furniture', 'Owned', 'Procurement', '2022-03-10', 2024, '0.00', '14560.00', '0.00', '0.00', 7, '2024-01-29 20:30:00'),
(20, 'Mobile Crane', 'GRV020', 'ID00000020', 'PV000020', '', 'Teaching Equipment', 'Owned', 'Engineering Workshop', '2022-03-14', 2024, '0.00', '3000.00', '0.00', '0.00', 7, '2024-01-29 20:33:35'),
(21, '2 Drawer Table', 'GRV021', 'ID00000021', 'PV000021', '', 'Classroom Furniture', 'Owned', 'Language Centre', '2022-03-21', 2024, '0.00', '3000.00', '0.00', '0.00', 7, '2024-01-29 20:36:54'),
(22, 'Test 1', 'GRV022', 'ID00000022', 'PV000022', '', 'Computer & Accessories', 'Owned', 'Computer Lab 1', '2022-03-27', 2024, '0.00', '4750.00', '0.00', '0.00', 7, '2024-01-29 20:39:20'),
(23, 'Swivel Chair', 'GRV023', 'ID00000023', 'PV000023', '', 'Office Furniture', 'Owned', 'Pro/Quality Control', '2022-03-27', 2024, '0.00', '4442.64', '0.00', '0.00', 7, '2024-01-29 20:42:40'),
(24, 'Welding Training', 'GRV024', 'ID00000024', 'PV000024', '', 'Teaching Equipment', 'Owned', 'Engineering Workshop', '2022-03-30', 2024, '0.00', '294.19', '0.00', '0.00', 7, '2024-01-29 20:44:41'),
(25, 'Waiting Chair', 'GRV025', 'ID00000025', 'PV000025', '', 'Office Furniture', 'Owned', 'Graduate School', '2022-03-30', 2024, '0.00', '4500.00', '0.00', '0.00', 7, '2024-01-29 20:47:35'),
(26, 'Executive Swivel Chair', 'GRV026', 'ID00000026', 'PV000026', '', 'Office Furniture', 'Owned', 'Pro/Quality Control', '2022-03-30', 2024, '0.00', '2912.00', '0.00', '0.00', 7, '2024-01-29 20:49:52'),
(27, 'L-Shaped Desk', 'GRV027', 'ID00000027', 'PV000027', '', 'Office Furniture', 'Owned', 'Procurement', '2022-03-30', 2024, '0.00', '5200.00', '0.00', '0.00', 7, '2024-01-29 20:51:11'),
(28, 'Printer', 'GRV028', 'ID00000028', 'PV000028', '', 'Computer & Accessories', 'Owned', 'Registry Records', '2022-04-03', 2024, '0.00', '111020.00', '0.00', '0.00', 7, '2024-01-29 20:52:36'),
(29, 'Table', 'GRV029', 'ID00000029', 'PV000029', '', 'Office Furniture', 'Owned', 'Estate Works Supervisor', '2022-04-05', 2024, '0.00', '12302.15', '0.00', '0.00', 7, '2024-01-29 20:55:05'),
(30, 'Table Top Fridge', 'GRV030', 'ID00000030', 'PV000030', '', 'Office Equipment', 'Owned', 'Pro-Vice Chancellor\'s office', '2022-04-05', 2024, '0.00', '2039.25', '0.00', '0.00', 7, '2024-01-29 21:00:53'),
(31, 'Paper Shredder', 'GRV031', 'ID00000031', 'PV000031', '', 'Office Equipment', 'Owned', 'Archives', '2022-04-06', 2024, '0.00', '6202.56', '0.00', '0.00', 7, '2024-01-29 22:04:00'),
(32, 'HP Scanner', 'GRV032', 'ID00000032', 'PV000032', '', 'Computer & Accessories', 'Owned', 'Graduate School', '2022-04-06', 2024, '0.00', '5460.00', '0.00', '0.00', 7, '2024-01-29 22:06:00'),
(33, 'AC Control Board', 'GRV033', 'ID00000033', 'PV000033', '', 'Office Equipment', 'Owned', 'Electronics Laboratory', '2022-04-13', 2024, '0.00', '18400.00', '0.00', '0.00', 7, '2024-01-29 22:07:31'),
(34, 'Projector', 'GRV034', 'ID00000034', 'PV000034', '', 'Computer & Accessories', 'Owned', 'Language Centre', '2022-04-14', 2024, '0.00', '26520.00', '0.00', '0.00', 7, '2024-01-29 22:09:04'),
(35, 'HP Scanner', 'GRV035', 'ID00000035', 'PV000035', '', 'Computer & Accessories', 'Owned', 'Registry Room 4', '2022-04-19', 2024, '0.00', '1820.00', '0.00', '0.00', 7, '2024-01-29 22:11:21'),
(36, 'Air Conditioner', 'GRV036', 'ID00000036', 'PV000036', '', 'Office Equipment', 'Owned', 'Bungalow R2', '2022-04-20', 2024, '0.00', '4000.00', '0.00', '0.00', 7, '2024-01-29 22:12:41'),
(37, 'Laptop Computer', 'GRV037', 'ID00000037', 'PV000037', '', 'Computer & Accessories', 'Owned', 'Deputy Registrar', '2022-04-26', 2024, '0.00', '55600.01', '0.00', '0.00', 7, '2024-01-29 22:14:10'),
(38, 'Split Air Conditioner 2.5 HP', 'GRV038', 'ID00000038', 'PV000038', '', 'Office Equipment', 'Owned', 'Pro-Vice Chancellor\'s office', '2022-04-27', 2024, '0.00', '19998.00', '0.00', '0.00', 7, '2024-01-29 22:16:05'),
(39, 'HP Laserjet Pro 401 DN Printer', 'GRV039', 'ID00000039', 'PV000039', '', 'Computer & Accessories', 'Owned', 'MSF Secretariat', '2022-05-28', 2024, '0.00', '6972.68', '0.00', '0.00', 7, '2024-01-29 22:17:25'),
(40, 'UPS', 'GRV040', 'ID00000040', 'PV000040', '', 'Computer & Accessories', 'Owned', 'Account\'s Clerk', '2022-05-02', 2024, '0.00', '3742.00', '0.00', '0.00', 7, '2024-01-29 22:19:13'),
(41, 'Life Jacket ', 'GRV041', 'ID00000041', 'PV000041', '', 'Machines, Equipment & Tools', 'Owned', 'MSF Secretariat', '2022-05-03', 2024, '0.00', '58240.00', '0.00', '0.00', 7, '2024-01-29 22:20:23'),
(42, 'Life Jacket ', 'GRV042', 'ID00000043', 'PV000043', '', 'Computer & Accessories', 'Owned', 'Graduate School', '2022-05-04', 2024, '0.00', '40958.40', '0.00', '0.00', 7, '2024-01-29 22:21:44'),
(43, 'UPS', 'GRV043', 'ID00000042', 'PV000042', '', 'Computer & Accessories', 'Owned', 'Registry Records', '2022-05-04', 2024, '0.00', '7168.00', '0.00', '0.00', 7, '2024-01-29 22:25:34'),
(44, 'Generator', 'GRV044', 'ID00000044', 'PV000044', '', 'Machines, Equipment & Tools', 'Owned', 'MSF Secretariat', '2022-05-08', 2024, '0.00', '22175.36', '0.00', '0.00', 7, '2024-01-29 22:26:49'),
(45, 'Refurbished Dell Optiplex System Unit', 'GRV045', 'ID00000045', 'PV000045', '', 'Computer & Accessories', 'Owned', 'Registry\'s Secretariat/ General Office', '2022-05-09', 2024, '0.00', '4264.00', '0.00', '0.00', 7, '2024-01-29 22:30:29'),
(46, 'Test 2', 'GRV046', 'ID00000046', 'PV000046', '', 'Computer & Accessories', 'Owned', 'Computer Lab 2', '2022-05-10', 2024, '0.00', '8776.80', '0.00', '0.00', 7, '2024-01-29 22:31:50'),
(47, 'ABB Meters', 'GRV047', 'ID00000047', 'PV000047', '', 'Machines, Equipment & Tools', 'Owned', 'Computer Lab 2', '2022-05-10', 2024, '0.00', '11092.80', '0.00', '0.00', 7, '2024-01-29 22:33:50'),
(48, 'HP Laserjet Pro 401 DN Printer', 'GRV048', 'ID00000048', 'PV000048', '', 'Computer & Accessories', 'Owned', 'Language Centre', '2022-05-13', 2024, '0.00', '24959.96', '0.00', '0.00', 7, '2024-01-29 22:35:57'),
(49, 'UPS 800 VA', 'GRV049', 'ID00000049', 'PV000049', '', 'Computer & Accessories', 'Owned', 'Simulator Lab', '2022-06-22', 2024, '0.00', '12096.00', '0.00', '0.00', 7, '2024-01-29 22:38:13'),
(53, 'Steel Cabinet', 'GRV053', 'ID00000053', 'PV000053', '', 'Office Equipment', 'Owned', 'Registry Room 3', '2022-06-29', 2024, '0.00', '3839.35', '0.00', '0.00', 7.5, '2024-01-29 22:48:05'),
(118, 'Extenstion Of MEE Lab', '2347', '2347', '2347', 'Aay Great Provider', 'Land & Buildings', 'Leased', 'Procurement', '2024-01-01', 2024, '0.00', '120000.00', '0.00', '0.00', 9, '2024-02-02 12:46:48'),
(55, 'Ladder', 'GRV055', 'ID00000055', 'PV000055', '', 'Machines, Equipment & Tools', 'Owned', 'University Campus', '2022-08-01', 2024, '0.00', '2500.00', '0.00', '0.00', 7.5, '2024-01-29 22:52:45'),
(56, 'Welding Machine', 'GRV056', 'ID00000056', 'PV000056', '', 'Machines, Equipment & Tools', 'Owned', 'VSTC Head', '2022-08-01', 2024, '0.00', '2700.00', '0.00', '0.00', 7.5, '2024-01-29 22:54:10'),
(57, 'Printer', 'GRV057', 'ID00000057', 'PV000057', '', 'Computer & Accessories', 'Owned', 'Sick Bay Consulting Room 1', '2022-08-03', 2024, '0.00', '6972.68', '0.00', '0.00', 7.5, '2024-01-29 22:55:28'),
(58, 'Microphone', 'GRV058', 'ID00000058', 'PV000058', '', 'Machines, Equipment & Tools', 'Owned', 'Auditorium', '2022-08-14', 2024, '0.00', '6000.00', '0.00', '0.00', 7.5, '2024-01-29 22:56:35'),
(59, 'UPS', 'GRV059', 'ID00000059', 'PV000059', '', 'Computer & Accessories', 'Owned', 'Library', '2022-08-14', 2024, '0.00', '1617.61', '0.00', '0.00', 7.5, '2024-01-29 22:57:51'),
(60, 'Generator', 'GRV060', 'ID00000060', 'PV000060', '', 'Machines, Equipment & Tools', 'Owned', 'University Campus', '2022-08-17', 2024, '0.00', '63615.00', '0.00', '0.00', 7.5, '2024-01-29 22:59:26'),
(61, 'Curtains Assorted', 'GRV061', 'ID00000061', 'PV000061', '', 'Office Furniture', 'Owned', 'ICT HOD', '2022-08-29', 2024, '0.00', '960.00', '0.00', '0.00', 7.5, '2024-01-29 23:01:10'),
(62, 'Laptop ', 'GRV062', 'ID00000062', 'PV000062', '', 'Computer & Accessories', 'Owned', 'Pro-Vice Chancellor\'s office', '2022-08-29', 2024, '0.00', '20400.00', '0.00', '0.00', 7.5, '2024-01-29 23:02:26'),
(63, 'Refurbished Dell Optiplex System Unit', 'GRV063', 'ID00000063', 'PV000063', '', 'Computer & Accessories', 'Owned', 'Stores', '2022-08-30', 2024, '0.00', '4264.00', '0.00', '0.00', 7.5, '2024-01-29 23:05:31'),
(64, 'Curtain', 'GRV064', 'ID00000064', 'PV000064', '', 'Office Furniture', 'Owned', 'ICT HOD', '2022-09-14', 2024, '0.00', '14740.00', '0.00', '0.00', 7.5, '2024-01-29 23:08:55'),
(65, 'Mobile Crane', 'GRV065', 'ID00000065', 'PV000065', '', 'Teaching Equipment', 'Owned', 'VSTC Head', '2022-09-14', 2024, '0.00', '67654.50', '0.00', '0.00', 7.5, '2024-01-29 23:11:17'),
(66, 'Telephone PBX KX T7436', 'GRV066', 'ID00000066', 'PV000066', '', 'Computer & Accessories', 'Owned', 'I.T Manager', '2022-09-18', 2024, '0.00', '23090.94', '0.00', '0.00', 7.5, '2024-01-29 23:13:26'),
(67, 'Curtain', 'GRV067', 'ID00000067', 'PV000067', '', 'Office Furniture', 'Owned', 'ICT HOD', '2022-09-27', 2024, '0.00', '1500.00', '0.00', '0.00', 7.5, '2024-01-29 23:14:58'),
(68, 'Curtain', 'GRV068', 'ID00000068', 'PV000068', '', 'Office Furniture', 'Owned', 'MSF Secretariat', '2022-10-09', 2024, '0.00', '17100.00', '0.00', '0.00', 7.5, '2024-01-29 23:16:15'),
(69, 'Desktop Computer', 'GRV069', 'ID00000069', 'PV000069', '', 'Computer & Accessories', 'Owned', 'Human Resource/Administration Officer', '2022-10-10', 2024, '0.00', '10239.60', '0.00', '0.00', 7.5, '2024-01-29 23:18:40'),
(70, 'UPS Battery 12V/7', 'GRV070', 'ID00000070', 'PV000070', '', 'Computer & Accessories', 'Owned', 'Nautical Studies Secretariat', '2022-10-10', 2024, '0.00', '1557.88', '0.00', '0.00', 7.5, '2024-01-29 23:21:05'),
(71, 'Internet Switch', 'GRV071', 'ID00000071', 'PV000071', '', 'Computer & Accessories', 'Owned', 'Pro-Vice Chancellor\'s office', '2022-10-10', 2024, '0.00', '38032.80', '0.00', '0.00', 7.5, '2024-01-30 10:02:15'),
(72, 'Swivel Chair', 'GRV072', 'ID00000072', 'PV000072', '', 'Office Furniture', 'Owned', 'Pro-Vice Chancellor\'s office', '2022-10-10', 2024, '0.00', '11232.00', '0.00', '0.00', 7.5, '2024-01-30 10:04:12'),
(73, 'Computer', 'GRV073', 'ID00000073', 'PV000073', '', 'Computer & Accessories', 'Owned', 'Transport Unit', '2022-10-10', 2024, '0.00', '14500.00', '0.00', '0.00', 7.5, '2024-01-30 10:07:13'),
(74, 'Visitor Chair', 'GRV074', 'ID00000074', 'PV000074', '', 'Office Furniture', 'Owned', 'Accountant', '2022-10-11', 2024, '0.00', '1200.00', '0.00', '0.00', 7.5, '2024-01-30 10:13:01'),
(117, 'Extenstion Of MEE Lab', '2346', '2346', '2346', 'Banku Services', 'Land & Buildings', 'Leased', 'Computer Lab 2', '2024-01-01', 2024, '0.00', '80000.00', '0.00', '0.00', 9, '2024-02-02 12:45:34'),
(76, 'Projector Screen', 'GRV076', 'ID00000076', 'PV000076', '', 'Computer & Accessories', 'Owned', 'Auditorium', '2022-10-13', 2024, '0.00', '12300.00', '0.00', '0.00', 7.5, '2024-01-30 10:15:41'),
(77, 'Wireless Internet Switch', 'GRV077', 'ID00000077', 'PV000077', '', 'Computer & Accessories', 'Owned', 'Marine Engineering HOD Secretary\'s Office', '2022-10-16', 2024, '0.00', '4900.00', '0.00', '0.00', 7.5, '2024-01-30 10:18:35'),
(78, 'Mattress', 'GRV078', 'ID00000078', 'PV000078', '', 'Student Furniture', 'Owned', 'Marine Engineering HOD Secretary\'s Office', '2022-10-31', 2024, '0.00', '55000.00', '0.00', '0.00', 7.5, '2024-01-30 10:23:08'),
(116, 'Extenstion Of MEE Lab', '2345', '2345', '2345', 'Hp Services', 'Land & Buildings', 'Owned', 'Computer Lab 1', '2024-01-01', 2024, '0.00', '228258.00', '0.00', '0.00', 9, '2024-02-02 12:42:39'),
(80, 'Swivel Chair', 'GRV080', 'ID00000080', 'PV000080', '', 'Office Furniture', 'Owned', 'Registrar', '2022-10-31', 2024, '0.00', '8736.00', '0.00', '0.00', 7.5, '2024-01-30 10:27:46'),
(81, 'Slasher Head Gear', 'GRV081', 'ID00000081', 'PV000081', '', 'Machines, Equipment & Tools', 'Owned', 'Estate Works Supervisor', '2022-11-01', 2024, '0.00', '6100.00', '0.00', '0.00', 7.5, '2024-01-30 10:32:14'),
(82, 'Shredder', 'GRV082', 'ID00000082', 'PV000082', '', 'Office Equipment', 'Owned', 'Pro-Vice Chancellor\'s office', '2022-11-01', 2024, '0.00', '1650.53', '0.00', '0.00', 7.5, '2024-01-30 10:39:58'),
(83, 'Laminating Machine', 'GRV083', 'ID00000083', 'PV000083', '', 'Office Equipment', 'Owned', 'Registry Room 4', '2022-11-01', 2024, '0.00', '2133.35', '0.00', '0.00', 7.5, '2024-01-30 10:42:30'),
(84, 'Desktop Computer', 'GRV084', 'ID00000084', 'PV000084', '', 'Computer & Accessories', 'Owned', 'Graduate School', '2022-11-06', 2024, '0.00', '76065.60', '0.00', '0.00', 7.5, '2024-01-30 10:45:32'),
(85, 'Laptop Computer', 'GRV085', 'ID00000085', 'PV000085', '', 'Computer & Accessories', 'Owned', 'ICT HOD', '2022-11-06', 2024, '0.00', '25116.28', '0.00', '0.00', 7.5, '2024-01-30 10:46:41'),
(115, 'Tilling Of Audit 2', '8901', '8901', '8901', 'Hp Services', 'Land & Buildings', 'Owned', 'MSF Secretariat', '2023-01-01', 2024, '0.00', '4500.00', '0.00', '0.00', 9, '2024-02-02 12:40:57'),
(87, 'Slasher Head Gear', 'GRV087', 'ID00000087', 'PV000087', '', 'Machines, Equipment & Tools', 'Owned', 'DSU', '2022-11-14', 2024, '0.00', '5000.00', '0.00', '0.00', 7.5, '2024-01-30 10:50:22'),
(88, 'Benches', 'GRV088', 'ID00000088', 'PV000088', '', 'Classroom Furniture', 'Owned', 'Engineering Workshop', '2022-11-14', 2024, '0.00', '13135.00', '0.00', '0.00', 7.5, '2024-01-30 10:53:53'),
(89, 'HP Laserjet Pro 401 DN Printer', 'GRV089', 'ID00000089', 'PV000089', '', 'Computer & Accessories', 'Owned', 'SEC. to Director of Administration', '2022-11-14', 2024, '0.00', '4900.00', '0.00', '0.00', 7.5, '2024-01-30 10:55:20'),
(90, 'Double Door Steel Cabinet', 'GRV090', 'ID00000090', 'PV000090', '', 'Office Equipment', 'Owned', 'University\'s Registrar\'s Office', '2022-11-14', 2024, '0.00', '3839.85', '0.00', '0.00', 7.5, '2024-01-30 11:01:10'),
(91, 'Dell Optiplex Intel Core', 'GRV091', 'ID00000091', 'PV000091', '', 'Computer & Accessories', 'Owned', 'I.T Manager', '2022-11-16', 2024, '0.00', '15500.00', '0.00', '0.00', 7.5, '2024-01-30 11:04:35'),
(92, 'Office Desk', 'GRV092', 'ID00000092', 'PV000092', '', 'Office Furniture', 'Owned', 'Library', '2022-11-16', 2024, '0.00', '10400.00', '0.00', '0.00', 7.5, '2024-01-30 11:08:12'),
(93, 'Executive Swivel Chair', 'GRV093', 'ID00000093', 'PV000093', '', 'Office Furniture', 'Owned', 'Marketing Officer', '2022-11-16', 2024, '0.00', '5824.00', '0.00', '0.00', 7.5, '2024-01-30 11:11:45'),
(94, 'Projector', 'GRV094', 'ID00000094', 'PV000094', '', 'Teaching Equipment', 'Owned', 'Auditorium', '2022-11-21', 2024, '0.00', '3524.00', '0.00', '0.00', 7.5, '2024-01-30 11:13:30'),
(95, 'Air Conditioner', 'GRV095', 'ID00000095', 'PV000095', '', 'Office Equipment', 'Owned', 'Library', '2022-11-21', 2024, '0.00', '42500.00', '0.00', '0.00', 7.5, '2024-01-30 11:15:26'),
(96, 'Wireless Internet Switch', 'GRV096', 'ID00000096', 'PV000096', '', 'Computer & Accessories', 'Owned', 'Marine Engineering HOD Secretary\'s Office', '2022-11-23', 2024, '0.00', '37500.00', '0.00', '0.00', 7.5, '2024-01-30 11:20:11'),
(97, 'Air Conditioner', 'GRV097', 'ID00000097', 'PV000097', '', 'Office Equipment', 'Owned', 'MSF Secretariat', '2022-12-13', 2024, '0.00', '33020.80', '0.00', '0.00', 7.5, '2024-01-30 11:22:45'),
(111, 'Test', '1234', '1234', '1234', 'Banku Services', 'Plant & Equipment', 'Owned', 'MSF Secretariat', '2023-11-01', 2024, '0.00', '0.00', '900.00', '0.00', 2, '2024-02-02 08:36:39'),
(112, 'Nissan GTR', '12345', '12345', '12345', 'Hp Services', 'Motor Vehicles', 'Owned', 'Pro/Quality Control', '2021-02-02', 2024, '0.00', '0.00', '0.00', '0.00', 9, '2024-02-02 11:37:33'),
(113, 'Nissan GTR', '12346', '12346', '12346', 'Hp Services', 'Motor Vehicles', 'Owned', 'Pro/Quality Control', '2021-02-02', 2024, '0.00', '0.00', '100.00', '0.00', 9, '2024-02-02 11:39:33'),
(114, 'Nissan GTR', '12347', '12347', '12347', 'Hp Services', 'Equipments', 'Owned', 'Pro/Quality Control', '2022-02-02', 2024, '0.00', '1200.00', '0.00', '0.00', 9, '2024-02-02 11:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `assets_archive`
--

DROP TABLE IF EXISTS `assets_archive`;
CREATE TABLE IF NOT EXISTS `assets_archive` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) NOT NULL,
  `grv_number` varchar(7) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `id_number` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `pv_number` varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'N/A',
  `supplier_name` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `asset_class` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `asset_type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `location` varchar(50) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assets_archive`
--

INSERT INTO `assets_archive` (`asset_id`, `asset_name`, `grv_number`, `id_number`, `pv_number`, `supplier_name`, `asset_class`, `asset_type`, `location`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(109, 'Test', '110000', '0001100000', '09000000', 'Access A Properties Limited', 'Plant & Equipment', 'Owned', 'Graduate School', '2024-01-01', 2024, '0.00', '0.00', '0.00', '0.00', 3.5, '2024-01-31 11:25:22'),
(110, 'Test', '1234', '1234', '1234', 'Banku Services', 'Plant & Equipment', 'Owned', 'MSF Secretariat', '2023-07-04', 2024, '0.00', '0.00', '0.00', '0.00', 2, '2024-02-02 08:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes`
--

DROP TABLE IF EXISTS `asset_classes`;
CREATE TABLE IF NOT EXISTS `asset_classes` (
  `ast_id` int NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(50) NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `opbal_plus_additions` decimal(20,2) NOT NULL,
  `dep_rate` float NOT NULL,
  `estimated_life` int NOT NULL,
  PRIMARY KEY (`ast_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_classes`
--

INSERT INTO `asset_classes` (`ast_id`, `asset_class`, `opening_bal`, `opbal_plus_additions`, `dep_rate`, `estimated_life`) VALUES
(1, 'Land & Buildings', '500000.00', '932758.00', 0.02, 50),
(2, 'Plant & Equipment', '250000.00', '250000.00', 0.2, 5),
(3, 'Computer & Accessories', '100000.00', '726197.24', 0.25, 4),
(4, 'Office Equipment', '50000.00', '218623.68', 0.25, 4),
(5, 'Office Furniture', '500000.00', '721926.49', 0.2, 5),
(6, 'Motor Vehicles', '500000.00', '500000.00', 0.2, 5),
(7, 'Classroom Furniture', '500000.00', '594877.87', 0.25, 4),
(8, 'Teaching Equipment', '500000.00', '657686.21', 0.2, 5),
(9, 'Academic Gowns', '250000.00', '0.00', 0.2, 5),
(10, 'Solar Equipment', '300000.00', '0.00', 0.25, 4),
(11, 'School Band', '100000.00', '0.00', 0.33, 3),
(12, 'Bridge Simulators', '400000.00', '0.00', 0.2, 5),
(13, 'Library Books', '100000.00', '0.00', 0.2, 5),
(14, 'Swimming Pool', '100000.00', '0.00', 0.1, 10),
(15, 'Machines, Equipment & Tools', '250000.00', '432423.16', 0.2, 5),
(16, 'Student Furniture', '200000.00', '306600.00', 0.2, 5),
(17, 'Fish', '100.00', '0.00', 0.3, 3),
(18, 'Test', '1000.00', '0.00', 0.5, 2),
(19, 'Equipments', '10000.00', '11200.00', 0.02, 50);

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
-- Table structure for table `asset_location`
--

DROP TABLE IF EXISTS `asset_location`;
CREATE TABLE IF NOT EXISTS `asset_location` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_location`
--

INSERT INTO `asset_location` (`loc_id`, `location`) VALUES
(1, 'Back Accommodation'),
(2, 'MSF Secretariat'),
(3, 'M.S.F Lecture Halls'),
(4, 'Nautical Science HOD\'s Office'),
(5, 'NSD Classrooms'),
(6, 'University Campus'),
(7, 'Engineering Workshop'),
(8, 'Lectures/ Marine Electrical/Electronic'),
(9, 'Account\'\'s Clerk'),
(10, 'VSTC Head'),
(11, 'Language Centre'),
(12, 'Auditorium'),
(13, 'Procurement'),
(14, 'Computer Lab 1'),
(15, 'Computer Lab 2'),
(16, 'Pro/Quality Control'),
(17, 'Graduate School'),
(18, 'Registry Records'),
(19, 'Estate Works Supervisor'),
(20, 'Pro-Vice Chancellor\'\'s office'),
(21, 'Archives'),
(22, 'Electronics Laboratory'),
(23, 'Registry Room 4'),
(24, 'Bungalow R2'),
(25, 'Deputy Registrar'),
(26, 'Registry\'\'s Secretariat/ General Office'),
(27, 'Simulator Lab'),
(28, 'Gambian Hostel'),
(29, 'Registry Room 3'),
(30, 'Sick Bay Consulting Room 1'),
(31, 'Library'),
(32, 'ICT HOD'),
(33, 'I.T Manager'),
(34, 'Human Resource/Administration Officer'),
(35, 'Nautical Studies Secretariat'),
(36, 'SEC. to Director of Administration'),
(37, 'Transport Unit'),
(38, 'Accountant'),
(39, 'Marine Engineering HOD Secretary\'\'s Office'),
(40, 'Fabrication Workshop'),
(41, 'Registrar'),
(42, 'DSU'),
(43, 'University\'\'s Registrar\'\'s Office'),
(44, 'Marketing Officer'),
(54, 'Stores'),
(58, 'Back Hostel');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location_archive`
--

DROP TABLE IF EXISTS `asset_location_archive`;
CREATE TABLE IF NOT EXISTS `asset_location_archive` (
  `loc_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_location_archive`
--

INSERT INTO `asset_location_archive` (`loc_id`, `location`) VALUES
(47, 'location'),
(48, 'Test Location'),
(46, 'Yakubu\'s base'),
(49, 'Yakubu Estates'),
(50, 'Sowah'),
(51, 'Test'),
(53, 'Location Test'),
(45, 'Stores'),
(52, 'Stores'),
(55, 'Test'),
(56, 'Test'),
(57, 'Back Hostel');

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

DROP TABLE IF EXISTS `asset_type`;
CREATE TABLE IF NOT EXISTS `asset_type` (
  `type_id` int NOT NULL AUTO_INCREMENT,
  `asset_type` varchar(50) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_type`
--

INSERT INTO `asset_type` (`type_id`, `asset_type`) VALUES
(1, 'Owned'),
(2, 'Leased'),
(3, 'Rent');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dollar_rate`
--

INSERT INTO `dollar_rate` (`table_id`, `dollar_rate`, `rate_status`, `action_by`, `date_added`) VALUES
(1, 2, 'INACTIVE', 'Schedule Officer', '2024-02-01 20:06:23'),
(2, 12, 'INACTIVE', 'Schedule Officer', '2024-02-02 09:46:54'),
(3, 9, 'ACTIVE', 'Schedule Officer', '2024-02-02 11:26:20');

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
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`sup_id`, `name`, `location`, `number`) VALUES
(61, 'Yakubu\'S Furniture', 'Tema', '0201111111'),
(60, 'Hp Services', 'Tema', '0200000001'),
(57, 'Banku Services', 'Teshie-Nungua', '0200034432'),
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
(54, 'Forever Construction', 'Free Pipe', '0200034123');

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
