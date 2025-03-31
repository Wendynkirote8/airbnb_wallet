-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 31, 2025 at 09:07 AM
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
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `days`, `total_cost`, `booking_date`, `status`) VALUES
(62, 24, 8, 1, 4560.00, '2025-03-30 08:58:58', 'confirmed'),
(63, 24, 4, 1, 4500.00, '2025-03-30 09:05:34', 'booked'),
(66, 25, 8, 1, 4560.00, '2025-03-30 11:19:06', 'canceled'),
(67, 25, 9, 1, 4600.00, '2025-03-30 11:25:27', 'canceled'),
(68, 25, 4, 1, 4800.00, '2025-03-30 11:27:20', 'canceled'),
(69, 25, 9, 1, 4600.00, '2025-03-30 11:28:52', 'canceled'),
(70, 25, 8, 1, 4560.00, '2025-03-30 11:35:36', 'booked'),
(71, 25, 9, 1, 4140.00, '2025-03-30 12:41:42', 'booked'),
(72, 25, 9, 1, 4140.00, '2025-03-30 12:41:44', 'booked'),
(73, 25, 9, 1, 4140.00, '2025-03-30 12:42:26', 'booked'),
(74, 23, 8, 1, 4560.00, '2025-03-30 17:30:37', 'canceled'),
(77, 23, 8, 1, 4560.00, '2025-03-30 17:38:47', 'canceled'),
(78, 23, 10, 1, 3450.00, '2025-03-30 21:14:54', 'booked');

-- --------------------------------------------------------

--
-- Table structure for table `booking_logs`
--

