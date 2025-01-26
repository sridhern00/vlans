-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2024 at 03:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vlan_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `vlans`
--

CREATE TABLE `vlans` (
  `vlan_id` int(11) NOT NULL,
  `subnet` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `static_ip` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vlans`
--

INSERT INTO `vlans` (`vlan_id`, `subnet`, `description`, `location`, `static_ip`) VALUES
(1, '192.168.1.0/24', 'Main Office Network', 'chennai', '192.168.1.10\n192.168.121.0=Admin printer\n192.168.121.0'),
(2, '192.168.2.0/24', 'Guest Network', 'bangalore', '192.168.2.10\n192.168.121.0/24'),
(3, '192.168.3.0/24', 'Development Network', 'pune', '192.168.3.10\n192.168.3.20\n192.168.3.44'),
(4, '192.168.4.0/24', 'Production Network', 'singapore', '192.168.4.20\n192.168.4.30\n192.168.121.55'),
(55, '192.56.23.0/24', 'qd', 'chennai', NULL),
(400, '192.56.23.0/24', 'Example vlan', 'chennai', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vlans`
--
ALTER TABLE `vlans`
  ADD PRIMARY KEY (`vlan_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
