-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 08, 2025 at 08:11 AM
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
-- Database: `lagibutuh_db`
--
CREATE DATABASE IF NOT EXISTS `lagibutuh_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lagibutuh_db`;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_bookings`
--

CREATE TABLE `consultation_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `psychologist_id` int(11) NOT NULL,
  `schedule_time` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','canceled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jastip_orders`
--

CREATE TABLE `jastip_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `runner_id` int(11) DEFAULT NULL,
  `item_description` text NOT NULL,
  `purchase_location` varchar(255) NOT NULL,
  `delivery_location` varchar(255) NOT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `status` enum('open','accepted','purchased','delivered','cancelled') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jastip_orders`
--

INSERT INTO `jastip_orders` (`id`, `user_id`, `runner_id`, `item_description`, `purchase_location`, `delivery_location`, `estimated_price`, `status`, `created_at`) VALUES
(1, 1, 3, 'ayam goreng', 'dd', 'dda', 234.00, 'delivered', '2025-07-07 14:35:24'),
(2, 4, 3, 'ess teh manis', 'ddd', 'ddw', 9339.00, 'delivered', '2025-07-07 14:37:53'),
(3, 4, 3, 'Makansoto', 'ddd', 'dd', 10000.00, 'delivered', '2025-07-07 14:38:15'),
(4, 3, 2, 'udan', 'sas', 'sa', 3232.00, 'delivered', '2025-07-07 15:50:46'),
(5, 3, 2, 'ccc', 'dd', 'dd', 303030.00, 'delivered', '2025-07-07 16:18:30'),
(6, 2, 3, 'ayam goreng', 'ddd', 'ddd', 3000.00, 'delivered', '2025-07-07 16:23:56'),
(7, 3, NULL, 'dddd', 'ddd', 'dd', 424.00, 'cancelled', '2025-07-07 16:56:48'),
(8, 3, NULL, 'sscs', 'cscsc', 'dsdd', 5445.00, 'cancelled', '2025-07-07 17:07:20'),
(9, 3, NULL, 'rrwr', 'ffwf', 'fwfwf', 244.00, 'cancelled', '2025-07-07 17:07:37'),
(10, 3, NULL, 'cscs', 'dssd', 'dssd', 444.00, 'cancelled', '2025-07-07 17:48:18'),
(11, 2, NULL, 'udang keju', 'ddd', 'dd', 4424.00, 'cancelled', '2025-07-07 18:03:49'),
(12, 2, NULL, 'sddd', 'ewee', 'dff', 4555.00, 'cancelled', '2025-07-07 18:04:09'),
(13, 1, NULL, 'd', 'dd', 'dd', 442.00, 'cancelled', '2025-07-08 05:02:02'),
(14, 1, NULL, 'dddd', 'ddd', 'ddd', 342424.00, 'open', '2025-07-08 05:04:56');

-- --------------------------------------------------------

--
-- Table structure for table `laptops`
--

CREATE TABLE `laptops` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `specifications` text NOT NULL,
  `image_path` varchar(255) DEFAULT 'default_laptop.png',
  `availability_status` enum('available','rented','maintenance') NOT NULL DEFAULT 'available',
  `rental_rate_per_day` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptops`
--

INSERT INTO `laptops` (`id`, `owner_id`, `brand`, `model`, `specifications`, `image_path`, `availability_status`, `rental_rate_per_day`) VALUES
(1, 1, 'Apple', 'sss', 'dd', '686972ebcb47b_WhatsApp Image 2025-07-04 at 13.21.20.jpeg', 'rented', 111.00),
(2, 1, 'ss', 'sfs', 'ss', '68697334eea50_cystic-acne-face-cheeks-350x350.jpg', 'rented', 2.00),
(3, 1, 'apple', 'asss', 'dd', '686bc091a269f_Screenshot 2025-07-07 at 10.17.16.png', 'rented', 10000.00),
(4, 1, 'ddd', 'Macbook pro', 'dddd', '686bc5dbcbaa9_Screenshot 2025-07-07 at 10.33.01.png', 'rented', 1000.00),
(5, 1, 'macbook', 'dadd', 'ddd', '686bd58515109_Screenshot 2025-07-06 at 17.12.52.png', 'rented', 9339.00),
(6, 1, 'ddd', 'dsd', 'ddd', '686bd71817300_Screenshot 2025-07-07 at 10.33.01.png', 'rented', 12.00),
(7, 1, 'mannd', 'dddd', 'ddd', '686bd88d1b98c_Screenshot 2025-07-06 at 17.13.54.png', 'rented', 2133.00),
(8, 4, 'sotobakarenaknih', 'dd', 'ddcc', '686bdc4582095_Screenshot 2025-07-07 at 10.17.16.png', 'rented', 90000.00);

