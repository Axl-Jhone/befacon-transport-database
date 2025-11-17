-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 05:30 PM
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
-- Database: `befacon`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_data`
--

CREATE TABLE `access_data` (
  `access_id` int(11) NOT NULL,
  `access_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_data`
--

INSERT INTO `access_data` (`access_id`, `access_type`) VALUES
(2, 'Private'),
(1, 'Public');

-- --------------------------------------------------------

--
-- Table structure for table `costing_rules`
--

CREATE TABLE `costing_rules` (
  `cost_rule_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `access_id` int(11) NOT NULL,
  `purpose_id` int(11) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `fee_per_km` decimal(10,2) NOT NULL,
  `until_year_validity` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `costing_rules`
--

INSERT INTO `costing_rules` (`cost_rule_id`, `vehicle_type_id`, `access_id`, `purpose_id`, `base_price`, `fee_per_km`, `until_year_validity`) VALUES
(1, 1, 1, 1, 50.00, 5.00, '2027'),
(2, 1, 2, 2, 60.00, 4.50, '2025'),
(3, 1, 2, 3, 65.00, 4.00, '2026'),
(4, 1, 1, 4, 55.00, 5.50, '2027'),
(5, 2, 1, 1, 30.00, 3.50, '2028'),
(6, 2, 2, 2, 35.00, 3.25, '2025'),
(7, 2, 2, 3, 40.00, 3.00, '2026'),
(8, 2, 1, 4, 32.00, 3.75, '2029'),
(9, 3, 1, 1, 45.00, 4.50, '2028'),
(10, 3, 2, 2, 50.00, 4.00, '2027'),
(11, 3, 2, 3, 55.00, 3.75, '2025'),
(12, 3, 1, 4, 48.00, 4.25, '2025'),
(13, 4, 1, 1, 40.00, 4.00, '2026'),
(14, 4, 2, 2, 45.00, 3.75, '2026'),
(15, 4, 2, 3, 50.00, 3.50, '2027'),
(16, 4, 1, 4, 42.00, 4.25, '2025'),
(17, 1, 1, 2, 58.00, 5.00, '2025'),
(18, 2, 2, 4, 38.00, 3.25, '2029'),
(19, 3, 1, 2, 52.00, 4.25, '2028'),
(20, 4, 2, 1, 48.00, 3.75, '2025');

-- --------------------------------------------------------

--
-- Table structure for table `driver_info`
--

