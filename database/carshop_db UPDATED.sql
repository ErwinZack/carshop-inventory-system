-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 03:38 PM
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
-- Database: `carshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `action` varchar(50) DEFAULT NULL,
  `quantity_change` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `product_id`, `action`, `quantity_change`, `created_at`) VALUES
(1, 1, 'Added new product', 10, '2025-09-21 13:25:57'),
(2, 3, 'Updated quantity', -2, '2025-09-21 13:25:57'),
(3, 5, 'Deleted product', -1, '2025-09-21 13:25:57');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_added` datetime DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `status` enum('active','archived') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `description`, `image`, `quantity`, `created_at`, `date_added`, `added_by`, `status`) VALUES
(1, 'Brake Pads', 'Car Parts', 1250.00, 'erwin r zacarias', 'uploads/1759209912_istockphoto-503317759-612x612.jpg', 12, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(2, 'Engine Oil', 'Car Parts', 249.00, 'Engine Oil V2', 'uploads/1759210304_super2000.webp', 5, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(3, 'Car Battery', 'Car Parts', 5488.00, 'Motolite', 'uploads/1759210415_sovisualco-ai-230626130615.webp', 3, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(4, 'Car Wax', 'Accessories', 350.00, 'Car wax is a protective coating applied to a car\'s paint to shield it from UV rays, dirt, oxidation, and other contaminants, preserving its finish and shine.', 'uploads/1758796628_71kDu8PS5NL._UF894,1000_QL80_.jpg', 25, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(5, 'Seat Cover', 'Accessories', 1200.00, 'Seat Cover', 'uploads/1759211730_smooth-finish-seat-covers-676.jpg', 8, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(6, 'Toyota Corolla 2020', 'Vehicles', 1100000.00, 'Toyota Corolla 2020', 'uploads/1759210576_gris_metalico3.jpg', 2, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(7, 'Honda Civic 2019', 'Vehicles', 748000.00, 'Honda Civic 2019', 'uploads/1759211625_2019_honda_civic_hatchback_angularfront.jpg', 1, '2025-09-21 13:25:57', '2025-10-13 22:02:56', NULL, 'active'),
(8, 'Toyoto V8 Engine', 'Vehicles', 1200000.00, 'Fresh from Casa', 'uploads/1759131054_camryhybrid.jpg', 4, '2025-09-29 07:30:54', '2025-10-13 22:02:56', NULL, 'active'),
(10, 'BluEarth ES32 Tires', 'Auto Parts', 2883.00, 'The BluEarth ES32 is a tire designed to provide the perfect balance of safety, comfort and durability. Its symmetrical, directional tread pattern ensures consistent performance, delivering steady stability and uniform tread wear.', 'uploads/1760266208_BluEarth ES32.png', 1, '2025-10-08 07:09:59', '2025-10-13 22:02:56', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_contact` varchar(50) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `product_id`, `customer_name`, `customer_contact`, `customer_address`, `quantity`, `price_per_unit`, `sale_date`, `status`, `created_at`) VALUES
(1, 10, 'Jade', '09075418221', '133 carael west dagupan city', 9, 2883.00, '2025-10-22 20:41:59', 'active', '2025-10-22 20:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'assets/images/default-avatar.png',
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `member_since` date DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `phone_number`, `address`, `profile_image`, `password`, `role_id`, `status`, `member_since`, `last_login`, `created_by`, `updated_at`, `created_at`) VALUES
(9, 'Admin 1', 'Erwin', 'Zacarias', 'admin1@gmail.com', '09075418221', '133 Carael West Dagupan City', 'uploads/profiles/1760530985_1759242811__4b69eeec-d893-40bb-bdf0-f2285d77ee34.jpg', '827ccb0eea8a706c4c34a16891f84e7b', 1, 'Active', NULL, NULL, NULL, '2025-10-21 09:19:36', '2025-10-08 10:30:33'),
(10, 'inven_zack', 'Zack', 'Erwin', 'ezacarias25@gmail.com', '09858833971', '227 Carael West Dagupan City', 'uploads/profiles/1760586718_1759919625_cat.jpg', '827ccb0eea8a706c4c34a16891f84e7b', 2, 'Active', NULL, NULL, NULL, '2025-10-21 11:26:08', '2025-10-08 10:33:22'),
(11, 'sales_zack', 'Zack', 'Erwin', 'zackerwin25@gmail.com', '09075418221', '133 Carael West Dagupan City', 'uploads/profiles/1761032625_1760530985_1759242811__4b69eeec-d893-40bb-bdf0-f2285d77ee34.jpg', '$2y$10$k6odtqmvvaY5BF/AfH3.QObL5zO24Y/ZVMav86UT9b/voqm6goYY2', 2, 'Active', '2025-10-21', NULL, 9, '2025-10-21 08:04:19', '2025-10-21 07:39:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at VARCHAR(255) NOT NULL
);
