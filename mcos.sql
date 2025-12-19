-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 07:38 PM
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
-- Database: `mcos`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_ID` int(11) NOT NULL,
  `category_details` varchar(255) NOT NULL,
  `category_pict` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_ID`, `category_details`, `category_pict`) VALUES
(1, 'Main Course', 'Food_Category_697.jpg'),
(2, 'Beverages', 'Food_Category_697.jpg'),
(3, 'Desserts', 'Food_Category_697.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_ID` int(11) NOT NULL,
  `cust_username` varchar(50) NOT NULL,
  `cust_password` varchar(255) NOT NULL,
  `cust_first_name` varchar(100) DEFAULT NULL,
  `cust_last_name` varchar(100) DEFAULT NULL,
  `cust_contact_no` varchar(20) DEFAULT NULL,
  `cust_dorm` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_ID`, `cust_username`, `cust_password`, `cust_first_name`, `cust_last_name`, `cust_contact_no`, `cust_dorm`, `bank_name`, `bank_account`) VALUES
(1, 'na', 'pass123', 'John', 'Doe', '0123456789', 'Dorm A', 'Maybank', '123456789012');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_ID` int(11) NOT NULL,
  `delivery_time` time DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_status` varchar(50) DEFAULT NULL,
  `order_ID` int(11) DEFAULT NULL,
  `staff_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`delivery_ID`, `delivery_time`, `delivery_date`, `delivery_status`, `order_ID`, `staff_ID`) VALUES
