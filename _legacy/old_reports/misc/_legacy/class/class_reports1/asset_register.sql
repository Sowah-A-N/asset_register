-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 10:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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

CREATE TABLE `admin_logs` (
  `table_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_role` varchar(20) NOT NULL,
  `user_password` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `assets` (
  `asset_id` int(11) NOT NULL,
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
  `current_year` int(11) NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT 0.00,
  `additions` decimal(20,2) NOT NULL,
  `disposals` int(11) NOT NULL DEFAULT 0,
  `disposed` int(11) NOT NULL DEFAULT 0,
  `active_res_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `dollar_rate_used` double NOT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposals`, `disposed`, `active_res_value`, `dollar_rate_used`, `date_added`) VALUES
(83, 'TOYOTA HILUX', '234567', 'AHTKB8CDX05791160', '9876543', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Henry Snow', '2020-03-29', 2025, 0.00, 749659.98, 0, 0, 0.00, 8, '2025-01-15 12:00:58'),
(84, 'Peaugot 3008', 'Ertyu0', 'VF3M45GYVPS012511', 'Ht56788', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2023-10-04', 2025, 0.00, 297500.00, 0, 0, 0.00, 8.5, '2025-01-17 13:52:23'),
(85, 'Peugeot 3008 ', '999999', 'VF3M45GYVPS012506', '99999999', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Henry Snow', '2023-10-04', 2025, 0.00, 297500.00, 0, 0, 0.00, 8.5, '2025-01-28 10:40:55'),
(86, 'Peugeot 3008', '00000', 'VF3M45GYVPS012509', '00000', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2023-10-04', 2025, 0.00, 297500.00, 0, 0, 0.00, 8.5, '2025-01-30 14:16:13'),
(87, 'Peugeot 3008', '00001', 'VF3M45GYVPS012514', '00001', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2024-01-29', 2025, 0.00, 322560.00, 0, 0, 0.00, 8.5, '2025-01-30 14:22:27'),
(88, 'Peugeot LandTrek', '000003', 'VR3FDAFDJN3016395', '00003', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Henry Snow', '2019-06-20', 2025, 0.00, 324000.00, 0, 0, 0.00, 9, '2025-01-30 14:29:42'),
(89, 'Peugeot LandTrek', '000005', 'VR3FDAFDJN3015640', '00005', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2023-06-20', 2025, 0.00, 324000.00, 1, 0, 0.00, 9, '2025-01-30 14:37:27'),
(90, 'Toyota Haice Highroof', '000007', 'JFTBB90P706057042', '00007', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2022-10-02', 2025, 0.00, 884041.97, 1, 0, 0.00, 10, '2025-01-30 14:44:18'),
(91, 'LINDE FORKLIFT HT30Ts-01', 'GJ84989', 'C1129M00015-HT30TS', '00000077', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Henry Snow', '2024-04-29', 2025, 0.00, 438840.00, 0, 0, 0.00, 9, '2025-02-04 11:46:47');

-- --------------------------------------------------------

--
-- Table structure for table `assets_archive`
--

CREATE TABLE `assets_archive` (
  `asset_id` int(11) NOT NULL,
  `asset_name` varchar(50) NOT NULL,
  `grv_number` varchar(7) DEFAULT 'N/A',
  `serial_number` varchar(20) DEFAULT 'N/A',
  `pv_number` varchar(9) DEFAULT 'N/A',
  `id_number` varchar(100) DEFAULT 'N/A',
  `supplier_name` varchar(250) NOT NULL,
  `asset_class` varchar(50) NOT NULL,
  `sub_class` varchar(254) NOT NULL,
  `asset_type` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `user` varchar(150) DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `current_year` int(11) NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT 0.00,
  `additions` decimal(20,2) NOT NULL,
  `disposals` decimal(10,2) NOT NULL DEFAULT 0.00,
  `active_res_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `dollar_rate_used` double NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_allocation`
--

CREATE TABLE `asset_allocation` (
  `t_id` int(11) NOT NULL,
  `staff_id` varchar(40) NOT NULL,
  `asset_sn_number` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes`
--

CREATE TABLE `asset_classes` (
  `ast_id` int(11) NOT NULL,
  `asset_class` varchar(50) NOT NULL,
  `account_depr_open_bal` decimal(30,2) NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `opbal_plus_additions` decimal(20,2) NOT NULL,
  `dep_rate` float NOT NULL,
  `estimated_life` int(11) NOT NULL,
  `estimated_life_months` int(11) GENERATED ALWAYS AS (`estimated_life` * 12) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_classes`
--

INSERT INTO `asset_classes` (`ast_id`, `asset_class`, `account_depr_open_bal`, `opening_bal`, `opbal_plus_additions`, `dep_rate`, `estimated_life`) VALUES
(1, 'Computers & Accessories', 1.00, 0.00, 0.00, 0.2, 5),
(2, 'Land And Buildings', 1.00, 0.00, 0.00, 0.02, 50),
(4, 'Motor Vehicles', 0.00, 149932.00, 4274859.97, 0.2, 5),
(10, 'Furnitures & Fixtures', 0.00, 0.00, 0.00, 0.1, 10),
(11, 'Office Equipment', 0.00, 0.00, 0.00, 0.2, 5),
(12, 'Hostel Equipment', 0.00, 0.00, 0.00, 0.2, 5),
(13, 'Hostel F&F', 0.00, 0.00, 0.00, 0.1, 10),
(14, 'Machines, Equipments & Tools', 0.00, 0.00, 0.00, 0.2, 5),
(16, 'Building Works in Progress', 0.00, 0.00, 0.00, 0.02, 50),
(19, 'Library Books', 0.00, 0.00, 0.00, 0.2, 5),
(21, 'Simulators', 0.00, 0.00, 0.00, 0.2, 5),
(25, 'Academic Gowns', 0.00, 0.00, 0.00, 0.2, 5),
(26, 'Teaching Equipment', 0.00, 0.00, 0.00, 0.2, 5),
(27, 'Fixture & Fittings', 0.00, 0.00, 0.00, 0.2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `asset_classes_archive`
--

CREATE TABLE `asset_classes_archive` (
  `ast_id` int(11) NOT NULL,
  `asset_class` varchar(50) NOT NULL,
  `opening_bal` decimal(10,2) NOT NULL,
  `dep_rate` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_class_opbal_year`
--

CREATE TABLE `asset_class_opbal_year` (
  `id` int(11) NOT NULL,
  `asset_class` varchar(255) DEFAULT NULL,
  `opening_balance` decimal(12,2) DEFAULT NULL,
  `total_accum_depr_start` decimal(12,2) DEFAULT 0.00,
  `total_depr_year_charge` decimal(12,2) DEFAULT 0.00,
  `total_accum_depr_end` decimal(12,2) GENERATED ALWAYS AS (`total_depr_year_charge` + `total_accum_depr_start` - `disposals_depr`) VIRTUAL,
  `disposals_depr` decimal(12,2) DEFAULT 0.00,
  `net_book_value` decimal(12,2) GENERATED ALWAYS AS (`opening_balance` - `total_accum_depr_end`) VIRTUAL,
  `year` year(4) DEFAULT NULL,
  `expected_life_months` int(11) DEFAULT 0,
  `rate` decimal(6,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_class_opbal_year`
--

INSERT INTO `asset_class_opbal_year` (`id`, `asset_class`, `opening_balance`, `total_accum_depr_start`, `total_depr_year_charge`, `disposals_depr`, `year`, `expected_life_months`, `rate`) VALUES
(1, 'Motor Vehicles', 2191771.68, 255056.00, 438354.34, 7044.00, '2023', 60, 8.00),
(8, 'Motor Vehicles', 2156551.68, 686366.34, 431310.34, 25162.20, '2024', 60, 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `asset_class_sub_classes`
--

CREATE TABLE `asset_class_sub_classes` (
  `T_id` int(11) NOT NULL,
  `sub_class` varchar(250) NOT NULL,
  `sub_class_code` varchar(250) NOT NULL,
  `asset_class` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_class_sub_classes`
--

INSERT INTO `asset_class_sub_classes` (`T_id`, `sub_class`, `sub_class_code`, `asset_class`) VALUES
(1, 'Test Sub Class', '', 'Land And Buildings');

-- --------------------------------------------------------

--
-- Table structure for table `asset_location`
--

CREATE TABLE `asset_location` (
  `loc_id` int(11) NOT NULL,
  `location` varchar(50) NOT NULL,
  `loc_code` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `asset_location_archive` (
  `loc_id` int(11) NOT NULL,
  `location` varchar(50) NOT NULL,
  `loc_code` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

CREATE TABLE `asset_type` (
  `type_id` int(11) NOT NULL,
  `asset_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `asset_users` (
  `staff_id` varchar(40) NOT NULL,
  `staff_first_name` varchar(25) DEFAULT NULL,
  `staff_last_name` varchar(25) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `t_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `calculations` (
  `table_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
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
  `current_lifetime` int(11) DEFAULT NULL,
  `unexpired_lifetime` int(11) DEFAULT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calculations`
--

INSERT INTO `calculations` (`table_id`, `asset_id`, `asset_name`, `asset_class`, `asset_type`, `location`, `acquisition_date`, `active_res_value`, `dollar_rate_used`, `date_added`, `asset_cost_opening_balance`, `asset_cost_closing_balance`, `accumulated_depr_opening_balance`, `depreciation_cost`, `total_accumulated_depreciation`, `account_depreciation_closing_balance`, `closing_carrying_value`, `current_lifetime`, `unexpired_lifetime`, `year`) VALUES
(293, 1, 'fire Training Works', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 91.00, 0.00, 1.00, 1.00, 1.00, 89.00, 3, 47, 2010),
(294, 2, 'overhead tank', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 19.00, 0.00, 398.80, 398.80, 398.80, 19.00, 3, 47, 2010),
(295, 3, 'RENOVATION', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 86.00, 0.00, 1.00, 1.00, 1.00, 84.00, 3, 47, 2010),
(296, 4, 'RENOVATION', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 91.00, 0.00, 1.00, 1.00, 1.00, 89.00, 3, 47, 2010),
(297, 5, 'RENOVATION', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 305.00, 0.00, 6.00, 6.00, 6.00, 299.00, 3, 47, 2010),
(298, 6, 'tiling Works', 'Land and Buildings', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 28.00, 0.00, 568.56, 568.56, 568.56, 27.00, 3, 47, 2010),
(299, 7, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 17.00, 0.00, 3.00, 3.00, 3.00, 13.00, 3, 2, 2010),
(300, 8, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 1.00, 0.00, 280.00, 280.00, 280.00, 1.00, 1, 4, 2010),
(301, 9, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 4.00, 0.00, 900.00, 900.00, 900.00, 3.00, 1, 4, 2010),
(302, 10, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 2.00, 0.00, 599.80, 599.80, 599.80, 2.00, 3, 2, 2010),
(303, 11, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 5.00, 0.00, 1.00, 1.00, 1.00, 4.00, 3, 2, 2010),
(304, 12, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 1.00, 0.00, 248.28, 248.28, 248.28, 993.11, 3, 2, 2010),
(305, 13, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 1.00, 0.00, 370.00, 370.00, 370.00, 1.00, 3, 2, 2010),
(306, 14, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 1.00, 0.00, 200.00, 200.00, 200.00, 800.00, 3, 2, 2010),
(307, 15, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 2.00, 0.00, 400.00, 400.00, 400.00, 1.00, 3, 2, 2010),
(308, 16, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 4.00, 0.00, 900.00, 900.00, 900.00, 3.00, 3, 2, 2010),
(309, 17, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 12.00, 0.00, 2.00, 2.00, 2.00, 9.00, 3, 2, 2010),
(310, 18, 'air conditioner', 'Office Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 6.00, 0.00, 1.00, 1.00, 1.00, 4.00, 3, 2, 2010),
(311, 19, 'electrical pump', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 5.00, 0.00, 0.00, 0.00, 0.00, 5.00, 3, -3, 2010),
(312, 20, 'electrical pump', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 39.00, 0.00, 0.00, 0.00, 0.00, 39.00, 3, -3, 2010),
(313, 21, 'generator', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 50.00, 0.00, 0.00, 0.00, 0.00, 50.00, 3, -3, 2010),
(314, 22, 'generator', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 17.00, 0.00, 0.00, 0.00, 0.00, 17.00, 3, -3, 2010),
(315, 23, 'mower', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 10.00, 0.00, 0.00, 0.00, 0.00, 10.00, 3, -3, 2010),
(316, 24, 'mower', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 18.00, 0.00, 0.00, 0.00, 0.00, 18.00, 3, -3, 2010),
(317, 25, 'pumping machine motor', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 34.00, 0.00, 0.00, 0.00, 0.00, 34.00, 3, -3, 2010),
(318, 26, 'shear wire cutter', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 5.00, 0.00, 0.00, 0.00, 0.00, 5.00, 3, -3, 2010),
(319, 27, 'universal water pump', 'Machines,Equipments &Tools', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 10.00, 0.00, 0.00, 0.00, 0.00, 10.00, 3, -3, 2010),
(320, 28, '16GB RAM', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 1.00, 0.00, 360.00, 360.00, 360.00, 1.00, 3, 2, 2010),
(321, 29, '8 PORT D LINK SWITCH', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 33.00, 0.00, 6.00, 6.00, 6.00, 26.00, 3, 2, 2010),
(322, 30, '9 PORT D LINK SWITCH', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 14.00, 0.00, 2.00, 2.00, 2.00, 11.00, 3, 2, 2010),
(323, 31, 'bridge simulator software', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 85.00, 0.00, 17.00, 17.00, 17.00, 68.00, 3, 2, 2010),
(324, 32, 'certificate', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 3.00, 0.00, 655.00, 655.00, 655.00, 2.00, 3, 2, 2010),
(325, 33, 'complete desktop computer', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 4.00, 0.00, 960.00, 960.00, 960.00, 3.00, 3, 2, 2010),
(326, 34, 'computer set', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 11.00, 0.00, 2.00, 2.00, 2.00, 8.00, 3, 2, 2010),
(327, 35, 'dell projector 1610HD', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 850.00, 0.00, 170.00, 170.00, 170.00, 680.00, 3, 2, 2010),
(328, 36, 'hp laserjet Pro 401DN', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 5.00, 0.00, 1.00, 1.00, 1.00, 4.00, 3, 2, 2010),
(329, 37, 'HP Scanner', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 3.00, 0.00, 683.06, 683.06, 683.06, 2.00, 3, 2, 2010),
(330, 38, 'HP Scanner', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 5.00, 0.00, 1.00, 1.00, 1.00, 4.00, 3, 2, 2010),
(331, 39, 'internet switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 3.00, 0.00, 727.90, 727.90, 727.90, 2.00, 3, 2, 2010),
(332, 40, 'internet switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 14.00, 0.00, 2.00, 2.00, 2.00, 11.00, 3, 2, 2010),
(333, 41, 'laptop computer', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 15.00, 0.00, 3.00, 3.00, 3.00, 12.00, 3, 2, 2010),
(334, 42, 'laptop computer', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 6.00, 0.00, 1.00, 1.00, 1.00, 5.00, 3, 2, 2010),
(335, 43, 'LCD EPSON 2800 PROJECTOR', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 50.00, 0.00, 10.00, 10.00, 10.00, 40.00, 3, 2, 2010),
(336, 44, 'network housing', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 4.00, 0.00, 981.76, 981.76, 981.76, 3.00, 3, 2, 2010),
(337, 45, 'projector', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 7.00, 0.00, 1.00, 1.00, 1.00, 5.00, 3, 2, 2010),
(338, 46, 'projector', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 9.00, 0.00, 1.00, 1.00, 1.00, 7.00, 3, 2, 2010),
(339, 47, 'switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 43.00, 0.00, 8.00, 8.00, 8.00, 34.00, 3, 2, 2010),
(340, 48, 'switch', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 35.00, 0.00, 7.00, 7.00, 7.00, 28.00, 3, 2, 2010),
(341, 49, 'system unit', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 1.00, 0.00, 360.00, 360.00, 360.00, 1.00, 3, 2, 2010),
(342, 50, 'system unit', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 5.00, 0.00, 1.00, 1.00, 1.00, 4.00, 3, 2, 2010),
(343, 51, 'ups', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 24.00, 0.00, 4.00, 4.00, 4.00, 19.00, 3, 2, 2010),
(344, 52, 'ups', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 9.00, 0.00, 1.00, 1.00, 1.00, 7.00, 3, 2, 2010),
(345, 53, 'ups 1000VA', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 1.00, 0.00, 275.00, 275.00, 275.00, 1.00, 3, 2, 2010),
(346, 54, 'ups 1000VA', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 3.00, 0.00, 680.00, 680.00, 680.00, 2.00, 3, 2, 2010),
(347, 55, 'visual Fault Locator', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 15.00, 0.00, 3.00, 3.00, 3.00, 12.00, 3, 2, 2010),
(348, 56, 'Windows Pro Software 20 User License', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 7.00, 0.00, 1.00, 1.00, 1.00, 6.00, 3, 2, 2010),
(349, 57, 'Windows Pro Software 20 User License', 'Computers & Accessories', 'owned', 'Registry Records', '0000-00-00', 0.00, 6.00, '0000-00-00', 0.00, 38.00, 0.00, 7.00, 7.00, 7.00, 30.00, 3, 2, 2010),
(350, 58, '3 IN 1 VISITORS CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 1.00, 0.00, 171.72, 171.72, 171.72, 1.00, 3, 7, 2010),
(351, 59, '6 DRAWER DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 2.00, 0.00, 240.00, 240.00, 240.00, 2.00, 3, 7, 2010),
(352, 60, 'CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 8.00, 0.00, 833.56, 833.56, 833.56, 7.00, 3, 7, 2010),
(353, 61, 'CONFERENCE TABLE & CHAIRS', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 3.00, 0.00, 320.00, 320.00, 320.00, 2.00, 3, 7, 2010),
(354, 62, 'CURTAIN', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 5.00, 0.00, 509.40, 509.40, 509.40, 4.00, 3, 7, 2010),
(355, 63, 'CURTAIN', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 7.00, 0.00, 710.00, 710.00, 710.00, 6.00, 3, 7, 2010),
(356, 64, 'CURTAIN', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 3.00, 0.00, 349.40, 349.40, 349.40, 3.00, 3, 7, 2010),
(357, 65, 'EXECUTIVE SWIVEL CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 6.00, 0.00, 600.00, 600.00, 600.00, 5.00, 3, 7, 2010),
(358, 66, 'EXECUTIVE SWIVEL CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 4.00, 0.00, 416.00, 416.00, 416.00, 3.00, 3, 7, 2010),
(359, 67, 'EXECUTIVE WRITING DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 9.00, 0.00, 975.00, 975.00, 975.00, 8.00, 3, 7, 2010),
(360, 68, 'EXECUTIVE WRITING DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 8.00, 0.00, 884.00, 884.00, 884.00, 7.00, 3, 7, 2010),
(361, 69, 'L SHAPED DESK', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 6.00, 0.00, 650.00, 650.00, 650.00, 5.00, 3, 7, 2010),
(362, 70, 'LECTURE HALL METAL DESKS', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 60.00, 0.00, 6.00, 6.00, 6.00, 54.00, 3, 7, 2010),
(363, 71, 'SECRETARY SWIVEL CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 4.00, 0.00, 457.60, 457.60, 457.60, 4.00, 3, 7, 2010),
(364, 72, 'SUGGESTION BOX', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 1.00, 0.00, 140.00, 140.00, 140.00, 1.00, 3, 7, 2010),
(365, 73, 'TABLE', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 15.00, 0.00, 1.00, 1.00, 1.00, 13.00, 3, 7, 2010),
(366, 74, 'WAITING CHAIR', 'Furnitures & Fixtures', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 1.00, 0.00, 171.72, 171.72, 171.72, 1.00, 3, 7, 2010),
(367, 75, 'GOWNS', 'Academic Gowns', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 59.00, 0.00, 11.00, 11.00, 11.00, 47.00, 3, 2, 2010),
(368, 76, 'HIGH EXPANSION FOAM GENERATIOR', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 63.00, 0.00, 12.00, 12.00, 12.00, 50.00, 3, 2, 2010),
(369, 77, 'LIFEJACKET', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 116.00, 0.00, 23.00, 23.00, 23.00, 93.00, 3, 2, 2010),
(370, 78, 'STAINLESS STEEL TRAY', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 41.00, 0.00, 8.00, 8.00, 8.00, 32.00, 3, 2, 2010),
(371, 79, 'TECQUIPMENT', 'Teaching Equipment', 'owned', 'Registry Records', '0000-00-00', 0.00, 7.00, '0000-00-00', 0.00, 71.00, 0.00, 14.00, 14.00, 14.00, 57.00, 3, 2, 2010),
(372, 84, 'Peaugot 3008', 'Motor Vehicles', 'Owned', 'ICT LAB', '0000-00-00', 0.00, 8.00, '0000-00-00', 0.00, 350.00, 0.00, 70.00, 70.00, 70.00, 280.00, 3, 2, 2010),
(373, 83, 'TOYOTA HILUX', 'Motor Vehicles', 'Owned', 'Registry Records', '0000-00-00', 0.00, 8.00, '0000-00-00', 0.00, 749.00, 0.00, 149.00, 149.00, 149.00, 599.00, 4, 1, 2010),
(374, 85, 'Peugeot 3008 1', 'Motor Vehicles', 'Owned', 'Registry Records', '0000-00-00', 0.00, 8.00, '0000-00-00', 0.00, 535.00, 0.00, 107.00, 107.00, 107.00, 428.00, 2, 3, 2010);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `t_id` int(11) NOT NULL,
  `dep_id` varchar(37) NOT NULL,
  `dep_name` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`t_id`, `dep_id`, `dep_name`) VALUES
(1, 'DEP001', 'ICT');

-- --------------------------------------------------------

--
-- Table structure for table `disposals`
--

CREATE TABLE `disposals` (
  `asset_id` int(11) NOT NULL,
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
  `current_year` int(11) NOT NULL,
  `historical_cost` decimal(20,2) NOT NULL DEFAULT 0.00,
  `additions` decimal(20,2) NOT NULL,
  `disposal_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `active_res_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `dollar_rate_used` double NOT NULL,
  `date_of_disposal` timestamp NULL DEFAULT current_timestamp(),
  `disposal_month` int(11) DEFAULT 0,
  `estimated_life_months` int(11) DEFAULT 0,
  `disposal_depreciation` decimal(12,2) GENERATED ALWAYS AS (`additions` / `estimated_life_months` * `disposal_month`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disposals`
--

INSERT INTO `disposals` (`asset_id`, `asset_name`, `grv_number`, `serial_number`, `pv_number`, `id_number`, `supplier_name`, `asset_class`, `sub_class`, `asset_type`, `location`, `user`, `acquisition_date`, `current_year`, `historical_cost`, `additions`, `disposal_value`, `active_res_value`, `dollar_rate_used`, `date_of_disposal`, `disposal_month`, `estimated_life_months`) VALUES
(1, 'Toyota Haice Highroof', '000007', 'JFTBB90P706057042', '00007', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2022-10-02', 2025, 0.00, 884047.97, 35946.00, 0.00, 10, '2024-12-16 19:33:50', 12, 60),
(2, 'Peugeot LandTrek', '000005', 'VR3FDAFDJN3015640', '00005', 'RMU///0/25', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2023-08-07', 2025, 0.00, 535680.00, 35946.00, 0.00, 9, '2024-09-16 19:56:47', 9, 60),
(3, 'Toyota Haice Highroof', '000007', 'JFTBB90P706057041', '00007', 'RMU///0/23', 'Yakubu Furnitures', 'Motor Vehicles', 'Test Sub Class', 'Owned', 'TRANSPORT DEPT', 'Ismail Abdulai-Saiku', '2022-10-02', 2025, 0.00, 884047.97, 35220.00, 0.00, 10, '2023-12-16 19:33:50', 7, 60);

-- --------------------------------------------------------

--
-- Table structure for table `dollar_rate`
--

CREATE TABLE `dollar_rate` (
  `table_id` int(11) NOT NULL,
  `dollar_rate` double NOT NULL,
  `rate_status` varchar(20) NOT NULL,
  `action_by` varchar(20) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `moved_assets` (
  `t_id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `notes` text NOT NULL,
  `old_location` varchar(100) NOT NULL,
  `old_user` varchar(100) NOT NULL,
  `New_location` varchar(100) NOT NULL,
  `new_user` varchar(100) NOT NULL,
  `date_of_action` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `other_values` (
  `acc_depr_opening_bal` decimal(20,2) NOT NULL,
  `s/n` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `sup_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `location` varchar(250) NOT NULL,
  `number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`sup_id`, `name`, `location`, `number`) VALUES
(1, 'Yakubu Furnitures', 'Tema', '0200034432');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers_archive`
--

CREATE TABLE `suppliers_archive` (
  `sup_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `location` varchar(250) NOT NULL,
  `number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`),
  ADD KEY `asset_class` (`asset_class`),
  ADD KEY `asset_type` (`asset_type`),
  ADD KEY `location` (`location`);

--
-- Indexes for table `assets_archive`
--
ALTER TABLE `assets_archive`
  ADD PRIMARY KEY (`asset_id`),
  ADD KEY `asset_class` (`asset_class`),
  ADD KEY `asset_type` (`asset_type`),
  ADD KEY `location` (`location`);

--
-- Indexes for table `asset_allocation`
--
ALTER TABLE `asset_allocation`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `asset_classes`
--
ALTER TABLE `asset_classes`
  ADD PRIMARY KEY (`ast_id`);

--
-- Indexes for table `asset_classes_archive`
--
ALTER TABLE `asset_classes_archive`
  ADD PRIMARY KEY (`ast_id`);

--
-- Indexes for table `asset_class_opbal_year`
--
ALTER TABLE `asset_class_opbal_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_class_sub_classes`
--
ALTER TABLE `asset_class_sub_classes`
  ADD PRIMARY KEY (`T_id`);

--
-- Indexes for table `asset_location`
--
ALTER TABLE `asset_location`
  ADD PRIMARY KEY (`loc_id`);

--
-- Indexes for table `asset_location_archive`
--
ALTER TABLE `asset_location_archive`
  ADD PRIMARY KEY (`loc_id`);

--
-- Indexes for table `asset_type`
--
ALTER TABLE `asset_type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `asset_users`
--
ALTER TABLE `asset_users`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `calculations`
--
ALTER TABLE `calculations`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `disposals`
--
ALTER TABLE `disposals`
  ADD PRIMARY KEY (`asset_id`),
  ADD KEY `asset_class` (`asset_class`),
  ADD KEY `asset_type` (`asset_type`),
  ADD KEY `location` (`location`);

--
-- Indexes for table `dollar_rate`
--
ALTER TABLE `dollar_rate`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `moved_assets`
--
ALTER TABLE `moved_assets`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `other_values`
--
ALTER TABLE `other_values`
  ADD PRIMARY KEY (`s/n`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`sup_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `number` (`number`);

--
-- Indexes for table `suppliers_archive`
--
ALTER TABLE `suppliers_archive`
  ADD PRIMARY KEY (`sup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `assets_archive`
--
ALTER TABLE `assets_archive`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_allocation`
--
ALTER TABLE `asset_allocation`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_classes`
--
ALTER TABLE `asset_classes`
  MODIFY `ast_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `asset_classes_archive`
--
ALTER TABLE `asset_classes_archive`
  MODIFY `ast_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_class_opbal_year`
--
ALTER TABLE `asset_class_opbal_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `asset_class_sub_classes`
--
ALTER TABLE `asset_class_sub_classes`
  MODIFY `T_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `asset_location`
--
ALTER TABLE `asset_location`
  MODIFY `loc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_location_archive`
--
ALTER TABLE `asset_location_archive`
  MODIFY `loc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_type`
--
ALTER TABLE `asset_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `asset_users`
--
ALTER TABLE `asset_users`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `calculations`
--
ALTER TABLE `calculations`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=375;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `disposals`
--
ALTER TABLE `disposals`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dollar_rate`
--
ALTER TABLE `dollar_rate`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `moved_assets`
--
ALTER TABLE `moved_assets`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `other_values`
--
ALTER TABLE `other_values`
  MODIFY `s/n` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `sup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers_archive`
--
ALTER TABLE `suppliers_archive`
  MODIFY `sup_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
