-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 05:43 AM
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

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Great to hear about your day!', '2024-12-01 02:24:08', '2024-12-01 02:24:08'),
(2, 1, 3, 'Sounds productive, well done!', '2024-12-01 02:24:08', '2024-12-01 02:24:08'),
(3, 1, 4, 'What tasks did you complete?', '2024-12-01 02:24:08', '2024-12-01 02:24:08'),
(4, 7, 2, 'wow what is that :D', '2024-12-01 04:33:57', '2024-12-01 04:33:57'),
(5, 1, 1, 'ty', '2024-12-01 04:34:29', '2024-12-01 04:34:29'),
(6, 1, 1, 'lol', '2024-12-01 04:34:36', '2024-12-01 04:34:36'),
(7, 1, 1, 'lmao', '2024-12-01 04:35:27', '2024-12-01 04:35:27'),
(8, 1, 4, 'well done', '2024-12-01 04:39:14', '2024-12-01 04:39:14');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted','blocked') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`user_id`, `friend_id`, `status`) VALUES
(1, 2, 'accepted'),
(1, 3, 'accepted'),
(1, 4, 'accepted'),
(2, 1, 'accepted'),
(2, 3, 'accepted'),
(3, 1, 'accepted'),
(3, 2, 'accepted'),
(4, 1, 'accepted'),
(4, 5, 'accepted'),
(4, 9, 'accepted'),
(5, 4, 'accepted'),
(9, 4, 'accepted');

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
(1, 1, 13, 4, '2024-11-10', 'Today was very enjoyable! I managed to accomplish a lot of tasks.', 1, '2024-11-16 05:54:48', '2024-11-17 02:11:44'),
(2, 2, 3, 2, '2024-11-09', 'I feel annoyed because many things did not go as planned.', 1, '2024-11-16 05:54:48', '2024-11-17 02:11:44'),
(3, 3, 17, 5, '2024-11-08', 'Amazing! I won a very challenging competition!', 1, '2024-11-16 05:54:48', '2024-11-17 02:11:44'),
(4, 1, 9, 3, '2024-11-07', 'An ordinary day, nothing special.', 1, '2024-11-16 05:54:48', '2024-11-17 02:11:44'),
(5, 2, 1, 1, '2024-11-06', 'I feel sad because I lost something valuable.', 1, '2024-11-16 05:54:48', '2024-11-17 02:11:44'),
(6, 3, 14, 4, '2024-11-05', 'I am very excited about the new project I am working on.', 1, '2024-11-16 05:54:48', '2024-11-17 02:11:44'),
(7, 1, 17, 5, '2024-11-27', 'This is the best day of my life! I got a big suprize from my friends', 1, '2024-11-28 04:53:00', '2024-11-28 04:53:00'),
(8, 9, 15, 4, '0000-00-00', 'Hello worlds', 1, '2024-11-29 03:23:08', '2024-11-29 03:23:08'),
(9, 9, 19, 5, '2024-11-26', 'hari ini sangat cerah', 1, '2024-11-29 03:25:03', '2024-11-29 03:25:51'),
(10, 9, 4, 1, '2024-11-28', 'terjadi kecelekaaan', 1, '2024-11-29 03:26:54', '2024-11-29 03:26:54'),
(11, 9, 11, 3, '2024-11-29', 'hari ini normal', 1, '2024-11-29 03:27:31', '2024-11-29 03:27:31'),
(12, 9, 19, 5, '2024-11-01', 'saya ulang tahun', 1, '2024-11-29 03:29:22', '2024-11-29 03:29:22'),
(13, 4, 2, 2, '2024-11-29', 'Your post content here', 1, '2024-11-29 14:30:09', '2024-11-29 14:30:09'),
(14, 4, 5, 5, '2024-11-30', 'wow', 1, '2024-11-30 05:46:06', '2024-11-30 05:46:06'),
(15, 1, 1, 1, '2024-11-30', 'i got fight with my lil sister', 0, '2024-11-30 06:25:12', '2024-11-30 06:25:12'),
(16, 1, 1, 1, '2024-11-30', 'd', 0, '2024-11-30 06:28:42', '2024-11-30 06:28:42'),
(17, 4, 20, 5, '2024-11-30', 'sup', 1, '2024-11-30 06:47:50', '2024-11-30 06:47:50'),
(18, 4, 19, 5, '2024-11-30', 'w', 0, '2024-11-30 06:49:05', '2024-11-30 06:49:05'),
(19, 4, 17, 5, '2024-11-30', 'feeling good', 1, '2024-11-30 06:50:23', '2024-11-30 06:50:23'),
(20, 4, 14, 4, '2024-11-30', 'nc', 1, '2024-11-30 06:51:03', '2024-11-30 06:51:03'),
(21, 4, 5, 2, '2024-11-30', 'nothing special', 1, '2024-11-30 07:03:44', '2024-11-30 07:03:44'),
(22, 4, 6, 2, '2024-11-20', 'back to back', 1, '2024-11-30 07:31:02', '2024-11-30 07:31:02'),
(23, 3, 9, 3, '2024-12-01', 'need more time to finish my assigment..', 1, '2024-12-01 04:41:17', '2024-12-01 04:41:17');

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
(1, 'anniey', 'Annieyy', '$2y$10$rWJ9tBBcP9br.6kwS4.8S.9vvnta7b.WmI9t6r6dMPsSJanNRMQc.', 'assets/images/people1.png', '2024-11-16 05:47:27'),
(2, 'stevia_', 'Stevia Wize', '$2y$10$vHfUZRAmUAfAZlwkkxTTduNssOPC9lEOfGl5lwknBD3YCpxvyiG.q', 'assets/images/people5.png', '2024-11-16 05:47:27'),
(3, 'mar_lize', 'Marlize Abraham', '$2y$10$/w4tjzSCW8F4QuE31e8hHegat6ez5kP453hfmW41bPfui0p.WpKBe', 'assets/images/people2.png', '2024-11-16 05:47:27'),
(4, 'noky', 'Noky Alrizqi P A', '$2y$10$OHlji36PRo5HewdbB.MNY.g.jKFlhuVbFNLfXcDjyyTYsB/ah9ezK', 'assets/images/people7.png', '2024-11-16 12:29:10'),
(5, 'dailysam', 'sam', '$2y$10$bFsv3nbLm8wl3V1dQdE38.QiNCasRD29x55ruGqVhV8mYry7ZHDny', 'assets/images/people2.png', '2024-11-25 22:12:53'),
(6, 'funnyacell', 'ashila', '$2y$10$TiNs8jbItsO/y5vl2I8L5.VIVLZSAq/VS7aOPGXsa0K5HCInIrPOW', NULL, '2024-11-25 22:13:14'),
(7, 'johnside', 'john', '$2y$10$09r8yQUpUoRp7qjnc.FoFOpfxCGmCu01Shykdnawp46TYpL5fM5ku', NULL, '2024-11-25 22:13:30'),
(8, 'chopi', 'thariq', '$2y$10$obWQR8.PcjjPMSP1MRbWXu2Tepx3DJ7AF/sYLNovUIbdzcwbBWbF6', 'assets/images/people5.png', '2024-11-29 02:52:08'),
(9, 'user1', 'user', '$2y$10$X3aHD6JQ4q3duvDxkK.Uh.sk0E9nYv1SqNvoQjLA6n6rL8tu2htJ2', 'assets/images/people6.png', '2024-11-29 03:18:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_suggestions`
--
ALTER TABLE `activity_suggestions`
  ADD PRIMARY KEY (`suggestion_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_suggestions`
--
ALTER TABLE `activity_suggestions`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `moods`
--
ALTER TABLE `moods`
  MODIFY `mood_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
