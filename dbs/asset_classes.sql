-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 13, 2023 at 09:46 PM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

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
-- Table structure for table `asset_classes`
--

DROP TABLE IF EXISTS `asset_classes`;
CREATE TABLE IF NOT EXISTS `asset_classes` (
  `ast_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_class` varchar(50) NOT NULL,
  PRIMARY KEY (`ast_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_classes`
--

INSERT INTO `asset_classes` (`ast_id`, `asset_class`) VALUES
(1, 'Land and Buildings'),
(2, 'Plant and Equipment'),
(3, 'Computer and Accessories'),
(4, 'Office Equipment'),
(8, 'Motor Vehicles'),
(7, 'Office Furniture'),
(9, 'Classroom Furniture'),
(10, 'Teaching Equipment');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
