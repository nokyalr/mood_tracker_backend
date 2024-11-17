-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 02:18 AM
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
-- Database: `mood_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_date` date NOT NULL,
  `activity_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_suggestions`
--

CREATE TABLE `activity_suggestions` (
  `suggestion_id` int(11) NOT NULL,
  `mood_category` enum('Depressed','Anxious','Angry','Overwhelmed','Sad','Tired','Frustrated','Disappointed','Okay','Satisfied','Hopeful','Relaxed','Happy','Excited','Proud','Loved','Euphoric','Inspired','Grateful','Accomplished') NOT NULL,
  `suggestion_text` text NOT NULL,
  `link_to_article` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE `calendar` (
  `calendar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mood_date` date NOT NULL,
  `mood_summary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted','blocked') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moods`
--

CREATE TABLE `moods` (
  `mood_id` int(11) NOT NULL,
  `mood_level` enum('Bad','Poor','Medium','Good','Excellent') NOT NULL,
  `mood_category` enum('Depressed','Anxious','Angry','Overwhelmed','Sad','Tired','Frustrated','Disappointed','Okay','Satisfied','Hopeful','Relaxed','Happy','Excited','Proud','Loved','Euphoric','Inspired','Grateful','Accomplished') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moods`
--

INSERT INTO `moods` (`mood_id`, `mood_level`, `mood_category`) VALUES
(1, 'Bad', 'Depressed'),
(2, 'Bad', 'Anxious'),
(3, 'Bad', 'Angry'),
(4, 'Bad', 'Overwhelmed'),
(5, 'Poor', 'Sad'),
(6, 'Poor', 'Tired'),
(7, 'Poor', 'Frustrated'),
(8, 'Poor', 'Disappointed'),
(9, 'Medium', 'Okay'),
(10, 'Medium', 'Satisfied'),
(11, 'Medium', 'Hopeful'),
(12, 'Medium', 'Relaxed'),
(13, 'Good', 'Happy'),
(14, 'Good', 'Excited'),
(15, 'Good', 'Proud'),
(16, 'Good', 'Loved'),
(17, 'Excellent', 'Euphoric'),
(18, 'Excellent', 'Inspired'),
(19, 'Excellent', 'Grateful'),
(20, 'Excellent', 'Accomplished');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mood_id` int(11) NOT NULL,
  `mood_score` int(11) DEFAULT NULL CHECK (`mood_score` between 1 and 5),
  `post_date` date NOT NULL,
  `content` text DEFAULT NULL,
  `is_posted` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `mood_id`, `mood_score`, `post_date`, `content`, `is_posted`, `created_at`, `updated_at`) VALUES
(1, 1, 13, 4, '2024-11-10', 'Hari ini sangat menyenangkan! Saya berhasil menyelesaikan banyak tugas.', 1, '2024-11-16 05:54:48', '2024-11-16 05:54:48'),
(2, 2, 3, 2, '2024-11-09', 'Saya merasa kesal karena banyak hal tidak berjalan sesuai rencana.', 1, '2024-11-16 05:54:48', '2024-11-16 05:54:48'),
(3, 3, 17, 5, '2024-11-08', 'Luar biasa! Saya memenangkan kompetisi yang sangat sulit!', 1, '2024-11-16 05:54:48', '2024-11-16 05:54:48'),
(4, 1, 9, 3, '2024-11-07', 'Hari yang biasa saja, tidak ada hal spesial.', 1, '2024-11-16 05:54:48', '2024-11-16 05:54:48'),
(5, 2, 1, 1, '2024-11-06', 'Saya merasa sedih karena kehilangan sesuatu yang berharga.', 1, '2024-11-16 05:54:48', '2024-11-16 10:35:05'),
(6, 3, 14, 4, '2024-11-05', 'Saya sangat bersemangat tentang proyek baru yang sedang saya kerjakan.', 1, '2024-11-16 05:54:48', '2024-11-16 05:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_month` date NOT NULL,
  `report_data` longtext DEFAULT NULL CHECK (json_valid(`report_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_moods`
--

CREATE TABLE `sub_moods` (
  `sub_mood_id` int(11) NOT NULL,
  `mood_id` int(11) NOT NULL,
  `sub_mood_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `name`, `password`, `profile_picture`, `created_at`) VALUES
(1, 'anniey', 'Anniey Flare', '$2y$10$ZDPK7N5ys4brPgK0BG21Du57IMQVzgS4mfJ/A9J21jHfjM.0OYOt2', 'assets/images/people4.png', '2024-11-16 05:47:27'),
(2, 'stevia_', 'Stevia Wize', '$2y$10$vHfUZRAmUAfAZlwkkxTTduNssOPC9lEOfGl5lwknBD3YCpxvyiG.q', 'assets/images/people5.png', '2024-11-16 05:47:27'),
(3, 'mar_lize', 'Marlize Abraham', '$2y$10$lgUx74f8R87MEvWTfAsUiuwjdbBJbDJ.XYs91.ZKxL74.XzDQlhTi', 'assets/images/people3.png', '2024-11-16 05:47:27'),
(4, 'noky', 'Noky Alrizqi', '$2y$10$lYxCQHuv8HprSHV.LPACcOcErcaYBC48OGWKC8bEo/kgT6T28nicC', 'assets/images/people3.png', '2024-11-16 12:29:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `activity_logs_fk_user` (`user_id`);

--
-- Indexes for table `activity_suggestions`
--
ALTER TABLE `activity_suggestions`
  ADD PRIMARY KEY (`suggestion_id`);

--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`calendar_id`),
  ADD KEY `calendar_fk_user` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `comments_fk_post` (`post_id`),
  ADD KEY `comments_fk_user` (`user_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`user_id`,`friend_id`),
  ADD KEY `friends_fk_friend` (`friend_id`);

--
-- Indexes for table `moods`
--
ALTER TABLE `moods`
  ADD PRIMARY KEY (`mood_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `posts_fk_user` (`user_id`),
  ADD KEY `posts_fk_mood` (`mood_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reports_fk_user` (`user_id`);

--
-- Indexes for table `sub_moods`
--
ALTER TABLE `sub_moods`
  ADD PRIMARY KEY (`sub_mood_id`),
  ADD KEY `sub_mood_fk_mood` (`mood_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_suggestions`
--
ALTER TABLE `activity_suggestions`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
  MODIFY `calendar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moods`
--
ALTER TABLE `moods`
  MODIFY `mood_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_moods`
--
ALTER TABLE `sub_moods`
  MODIFY `sub_mood_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `calendar`
--
ALTER TABLE `calendar`
  ADD CONSTRAINT `calendar_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_fk_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_fk_friend` FOREIGN KEY (`friend_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_fk_mood` FOREIGN KEY (`mood_id`) REFERENCES `moods` (`mood_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_moods`
--
ALTER TABLE `sub_moods`
  ADD CONSTRAINT `sub_mood_fk_mood` FOREIGN KEY (`mood_id`) REFERENCES `moods` (`mood_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
