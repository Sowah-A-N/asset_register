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
-- Table structure for table `asset_location`
--

DROP TABLE IF EXISTS `asset_location`;
CREATE TABLE IF NOT EXISTS `asset_location` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset_location`
--

INSERT INTO `asset_location` (`loc_id`, `location`) VALUES
(1, 'Transport Department'),
(2, 'Cadet mess'),
(3, 'Cadet Accommodation'),
(4, 'Student Accommodation'),
(5, 'Sickbay'),
(6, 'Lab Complex'),
(7, 'Nautical Science Block'),
(8, 'MEE Block'),
(9, 'Office Complex'),
(10, 'Research Library'),
(11, 'Administration'),
(12, 'Stores Unit'),
(13, 'Safety Department'),
(14, 'Library '),
(15, 'ICT Lab 1'),
(16, 'ICT Lab 2'),
(17, 'DSU'),
(18, 'Bridge Simulator Room'),
(19, 'Registry ');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
