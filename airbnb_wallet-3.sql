-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 26, 2025 at 05:17 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `created_at`, `email`) VALUES
(4, 'admin', 'admin', '2025-03-23 05:14:45', 'admin@wesh.com'),
(5, 'pmc', '$2y$10$blhyVsXj8ObXwHBMP2ajMuMQMMtw9Xj8r/xocV0Oooq44D/A96Bau', '2025-03-23 05:56:23', 'pm@gmail.com'),
(6, 'wesh', '$2y$10$sHyNSAPDVVxxvdXeyQAq0upEkrQHkHGmyhpKCMOvPtb/.DJ0wdAHm', '2025-03-23 10:35:04', 'wesh@admin.com'),
(7, 'paul', '$2y$10$00ir6Tl5cz6xQQRylBrnEOeNxc8lMdgK.Cf.1KWPDVgGcqF5wsese', '2025-03-23 14:14:10', 'pmc.ac.ke@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

CREATE TABLE `admin_password_resets` (
  `admin_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_password_resets`
--

INSERT INTO `admin_password_resets` (`admin_id`, `token`, `created_at`) VALUES
(5, 'c6a9ff173445c77d951f1709d8171ba1', '2025-03-23 14:41:09'),
(7, '55b6fa50c137810998b4a271101bb64b', '2025-03-23 17:17:19');

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `account_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `days`, `total_cost`, `booking_date`) VALUES
(1, 10, 4, 1, 4500.00, '2025-03-25 21:20:36'),
(2, 10, 4, 1, 4500.00, '2025-03-25 21:24:11'),
(3, 10, 4, 1, 4500.00, '2025-03-25 21:24:45'),
(4, 10, 4, 1, 4500.00, '2025-03-25 21:26:22'),
(5, 10, 4, 1, 4500.00, '2025-03-26 05:16:59'),
(6, 10, 4, 1, 4500.00, '2025-03-26 05:19:17'),
(7, 10, 3, 1, 234.00, '2025-03-26 05:19:33'),
(8, 10, 4, 1, 4050.00, '2025-03-26 05:25:02'),
(9, 10, 4, 1, 4050.00, '2025-03-26 05:25:06'),
(10, 10, 4, 1, 4050.00, '2025-03-26 05:25:07'),
(11, 10, 4, 1, 4050.00, '2025-03-26 05:25:08'),
(12, 10, 4, 1, 4050.00, '2025-03-26 05:25:09'),
(13, 10, 4, 1, 4050.00, '2025-03-26 05:25:12'),
(14, 10, 4, 1, 4050.00, '2025-03-26 06:02:16'),
(15, 10, 4, 1, 4050.00, '2025-03-26 06:02:39'),
(16, 10, 4, 1, 4050.00, '2025-03-26 06:02:43'),
(17, 10, 4, 1, 4050.00, '2025-03-26 06:02:43'),
(18, 10, 4, 1, 4050.00, '2025-03-26 06:02:44'),
(19, 10, 4, 1, 4050.00, '2025-03-26 06:02:45'),
(20, 10, 4, 1, 4050.00, '2025-03-26 06:02:45'),
(21, 10, 4, 1, 4050.00, '2025-03-26 06:02:45'),
(22, 10, 4, 1, 4050.00, '2025-03-26 06:02:45'),
(23, 10, 4, 1, 4050.00, '2025-03-26 06:02:45'),
(24, 10, 4, 1, 4050.00, '2025-03-26 06:02:46'),
(25, 10, 4, 1, 4050.00, '2025-03-26 06:02:46'),
(26, 10, 4, 1, 4050.00, '2025-03-26 06:02:46'),
(27, 10, 4, 1, 4050.00, '2025-03-26 06:02:46'),
(28, 10, 4, 1, 4050.00, '2025-03-26 06:02:46'),
(29, 10, 4, 1, 4050.00, '2025-03-26 06:02:47'),
(30, 10, 4, 1, 4050.00, '2025-03-26 06:02:47'),
(31, 10, 4, 1, 4050.00, '2025-03-26 06:02:47'),
(32, 10, 4, 1, 4050.00, '2025-03-26 06:02:47'),
(33, 10, 4, 1, 4050.00, '2025-03-26 06:02:47'),
(34, 10, 4, 1, 4050.00, '2025-03-26 06:02:47'),
(35, 10, 4, 1, 4050.00, '2025-03-26 06:02:48'),
(36, 10, 4, 1, 4050.00, '2025-03-26 06:02:48'),
(37, 10, 4, 1, 4050.00, '2025-03-26 06:02:48'),
(38, 10, 4, 1, 4050.00, '2025-03-26 06:02:49'),
(39, 10, 4, 1, 4050.00, '2025-03-26 06:02:49'),
(40, 10, 5, 1, 3500.00, '2025-03-26 07:16:07');

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
(1, 1, 100, '2025-02-15 14:14:04'),
(2, 3, 88, '2025-02-14 14:06:40'),
(3, 7, 366, '2025-02-14 12:45:04'),
(4, 10, 6750, '2025-03-26 03:10:29'),
(5, 9, 10, '2025-02-19 13:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires`) VALUES
(1, 3, 'b4e0655644bb993c82d0ebc838c5b623935321ad54ca5e55fd01302b011ce2db8d775e6403b5021a2679a4590a8e68968cfe', '2025-03-22 19:51:40');

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
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price`, `capacity`, `image`, `created_at`) VALUES
(3, 'Kino finest', 'ghcfdsfghb', 234.00, 3, 'uploads/rooms/room_67dfa9474565c6.65955139.jpg', '2025-03-23 06:25:11'),
(4, 'qwerty', 'qwerty homes', 4500.00, 3, 'uploads/rooms/room_67dfbfb70693f2.81350236.jpg', '2025-03-23 08:00:55'),
(5, 'kwa wesh heights', 'humbllllle parking available', 3500.00, 2, 'uploads/rooms/room_67e37f3de37ab3.49923647.jpg', '2025-03-26 04:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` varchar(10) DEFAULT NULL,
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
(49, 2, 2.70, 'withdrawal', 'completed', '2025-02-14 14:06:40'),
(50, 1, 300.00, 'deposit', 'completed', '2025-02-15 14:11:48'),
(51, 1, 200.00, 'deposit', 'completed', '2025-02-15 14:12:54'),
(52, 1, 200.00, 'withdrawal', 'completed', '2025-02-15 14:13:10'),
(53, 1, 600.00, 'withdrawal', 'completed', '2025-02-15 14:13:29'),
(54, 1, 9.00, 'withdrawal', 'completed', '2025-02-15 14:14:04'),
(55, 4, 200.00, 'deposit', 'completed', '2025-02-18 10:15:11'),
(56, 4, 10.00, 'deposit', 'completed', '2025-02-18 10:36:50'),
(57, 4, 9.00, 'withdrawal', 'completed', '2025-02-19 07:13:13'),
(58, 5, 2000.00, 'deposit', 'completed', '2025-02-19 12:33:32'),
(59, 5, 10.00, 'deposit', 'completed', '2025-02-19 13:07:58'),
(60, 5, 200.00, 'deposit', 'completed', '2025-02-19 13:58:27'),
(61, 4, 12.00, 'withdrawal', 'completed', '2025-03-23 08:57:55'),
(62, 4, 12.00, 'withdrawal', 'completed', '2025-03-23 08:58:05'),
(63, 4, 20000.00, 'deposit', 'completed', '2025-03-23 09:28:25'),
(64, 4, 20000.00, 'deposit', 'completed', '2025-03-23 09:29:35'),
(65, 4, 20000.00, 'deposit', 'completed', '2025-03-23 09:31:00'),
(66, 4, 10.00, 'deposit', 'completed', '2025-03-23 09:31:07'),
(67, 4, 200.00, 'withdrawal', 'completed', '2025-03-23 09:32:52'),
(68, 4, 200.00, 'withdrawal', 'completed', '2025-03-23 09:34:21'),
(69, 4, 10.00, 'deposit', 'completed', '2025-03-23 09:34:29'),
(70, 4, 10.00, 'deposit', 'completed', '2025-03-23 09:35:26'),
(71, 4, 10.00, 'deposit', 'completed', '2025-03-23 09:39:58'),
(72, 4, 200.00, 'withdrawal', 'completed', '2025-03-23 09:40:06'),
(73, 4, 2000.00, 'deposit', 'completed', '2025-03-23 09:40:43'),
(74, 4, 10.00, 'deposit', 'completed', '2025-03-23 18:08:38'),
(75, 4, 10.00, 'redeem', 'completed', '2025-03-26 02:57:34'),
(76, 4, 100000.00, 'deposit', 'completed', '2025-03-26 02:57:58'),
(77, 4, 35000.00, 'deposit', 'completed', '2025-03-26 03:10:29');

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
(1, 'Nkirote gituma', '../uploads/1739629487_code-8779047_1280.jpg', 'nkirote@gmail.com', '0741559992', '$2y$10$stBeLbFFJfSzQlHgsCJb1egQJS6ehGbKTpNzRe/hGW0fpKsXKLcTe', 'guest', '2025-02-10 14:45:26'),
(3, 'Muchiri Paul', '../uploads/1739541322_dog-287420_1280.jpg', 'pmc.ac.ke@gmail.com', '0710664060', '$2y$10$gktgDmSYiavWNumPAcpbEeT2WM92mUeDT4lLQmpXjCT3jeqsgwlp6', 'guest', '2025-02-11 12:07:03'),
(4, 'Nkirote', '../assets/imgs/default-user.png', 'pmc.pmc@gmail.com', '071234567', '$2y$10$xjWEa4wHxUOFiqccxcPgGu6rj5b5x/YRh9CS7cHt4Qm17mp8mrAUu', 'guest', '2025-02-13 13:58:02'),
(6, 'Makau', '../assets/imgs/default-user.png', 'makau@gmail.com', '0710345657', '$2y$10$pXSGgzUwKr1q8fbWRk55tuHXLGDIKdi3Icfvripk158DvvtBUd9Y.', 'guest', '2025-02-14 08:43:54'),
(7, '', '1739540272_WhatsApp Image 2025-02-07 at 16.34.39_4c7c1df9.JPG', '', '0710345653', '$2y$10$z/P.4Y4r67O2bbt9x7Ovzu.A0/21NhF7PYQALdjpPNI.vAMilRAgm', 'guest', '2025-02-14 08:49:39'),
(9, 'Admin', '../assets/imgs/default-user.png', 'admin@gmail.com', '07123456789', '$2y$10$0GXLt.xoLpKJrsr8qdKtTu3clX7770Sh3DKnlj1jFzgRmdJJkM8Ge', 'guest', '2025-02-14 17:07:07'),
(10, 'pmc muchiri', 'profile_67e3736dde9f04.02647944.jpg', 'pm@gmail.com', '0987654323', '$2y$10$g4vTjpvVQoGVDcrUL7pbm.dgWjy1gkAqI0GlqS604r.A3dXu.hgve', 'guest', '2025-02-18 08:22:23'),
(11, 'kim', '../uploads/1742666323_code-8779047_1280.jpg', 'kim@gmail.com', '987685534', '$2y$10$tp4BAvzHsPRLZY30WB6nkebQb5xsf64nU0IFsd.9tMfgfD7Bf6B4y', 'guest', '2025-03-22 17:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT 0,
  `sms_notifications` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`user_id`, `email_notifications`, `sms_notifications`, `created_at`, `updated_at`) VALUES
(10, 1, 0, '2025-03-26 06:24:29', '2025-03-26 06:24:29');

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
(1, 1, 6253.00, 'USD'),
(2, 3, 4071.20, 'USD'),
(3, 7, 24464.40, 'USD'),
(4, 10, 36303.00, 'USD'),
(5, 9, 2220.00, 'USD');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
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
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`user_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD CONSTRAINT `loyalty_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
