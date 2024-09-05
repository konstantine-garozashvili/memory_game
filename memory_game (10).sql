-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 05, 2024 at 07:26 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `memory_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `user_id`, `message`, `created_at`) VALUES
(1, 15, 'hello', '2024-09-04 14:23:17'),
(2, 15, 'hello', '2024-09-04 14:23:19'),
(3, 15, 'hello', '2024-09-04 14:23:19'),
(4, 15, 'hello', '2024-09-04 14:23:19'),
(5, 15, 'hello', '2024-09-04 14:23:19'),
(6, 15, 'hello', '2024-09-04 14:23:20'),
(7, 15, 'hello', '2024-09-04 14:23:20'),
(8, 15, 'hello', '2024-09-04 14:23:20'),
(9, 15, 'hello', '2024-09-04 14:23:23'),
(10, 15, 'hello', '2024-09-04 14:23:24'),
(11, 15, 'hello', '2024-09-04 14:23:24'),
(12, 15, 'hello', '2024-09-04 14:23:25'),
(13, 10, 'd', '2024-09-04 14:23:37'),
(14, 10, 'd', '2024-09-04 14:23:37'),
(15, 10, 'dd', '2024-09-04 14:23:46'),
(16, 15, 'hello', '2024-09-04 14:23:48'),
(17, 15, 'hello', '2024-09-04 14:23:48'),
(18, 10, 'dd', '2024-09-04 14:26:31'),
(19, 10, 'dd', '2024-09-04 14:26:31'),
(20, 10, 'dd', '2024-09-04 14:26:31'),
(21, 10, 'dd', '2024-09-04 14:26:31'),
(22, 10, 'dd', '2024-09-04 14:26:32'),
(23, 10, 'dd', '2024-09-04 14:26:32'),
(24, 10, 'dd', '2024-09-04 14:26:32'),
(25, 10, 'h', '2024-09-04 14:32:22'),
(26, 10, 'h', '2024-09-04 14:32:22'),
(27, 10, 'd', '2024-09-04 14:40:03'),
(28, 10, 'd', '2024-09-04 14:40:04'),
(29, 10, 'd', '2024-09-04 14:40:04'),
(30, 10, 'd', '2024-09-04 14:40:04'),
(31, 10, 'd', '2024-09-04 14:40:04'),
(32, 10, 'd', '2024-09-04 14:40:04'),
(33, 10, 'd', '2024-09-04 14:40:04'),
(34, 15, 'd', '2024-09-04 14:40:14'),
(35, 15, 'd', '2024-09-04 14:40:14');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `player1_id` int DEFAULT NULL,
  `player2_id` int DEFAULT NULL,
  `status` enum('pending','active','finished') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `current_turn_id` int DEFAULT NULL,
  `card_positions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `player1_matches` int DEFAULT '0',
  `player2_matches` int DEFAULT '0',
  `winner_id` int DEFAULT NULL,
  `game_mode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'hidden_memory'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_moves`
--

