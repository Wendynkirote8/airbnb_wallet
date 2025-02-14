-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2025 at 06:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `airbnb_wallet`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points`
--

CREATE TABLE `loyalty_points` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_points`
--

INSERT INTO `loyalty_points` (`id`, `user_id`, `points`, `last_updated`) VALUES
(1, 1, 85, '2025-02-13 13:37:50'),
(2, 3, 88, '2025-02-14 14:06:40'),
(3, 7, 366, '2025-02-14 12:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `provider` enum('Stripe','PayPal','Bank Transfer') NOT NULL,
  `reference` varchar(255) NOT NULL,
  `status` enum('success','failed','pending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` enum('deposit','withdrawal','payment','refund') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `wallet_id`, `amount`, `transaction_type`, `status`, `created_at`) VALUES
(1, 1, 100.00, 'deposit', 'completed', '2025-02-12 09:58:15'),
(2, 1, 10.00, 'withdrawal', 'completed', '2025-02-12 09:58:26'),
(3, 1, 20.00, 'deposit', 'completed', '2025-02-12 10:02:18'),
(4, 1, 20.00, 'deposit', 'completed', '2025-02-12 12:22:11'),
(5, 1, 200.00, 'deposit', 'completed', '2025-02-12 13:57:19'),
(6, 1, 3000.00, 'deposit', 'completed', '2025-02-13 09:20:55'),
(7, 1, 4500.00, 'deposit', 'completed', '2025-02-13 10:42:01'),
(8, 1, 3000.00, 'withdrawal', 'completed', '2025-02-13 11:39:46'),
(9, 1, 1.00, 'deposit', 'completed', '2025-02-13 13:03:39'),
(10, 1, 1.00, 'deposit', 'completed', '2025-02-13 13:03:48'),
(11, 1, 10.00, 'deposit', 'completed', '2025-02-13 13:33:10'),
(12, 1, 1200.00, 'deposit', 'completed', '2025-02-13 13:33:26'),
(13, 1, 200.00, 'deposit', 'completed', '2025-02-13 13:37:50'),
(14, 2, 10.00, 'deposit', 'completed', '2025-02-13 13:45:33'),
(15, 2, 2000.00, 'deposit', 'completed', '2025-02-13 13:45:46'),
(16, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:36:27'),
(17, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:37:02'),
(18, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:38:04'),
(19, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:38:08'),
(20, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:38:29'),
(21, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:40:28'),
(22, 2, 200.00, 'withdrawal', 'completed', '2025-02-14 07:40:39'),
(23, 2, 10.00, 'withdrawal', 'completed', '2025-02-14 07:40:52'),
(24, 2, 10.00, 'withdrawal', 'completed', '2025-02-14 07:44:18'),
(25, 2, 23.00, 'withdrawal', 'completed', '2025-02-14 07:44:27'),
(26, 2, 9.00, 'withdrawal', 'completed', '2025-02-14 07:44:35'),
(27, 2, 18.00, 'withdrawal', 'completed', '2025-02-14 07:44:50'),
(28, 2, 10.00, 'withdrawal', 'completed', '2025-02-14 07:47:17'),
(29, 2, 20.00, 'withdrawal', 'completed', '2025-02-14 07:47:27'),
(30, 2, 20.00, 'withdrawal', 'completed', '2025-02-14 07:47:45'),
(31, 2, 3000.00, 'deposit', 'completed', '2025-02-14 07:53:06'),
(32, 2, 1000.00, 'withdrawal', 'completed', '2025-02-14 07:53:23'),
(33, 3, 2000.00, 'deposit', 'completed', '2025-02-14 08:50:14'),
(34, 3, 30000.00, 'deposit', 'completed', '2025-02-14 08:50:29'),
(35, 3, 2345.00, 'withdrawal', 'completed', '2025-02-14 08:50:45'),
(36, 2, 198.00, 'withdrawal', 'completed', '2025-02-14 12:21:59'),
(37, 3, 12.00, 'withdrawal', 'completed', '2025-02-14 12:44:37'),
(38, 3, 1600.00, 'withdrawal', 'completed', '2025-02-14 12:44:43'),
(39, 3, 1110.60, 'withdrawal', 'completed', '2025-02-14 12:45:04'),
(40, 3, 1234.00, 'withdrawal', 'completed', '2025-02-14 12:45:19'),
(41, 3, 1234.00, 'withdrawal', 'completed', '2025-02-14 12:45:29'),
(42, 2, 2000.00, 'deposit', 'completed', '2025-02-14 13:56:18'),
(43, 2, 34.00, 'deposit', 'completed', '2025-02-14 13:57:13'),
(44, 2, 122.00, 'withdrawal', 'completed', '2025-02-14 13:58:57'),
(45, 2, 122.00, 'withdrawal', 'completed', '2025-02-14 14:00:27'),
(46, 2, 2.70, 'withdrawal', 'completed', '2025-02-14 14:00:38'),
(47, 2, 2.70, 'withdrawal', 'completed', '2025-02-14 14:01:29'),
(48, 2, 2.70, 'withdrawal', 'completed', '2025-02-14 14:06:37'),
(49, 2, 2.70, 'withdrawal', 'completed', '2025-02-14 14:06:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('guest','host','admin') NOT NULL DEFAULT 'guest',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `profile_picture`, `email`, `phone`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Vivian Makena Gituma', '../uploads/1739542029_WhatsApp Image 2025-02-07 at 16.34.39_4c7c1df9 (1).JPG', 'viviankenya254@gmail.com', '0741559992', '$2y$10$stBeLbFFJfSzQlHgsCJb1egQJS6ehGbKTpNzRe/hGW0fpKsXKLcTe', 'guest', '2025-02-10 14:45:26'),
(3, 'Muchiri Paul', '../uploads/1739541322_dog-287420_1280.jpg', 'pmc.ac.ke@gmail.com', '0710664060', '$2y$10$gktgDmSYiavWNumPAcpbEeT2WM92mUeDT4lLQmpXjCT3jeqsgwlp6', 'guest', '2025-02-11 12:07:03'),
(4, 'Nkirote', '../assets/imgs/default-user.png', 'pmc.pmc@gmail.com', '071234567', '$2y$10$xjWEa4wHxUOFiqccxcPgGu6rj5b5x/YRh9CS7cHt4Qm17mp8mrAUu', 'guest', '2025-02-13 13:58:02'),
(6, 'Makau', '../assets/imgs/default-user.png', 'makau@gmail.com', '0710345657', '$2y$10$pXSGgzUwKr1q8fbWRk55tuHXLGDIKdi3Icfvripk158DvvtBUd9Y.', 'guest', '2025-02-14 08:43:54'),
(7, '', '1739540272_WhatsApp Image 2025-02-07 at 16.34.39_4c7c1df9.JPG', '', '0710345653', '$2y$10$z/P.4Y4r67O2bbt9x7Ovzu.A0/21NhF7PYQALdjpPNI.vAMilRAgm', 'guest', '2025-02-14 08:49:39'),
(9, 'Admin', '../assets/imgs/default-user.png', 'admin@gmail.com', '07123456789', '$2y$10$0GXLt.xoLpKJrsr8qdKtTu3clX7770Sh3DKnlj1jFzgRmdJJkM8Ge', 'guest', '2025-02-14 17:07:07');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'USD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`wallet_id`, `user_id`, `balance`, `currency`) VALUES
(1, 1, 6562.00, 'USD'),
(2, 3, 4071.20, 'USD'),
(3, 7, 24464.40, 'USD');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `host_id` (`host_id`);

--
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `wallet_id` (`wallet_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD CONSTRAINT `loyalty_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
