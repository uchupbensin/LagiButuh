-- Skema Database Lengkap untuk Proyek LagiButuh

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

--
-- Database: `lagibutuh_db`
--

--
-- Tabel untuk `users` (Pengguna)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `psychologists_details` (Detail Psikolog)
--
CREATE TABLE `psychologists_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `experience_years` int(3) NOT NULL,
  `description` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `consultation_bookings` (Booking Konsultasi)
--
CREATE TABLE `consultation_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `psychologist_id` int(11) NOT NULL,
  `schedule_time` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','canceled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `nebeng_rides` (Tumpangan Nebeng)
--
CREATE TABLE `nebeng_rides` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `departure_time` datetime NOT NULL,
  `available_seats` int(3) NOT NULL,
  `status` enum('active','full','completed','canceled') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `print_jobs` (Pekerjaan Cetak)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `laptops` (Data Laptop)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `laptop_bookings` (Booking Laptop)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `jastip_orders` (Pesanan Jasa Titip)
--
CREATE TABLE `jastip_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `runner_id` int(11) DEFAULT NULL,
  `item_description` text NOT NULL,
  `purchase_location` varchar(255) NOT NULL,
  `delivery_location` varchar(255) NOT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `status` enum('open','accepted','purchased','delivered','canceled') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `ratings_reviews` (Rating dan Ulasan) - INI YANG HILANG
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `transactions` (Transaksi)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabel untuk `messages` (Pesan Chat)
--
CREATE TABLE `messages` (
  `id` bigint(20) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--
ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `psychologists_details` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);
ALTER TABLE `consultation_bookings` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `psychologist_id` (`psychologist_id`);
ALTER TABLE `nebeng_rides` ADD PRIMARY KEY (`id`), ADD KEY `driver_id` (`driver_id`);
ALTER TABLE `print_jobs` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `printer_provider_id` (`printer_provider_id`);
ALTER TABLE `laptops` ADD PRIMARY KEY (`id`), ADD KEY `owner_id` (`owner_id`);
ALTER TABLE `laptop_bookings` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `laptop_id` (`laptop_id`);
ALTER TABLE `jastip_orders` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `runner_id` (`runner_id`);
ALTER TABLE `ratings_reviews` ADD PRIMARY KEY (`id`), ADD KEY `reviewer_id` (`reviewer_id`), ADD KEY `provider_id` (`provider_id`);
ALTER TABLE `transactions` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);
ALTER TABLE `messages` ADD PRIMARY KEY (`id`), ADD KEY `consultation_id` (`consultation_id`), ADD KEY `sender_id` (`sender_id`), ADD KEY `receiver_id` (`receiver_id`);

--
-- AUTO_INCREMENT for dumped tables
--
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `psychologists_details` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `consultation_bookings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nebeng_rides` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `print_jobs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `laptops` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `laptop_bookings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `jastip_orders` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ratings_reviews` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `transactions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `messages` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--
ALTER TABLE `psychologists_details` ADD CONSTRAINT `psychologists_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `consultation_bookings` ADD CONSTRAINT `consultation_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `consultation_bookings_ibfk_2` FOREIGN KEY (`psychologist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `nebeng_rides` ADD CONSTRAINT `nebeng_rides_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `print_jobs` ADD CONSTRAINT `print_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `print_jobs_ibfk_2` FOREIGN KEY (`printer_provider_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `laptops` ADD CONSTRAINT `laptops_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `laptop_bookings` ADD CONSTRAINT `laptop_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `laptop_bookings_ibfk_2` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE;
ALTER TABLE `jastip_orders` ADD CONSTRAINT `jastip_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `jastip_orders_ibfk_2` FOREIGN KEY (`runner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

COMMIT;