-- --------------------------------------------------------

--
-- Table structure for table `laptop_bookings`
--

CREATE TABLE `laptop_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `laptop_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','confirmed','ongoing','completed','canceled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptop_bookings`
--

INSERT INTO `laptop_bookings` (`id`, `user_id`, `laptop_id`, `start_date`, `end_date`, `status`, `payment_status`, `created_at`) VALUES
(1, 1, 1, '2025-07-07', '2025-07-08', 'pending', 'unpaid', '2025-07-05 18:46:33'),
(2, 1, 2, '2025-07-16', '2025-07-21', 'pending', 'unpaid', '2025-07-05 18:47:29'),
(3, 1, 4, '2025-07-19', '2025-07-25', 'pending', 'unpaid', '2025-07-07 13:25:01'),
(4, 1, 3, '2025-07-18', '2025-08-02', 'pending', 'unpaid', '2025-07-07 14:08:48'),
(5, 1, 5, '2025-07-19', '2025-07-26', 'pending', 'unpaid', '2025-07-07 14:11:36'),
(6, 1, 6, '2025-07-12', '2025-07-18', 'pending', 'unpaid', '2025-07-07 14:18:09'),
(7, 4, 7, '2025-07-11', '2025-07-18', 'pending', 'unpaid', '2025-07-07 14:37:13'),
(8, 1, 8, '2025-07-04', '2025-07-04', 'pending', 'unpaid', '2025-07-08 05:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nebeng_bookings`
--