CREATE TABLE `driver_info` (
  `driver_id` int(11) NOT NULL,
  `driver_lname` varchar(100) NOT NULL,
  `driver_fname` varchar(100) NOT NULL,
  `driver_middleinitial` varchar(5) DEFAULT NULL,
  `driver_sex` char(1) NOT NULL,
  `birthdate` date NOT NULL,
  `contact_no` varchar(13) NOT NULL,
  `driver_status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_info`
--

INSERT INTO `driver_info` (`driver_id`, `driver_lname`, `driver_fname`, `driver_middleinitial`, `driver_sex`, `birthdate`, `contact_no`, `driver_status_id`) VALUES
(1, 'De La Cruz', 'Joan', 'M', 'F', '1990-05-15', '09123456789', 1),
(2, 'Rizal', 'Jose', 'P', 'M', '1861-06-19', '09090909090', 2),
(3, 'Bonifacio', 'Andres', 'C', 'M', '1863-11-30', '09999999999', 3),
(4, 'Aguinaldo', 'Emilio', NULL, 'M', '1869-03-22', '09000000000', 4),
(5, 'Santos', 'Maria', 'L', 'F', '1990-01-15', '09112233344', 2),
(6, 'Reyes', 'Juan', 'R', 'M', '1985-03-08', '09223334444', 4),
(7, 'Dela Rosa', 'Kristine', 'A', 'F', '1992-07-22', '09444455555', 1),
(8, 'Mendoza', 'Carlo', 'B', 'M', '1988-05-30', '09445556667', 1),
(9, 'Cruz', 'Angelica', 'S', 'F', '1995-12-12', '09556667778', 3),
(10, 'Villanueva', 'Miguel', 'D', 'M', '1980-09-10', '09667778889', 4),
(11, 'Castillo', 'Theresa', 'G', 'F', '1998-11-18', '09778889990', 2),
(12, 'Velasco', 'Mark', 'H', 'M', '1991-02-25', '09889990111', 2),
(13, 'Flores', 'Katrina', 'J', 'F', '1993-06-06', '09990001122', 1),
(14, 'Navarro', 'Anthony', 'K', 'M', '1987-10-03', '09110112233', 2),
(15, 'Aquino', 'Jessica', 'M', 'F', '1994-08-15', '09221123344', 4),
(16, 'Gonzales', 'Paolo', 'N', 'M', '1990-01-29', '09218983553', 1),
(17, 'Fernandez', 'Camille', 'O', 'F', '1996-03-14', '09353455432', 1),
(18, 'Soriano', 'Luis', 'P', 'M', '1983-07-07', '09963452271', 3),
(19, 'Caballero', 'Angel', 'Q', 'M', '1992-12-25', '09217743212', 1),
(20, 'Bautista', 'Ramon', 'R', 'M', '1989-05-05', '09778899001', 1);

-- --------------------------------------------------------

--
-- Table structure for table `driver_status_data`
--

CREATE TABLE `driver_status_data` (
  `driver_status_id` int(11) NOT NULL,
  `driver_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_status_data`
--

INSERT INTO `driver_status_data` (`driver_status_id`, `driver_status`) VALUES
(1, 'Available'),
(3, 'On Leave'),
(2, 'On Trip'),
(5, 'Suspended'),
(4, 'Unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `purpose_data`
--

CREATE TABLE `purpose_data` (
  `purpose_id` int(11) NOT NULL,
  `purpose` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purpose_data`
--

INSERT INTO `purpose_data` (`purpose_id`, `purpose`) VALUES
(2, 'Food Delivery'),
(3, 'Package Delivery'),
(4, 'Personnel Transport'),
(1, 'PUV');

-- --------------------------------------------------------

--
-- Table structure for table `trip_cost_info`
--

CREATE TABLE `trip_cost_info` (
  `trip_id` int(11) NOT NULL,
  `cost_rule_id` int(11) NOT NULL,
  `distance_km` decimal(6,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_cost_info`
--

INSERT INTO `trip_cost_info` (`trip_id`, `cost_rule_id`, `distance_km`, `total_cost`) VALUES
(1, 1, 300.00, 1550.00),
(2, 2, 350.00, 1627.50),
(3, 3, 180.00, 1370.00),
(4, 4, 90.00, 550.00),
(5, 5, 60.00, 240.00),
(6, 6, 300.00, 1275.00),
(7, 7, 120.00, 400.00),
(8, 8, 450.00, 1768.75),
(9, 9, 220.00, 1050.00),
(10, 10, 300.00, 1250.00),
(11, 11, 200.00, 830.00),
(12, 12, 250.00, 1113.75),
(13, 13, 350.00, 1460.00),
(14, 14, 100.00, 420.00),
(15, 15, 80.00, 330.00),
(16, 16, 120.00, 510.00),
(17, 17, 400.00, 2580.00),
(18, 18, 220.00, 715.00),
(19, 19, 180.00, 1020.00),
(20, 20, 300.00, 1575.00);

-- --------------------------------------------------------

--
-- Table structure for table `trip_info`
--

CREATE TABLE `trip_info` (
  `trip_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `purpose_id` int(11) NOT NULL,
  `departure_date_time` datetime NOT NULL,
  `arrival_date_time` datetime NOT NULL,
  `trip_status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_info`
--

INSERT INTO `trip_info` (`trip_id`, `driver_id`, `vehicle_id`, `purpose_id`, `departure_date_time`, `arrival_date_time`, `trip_status_id`) VALUES
(1, 1, 1, 1, '2025-08-18 10:05:00', '2025-08-20 09:50:00', 2),
(2, 2, 2, 2, '2025-08-20 14:20:00', '2025-08-20 22:00:00', 2),
(3, 3, 3, 3, '2025-08-20 14:30:00', '2025-08-20 20:25:00', 1),
(4, 4, 4, 4, '2025-08-21 09:05:00', '2025-08-21 11:35:00', 1),
(5, 5, 5, 1, '2025-08-21 13:10:00', '2025-08-21 14:40:00', 1),
(6, 6, 6, 2, '2025-08-21 22:10:00', '2025-08-22 03:55:00', 2),
(7, 7, 7, 3, '2025-08-22 05:40:00', '2025-08-22 07:55:00', 1),
(8, 8, 8, 4, '2025-08-22 21:10:00', '2025-08-23 05:25:00', 2),
(9, 9, 9, 1, '2025-08-23 09:10:00', '2025-08-23 11:20:00', 1),
(10, 10, 10, 2, '2025-08-23 06:10:00', '2025-08-23 09:20:00', 1),
(11, 11, 11, 3, '2025-08-23 13:10:00', '2025-08-23 17:25:00', 2),
(12, 12, 12, 4, '2025-08-24 07:10:00', '2025-08-24 10:05:00', 1),
(13, 13, 13, 1, '2025-08-24 13:35:00', '2025-08-24 18:40:00', 2),
(14, 14, 14, 2, '2025-08-24 17:05:00', '2025-08-24 19:25:00', 1),
(15, 15, 15, 3, '2025-08-25 06:05:00', '2025-08-25 09:25:00', 2),
(16, 16, 16, 4, '2025-08-25 10:05:00', '2025-08-25 12:55:00', 1),
(17, 17, 17, 1, '2025-08-25 15:05:00', '2025-08-25 17:55:00', 1),
(18, 18, 18, 2, '2025-08-26 05:05:00', '2025-08-26 08:25:00', 1),
(19, 19, 19, 3, '2025-08-26 12:05:00', '2025-08-26 15:10:00', 2),
(20, 20, 20, 4, '2025-08-26 21:10:00', '2025-08-27 05:20:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `trip_schedule_info`
--

CREATE TABLE `trip_schedule_info` (
  `trip_id` int(11) NOT NULL,
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `sched_depart_datetime` datetime NOT NULL,
  `sched_arrival_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_schedule_info`
--

INSERT INTO `trip_schedule_info` (`trip_id`, `origin`, `destination`, `sched_depart_datetime`, `sched_arrival_datetime`) VALUES
(1, 'Baguio', 'Cubao', '2025-08-18 10:00:00', '2025-08-18 18:00:00'),
(2, 'Ilocos Sur', 'Baguio', '2025-08-19 08:00:00', '2025-08-19 16:00:00'),
(3, 'Dagupan', 'Vigan', '2025-08-20 07:30:00', '2025-08-20 12:30:00'),
(4, 'Manila', 'Pampanga', '2025-08-21 09:00:00', '2025-08-21 11:30:00'),
(5, 'Pampanga', 'Tarlac', '2025-08-21 13:00:00', '2025-08-21 14:30:00'),
(6, 'Cubao', 'Baguio', '2025-08-21 22:00:00', '2025-08-22 04:00:00'),
(7, 'Batangas City', 'Manila', '2025-08-22 05:30:00', '2025-08-22 08:00:00'),
(8, 'Manila', 'Naga City', '2025-08-22 21:00:00', '2025-08-23 05:30:00'),
(9, 'Laoag', 'Vigan', '2025-08-23 09:00:00', '2025-08-23 11:30:00'),
(10, 'Tuguegarao', 'Santiago City', '2025-08-23 06:00:00', '2025-08-23 09:15:00'),
(11, 'Vigan', 'Dagupan', '2025-08-23 13:00:00', '2025-08-23 17:30:00'),
(12, 'Alaminos', 'Baguio', '2025-08-24 07:00:00', '2025-08-24 10:00:00'),
(13, 'Baguio', 'Laoag', '2025-08-24 13:30:00', '2025-08-24 18:45:00'),
(14, 'Tarlac', 'Quezon City', '2025-08-24 17:00:00', '2025-08-24 19:30:00'),
(15, 'Lucena', 'Manila', '2025-08-25 06:00:00', '2025-08-25 09:30:00'),
(16, 'Naga City', 'Legazpi City', '2025-08-25 10:00:00', '2025-08-25 13:00:00'),
(17, 'Legazpi City', 'Naga City', '2025-08-25 15:00:00', '2025-08-25 18:00:00'),
(18, 'Quezon City', 'Batangas City', '2025-08-26 05:00:00', '2025-08-26 08:30:00'),
(19, 'Baguio', 'Dagupan', '2025-08-26 12:00:00', '2025-08-26 15:00:00'),
(20, 'Vigan', 'Manila', '2025-08-26 21:00:00', '2025-08-27 05:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `trip_status_data`
--

CREATE TABLE `trip_status_data` (
  `trip_status_id` int(11) NOT NULL,
  `trip_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_status_data`
--

INSERT INTO `trip_status_data` (`trip_status_id`, `trip_status`) VALUES
(1, 'Arrived'),
(4, 'Cancelled'),
(2, 'In Transit'),
(3, 'Waiting');

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `User_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `passcode` varchar(50) NOT NULL,
  `role` enum('admin','driver') NOT NULL,
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`User_id`, `username`, `passcode`, `role`, `driver_id`) VALUES
(1, 'Admin1', 'AdMin1', 'admin', NULL),
(2, 'joandelacruz1', 'driver123', 'driver', 1),
(3, 'joserizal2', 'driver123', 'driver', 2),
(4, 'andresbonifacio3', 'driver123', 'driver', 3),
(5, 'emilioaguinaldo4', 'driver123', 'driver', 4),
(6, 'mariasantos5', 'driver123', 'driver', 5),
(7, 'juanreyes6', 'driver123', 'driver', 6),
(8, 'kristinedelarosa7', 'driver123', 'driver', 7),
(9, 'carlomendoza8', 'driver123', 'driver', 8),
(10, 'angelicacruz9', 'driver123', 'driver', 9),
(11, 'miguelvillanueva10', 'driver123', 'driver', 10),
(12, 'theresacastillo11', 'driver123', 'driver', 11),
(13, 'markvelasco12', 'driver123', 'driver', 12),
(14, 'katrinaflores13', 'driver123', 'driver', 13),
(15, 'anthonynavarro14', 'driver123', 'driver', 14),
(16, 'jessicaaquino15', 'driver123', 'driver', 15),
(17, 'paologonzales16', 'driver123', 'driver', 16),
(18, 'camillefernandez17', 'driver123', 'driver', 17),
(19, 'luissoriano18', 'driver123', 'driver', 18),
(20, 'angelcaballero19', 'driver123', 'driver', 19),
(21, 'ramonbautista20', 'driver123', 'driver', 20);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_condition_data`
--

CREATE TABLE `vehicle_condition_data` (
  `vehicle_condition_id` int(11) NOT NULL,
  `vehicle_condition` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_condition_data`
--

INSERT INTO `vehicle_condition_data` (`vehicle_condition_id`, `vehicle_condition`) VALUES
(1, 'Good'),
(3, 'In Repair'),
(2, 'Needs Repair'),
(4, 'To Discard');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_info`
--

CREATE TABLE `vehicle_info` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `plate_no` varchar(8) NOT NULL,
  `vehicle_condition_id` int(11) NOT NULL,
  `access_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_info`
--

INSERT INTO `vehicle_info` (`vehicle_id`, `vehicle_type_id`, `plate_no`, `vehicle_condition_id`, `access_id`) VALUES
(1, 1, 'BZA4601', 1, 2),
(2, 4, 'XYZ2789', 1, 2),
(3, 3, 'JPN6543', 2, 1),
(4, 2, 'MOT432', 4, 1),
(5, 2, 'MPF119', 3, 2),
(6, 1, 'BXY4281', 3, 1),
(7, 1, 'QTR1569', 2, 2),
(8, 2, 'MTR729', 4, 1),
(9, 2, 'KLM481', 1, 2),
(10, 3, 'JPN3842', 2, 1),
(11, 3, 'YUZ9051', 3, 1),
(12, 4, 'ABC1284', 1, 1),
(13, 4, 'XYZ7391', 4, 2),
(14, 1, 'BUS3510', 2, 1),
(15, 2, 'MTR462', 3, 2),
(16, 3, 'JPN1847', 1, 1),
(17, 4, 'DEF5902', 2, 1),
(18, 4, 'GHI6093', 1, 1),
(19, 2, 'MTR317', 3, 2),
(20, 4, 'CAR4826', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_location_info`
--

CREATE TABLE `vehicle_location_info` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_status_id` int(11) NOT NULL,
  `current_location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_location_info`
--

INSERT INTO `vehicle_location_info` (`vehicle_id`, `vehicle_status_id`, `current_location`) VALUES
(1, 1, 'Baguio'),
(2, 2, 'Manila'),
(3, 3, 'Dagupan'),
(4, 4, 'Pampanga'),
(5, 5, 'Tarlac'),
(6, 6, 'Cubao'),
(7, 1, 'Quezon City'),
(8, 2, 'Naga City'),
(9, 3, 'Laoag'),
(10, 4, 'Tuguegarao'),
(11, 5, 'Vigan'),
(12, 6, 'Alaminos'),
(13, 7, 'Baguio'),
(14, 1, 'Tarlac'),
(15, 2, 'Lucena'),
(16, 3, 'Naga City'),
(17, 4, 'Legazpi City'),
(18, 5, 'Quezon City'),
(19, 6, 'Baguio'),
(20, 7, 'Vigan');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_status_data`
--

CREATE TABLE `vehicle_status_data` (
  `vehicle_status_id` int(11) NOT NULL,
  `vehicle_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_status_data`
--

INSERT INTO `vehicle_status_data` (`vehicle_status_id`, `vehicle_status`) VALUES
(1, 'Available'),
(6, 'Coding'),
(7, 'Discarded'),
(4, 'In Repair'),
(5, 'Maintenance'),
(2, 'On Trip'),
(3, 'Reserved');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_type_data`
--

CREATE TABLE `vehicle_type_data` (
  `vehicle_type_id` int(11) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_type_data`
--

INSERT INTO `vehicle_type_data` (`vehicle_type_id`, `vehicle_type`) VALUES
(1, 'Bus'),
(4, 'Car'),
(3, 'Jeepney'),
(2, 'Motor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_data`
--
ALTER TABLE `access_data`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `access_id` (`access_id`),
  ADD UNIQUE KEY `access_type` (`access_type`);

--
-- Indexes for table `costing_rules`
--
ALTER TABLE `costing_rules`
  ADD PRIMARY KEY (`cost_rule_id`),
  ADD UNIQUE KEY `cost_rule_id` (`cost_rule_id`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`),
  ADD KEY `access_id` (`access_id`),
  ADD KEY `purpose_id` (`purpose_id`);

--
-- Indexes for table `driver_info`
--
ALTER TABLE `driver_info`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `driver_id` (`driver_id`),
  ADD KEY `driver_status_id` (`driver_status_id`);

--
-- Indexes for table `driver_status_data`
--
ALTER TABLE `driver_status_data`
  ADD PRIMARY KEY (`driver_status_id`),
  ADD UNIQUE KEY `driver_status_id` (`driver_status_id`),
  ADD UNIQUE KEY `driver_status` (`driver_status`);

--
-- Indexes for table `purpose_data`
--
ALTER TABLE `purpose_data`
  ADD PRIMARY KEY (`purpose_id`),
  ADD UNIQUE KEY `purpose_id` (`purpose_id`),
  ADD UNIQUE KEY `purpose` (`purpose`);

--
-- Indexes for table `trip_cost_info`
--
ALTER TABLE `trip_cost_info`
  ADD PRIMARY KEY (`trip_id`),
  ADD UNIQUE KEY `trip_id` (`trip_id`),
  ADD KEY `cost_rule_id` (`cost_rule_id`);

--
-- Indexes for table `trip_info`
--
ALTER TABLE `trip_info`
  ADD PRIMARY KEY (`trip_id`),
  ADD UNIQUE KEY `trip_id` (`trip_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `purpose_id` (`purpose_id`),
  ADD KEY `trip_status_id` (`trip_status_id`);

--
-- Indexes for table `trip_schedule_info`
--
ALTER TABLE `trip_schedule_info`
  ADD PRIMARY KEY (`trip_id`),
  ADD UNIQUE KEY `trip_id` (`trip_id`);

--
-- Indexes for table `trip_status_data`
--
ALTER TABLE `trip_status_data`
  ADD PRIMARY KEY (`trip_status_id`),
  ADD UNIQUE KEY `trip_status_id` (`trip_status_id`),
  ADD UNIQUE KEY `trip_status` (`trip_status`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`User_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `vehicle_condition_data`
--
ALTER TABLE `vehicle_condition_data`
  ADD PRIMARY KEY (`vehicle_condition_id`),
  ADD UNIQUE KEY `vehicle_condition_id` (`vehicle_condition_id`),
  ADD UNIQUE KEY `vehicle_condition` (`vehicle_condition`);

--
-- Indexes for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `vehicle_id` (`vehicle_id`),
  ADD UNIQUE KEY `plate_no` (`plate_no`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`),
  ADD KEY `vehicle_condition_id` (`vehicle_condition_id`),
  ADD KEY `access_id` (`access_id`);

--
-- Indexes for table `vehicle_location_info`
--
ALTER TABLE `vehicle_location_info`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `vehicle_status_id` (`vehicle_status_id`);

--
-- Indexes for table `vehicle_status_data`
--
ALTER TABLE `vehicle_status_data`
  ADD PRIMARY KEY (`vehicle_status_id`),
  ADD UNIQUE KEY `vehicle_status_id` (`vehicle_status_id`),
  ADD UNIQUE KEY `vehicle_status` (`vehicle_status`);

--
-- Indexes for table `vehicle_type_data`
--
ALTER TABLE `vehicle_type_data`
  ADD PRIMARY KEY (`vehicle_type_id`),
  ADD UNIQUE KEY `vehicle_type_id` (`vehicle_type_id`),
  ADD UNIQUE KEY `vehicle_type` (`vehicle_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `User_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `costing_rules`
--
ALTER TABLE `costing_rules`
  ADD CONSTRAINT `costing_rules_ibfk_1` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_data` (`vehicle_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `costing_rules_ibfk_2` FOREIGN KEY (`access_id`) REFERENCES `access_data` (`access_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `costing_rules_ibfk_3` FOREIGN KEY (`purpose_id`) REFERENCES `purpose_data` (`purpose_id`) ON UPDATE CASCADE;

--
-- Constraints for table `driver_info`
--
ALTER TABLE `driver_info`
  ADD CONSTRAINT `driver_info_ibfk_1` FOREIGN KEY (`driver_status_id`) REFERENCES `driver_status_data` (`driver_status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `trip_cost_info`
--
ALTER TABLE `trip_cost_info`
  ADD CONSTRAINT `trip_cost_info_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trip_info` (`trip_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_cost_info_ibfk_2` FOREIGN KEY (`cost_rule_id`) REFERENCES `costing_rules` (`cost_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trip_info`
--
ALTER TABLE `trip_info`
  ADD CONSTRAINT `trip_info_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trip_schedule_info` (`trip_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `driver_info` (`driver_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_info` (`vehicle_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_4` FOREIGN KEY (`purpose_id`) REFERENCES `purpose_data` (`purpose_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_5` FOREIGN KEY (`trip_status_id`) REFERENCES `trip_status_data` (`trip_status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_login`
--
ALTER TABLE `user_login`
  ADD CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_info` (`driver_id`);

--
-- Constraints for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
  ADD CONSTRAINT `vehicle_info_ibfk_1` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_data` (`vehicle_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_info_ibfk_2` FOREIGN KEY (`vehicle_condition_id`) REFERENCES `vehicle_condition_data` (`vehicle_condition_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_info_ibfk_3` FOREIGN KEY (`access_id`) REFERENCES `access_data` (`access_id`) ON UPDATE CASCADE;

--
-- Constraints for table `vehicle_location_info`
--
ALTER TABLE `vehicle_location_info`
  ADD CONSTRAINT `vehicle_location_info_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_info` (`vehicle_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_location_info_ibfk_2` FOREIGN KEY (`vehicle_status_id`) REFERENCES `vehicle_status_data` (`vehicle_status_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