CREATE TABLE `game_moves` (
  `id` int NOT NULL,
  `game_id` int NOT NULL,
  `player_id` int NOT NULL,
  `card_index` int NOT NULL,
  `action` enum('flip','unflip','match') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_moves`
--

INSERT INTO `game_moves` (`id`, `game_id`, `player_id`, `card_index`, `action`, `created_at`) VALUES
(970, 35, 9, 2, 'flip', '2024-09-04 07:11:51'),
(971, 35, 9, 1, 'flip', '2024-09-04 07:11:53'),
(972, 35, 9, 2, 'unflip', '2024-09-04 07:11:53'),
(973, 35, 9, 1, 'unflip', '2024-09-04 07:11:53'),
(974, 35, 8, 2, 'flip', '2024-09-04 07:11:57'),
(975, 35, 8, 1, 'flip', '2024-09-04 07:11:58'),
(976, 35, 8, 2, 'unflip', '2024-09-04 07:11:59'),
(977, 35, 8, 1, 'unflip', '2024-09-04 07:11:59'),
(978, 35, 9, 3, 'flip', '2024-09-04 07:12:05'),
(979, 35, 9, 2, 'flip', '2024-09-04 07:12:07'),
(980, 35, 9, 3, 'match', '2024-09-04 07:12:07'),
(981, 35, 9, 2, 'match', '2024-09-04 07:12:07'),
(982, 35, 9, 7, 'flip', '2024-09-04 07:12:10'),
(983, 35, 9, 0, 'flip', '2024-09-04 07:12:11'),
(984, 35, 9, 7, 'match', '2024-09-04 07:12:11'),
(985, 35, 9, 0, 'match', '2024-09-04 07:12:11'),
(986, 35, 9, 1, 'flip', '2024-09-04 07:12:12'),
(987, 35, 9, 4, 'flip', '2024-09-04 07:12:14'),
(988, 35, 9, 1, 'unflip', '2024-09-04 07:12:14'),
(989, 35, 9, 4, 'unflip', '2024-09-04 07:12:14'),
(990, 35, 8, 5, 'flip', '2024-09-04 07:12:16'),
(991, 35, 8, 10, 'flip', '2024-09-04 07:12:18'),
(992, 35, 8, 5, 'unflip', '2024-09-04 07:12:19'),
(993, 35, 8, 10, 'unflip', '2024-09-04 07:12:19'),
(994, 35, 9, 11, 'flip', '2024-09-04 07:12:20'),
(995, 35, 9, 4, 'flip', '2024-09-04 07:12:21'),
(996, 35, 9, 11, 'match', '2024-09-04 07:12:22'),
(997, 35, 9, 4, 'match', '2024-09-04 07:12:22'),
(998, 35, 9, 1, 'flip', '2024-09-04 07:12:22'),
(999, 35, 9, 8, 'flip', '2024-09-04 07:12:24'),
(1000, 35, 9, 1, 'unflip', '2024-09-04 07:12:24'),
(1001, 35, 9, 8, 'unflip', '2024-09-04 07:12:24'),
(1002, 35, 8, 5, 'flip', '2024-09-04 07:12:26'),
(1003, 35, 8, 10, 'flip', '2024-09-04 07:12:27'),
(1004, 35, 8, 5, 'unflip', '2024-09-04 07:12:28'),
(1005, 35, 8, 10, 'unflip', '2024-09-04 07:12:28'),
(1006, 35, 9, 8, 'flip', '2024-09-04 07:12:29'),
(1007, 35, 9, 10, 'flip', '2024-09-04 07:12:30'),
(1008, 35, 9, 8, 'match', '2024-09-04 07:12:31'),
(1009, 35, 9, 10, 'match', '2024-09-04 07:12:31'),
(1010, 35, 9, 6, 'flip', '2024-09-04 07:12:32'),
(1011, 35, 9, 14, 'flip', '2024-09-04 07:12:34'),
(1012, 35, 9, 6, 'unflip', '2024-09-04 07:12:35'),
(1013, 35, 9, 14, 'unflip', '2024-09-04 07:12:35'),
(1014, 35, 8, 5, 'flip', '2024-09-04 07:12:37'),
(1015, 35, 8, 14, 'flip', '2024-09-04 07:12:38'),
(1016, 35, 8, 5, 'match', '2024-09-04 07:12:39'),
(1017, 35, 8, 14, 'match', '2024-09-04 07:12:39'),
(1018, 35, 8, 13, 'flip', '2024-09-04 07:12:44'),
(1019, 35, 8, 15, 'flip', '2024-09-04 07:12:46'),
(1020, 35, 8, 13, 'unflip', '2024-09-04 07:12:46'),
(1021, 35, 8, 15, 'unflip', '2024-09-04 07:12:46'),
(1022, 35, 9, 6, 'flip', '2024-09-04 07:12:48'),
(1023, 35, 9, 15, 'flip', '2024-09-04 07:12:49'),
(1024, 35, 9, 6, 'match', '2024-09-04 07:12:50'),
(1025, 35, 9, 15, 'match', '2024-09-04 07:12:50'),
(1026, 35, 9, 9, 'flip', '2024-09-04 07:12:51'),
(1027, 35, 9, 13, 'flip', '2024-09-04 07:12:52'),
(1028, 35, 9, 9, 'match', '2024-09-04 07:12:53'),
(1029, 35, 9, 13, 'match', '2024-09-04 07:12:53'),
(1030, 35, 9, 12, 'flip', '2024-09-04 07:12:53'),
(1031, 35, 9, 1, 'flip', '2024-09-04 07:12:55'),
(1032, 35, 9, 12, 'match', '2024-09-04 07:12:55'),
(1033, 35, 9, 1, 'match', '2024-09-04 07:12:55'),
(1034, 36, 13, 2, 'flip', '2024-09-04 07:58:41'),
(1035, 36, 13, 1, 'flip', '2024-09-04 07:58:43'),
(1036, 36, 13, 2, 'unflip', '2024-09-04 07:58:43'),
(1037, 36, 13, 1, 'unflip', '2024-09-04 07:58:43'),
(1038, 36, 12, 2, 'flip', '2024-09-04 07:58:46'),
(1039, 36, 12, 6, 'flip', '2024-09-04 07:58:47'),
(1040, 36, 12, 2, 'unflip', '2024-09-04 07:58:48'),
(1041, 36, 12, 6, 'unflip', '2024-09-04 07:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE `invitations` (
  `id` int NOT NULL,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `status` enum('pending','accepted','declined') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `game_mode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `last_login`) VALUES
(10, 'admin', '$2y$10$nmh3Tu8LK8KvWeMP1GPbDeXLDwG9Ywz2k4TIp0i49uCf3FZ5Bz4by', 'admin@gmail.com', 'admin', '2024-09-05 07:21:32'),
(13, 'bb', '$2y$10$6LIQY4GOaq7YMaE689MKYOwaZAH/IyZGa7qKHQYBvd62GZchykSRG', 'A@gmail.com', 'user', NULL),
(14, 'ddd', '$2y$10$ZydLt35AB8KEmcrlfC9jeulXkgWI0TJjSoxIK0iFKbvu3qF2CGD8q', 'dada@gmail.com', 'user', '2024-09-05 07:21:10'),
(15, 'aaaa', '$2y$10$SHpvmG9vD9ZKOjEkJ4kfm.kiSWm/CYhHMW/Yty4xTgA3Ir/qVVxe6', 'tata@gmail.com', 'user', '2024-09-04 14:22:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Indexes for table `game_moves`
--
ALTER TABLE `game_moves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

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
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `game_moves`
--
ALTER TABLE `game_moves`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1042;

--
-- AUTO_INCREMENT for table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`player1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `games_ibfk_2` FOREIGN KEY (`player2_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `invitations_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
