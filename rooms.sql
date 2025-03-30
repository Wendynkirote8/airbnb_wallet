-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 30, 2025 at 07:35 PM
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
(9, 'Ukunda Mombasa', 'Inshallah Kenya is unquestionably one of Kenya’s most charismatic & private beach front escapes, located along the heart of Diani Beach. There are some places that cannot be defined by an ordinary sense of time or words; they are blessed, pure & timeless. This luxurious Beach Suite with stunning sea views & a private pool is the ideal retreat for couples, honeymooners, friends or singles longing for a place to UNPLUG - RELAX - RESET.', 4600.00, 2, 'uploads/rooms/room_67e8f09ec1b8e8.05452220.jpg', '2025-03-30 07:17:39', 4.0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
