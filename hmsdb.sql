-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2024 at 06:35 PM
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
-- Database: `hmsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `profile` varchar(100) NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'Admin',
  `status` varchar(30) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `profile`, `role`, `status`) VALUES
(2, 'Mostafiz', '$2y$10$b/Ekk1TfGQucTcd31BHDLu6dIwLExnyw9/vc9OGPzqj9oCFuo9Koi', 'IMG_9956-01-01-01.jpeg', 'Super Admin', 'Active'),
(27, 'fahim', '$2y$10$79mm3.nTD7h6YRj2vPrtzeBhEeV9zHVmiqlkF5YyBD4dyEMNMhrSi', 'IMG_9959-01.jpeg', 'Admin', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `salary` varchar(100) NOT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_reg` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `profile` varchar(100) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `certification` text DEFAULT NULL,
  `experience` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `firstname`, `surname`, `username`, `email`, `gender`, `phone`, `address`, `password`, `salary`, `consultation_fee`, `data_reg`, `status`, `profile`, `qualification`, `specialization`, `license_number`, `certification`, `experience`) VALUES
(1, 'asadut', 'Jaman', 'jaman', 'asad@gmail.com', 'male', '01785227997', 'Dhaka, Bangladesh', '$2y$10$Yr6Vm3AgUuxVwXjcFg/Vtuvj4Ssoo4A9nQRCLWxtSbix.Q9J3/0jq', '200', 800.00, '2024-01-24 18:09:13', 'Approved', 'doctor.jpg', 'MBBS', 'General Medicine', 'BMDC-0001', 'Verified sample doctor record.', 'Outpatient and inpatient care.');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(100) NOT NULL,
  `patient_username` varchar(100) NOT NULL,
  `doctor_username` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time DEFAULT NULL,
  `symptoms` text NOT NULL,
  `appointment_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(30) NOT NULL DEFAULT 'Unpaid',
  `prescription_status` varchar(30) NOT NULL DEFAULT 'Not Created',
  `status` varchar(100) NOT NULL,
  `date_booked` varchar(100) NOT NULL,
  `updated_at` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(100) NOT NULL,
  `doctor` varchar(100) NOT NULL,
  `patient_username` varchar(100) NOT NULL,
  `patient` varchar(100) NOT NULL,
  `appointment_id` int(100) NOT NULL,
  `date_discharge` varchar(100) NOT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `additional_charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL,
  `waived_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(30) NOT NULL DEFAULT 'Unpaid',
  `paid_at` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `created_at` varchar(100) DEFAULT NULL,
  `updated_at` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `username` varchar(100) NOT NULL,
  `date_send` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(100) NOT NULL,
  `appointment_id` int(100) NOT NULL,
  `doctor_username` varchar(100) NOT NULL,
  `patient_username` varchar(100) NOT NULL,
  `symptoms` text DEFAULT NULL,
  `diagnosis` text NOT NULL,
  `medicine` text NOT NULL,
  `dosage` text DEFAULT NULL,
  `duration` text DEFAULT NULL,
  `tests` text DEFAULT NULL,
  `advice` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` varchar(100) NOT NULL,
  `updated_at` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `gender` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date_reg` varchar(100) NOT NULL,
  `profile` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `firstname`, `surname`, `username`, `email`, `phone`, `address`, `gender`, `password`, `date_reg`, `profile`) VALUES
(5, 'asad', 'a', 'jaman', 'a@gmail.com', '0178659562', 'Dhaka, Bangladesh', 'Male', '$2y$10$b/Ekk1TfGQucTcd31BHDLu6dIwLExnyw9/vc9OGPzqj9oCFuo9Koi', '2024-01-30 04:00:28', 'patient.jpg.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_username` (`username`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_doctor_username` (`username`),
  ADD UNIQUE KEY `unique_doctor_email` (`email`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_patient_username` (`username`),
  ADD UNIQUE KEY `unique_patient_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Cleanup normalization added by ChatGPT
UPDATE `doctors` SET `status` = 'Pending' WHERE LOWER(`status`) = 'pendding';
UPDATE `appointment` SET `status` = 'Completed', `appointment_status` = 'Completed' WHERE `status` IN ('Discharge', 'Discharged');