CREATE TABLE `booking_logs` (
  `log_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_date` datetime DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `action_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_logs`
--

INSERT INTO `booking_logs` (`log_id`, `booking_id`, `user_id`, `room_id`, `days`, `total_cost`, `booking_date`, `action`, `action_date`, `status`) VALUES
(1, 66, 25, 8, 1, 4560.00, '2025-03-30 11:19:06', 'canceled', '2025-03-30 11:19:18', 'canceled'),
(2, 67, 25, 9, 1, 4600.00, '2025-03-30 11:25:27', 'canceled', '2025-03-30 11:26:05', 'canceled'),
(3, 68, 25, 4, 1, 4800.00, '2025-03-30 11:27:20', 'canceled', '2025-03-30 11:27:29', 'canceled'),
(4, 69, 25, 9, 1, 4600.00, '2025-03-30 11:28:52', 'canceled', '2025-03-30 11:29:09', 'canceled'),
(5, 70, 25, 8, 1, 4560.00, '2025-03-30 11:35:36', 'canceled', '2025-03-30 11:35:42', 'canceled'),
(6, 74, 23, 8, 1, 4560.00, '2025-03-30 17:30:37', 'canceled', '2025-03-30 17:30:56', 'canceled'),
(7, 77, 23, 8, 1, 4560.00, '2025-03-30 17:38:47', 'canceled', '2025-03-30 21:14:24', 'canceled');

-- --------------------------------------------------------

--
-- Table structure for table `earning`
--

CREATE TABLE `earning` (
  `earning_id` varchar(50) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `earning_type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `earning`
--

INSERT INTO `earning` (`earning_id`, `wallet_id`, `amount`, `earning_type`, `status`, `created_at`) VALUES
('earn_67e9577721cb9', 9, 228.00, 'fee', 'completed', '2025-03-30 17:38:47'),
('earn_67e98a1e40c83', 9, 172.50, 'fee', 'completed', '2025-03-30 21:14:54');

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
(6, 24, 1700, '2025-03-30 04:32:10'),
(7, 25, 225, '2025-03-30 14:25:27'),
(8, 23, 990, '2025-03-30 14:30:14');

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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `created_at`) VALUES
(1, 24, 'Your booking (ID: 62) has been confirmed.', '2025-03-30 17:05:46'),
(2, 24, 'Your booking (ID: 63) has been confirmed.', '2025-03-30 17:06:01'),
(3, 24, 'Your booking (ID: 63) has been confirmed.', '2025-03-30 17:10:17'),
(4, 25, 'Your booking (ID: 70) has been confirmed.', '2025-03-30 17:10:34'),
(5, 25, 'Your booking (ID: 70) has been confirmed.', '2025-03-30 17:14:52'),
(6, 25, 'Your booking (ID: 71) has been confirmed.', '2025-03-30 17:15:09'),
(7, 25, 'Your booking (ID: 71) has been confirmed.', '2025-03-30 17:15:16'),
(8, 25, 'Your booking (ID: 72) has been confirmed.', '2025-03-30 18:11:25'),
(9, 25, 'Your booking (ID: 72) has been confirmed.', '2025-03-30 18:13:59'),
(10, 25, 'Your booking (ID: 72) has been confirmed.', '2025-03-30 18:16:51'),
(11, 25, 'Your booking (ID: 72) has been confirmed.', '2025-03-30 18:17:52'),
(12, 25, 'Your booking (ID: 72) has been confirmed.', '2025-03-30 18:19:11'),
(13, 25, 'Your booking (ID: 73) has been confirmed.', '2025-03-30 18:50:22'),
(14, 23, 'Your booking (ID: 78) has been confirmed.', '2025-03-30 21:15:33');

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

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `provider` varchar(10) DEFAULT NULL,
  `reference` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_logs`
--

INSERT INTO `payment_logs` (`payment_id`, `user_id`, `transaction_id`, `provider`, `reference`, `status`, `created_at`) VALUES
(2, 24, '1757627478', 'w', 'booking_id: 57', 'completed', '2025-03-30 04:56:45'),
(3, 24, 'tx_7ca33df9842b104d', 'w', 'booking_id: 58', 'completed', '2025-03-30 05:03:40'),
(4, 24, 'tx_67e8d635cb995', 'w', 'booking_id: 59', 'completed', '2025-03-30 05:27:17'),
(5, 24, 'tx_67e8db7b5dabe', 'w', 'booking_id: 60', 'completed', '2025-03-30 05:49:47'),
(6, 24, 'tx_67e8dc698e68c', 'w', 'booking_id: 61', 'completed', '2025-03-30 05:53:45'),
(7, 24, 'tx_67e8dda2775f1', 'w', 'booking_id: 62', 'completed', '2025-03-30 05:58:58'),
(8, 24, 'tx_67e8df2e14be6', 'w', 'booking_id: 63', 'completed', '2025-03-30 06:05:34'),
(9, 25, 'tx_67e8fbbeb368a', 'w', 'booking_id: 64', 'completed', '2025-03-30 08:07:26'),
(10, 25, 'tx_67e8fd0cb1a11', 'w', 'booking_id: 65', 'completed', '2025-03-30 08:13:00'),
(11, 25, 'tx_67e8fe7aa0b31', 'w', 'booking_id: 66', 'completed', '2025-03-30 08:19:06'),
(12, 25, 'tx_67e8fff73866d', 'w', 'booking_id: 67', 'completed', '2025-03-30 08:25:27'),
(13, 25, 'tx_67e90068b149d', 'w', 'booking_id: 68', 'completed', '2025-03-30 08:27:20'),
(14, 25, 'tx_67e900c414987', 'w', 'booking_id: 69', 'completed', '2025-03-30 08:28:52'),
(15, 25, 'tx_67e90258b33e2', 'w', 'booking_id: 70', 'completed', '2025-03-30 08:35:36'),
(16, 25, 'tx_67e911d60830c', 'w', 'booking_id: 71', 'completed', '2025-03-30 09:41:42'),
(17, 25, 'tx_67e911d8be213', 'w', 'booking_id: 72', 'completed', '2025-03-30 09:41:44'),
(18, 25, 'tx_67e912022420e', 'w', 'booking_id: 73', 'completed', '2025-03-30 09:42:26'),
(19, 23, 'tx_67e9558d46348', 'w', 'booking_id: 74', 'completed', '2025-03-30 14:30:37'),
(20, 23, 'tx_67e95777210b3', 'w', 'booking_id: 77', 'completed', '2025-03-30 14:38:47'),
(21, 23, 'tx_67e98a1e40acd', 'w', 'booking_id: 78', 'completed', '2025-03-30 18:14:54');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating` decimal(2,1) DEFAULT NULL,
  `favourite` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price`, `capacity`, `image`, `created_at`, `rating`, `favourite`) VALUES
(4, 'Mombasa Kenya', 'qwerty homes', 4800.00, 3, 'uploads/rooms/room_67e8e8645308b5.65481223.jpg', '2025-03-23 08:00:55', NULL, 0),
(8, 'The Nest UKunda', 'awesome', 4560.00, 2, 'uploads/rooms/room_67e8d56b142e81.28473552.jpg', '2025-03-30 05:23:55', NULL, 0),
(9, 'Ukunda Mombasa', 'Inshallah Kenya is unquestionably one of Kenya’s most charismatic & private beach front escapes, located along the heart of Diani Beach. There are some places that cannot be defined by an ordinary sense of time or words; they are blessed, pure & timeless. This luxurious Beach Suite with stunning sea views & a private pool is the ideal retreat for couples, honeymooners, friends or singles longing for a place to UNPLUG - RELAX - RESET.', 4600.00, 2, 'uploads/rooms/room_67e98418a107d1.63768223.jpg', '2025-03-30 07:17:39', 4.0, 1),
(10, 'Katheru Park', 'Room for 2', 3450.00, 1, 'uploads/rooms/room_67e984578167e8.46802093.jpg', '2025-03-30 17:50:15', 5.0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` varchar(50) NOT NULL,
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
('1757627478', 6, 4500.00, 'booking', 'completed', '2025-03-30 04:56:45'),
('78', 6, 34000.00, 'deposit', 'completed', '2025-03-30 04:32:10'),
('tx_67e8d635cb995', 6, 4560.00, 'booking', 'completed', '2025-03-30 05:27:17'),
('tx_67e8db7b5dabe', 6, 4560.00, 'booking', 'completed', '2025-03-30 05:49:47'),
('tx_67e8dc698e68c', 6, 4560.00, 'booking', 'completed', '2025-03-30 05:53:45'),
('tx_67e8dda2775f1', 6, 4560.00, 'booking', 'completed', '2025-03-30 05:58:58'),
('tx_67e8df2e14be6', 6, 4500.00, 'booking', 'completed', '2025-03-30 06:05:34'),
('tx_67e8fb96616b5', 8, 2000.00, 'deposit', 'completed', '2025-03-30 08:06:46'),
('tx_67e8fbb31f2de', 8, 4500.00, 'deposit', 'completed', '2025-03-30 08:07:15'),
('tx_67e8fbbeb368a', 8, 4800.00, 'booking', 'completed', '2025-03-30 08:07:26'),
('tx_67e8fd0cb1a11', 8, 4800.00, 'booking', 'completed', '2025-03-30 08:13:00'),
('tx_67e8fe7aa0b31', 8, 4560.00, 'booking', 'completed', '2025-03-30 08:19:06'),
('tx_67e8fff73866d', 8, 4600.00, 'booking', 'completed', '2025-03-30 08:25:27'),
('tx_67e90068b149d', 8, 4800.00, 'booking', 'completed', '2025-03-30 08:27:20'),
('tx_67e900c414987', 8, 4600.00, 'booking', 'completed', '2025-03-30 08:28:52'),
('tx_67e90258b33e2', 8, 4560.00, 'booking', 'completed', '2025-03-30 08:35:36'),
('tx_67e9045fea7f7', 8, 20.00, 'redeem', 'completed', '2025-03-30 08:44:15'),
('tx_67e90d8e0b0a0', 8, 10.00, 'deposit', 'completed', '2025-03-30 09:23:26'),
('tx_67e9105c30522', 8, 2000.00, 'deposit', 'completed', '2025-03-30 09:35:24'),
('tx_67e911d60830c', 8, 4140.00, 'booking', 'completed', '2025-03-30 09:41:42'),
('tx_67e911d8be213', 8, 4140.00, 'booking', 'completed', '2025-03-30 09:41:44'),
('tx_67e911e7ac71d', 8, 40000.00, 'deposit', 'completed', '2025-03-30 09:41:59'),
('tx_67e912022420e', 8, 4140.00, 'booking', 'completed', '2025-03-30 09:42:26'),
('tx_67e95457894c7', 8, 200.00, 'redeem', 'completed', '2025-03-30 14:25:27'),
('tx_67e9555ced017', 9, 20000.00, 'deposit', 'completed', '2025-03-30 14:29:48'),
('tx_67e955768fef2', 9, 1.00, 'redeem', 'completed', '2025-03-30 14:30:14'),
('tx_67e9558d46348', 9, 4560.00, 'booking', 'completed', '2025-03-30 14:30:37'),
('tx_67e95777210b3', 9, 4560.00, 'booking', 'completed', '2025-03-30 14:38:47'),
('tx_67e98a1e40acd', 9, 3450.00, 'booking', 'completed', '2025-03-30 18:14:54'),
('tx_7ca33df9842b104d', 6, 4050.00, 'booking', 'completed', '2025-03-30 05:03:40'),
('tx_disc_67e912022460b', 8, 414.00, 'discount', 'completed', '2025-03-30 09:42:26');

-- --------------------------------------------------------

--
-- Table structure for table `trusted_devices`
--

CREATE TABLE `trusted_devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('guest','host','admin') NOT NULL DEFAULT 'guest',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `totp_secret` varchar(64) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `profile_picture`, `email`, `phone`, `password_hash`, `role`, `created_at`, `totp_secret`, `google_id`) VALUES
(23, 'vivian gituma', NULL, 'viviankenya254@gmail.com', NULL, '$2y$10$v3oS0f3TypAOfXXZOdndbu5Tf/bz3o/lnDOxlV2BYduAOWxo9a3zi', 'guest', '2025-03-29 16:39:52', 'PVCSEW2YNKYOMXJT', '112223214869731673857'),
(24, 'Paul Muchiri', 'profile_67e83253b19e23.34429866.jpg', 'paul.muchiri@gmail.com', '09674352', '$2y$10$0OiK9PUGJgbBlkjicNCv9ekd890dbXRrWz2q.DKHB0QZoW8z/ucXG', 'guest', '2025-03-29 17:45:06', 'QHIXURNMNXBTMJS3', NULL),
(25, 'Paul Muchiri', NULL, 'paul.muchiri43@gmail.com', NULL, '$2y$10$RJJvHkEFkQ3FUco9MOAu.etMBO4W2a1uM/.S7lzu8xuBGXn6bC6RS', 'guest', '2025-03-30 06:08:22', 'NDPMTOWWD3GLBLVM', '104763477545254761391');

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
(24, 0, 0, '2025-03-29 20:48:03', '2025-03-29 20:48:03');

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
(6, 24, 20440.00, 'KSH'),
(8, 25, 36724.00, 'USD'),
(9, 23, 16150.50, 'USD');

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
-- Indexes for table `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `earning`
--
ALTER TABLE `earning`
  ADD PRIMARY KEY (`earning_id`),
  ADD KEY `wallet_id` (`wallet_id`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

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
-- Indexes for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `booking_logs`
--
ALTER TABLE `booking_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Constraints for table `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD CONSTRAINT `booking_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `booking_logs_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

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
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
  ADD CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  ADD CONSTRAINT `trusted_devices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