CREATE TABLE `nebeng_bookings` (
  `id` int(11) NOT NULL,
  `ride_id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `status` enum('confirmed','canceled') NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nebeng_bookings`
--

INSERT INTO `nebeng_bookings` (`id`, `ride_id`, `passenger_id`, `status`, `created_at`) VALUES
(1, 4, 2, 'confirmed', '2025-07-05 18:03:59'),
(2, 3, 2, 'confirmed', '2025-07-05 18:04:39'),
(3, 5, 1, 'confirmed', '2025-07-05 18:17:02'),
(4, 6, 4, 'confirmed', '2025-07-07 14:38:58'),
(5, 6, 3, 'confirmed', '2025-07-07 16:12:33'),
(6, 11, 3, 'confirmed', '2025-07-07 18:02:09'),
(7, 8, 3, 'confirmed', '2025-07-07 18:02:16');

-- --------------------------------------------------------

--
-- Table structure for table `nebeng_rides`
--

CREATE TABLE `nebeng_rides` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `departure_time` datetime NOT NULL,
  `available_seats` int(3) NOT NULL,
  `status` enum('active','full','completed','canceled') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `origin_lat` decimal(10,8) DEFAULT NULL,
  `origin_lng` decimal(11,8) DEFAULT NULL,
  `destination_lat` decimal(10,8) DEFAULT NULL,
  `destination_lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nebeng_rides`
--

INSERT INTO `nebeng_rides` (`id`, `driver_id`, `origin`, `destination`, `departure_time`, `available_seats`, `status`, `notes`, `origin_lat`, `origin_lng`, `destination_lat`, `destination_lng`) VALUES
(1, 1, 'seturan', '333', '2025-07-04 00:25:00', 3, 'active', '1', NULL, NULL, NULL, NULL),
(2, 1, 'jakarya', 'd', '2025-07-03 00:43:00', 1, 'active', 'dd', NULL, NULL, NULL, NULL),
(3, 1, 'jakarta', 'uii', '2025-07-07 00:47:00', 0, 'full', 'd', NULL, NULL, NULL, NULL),
(4, 1, 'rumah guntur', 'uii', '2025-07-06 03:47:00', 1, 'active', '', NULL, NULL, NULL, NULL),
(5, 2, 'jakarta', 's', '2025-07-07 01:16:00', 0, 'full', 's', NULL, NULL, NULL, NULL),
(6, 1, 'adada', 'dda', '2025-07-10 19:46:00', 9, 'active', 'amd', NULL, NULL, NULL, NULL),
(7, 1, 'amikom', 'amikom', '2025-07-12 02:56:00', 1, 'active', '', NULL, NULL, NULL, NULL),
(8, 1, 'jak', 'ss', '2025-07-18 03:18:00', 0, 'full', 'as', NULL, NULL, NULL, NULL),
(9, 1, 'jakarta', 'jakarta', '2025-07-07 03:21:00', 1, 'active', 'd', NULL, NULL, NULL, NULL),
(10, 1, 'ass', 'asas', '2025-07-05 03:21:00', 1, 'active', 'ada', NULL, NULL, NULL, NULL),
(11, 1, 'jakarta', 'lampung', '2025-08-02 08:00:00', 0, 'full', 'ddd', -6.17540490, 106.82716800, -4.85550390, 105.02729860),
(12, 1, 'Jalan Wates, Pakuncen, Wirobrajan, Yogyakarta, Special Region of Yogyakarta, Java, 55182, Indonesia', 'Banguntapan, Bantul Regency, Special Region of Yogyakarta, Java, 55221, Indonesia', '2025-07-12 08:12:00', 1, 'active', '', -7.80079971, 110.34736633, -7.78702358, 110.39749146),
(13, 1, 'Ngampilan, Yogyakarta, Special Region of Yogyakarta, Java, 55261, Indonesia', 'Gang Delima, Muja muju, Umbulharjo, Yogyakarta, Special Region of Yogyakarta, Java, 55165, Indonesia', '2025-07-12 09:00:00', 1, 'active', '', -7.79705809, 110.35766602, -7.79654786, 110.39079666),
(14, 1, 'Jalan Cungkuk Raya, Ngestiharjo, Kasihan, Bantul Regency, Special Region of Yogyakarta, Java, 55253, Indonesia', 'Gang Tanjung 7, Muja muju, Umbulharjo, Yogyakarta, Special Region of Yogyakarta, Java, 55165, Indonesia', '2025-07-16 12:02:00', 133, 'active', 'fsfs', -7.79501719, 110.34753799, -7.79773838, 110.38976669);

-- --------------------------------------------------------

--
-- Table structure for table `print_jobs`
--

CREATE TABLE `print_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `printer_provider_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name_encrypted` varchar(255) NOT NULL,
  `copies` int(5) NOT NULL DEFAULT 1,
  `status` enum('pending','accepted','printing','completed','canceled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psychologists_details`
--

CREATE TABLE `psychologists_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `experience_years` int(3) NOT NULL,
  `description` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings_reviews`
--

CREATE TABLE `ratings_reviews` (
  `id` int(11) NOT NULL,
  `service_type` enum('psychologist','nebeng','print','laptop','jastip') NOT NULL,
  `service_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_type` enum('psychologist','nebeng','print','laptop','jastip') NOT NULL,
  `service_booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('user','psychologist','admin','runner') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `profile_picture`, `phone_number`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Makan123', 'soto@gmail.com', '$2y$10$W0cOaC9lzUbSpdasM1ZATuCJbFWXzDz9qKDhlaRa8oKE80g3R3aTm', 'Fatah', '1_686973ac57579_WhatsApp Image 2025-07-04 at 13.21.20.jpeg', NULL, 'admin', '2025-07-05 17:18:48', '2025-07-07 15:12:41'),
(2, 'Ayah Nasi', 'a@gmail.com', '$2y$10$oOF0mwoPjhDkm.kiViHwjeRRBL7koURIwmHuGGyasoHCovW6E.sQ.', NULL, 'default.png', NULL, 'user', '2025-07-05 18:03:28', '2025-07-05 18:03:28'),
(3, 'sotoenak', 'yaalllahkokbisa@gmail.com', '$2y$10$rFJwMHt2RsoqRqjrMtSpN.Hs1Emrxl/CEJF7N5/ijgWsH4isntqBC', NULL, 'default.png', NULL, 'runner', '2025-07-06 05:29:18', '2025-07-07 15:14:11'),
(4, 'ujangkedu', 'ujangkedu@gmail.com', '$2y$10$J57k9U2R.7naY..szVrFUuWx2CORJRW7tK0/dZ6HjAAx4ZZGN/dCK', NULL, 'default.png', NULL, 'psychologist', '2025-07-07 14:36:36', '2025-07-07 15:14:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consultation_bookings`
--
ALTER TABLE `consultation_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `psychologist_id` (`psychologist_id`);

--
-- Indexes for table `jastip_orders`
--
ALTER TABLE `jastip_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `runner_id` (`runner_id`);

--
-- Indexes for table `laptops`
--
ALTER TABLE `laptops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `laptop_bookings`
--
ALTER TABLE `laptop_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `laptop_id` (`laptop_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `nebeng_bookings`
--
ALTER TABLE `nebeng_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ride_id` (`ride_id`),
  ADD KEY `passenger_id` (`passenger_id`);

--
-- Indexes for table `nebeng_rides`
--
ALTER TABLE `nebeng_rides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `print_jobs`
--
ALTER TABLE `print_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `printer_provider_id` (`printer_provider_id`);

--
-- Indexes for table `psychologists_details`
--
ALTER TABLE `psychologists_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings_reviews`
--
ALTER TABLE `ratings_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultation_bookings`
--
ALTER TABLE `consultation_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jastip_orders`
--
ALTER TABLE `jastip_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `laptops`
--
ALTER TABLE `laptops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `laptop_bookings`
--
ALTER TABLE `laptop_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nebeng_bookings`
--
ALTER TABLE `nebeng_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nebeng_rides`
--
ALTER TABLE `nebeng_rides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `print_jobs`
--
ALTER TABLE `print_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psychologists_details`
--
ALTER TABLE `psychologists_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings_reviews`
--
ALTER TABLE `ratings_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultation_bookings`
--
ALTER TABLE `consultation_bookings`
  ADD CONSTRAINT `consultation_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_bookings_ibfk_2` FOREIGN KEY (`psychologist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jastip_orders`
--
ALTER TABLE `jastip_orders`
  ADD CONSTRAINT `jastip_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jastip_orders_ibfk_2` FOREIGN KEY (`runner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `laptops`
--
ALTER TABLE `laptops`
  ADD CONSTRAINT `laptops_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laptop_bookings`
--
ALTER TABLE `laptop_bookings`
  ADD CONSTRAINT `laptop_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laptop_bookings_ibfk_2` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nebeng_bookings`
--
ALTER TABLE `nebeng_bookings`
  ADD CONSTRAINT `nebeng_bookings_ibfk_1` FOREIGN KEY (`ride_id`) REFERENCES `nebeng_rides` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nebeng_bookings_ibfk_2` FOREIGN KEY (`passenger_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nebeng_rides`
--
ALTER TABLE `nebeng_rides`
  ADD CONSTRAINT `nebeng_rides_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `print_jobs`
--
ALTER TABLE `print_jobs`
  ADD CONSTRAINT `print_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `print_jobs_ibfk_2` FOREIGN KEY (`printer_provider_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `psychologists_details`
--
ALTER TABLE `psychologists_details`
  ADD CONSTRAINT `psychologists_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(11) NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

--
-- Dumping data for table `pma__export_templates`
--

INSERT INTO `pma__export_templates` (`id`, `username`, `export_type`, `template_name`, `template_data`) VALUES
(1, 'root', 'database', 'lagibutuh', '{\"quick_or_custom\":\"quick\",\"what\":\"sql\",\"structure_or_data_forced\":\"0\",\"table_select[]\":[\"consultation_bookings\",\"jastip_orders\",\"laptops\",\"laptop_bookings\",\"messages\",\"nebeng_bookings\",\"nebeng_rides\",\"print_jobs\",\"psychologists_details\",\"ratings_reviews\",\"transactions\",\"users\"],\"table_structure[]\":[\"consultation_bookings\",\"jastip_orders\",\"laptops\",\"laptop_bookings\",\"messages\",\"nebeng_bookings\",\"nebeng_rides\",\"print_jobs\",\"psychologists_details\",\"ratings_reviews\",\"transactions\",\"users\"],\"table_data[]\":[\"consultation_bookings\",\"jastip_orders\",\"laptops\",\"laptop_bookings\",\"messages\",\"nebeng_bookings\",\"nebeng_rides\",\"print_jobs\",\"psychologists_details\",\"ratings_reviews\",\"transactions\",\"users\"],\"aliases_new\":\"\",\"output_format\":\"sendit\",\"filename_template\":\"@DATABASE@\",\"remember_template\":\"on\",\"charset\":\"utf-8\",\"compression\":\"none\",\"maxsize\":\"\",\"codegen_structure_or_data\":\"data\",\"codegen_format\":\"0\",\"csv_separator\":\",\",\"csv_enclosed\":\"\\\"\",\"csv_escaped\":\"\\\"\",\"csv_terminated\":\"AUTO\",\"csv_null\":\"NULL\",\"csv_columns\":\"something\",\"csv_structure_or_data\":\"data\",\"excel_null\":\"NULL\",\"excel_columns\":\"something\",\"excel_edition\":\"win\",\"excel_structure_or_data\":\"data\",\"json_structure_or_data\":\"data\",\"json_unicode\":\"something\",\"latex_caption\":\"something\",\"latex_structure_or_data\":\"structure_and_data\",\"latex_structure_caption\":\"Structure of table @TABLE@\",\"latex_structure_continued_caption\":\"Structure of table @TABLE@ (continued)\",\"latex_structure_label\":\"tab:@TABLE@-structure\",\"latex_relation\":\"something\",\"latex_comments\":\"something\",\"latex_mime\":\"something\",\"latex_columns\":\"something\",\"latex_data_caption\":\"Content of table @TABLE@\",\"latex_data_continued_caption\":\"Content of table @TABLE@ (continued)\",\"latex_data_label\":\"tab:@TABLE@-data\",\"latex_null\":\"\\\\textit{NULL}\",\"mediawiki_structure_or_data\":\"structure_and_data\",\"mediawiki_caption\":\"something\",\"mediawiki_headers\":\"something\",\"htmlword_structure_or_data\":\"structure_and_data\",\"htmlword_null\":\"NULL\",\"ods_null\":\"NULL\",\"ods_structure_or_data\":\"data\",\"odt_structure_or_data\":\"structure_and_data\",\"odt_relation\":\"something\",\"odt_comments\":\"something\",\"odt_mime\":\"something\",\"odt_columns\":\"something\",\"odt_null\":\"NULL\",\"pdf_report_title\":\"\",\"pdf_structure_or_data\":\"structure_and_data\",\"phparray_structure_or_data\":\"data\",\"sql_include_comments\":\"something\",\"sql_header_comment\":\"\",\"sql_use_transaction\":\"something\",\"sql_compatibility\":\"NONE\",\"sql_structure_or_data\":\"structure_and_data\",\"sql_create_table\":\"something\",\"sql_auto_increment\":\"something\",\"sql_create_view\":\"something\",\"sql_procedure_function\":\"something\",\"sql_create_trigger\":\"something\",\"sql_backquotes\":\"something\",\"sql_type\":\"INSERT\",\"sql_insert_syntax\":\"both\",\"sql_max_query_size\":\"50000\",\"sql_hex_for_binary\":\"something\",\"sql_utc_time\":\"something\",\"texytext_structure_or_data\":\"structure_and_data\",\"texytext_null\":\"NULL\",\"xml_structure_or_data\":\"data\",\"xml_export_events\":\"something\",\"xml_export_functions\":\"something\",\"xml_export_procedures\":\"something\",\"xml_export_tables\":\"something\",\"xml_export_triggers\":\"something\",\"xml_export_views\":\"something\",\"xml_export_contents\":\"something\",\"yaml_structure_or_data\":\"data\",\"\":null,\"lock_tables\":null,\"as_separate_files\":null,\"csv_removeCRLF\":null,\"excel_removeCRLF\":null,\"json_pretty_print\":null,\"htmlword_columns\":null,\"ods_columns\":null,\"sql_dates\":null,\"sql_relation\":null,\"sql_mime\":null,\"sql_disable_fk\":null,\"sql_views_as_tables\":null,\"sql_metadata\":null,\"sql_create_database\":null,\"sql_drop_table\":null,\"sql_if_not_exists\":null,\"sql_simple_view_export\":null,\"sql_view_current_user\":null,\"sql_or_replace_view\":null,\"sql_truncate\":null,\"sql_delayed\":null,\"sql_ignore\":null,\"texytext_columns\":null}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"lagibutuh_db\",\"table\":\"jastip_orders\"},{\"db\":\"lagibutuh_db\",\"table\":\"users\"},{\"db\":\"lagibutuh_db\",\"table\":\"nebeng_rides\"},{\"db\":\"lagibutuh_db\",\"table\":\"nebeng_bookings\"},{\"db\":\"lagibutuh_db\",\"table\":\"laptops\"},{\"db\":\"lagibutuh_db\",\"table\":\"print_jobs\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

--
-- Dumping data for table `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'lagibutuh_db', 'users', '{\"CREATE_TIME\":\"2025-07-06 00:16:52\",\"col_order\":[0,3,2,4,1,5,6,7,8,9],\"col_visib\":[1,1,1,1,1,1,1,1,1,1]}', '2025-07-07 17:37:56');

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-07-08 00:31:38', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
