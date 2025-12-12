-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 04:00 AM
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
-- Database: `befacon_transpo_records`
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
(1, 'Company'),
(3, 'Delivery'),
(2, 'Public');

-- --------------------------------------------------------

--
-- Table structure for table `admin_info`
--

CREATE TABLE `admin_info` (
  `admin_id` int(11) NOT NULL,
  `admin_lname` varchar(50) NOT NULL,
  `admin_fname` varchar(50) NOT NULL,
  `admin_mi` varchar(2) DEFAULT NULL,
  `admin_sex` enum('M','F','O') NOT NULL,
  `birthdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_info`
--

INSERT INTO `admin_info` (`admin_id`, `admin_lname`, `admin_fname`, `admin_mi`, `admin_sex`, `birthdate`) VALUES
(5, 'Conchada', 'Axl', 'P', 'M', '2005-03-15'),
(6, 'Factora', 'Miguel', NULL, 'M', '2017-12-01'),
(7, 'Leonardo', 'Belando', NULL, 'M', '2017-12-20');

--
-- Triggers `admin_info`
--
DELIMITER $$
CREATE TRIGGER `trg_create_user_for_admin` AFTER INSERT ON `admin_info` FOR EACH ROW BEGIN
    INSERT INTO user_login (role, admin_id)
    VALUES ('admin', NEW.admin_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `driver_info`
--

CREATE TABLE `driver_info` (
  `driver_id` int(11) NOT NULL,
  `driver_lname` varchar(50) NOT NULL,
  `driver_fname` varchar(50) NOT NULL,
  `driver_mi` char(2) DEFAULT NULL,
  `driver_sex` enum('M','F','O') NOT NULL,
  `birthdate` date NOT NULL,
  `contact_no` char(11) NOT NULL,
  `license_no` varchar(20) NOT NULL,
  `license_type_id` int(11) NOT NULL,
  `driver_status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_info`
--

INSERT INTO `driver_info` (`driver_id`, `driver_lname`, `driver_fname`, `driver_mi`, `driver_sex`, `birthdate`, `contact_no`, `license_no`, `license_type_id`, `driver_status_id`) VALUES
(1, 'Santos', 'Juan', 'DC', 'M', '1985-04-12', '09171234567', 'L1234567', 1, 1),
(2, 'Reyes', 'Maria', '', 'F', '1990-08-23', '09281234567', 'L2345678', 2, 1),
(3, 'Dela Cruz', 'Jose', 'R', 'M', '1982-01-05', '09391234567', 'L3456789', 1, 2),
(4, 'Lopez', 'Ana', 'M', 'F', '1995-11-15', '09451234567', 'L4567890', 2, 1),
(5, 'Gonzalez', 'Pedro', 'S.', 'M', '1988-06-30', '09561234567', 'L56789017576', 1, 1);

--
-- Triggers `driver_info`
--
DELIMITER $$
CREATE TRIGGER `trg_create_user_for_driver` AFTER INSERT ON `driver_info` FOR EACH ROW BEGIN
    INSERT INTO user_login (driver_id, role)
    VALUES (NEW.driver_id, 'driver');
END
$$
DELIMITER ;

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
(1, 'Active'),
(2, 'On Leave'),
(3, 'Suspended');

-- --------------------------------------------------------

--
-- Table structure for table `license_type_data`
--

CREATE TABLE `license_type_data` (
  `license_type_id` int(11) NOT NULL,
  `license_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `license_type_data`
--

INSERT INTO `license_type_data` (`license_type_id`, `license_type`) VALUES
(2, 'Non-Professional'),
(1, 'Professional');

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
(4, 'Employee Transport'),
(2, 'Food Delivery'),
(3, 'Package Delivery'),
(1, 'Public Transport');

-- --------------------------------------------------------

--
-- Table structure for table `trip_info`
--

CREATE TABLE `trip_info` (
  `trip_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `purpose_id` int(11) NOT NULL,
  `origin` varchar(50) NOT NULL,
  `destination` varchar(50) NOT NULL,
  `sched_depart_datetime` datetime NOT NULL,
  `sched_arrival_datetime` datetime NOT NULL,
  `trip_status_id` int(11) NOT NULL,
  `trip_cost` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_info`
--

INSERT INTO `trip_info` (`trip_id`, `driver_id`, `vehicle_id`, `purpose_id`, `origin`, `destination`, `sched_depart_datetime`, `sched_arrival_datetime`, `trip_status_id`, `trip_cost`) VALUES
(1, 2, 1, 1, 'Baguio', 'Cubao', '2025-11-20 08:00:00', '2025-11-20 11:00:00', 1, 0.00),
(2, 2, 2, 2, 'Laguna', 'Cubao', '2025-11-21 09:00:00', '2025-11-21 11:30:00', 1, 0.00),
(3, 3, 3, 3, 'Cubao', 'Dagupan', '2025-11-22 07:30:00', '2025-11-22 13:00:00', 2, 0.00),
(4, 4, 4, 1, 'Pangasinan', 'Baguio', '2025-11-23 06:00:00', '2025-11-23 10:00:00', 1, 0.00),
(5, 5, 5, 2, 'Dagupan', 'Baguio', '2025-11-24 08:15:00', '2025-11-24 12:00:00', 1, 0.00),
(6, 2, 2, 1, 'Pangasinan', 'Baguio', '2025-11-19 22:00:43', '2025-11-19 22:00:43', 3, 0.00),
(7, 1, 4, 1, 'Manila', 'Baguio', '2025-11-25 08:00:00', '2025-11-25 12:00:00', 1, 1200.00),
(8, 2, 3, 2, 'Quezon City', 'Subic', '2025-11-26 09:00:00', '2025-11-26 13:30:00', 2, 900.00),
(9, 3, 2, 3, 'Cebu', 'Davao', '2025-11-27 07:30:00', '2025-11-27 15:00:00', 3, 1500.00),
(10, 4, 1, 4, 'Iloilo', 'Bacolod', '2025-11-28 10:00:00', '2025-11-28 12:00:00', 1, 600.00),
(11, 1, 4, 1, 'Davao', 'General Santos', '2025-11-29 14:00:00', '2025-11-29 17:30:00', 2, 1100.00),
(12, 2, 3, 2, 'Baguio', 'Manila', '2025-11-30 06:00:00', '2025-11-30 10:00:00', 3, 1200.00),
(13, 3, 2, 3, 'Subic', 'Quezon City', '2025-12-01 08:30:00', '2025-12-01 13:00:00', 4, 900.00),
(14, 4, 1, 4, 'Davao', 'Cebu', '2025-12-02 07:00:00', '2025-12-02 14:30:00', 1, 1500.00),
(15, 1, 4, 1, 'Bacolod', 'Iloilo', '2025-12-03 09:00:00', '2025-12-03 11:30:00', 2, 600.00),
(16, 2, 3, 2, 'General Santos', 'Davao', '2025-12-04 15:00:00', '2025-12-04 18:00:00', 3, 1100.00),
(17, 3, 2, 3, 'Manila', 'Subic', '2025-12-05 07:00:00', '2025-12-05 11:00:00', 4, 950.00),
(18, 4, 1, 4, 'Quezon City', 'Baguio', '2025-12-06 06:30:00', '2025-12-06 10:30:00', 1, 1150.00),
(19, 1, 4, 1, 'Cebu', 'Davao', '2025-12-07 08:15:00', '2025-12-07 15:45:00', 2, 1480.00),
(20, 2, 3, 2, 'Iloilo', 'Bacolod', '2025-12-08 09:00:00', '2025-12-08 11:00:00', 3, 620.00),
(21, 3, 2, 3, 'Baguio', 'Manila', '2025-12-09 06:00:00', '2025-12-09 10:00:00', 4, 1200.00),
(22, 4, 1, 4, 'Subic', 'Quezon City', '2025-12-10 10:00:00', '2025-12-10 14:00:00', 1, 900.00),
(23, 1, 4, 1, 'Baguio', 'General Santos', '2025-12-11 14:30:00', '2025-12-11 18:00:00', 2, 1100.00),
(24, 2, 3, 2, 'Bacolod', 'Iloilo', '2025-12-12 08:00:00', '2025-12-12 10:30:00', 3, 600.00),
(25, 3, 2, 3, 'General Santos', 'Davao', '2025-12-13 15:00:00', '2025-12-13 18:30:00', 4, 1150.00),
(26, 4, 1, 4, 'Manila', 'Baguio', '2025-12-14 07:00:00', '2025-12-14 11:00:00', 1, 1200.00),
(27, 1, 1, 1, 'Manila', 'Cebu', '2025-12-15 06:00:00', '2025-12-15 13:00:00', 2, 2500.00),
(28, 2, 2, 2, 'Cebu', 'Manila', '2025-12-16 08:00:00', '2025-12-16 15:00:00', 3, 2550.00),
(29, 3, 3, 3, 'Baguio', 'La Union', '2025-12-17 07:30:00', '2025-12-17 10:30:00', 1, 900.00),
(30, 4, 4, 4, 'La Union', 'Baguio', '2025-12-18 11:00:00', '2025-12-18 14:00:00', 4, 950.00),
(31, 1, 2, 1, 'Manila', 'Tagaytay', '2025-12-19 09:00:00', '2025-12-19 11:00:00', 2, 800.00),
(32, 2, 3, 2, 'Tagaytay', 'Manila', '2025-12-20 14:00:00', '2025-12-20 16:00:00', 3, 800.00),
(33, 3, 4, 3, 'Iloilo', 'Bacolod', '2025-12-21 06:30:00', '2025-12-21 09:30:00', 1, 1200.00),
(34, 4, 1, 4, 'Bacolod', 'Iloilo', '2025-12-22 13:00:00', '2025-12-22 16:00:00', 4, 1150.00),
(35, 1, 3, 1, 'Davao', 'General Santos', '2025-12-23 10:00:00', '2025-12-23 13:00:00', 2, 1100.00),
(36, 2, 4, 2, 'General Santos', 'Davao', '2025-12-24 15:00:00', '2025-12-24 18:00:00', 3, 1150.00),
(37, 3, 1, 3, 'Manila', 'Subic', '2025-12-25 07:00:00', '2025-12-25 10:00:00', 1, 1000.00),
(39, 1, 4, 1, 'Quezon City', 'Baguio', '2025-12-27 06:30:00', '2025-12-27 10:30:00', 2, 1150.00),
(40, 2, 1, 2, 'Baguio', 'Quezon City', '2025-12-28 11:00:00', '2025-12-28 15:00:00', 3, 1200.00),
(41, 3, 2, 3, 'Cebu', 'Davao', '2025-12-29 07:00:00', '2025-12-29 14:00:00', 1, 1400.00),
(42, 4, 3, 4, 'Davao', 'Cebu', '2025-12-30 09:00:00', '2025-12-30 16:00:00', 4, 1450.00),
(43, 1, 1, 1, 'Iloilo', 'Bacolod', '2025-12-31 06:00:00', '2025-12-31 09:00:00', 2, 1200.00),
(44, 2, 2, 2, 'Bacolod', 'Iloilo', '2026-01-01 08:00:00', '2026-01-01 11:00:00', 3, 1150.00),
(45, 3, 3, 3, 'Manila', 'Baguio', '2026-01-02 07:00:00', '2026-01-02 11:00:00', 1, 1200.00),
(47, 1, 1, 3, 'adasd', 'asdasd', '2025-11-20 16:13:00', '2025-11-27 16:13:00', 3, 123.00),
(48, 1, 1, 4, 'asd', 'asdasd', '2025-11-20 16:15:00', '2025-11-21 16:15:00', 3, 123.00),
(49, 1, 1, 4, 'asd', 'asd', '2025-11-20 17:22:00', '2025-12-04 17:22:00', 1, 0.00),
(50, 1, 1, 4, 'asd', 'asdasdsa', '2025-11-20 17:37:00', '2025-12-04 17:37:00', 3, 123.00),
(56, 2, 5, 4, '~', 'asdasd', '2025-11-21 08:11:00', '2025-12-01 08:11:00', 4, 123.00),
(58, 1, 4, 2, 'ajsdhjasjkhdsakjhdhjksadjkhsad', 'ajsdhjasjkhdsakjhdhjksadjkhsad', '2025-11-05 14:41:00', '2025-11-20 14:41:00', 1, 10000.00),
(59, 1, 4, 3, 'ssfg', 'fgs', '2025-11-21 16:38:00', '2025-11-30 16:38:00', 3, 2.01),
(62, 1, 5, 2, 'asd', 'asd', '2025-12-02 16:50:00', '2025-12-29 16:50:00', 4, 99999.00),
(63, 5, 4, 1, 'wuqhidhiuqwdhiqhuiduhiqwuidhqh', 'adowoiadwoijadwijoadwiojadwjio', '2025-12-02 15:52:00', '2025-12-03 15:52:00', 1, 1.00);

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
(4, 'Cancelled'),
(3, 'Completed'),
(2, 'Ongoing'),
(1, 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `user_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `passcode` varchar(255) NOT NULL,
  `role` enum('admin','driver') NOT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`user_id`, `driver_id`, `email`, `passcode`, `role`, `admin_id`) VALUES
(1, 1, 'jdsantos@gmail.com', '123', 'driver', NULL),
(2, 2, 'mreyes@gmail.com', 'driver123', 'driver', NULL),
(3, 3, 'jrdelacruz@gmail.com', 'driver123', 'driver', NULL),
(4, 4, 'amlopez@gmail.com', 'driver123', 'driver', NULL),
(5, 5, 'psgonzalez@gmail.com', 'driver123', 'driver', NULL),
(6, NULL, 'admin1@gmail.com', 'admin123', 'admin', NULL),
(9, NULL, 'axconchada@gmail.com', 'a', 'admin', 5),
(10, NULL, 'mfactora@gmail.com', 'admin123', 'admin', 6),
(11, NULL, 'bleonardo@gmail.com', 'admin123', 'admin', 7);

--
-- Triggers `user_login`
--
DELIMITER $$
CREATE TRIGGER `trg_userinfo_generate` BEFORE INSERT ON `user_login` FOR EACH ROW BEGIN
    DECLARE base VARCHAR(255);
    DECLARE temp VARCHAR(255);
    DECLARE counter INT DEFAULT 0;

    -- 1) Set DEFAULT PASSCODE
    IF NEW.role = 'admin' THEN
        SET NEW.passcode = 'admin123';
    ELSEIF NEW.role = 'driver' THEN
        SET NEW.passcode = 'driver123';
    END IF;

    -- 2) Only generate email for DRIVERS
    IF NEW.role = 'driver' AND NEW.driver_id IS NOT NULL THEN

        -- Fetch driver information from driver_info table
        SELECT 
            LOWER(LEFT(driver_fname, 1)),
            LOWER(IFNULL(NULLIF(LEFT(driver_mi, 1), '.'), '')),
            LOWER(REPLACE(driver_lname, ' ', ''))
        INTO @f, @m, @l
        FROM driver_info
        WHERE driver_id = NEW.driver_id;

        -- Build base (no @gmail.com yet)
        SET base = CONCAT(@f, @m, @l);

        -- First attempt
        SET temp = CONCAT(base, '@gmail.com');

        -- If duplicate, append 1, 2, 3...
        WHILE EXISTS (SELECT 1 FROM user_login WHERE email = temp) DO
            SET counter = counter + 1;
            SET temp = CONCAT(base, counter, '@gmail.com');
        END WHILE;

        SET NEW.email = temp;

    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_userinfo_generate_admin` BEFORE INSERT ON `user_login` FOR EACH ROW BEGIN
    DECLARE base VARCHAR(255);
    DECLARE temp VARCHAR(255);
    DECLARE counter INT DEFAULT 0;

    -- Only process for new admins
    IF NEW.role = 'admin' AND NEW.admin_id IS NOT NULL THEN

        -- Default password
        SET NEW.passcode = 'admin123';

        -- Fetch admin's personal info
        SELECT 
            LOWER(LEFT(admin_fname, 1)),
            LOWER(IFNULL(NULLIF(LEFT(admin_mi, 1), '.'), '')),
            LOWER(REPLACE(admin_lname, ' ', ''))
        INTO @f, @m, @l
        FROM admin_info
        WHERE admin_id = NEW.admin_id;

        -- Build the base username
        SET base = CONCAT(@f, @m, @l);
        SET temp = CONCAT(base, '@gmail.com');

        -- Add number suffix if email already exists
        WHILE EXISTS (SELECT 1 FROM user_login WHERE email = temp) DO
            SET counter = counter + 1;
            SET temp = CONCAT(base, counter, '@gmail.com');
        END WHILE;

        SET NEW.email = temp;
    END IF;

END
$$
DELIMITER ;

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
(3, 'Out of Service'),
(2, 'Pending Repair');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_info`
--

CREATE TABLE `vehicle_info` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `plate_no` varchar(8) NOT NULL,
  `vehicle_condition_id` int(11) NOT NULL,
  `access_id` int(11) NOT NULL,
  `license_type_id` int(11) NOT NULL,
  `current_location` varchar(50) NOT NULL,
  `vehicle_status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_info`
--

INSERT INTO `vehicle_info` (`vehicle_id`, `vehicle_type_id`, `plate_no`, `vehicle_condition_id`, `access_id`, `license_type_id`, `current_location`, `vehicle_status_id`) VALUES
(1, 1, 'ABC1234', 1, 1, 1, 'Baguio', 1),
(2, 2, 'DEF5678', 1, 2, 2, 'Laguna', 1),
(3, 3, 'GHI9012', 2, 1, 1, 'Cubao', 2),
(4, 4, 'JKL3456', 3, 3, 2, 'Pangasinan', 1),
(5, 1, 'ABV1233', 1, 1, 1, 'dagupan', 1),
(10, 1, 'ASD1239', 1, 1, 2, 'BAGUIO', 1);

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
(5, 'Decommissioned'),
(2, 'In Use'),
(3, 'Reserved'),
(4, 'Unavailable');

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
(2, 'Motorcycle'),
(5, 'Truck');

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
-- Indexes for table `admin_info`
--
ALTER TABLE `admin_info`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `driver_info`
--
ALTER TABLE `driver_info`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `license_no` (`license_no`),
  ADD KEY `license_type_id` (`license_type_id`),
  ADD KEY `driver_status_id` (`driver_status_id`);

--
-- Indexes for table `driver_status_data`
--
ALTER TABLE `driver_status_data`
  ADD PRIMARY KEY (`driver_status_id`),
  ADD UNIQUE KEY `driver_status_id` (`driver_status_id`),
  ADD UNIQUE KEY `driver_status` (`driver_status`);

--
-- Indexes for table `license_type_data`
--
ALTER TABLE `license_type_data`
  ADD PRIMARY KEY (`license_type_id`),
  ADD UNIQUE KEY `license_type` (`license_type`);

--
-- Indexes for table `purpose_data`
--
ALTER TABLE `purpose_data`
  ADD PRIMARY KEY (`purpose_id`),
  ADD UNIQUE KEY `purpose_id` (`purpose_id`),
  ADD UNIQUE KEY `purpose` (`purpose`);

--
-- Indexes for table `trip_info`
--
ALTER TABLE `trip_info`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `purpose_id` (`purpose_id`),
  ADD KEY `trip_status_id` (`trip_status_id`);

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
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `driver_id` (`driver_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

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
  ADD UNIQUE KEY `plate_no` (`plate_no`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`),
  ADD KEY `vehicle_condition_id` (`vehicle_condition_id`),
  ADD KEY `access_id` (`access_id`),
  ADD KEY `license_type_id` (`license_type_id`),
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
-- AUTO_INCREMENT for table `access_data`
--
ALTER TABLE `access_data`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_info`
--
ALTER TABLE `admin_info`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `driver_info`
--
ALTER TABLE `driver_info`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `driver_status_data`
--
ALTER TABLE `driver_status_data`
  MODIFY `driver_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `license_type_data`
--
ALTER TABLE `license_type_data`
  MODIFY `license_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purpose_data`
--
ALTER TABLE `purpose_data`
  MODIFY `purpose_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trip_info`
--
ALTER TABLE `trip_info`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `trip_status_data`
--
ALTER TABLE `trip_status_data`
  MODIFY `trip_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vehicle_condition_data`
--
ALTER TABLE `vehicle_condition_data`
  MODIFY `vehicle_condition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicle_status_data`
--
ALTER TABLE `vehicle_status_data`
  MODIFY `vehicle_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vehicle_type_data`
--
ALTER TABLE `vehicle_type_data`
  MODIFY `vehicle_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `driver_info`
--
ALTER TABLE `driver_info`
  ADD CONSTRAINT `driver_info_ibfk_1` FOREIGN KEY (`license_type_id`) REFERENCES `license_type_data` (`license_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `driver_info_ibfk_2` FOREIGN KEY (`driver_status_id`) REFERENCES `driver_status_data` (`driver_status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `trip_info`
--
ALTER TABLE `trip_info`
  ADD CONSTRAINT `trip_info_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_info` (`driver_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_info` (`vehicle_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_3` FOREIGN KEY (`purpose_id`) REFERENCES `purpose_data` (`purpose_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `trip_info_ibfk_4` FOREIGN KEY (`trip_status_id`) REFERENCES `trip_status_data` (`trip_status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_login`
--
ALTER TABLE `user_login`
  ADD CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_info` (`driver_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_login_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin_info` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
  ADD CONSTRAINT `vehicle_info_ibfk_1` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_data` (`vehicle_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_info_ibfk_2` FOREIGN KEY (`vehicle_condition_id`) REFERENCES `vehicle_condition_data` (`vehicle_condition_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_info_ibfk_3` FOREIGN KEY (`access_id`) REFERENCES `access_data` (`access_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_info_ibfk_4` FOREIGN KEY (`license_type_id`) REFERENCES `license_type_data` (`license_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_info_ibfk_5` FOREIGN KEY (`vehicle_status_id`) REFERENCES `vehicle_status_data` (`vehicle_status_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
