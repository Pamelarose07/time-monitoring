-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2025 at 05:44 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `time_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `v_record`
--

CREATE TABLE `v_record` (
  `Userid` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `CheckTime` datetime DEFAULT NULL,
  `StatusText` varchar(255) DEFAULT NULL,
  `Sensorid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `v_record`
--

INSERT INTO `v_record` (`Userid`, `Name`, `CheckTime`, `StatusText`, `Sensorid`) VALUES
(1, 'John Doe', '2024-11-23 08:00:00', 'In', 1001),
(2, 'Jane Smith', '2024-11-23 08:15:00', 'Out', 1002);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