(1, '12:30:00', '2023-10-27', 'Delivered', 1, 2),
(2, NULL, NULL, 'On Delivery', 8, NULL),
(3, NULL, NULL, 'Delivered', 5, NULL),
(4, NULL, NULL, 'Delivered', 3, NULL),
(5, NULL, NULL, 'Delivered', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_ID` int(11) NOT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `cust_ID` int(11) DEFAULT NULL,
  `feedback_cat_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_ID`, `feedback`, `created_at`, `cust_ID`, `feedback_cat_ID`) VALUES
(1, 'It was nice. Highly recommended!', '2025-12-19 21:48:22', 1, 1),
(2, 'It was nice. Highly recommended!', '2025-12-19 21:48:53', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_category`
--

CREATE TABLE `feedback_category` (
  `feedback_cat_ID` int(11) NOT NULL,
  `feedback_cat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_category`
--

INSERT INTO `feedback_category` (`feedback_cat_ID`, `feedback_cat_name`) VALUES
(1, 'Food Quality'),
(2, 'Delivery Speed'),
(3, 'App Bug');

-- --------------------------------------------------------

--
-- Table structure for table `full_time`
--

CREATE TABLE `full_time` (
  `staff_ID` int(11) NOT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `full_time`
--

INSERT INTO `full_time` (`staff_ID`, `monthly_salary`) VALUES
(1, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_ID` int(11) NOT NULL,
  `menu_name` varchar(150) NOT NULL,
  `menu_details` text DEFAULT NULL,
  `menu_price` decimal(10,2) NOT NULL,
  `menu_availability` tinyint(1) DEFAULT 1,
  `menu_pict` varchar(255) DEFAULT NULL,
  `category_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_ID`, `menu_name`, `menu_details`, `menu_price`, `menu_availability`, `menu_pict`, `category_ID`) VALUES
(1, 'Nasi Lemak', 'Fragrant rice with sambal', 5.50, 1, 'Food-Name-6340.jpg', 1),
(2, 'Iced Tea', 'Refreshing tea with ice', 2.00, 1, 'Food-Name-6340.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_ID` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `delivery_charge` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `cust_ID` int(11) DEFAULT NULL,
  `staff_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_ID`, `order_date`, `delivery_charge`, `grand_total`, `cust_ID`, `staff_ID`) VALUES
(1, '2025-12-19 18:03:12', 0.00, 13.00, 1, 2),
(2, '2025-12-19 22:09:49', 2.00, 6.00, 1, 2),
(3, '2025-12-19 22:10:07', 2.00, 4.00, 1, 2),
(4, '2025-12-19 22:10:23', 2.00, 4.00, 1, 2),
(5, '2025-12-19 22:12:23', 2.00, 4.00, 1, 2),
(6, '2025-12-19 22:12:50', 2.00, 7.50, 1, 2),
(7, '2025-12-19 22:14:22', 2.00, 4.00, 1, 2),
(8, '2025-12-19 22:19:57', 2.00, 4.00, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `order_menu`
--

CREATE TABLE `order_menu` (
  `order_ID` int(11) NOT NULL,
  `menu_ID` int(11) NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_menu`
--

INSERT INTO `order_menu` (`order_ID`, `menu_ID`, `order_quantity`, `sub_total`) VALUES
(1, 1, 2, 11.00),
(1, 2, 1, 2.00),
(2, 2, 2, 4.00),
(3, 2, 1, 2.00),
(4, 2, 1, 2.00),
(5, 2, 1, 2.00),
(6, 1, 1, 5.50),
(7, 2, 1, 2.00),
(8, 2, 1, 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `part_time`
--

CREATE TABLE `part_time` (
  `staff_ID` int(11) NOT NULL,
  `hourly_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part_time`
--

INSERT INTO `part_time` (`staff_ID`, `hourly_salary`) VALUES
(2, 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_ID` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `receipt_file` varchar(255) DEFAULT NULL,
  `order_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_ID`, `amount_paid`, `payment_method`, `payment_status`, `payment_date`, `receipt_file`, `order_ID`) VALUES
(1, 13.00, 'E-Wallet', 'Completed', '2025-12-19 18:03:13', NULL, 1),
(2, 4.00, 'Maybank', 'Pending', '2025-12-19 22:18:06', 'Receipt-Order-7-615.png', 7),
(3, 0.00, NULL, 'Verified', '2025-12-20 01:11:53', NULL, 8),
(4, 0.00, NULL, 'Verified', '2025-12-20 01:12:42', NULL, 5),
(5, 0.00, NULL, 'Verified', '2025-12-20 01:12:50', NULL, 3),
(6, 0.00, NULL, 'Verified', '2025-12-20 01:12:58', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_ID` int(11) NOT NULL,
  `staff_username` varchar(50) NOT NULL,
  `staff_password` varchar(255) NOT NULL,
  `staff_first_name` varchar(100) DEFAULT NULL,
  `staff_last_name` varchar(100) DEFAULT NULL,
  `staff_contact_no` varchar(20) DEFAULT NULL,
  `supervisor_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_ID`, `staff_username`, `staff_password`, `staff_first_name`, `staff_last_name`, `staff_contact_no`, `supervisor_ID`) VALUES
(1, 'admin', 'admin', 'Michael', 'Scott', '0123456789', NULL),
(2, 'nana', 'beet123', 'Dwight', 'Schrute', '01482917382', 1);

-- --------------------------------------------------------

--
-- Table structure for table `work_log`
--

CREATE TABLE `work_log` (
  `log_ID` int(11) NOT NULL,
  `work_date` date DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT NULL,
  `day_present` varchar(20) DEFAULT NULL,
  `staff_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_log`
--

INSERT INTO `work_log` (`log_ID`, `work_date`, `hours_worked`, `day_present`, `staff_ID`) VALUES
(1, '2023-10-27', 8.00, 'Friday', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_ID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_ID`),
  ADD UNIQUE KEY `cust_username` (`cust_username`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_ID`),
  ADD KEY `order_ID` (`order_ID`),
  ADD KEY `staff_ID` (`staff_ID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_ID`),
  ADD KEY `cust_ID` (`cust_ID`),
  ADD KEY `feedback_cat_ID` (`feedback_cat_ID`);

--
-- Indexes for table `feedback_category`
--
ALTER TABLE `feedback_category`
  ADD PRIMARY KEY (`feedback_cat_ID`);

--
-- Indexes for table `full_time`
--
ALTER TABLE `full_time`
  ADD PRIMARY KEY (`staff_ID`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_ID`),
  ADD KEY `category_ID` (`category_ID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_ID`),
  ADD KEY `cust_ID` (`cust_ID`),
  ADD KEY `staff_ID` (`staff_ID`);

--
-- Indexes for table `order_menu`
--
ALTER TABLE `order_menu`
  ADD PRIMARY KEY (`order_ID`,`menu_ID`),
  ADD KEY `menu_ID` (`menu_ID`);

--
-- Indexes for table `part_time`
--
ALTER TABLE `part_time`
  ADD PRIMARY KEY (`staff_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_ID`),
  ADD KEY `order_ID` (`order_ID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_ID`),
  ADD UNIQUE KEY `staff_username` (`staff_username`),
  ADD KEY `supervisor_ID` (`supervisor_ID`);

--
-- Indexes for table `work_log`
--
ALTER TABLE `work_log`
  ADD PRIMARY KEY (`log_ID`),
  ADD KEY `staff_ID` (`staff_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback_category`
--
ALTER TABLE `feedback_category`
  MODIFY `feedback_cat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `work_log`
--
ALTER TABLE `work_log`
  MODIFY `log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`order_ID`) REFERENCES `order` (`order_ID`),
  ADD CONSTRAINT `delivery_ibfk_2` FOREIGN KEY (`staff_ID`) REFERENCES `staff` (`staff_ID`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`cust_ID`) REFERENCES `customer` (`cust_ID`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`feedback_cat_ID`) REFERENCES `feedback_category` (`feedback_cat_ID`);

--
-- Constraints for table `full_time`
--
ALTER TABLE `full_time`
  ADD CONSTRAINT `full_time_ibfk_1` FOREIGN KEY (`staff_ID`) REFERENCES `staff` (`staff_ID`) ON DELETE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`category_ID`) REFERENCES `category` (`category_ID`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`cust_ID`) REFERENCES `customer` (`cust_ID`),
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`staff_ID`) REFERENCES `staff` (`staff_ID`);

--
-- Constraints for table `order_menu`
--
ALTER TABLE `order_menu`
  ADD CONSTRAINT `order_menu_ibfk_1` FOREIGN KEY (`order_ID`) REFERENCES `order` (`order_ID`),
  ADD CONSTRAINT `order_menu_ibfk_2` FOREIGN KEY (`menu_ID`) REFERENCES `menu` (`menu_ID`);

--
-- Constraints for table `part_time`
--
ALTER TABLE `part_time`
  ADD CONSTRAINT `part_time_ibfk_1` FOREIGN KEY (`staff_ID`) REFERENCES `staff` (`staff_ID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_ID`) REFERENCES `order` (`order_ID`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`supervisor_ID`) REFERENCES `staff` (`staff_ID`) ON DELETE SET NULL;

--
-- Constraints for table `work_log`
--
ALTER TABLE `work_log`
  ADD CONSTRAINT `work_log_ibfk_1` FOREIGN KEY (`staff_ID`) REFERENCES `staff` (`staff_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
