-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2024 at 08:00 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alifu`
--

-- --------------------------------------------------------

--
-- Table structure for table `apartments`
--

CREATE TABLE `apartments` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `description` text COLLATE utf8mb4_latvian_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_latvian_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_latvian_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `apartments`
--

INSERT INTO `apartments` (`id`, `name`, `description`, `price`, `type`, `image_url`, `created_at`, `updated_at`) VALUES
(5, 'Premium', 'The most premium experience you will ever have.', '300.00', 'Premium', 'img\\premium.jpg', '2023-09-03 21:11:10', '2024-06-19 23:11:59'),
(6, 'Suite', 'The best experience we can offer', '500.00', 'Suite', 'img\\suite.jpg', '2023-09-03 21:06:10', '2023-10-26 09:06:22'),
(7, 'Deluxe', 'Still great experience but at a friendlier price.', '200.00', 'Deluxe', 'img\\deluxe.jpg', '2023-12-18 11:36:01', '2023-12-18 11:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `apartment_id` int(11) UNSIGNED NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_latvian_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `apartment_id`, `check_in_date`, `check_out_date`, `price`, `status`, `created_at`, `updated_at`) VALUES
(10, 22, 5, '2024-06-21', '2024-06-22', '0.00', 'Accepted', '2024-06-13 21:20:08', '2024-06-20 05:33:42'),
(11, 23, 5, '2024-06-20', '2024-06-22', '0.00', 'Cancelled', '2024-06-13 21:23:24', '2024-06-13 21:23:26'),
(12, 22, 7, '2024-07-15', '2024-07-19', '0.00', 'Pending', '2024-06-13 23:55:48', '2024-06-13 23:55:48'),
(13, 22, 5, '2024-05-14', '2024-05-16', '0.00', 'Cancelled', '2024-06-13 23:56:00', '2024-06-19 23:10:25'),
(14, 22, 5, '2024-06-20', '2024-06-12', '0.00', 'Cancelled', '2024-06-19 21:22:41', '2024-06-19 21:22:46'),
(15, 22, 5, '2024-06-20', '2024-06-21', '0.00', 'Pending', '2024-06-19 21:25:05', '2024-06-19 21:25:05'),
(16, 22, 5, '2024-06-17', '2024-06-19', '0.00', 'Pending', '2024-06-19 21:25:57', '2024-06-19 21:25:57'),
(17, 22, 5, '2024-07-25', '2024-07-27', '0.00', 'Pending', '2024-06-19 21:31:16', '2024-06-19 21:31:16'),
(18, 24, 5, '2024-06-24', '2024-06-26', '0.00', 'Cancelled', '2024-06-19 22:04:38', '2024-06-19 22:09:05'),
(19, 24, 7, '2024-06-21', '2024-06-22', '0.00', 'Cancelled', '2024-06-19 22:17:59', '2024-06-19 22:22:00'),
(20, 24, 6, '2024-06-21', '2024-06-27', '0.00', 'Accepted', '2024-06-19 22:25:46', '2024-06-20 05:44:54'),
(21, 24, 6, '2024-06-20', '2024-06-21', '0.00', 'Cancelled', '2024-06-19 22:35:34', '2024-06-20 05:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `content` text COLLATE utf8mb4_latvian_ci NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `rating`, `content`, `approved`, `created_at`, `updated_at`) VALUES
(2, 22, 5, 'Good staff, I liked the place.', 1, '2024-06-04 17:19:39', '2024-06-20 05:34:16'),
(3, 23, 5, 'Bij baig lab, es brauks vel', 1, '2024-06-05 07:13:39', '2024-06-05 07:14:20'),
(4, 23, 4, 'Test', 1, '2024-06-05 07:27:04', '2024-06-05 07:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `spa_procedures`
--

CREATE TABLE `spa_procedures` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `description` text COLLATE utf8mb4_latvian_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `personal_id` char(12) COLLATE utf8mb4_latvian_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_latvian_ci NOT NULL,
  `password` varchar(200) COLLATE utf8mb4_latvian_ci NOT NULL,
  `total_reservations` int(11) UNSIGNED DEFAULT 0,
  `administrator` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `personal_id`, `phone_number`, `password`, `total_reservations`, `administrator`, `created_at`, `updated_at`) VALUES
(17, 'Markuss', 'Priede', '01dpmpriede@rvt.lv', '280904-41259', '20408925', '$2y$10$oT82CYwHeoAgazymwwsSeOC2f3O.XEWZLApcPqLRT8RTwyFGZMdJK', 0, 0, '2023-04-30 20:41:38', '2023-10-26 09:07:39'),
(20, 'Test', 'Test', 'test@test.test', 'test', 'test', '$2y$10$iWMmNdbK55Lvm3IFmASkEO4xfwCffnXxWJ6pPGxiZKxHEw8b0lsDS', 0, 0, '2023-05-12 09:49:56', '2024-06-13 20:43:03'),
(22, 'Valdis', 'Krūmiņš', 'valdiskrumins@gmail.com', '240914-41243', '26702882', '$2y$10$YkmxRIyNfLgqzInp00WGTuhBeIAT3jg3SIyq7JuWGwlpSlYJ3n6Ra', 0, 1, '2023-05-14 05:33:32', '2024-06-05 07:09:10'),
(23, 'Test', 'Test', 'Test@test.com', '000000-00000', '26702882', '$2y$10$Tx6GrS71eZWrJVwqsHiQjODLnmCu9X5IaduKioRH50bQa6ImwKJyi', 0, 1, '2024-06-04 19:40:33', '2024-06-13 20:43:07'),
(24, 'Markuss', 'Priede', 'markusspriede@inbox.lv', '280904-20353', '20408925', '$2y$10$UIjaQUZV6Zwx.kvqUJDgdOdJicnIvNjs0Z./M8wc3GZtVU0VTQaJq', 0, 1, '2024-06-19 22:02:18', '2024-06-19 22:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_spa_reservations`
--

CREATE TABLE `user_spa_reservations` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `spa_procedure_id` int(11) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_latvian_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apartments`
--
ALTER TABLE `apartments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `apartment_id` (`apartment_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `spa_procedures`
--
ALTER TABLE `spa_procedures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD UNIQUE KEY `personal_id_unique` (`personal_id`);

--
-- Indexes for table `user_spa_reservations`
--
ALTER TABLE `user_spa_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `spa_procedure_id` (`spa_procedure_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apartments`
--
ALTER TABLE `apartments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `spa_procedures`
--
ALTER TABLE `spa_procedures`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_spa_reservations`
--
ALTER TABLE `user_spa_reservations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_spa_reservations`
--
ALTER TABLE `user_spa_reservations`
  ADD CONSTRAINT `user_spa_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_spa_reservations_ibfk_2` FOREIGN KEY (`spa_procedure_id`) REFERENCES `spa_procedures` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
