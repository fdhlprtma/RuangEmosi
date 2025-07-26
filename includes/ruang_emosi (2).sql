-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 26, 2025 at 08:04 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ruang_emosi`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `counselor_id` int NOT NULL,
  `appointment_datetime` datetime NOT NULL,
  `message` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `article_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text,
  `featured_image` varchar(255) DEFAULT NULL,
  `author_id` int NOT NULL,
  `category` enum('depresi','kecemasan','self-care','stres','relationship') NOT NULL,
  `view_count` int DEFAULT '0',
  `is_published` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`article_id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author_id`, `category`, `view_count`, `is_published`, `created_at`, `updated_at`) VALUES
(7, 'asabsss', 'sasa', 'saa', 'sas', 'matic club motorcyle.png', 1, 'depresi', 3, 1, '2025-04-20 06:21:52', '2025-05-01 09:48:54'),
(8, 'wewew', 'wewwewe', 'ewew', 'wew', 'pharmacy-logo-vector.jpg', 1, 'depresi', 1, 1, '2025-04-20 06:27:27', '2025-05-01 09:48:52'),
(14, 'broken home', 'bjb', '99hjggu', 'bjbb', '—Pngtree—patient counseling with psychologist_15999393.png', 11, 'depresi', 0, 1, '2025-04-24 11:39:05', '2025-05-01 09:48:50');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_seen` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `consultation_id` int NOT NULL,
  `user_id` int NOT NULL,
  `counselor_id` int NOT NULL,
  `schedule` datetime DEFAULT NULL,
  `duration` int DEFAULT '60',
  `status` enum('pending','confirmed','completed','cancelled','accepted','rejected') DEFAULT 'pending',
  `notes` text,
  `rating` int DEFAULT NULL,
  `feedback` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `review` text,
  `counselor_reply` text,
  `reply_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`consultation_id`, `user_id`, `counselor_id`, `schedule`, `duration`, `status`, `notes`, `rating`, `feedback`, `created_at`, `updated_at`, `start_time`, `end_time`, `meeting_link`, `review`, `counselor_reply`, `reply_date`) VALUES
(22, 13, 10, '2222-02-12 00:00:00', 60, 'completed', NULL, 3, NULL, '2025-04-23 12:54:26', '2025-04-23 16:02:11', '11:02:00', '12:02:00', NULL, NULL, NULL, NULL),
(23, 13, 10, '2026-02-10 00:00:00', 60, 'completed', NULL, 2, NULL, '2025-04-23 13:05:43', '2025-04-23 17:48:34', '11:01:00', '12:03:00', NULL, NULL, NULL, NULL),
(24, 13, 10, '1212-02-21 00:00:00', 60, 'completed', NULL, 3, NULL, '2025-04-23 13:06:50', '2025-04-23 17:49:04', '12:02:00', '13:03:00', NULL, NULL, NULL, NULL),
(25, 13, 10, '2009-09-10 00:00:00', 60, 'completed', NULL, 1, NULL, '2025-04-23 13:13:58', '2025-04-23 17:48:55', '12:02:00', '21:02:00', NULL, NULL, NULL, NULL),
(26, 13, 10, '2000-09-11 00:00:00', 60, 'completed', 'pepek', 4, NULL, '2025-04-23 13:20:58', '2025-04-23 17:48:59', '12:02:00', '14:41:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(28, 13, 10, '2025-04-25 00:00:00', 60, 'completed', 'ashjagsjhags', 2, NULL, '2025-04-23 13:40:50', '2025-04-23 17:48:39', '12:00:00', '14:00:00', 'https://gjhugtjugt', NULL, NULL, NULL),
(29, 13, 10, '2025-04-25 00:00:00', 60, 'completed', 'hjhjh', 1, NULL, '2025-04-23 14:24:21', '2025-04-23 17:48:43', '23:27:00', '01:29:00', 'https://gjhugtjugt', NULL, NULL, NULL),
(30, 13, 10, '2025-04-24 00:00:00', 60, 'completed', 'sdsdsd', 3, NULL, '2025-04-23 14:43:50', '2025-04-23 17:48:47', '12:45:00', '13:07:00', 'https://gjhugtjugt', NULL, NULL, NULL),
(31, 13, 10, '8989-09-09 00:00:00', 60, 'completed', '1212', 4, NULL, '2025-04-23 15:18:30', '2025-04-23 16:02:02', '21:02:00', '12:02:00', 'https://gjhugtjugt', NULL, NULL, NULL),
(32, 13, 10, '2025-04-24 00:00:00', 60, 'completed', 'Zzsa', 3, NULL, '2025-04-23 15:40:57', '2025-04-23 17:48:51', '03:33:00', '03:33:00', 'https://gjhugtjugt', NULL, NULL, NULL),
(33, 13, 10, '3333-03-13 00:00:00', 60, 'completed', 'testing aja', 2, NULL, '2025-04-27 04:47:59', '2025-05-01 09:53:59', '23:23:00', '22:02:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(34, 13, 10, '7777-07-07 00:00:00', 60, 'completed', 'bbbb', 2, NULL, '2025-04-27 06:35:29', '2025-05-01 09:53:55', '06:06:00', '07:07:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(35, 13, 10, '9999-09-09 00:00:00', 60, 'completed', 'nnnnn', 5, NULL, '2025-04-27 06:39:18', '2025-05-01 09:53:36', '07:07:00', '08:08:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(36, 13, 10, '7777-07-08 00:00:00', 60, 'completed', 'bbbhhbh', 5, NULL, '2025-04-27 06:42:18', '2025-05-01 09:53:51', '08:08:00', '09:09:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(37, 13, 10, '8888-08-08 00:00:00', 60, 'completed', 'nnnn', 5, NULL, '2025-04-27 06:46:48', '2025-05-01 09:53:46', '09:08:00', '06:06:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(38, 13, 10, '8888-08-08 00:00:00', 60, 'completed', 'hhhggfdsdfgh', 5, NULL, '2025-04-27 06:53:14', '2025-05-01 09:53:41', '07:07:00', '08:08:00', 'https://google.meet/anjay', NULL, NULL, NULL),
(39, 13, 10, '9999-09-09 00:00:00', 60, 'rejected', NULL, NULL, NULL, '2025-04-27 07:03:44', '2025-04-27 07:06:07', '07:07:00', '08:08:00', NULL, NULL, NULL, NULL),
(40, 13, 13, '2025-02-01 00:00:00', 60, 'rejected', NULL, NULL, NULL, '2025-05-01 10:20:06', '2025-05-01 12:56:25', '21:02:00', '22:03:00', NULL, NULL, NULL, NULL),
(41, 13, 13, '2025-10-09 00:00:00', 60, 'rejected', NULL, NULL, NULL, '2025-05-01 10:22:21', '2025-05-01 12:56:08', '23:12:00', '23:40:00', NULL, NULL, NULL, NULL),
(42, 13, 13, '3333-03-22 00:00:00', 60, 'completed', 'sok iya kontol', 4, NULL, '2025-05-01 12:55:12', '2025-05-01 14:13:10', '03:02:00', '04:03:00', 'https://google.meet/anjay', 'mayan', 'yoi', '2025-05-01 21:13:10'),
(43, 13, 13, '7877-08-07 00:00:00', 60, 'completed', 'tes', 3, NULL, '2025-05-01 13:23:51', '2025-05-01 14:13:02', '05:05:00', '06:06:00', 'https://google.meet/anjay', 'mantap', 'tq abangku', '2025-05-01 21:13:02'),
(44, 13, 13, '7777-07-07 00:00:00', 60, 'completed', 'qwqwqw', 3, NULL, '2025-05-01 14:40:36', '2025-05-01 14:51:07', '07:59:00', '06:07:00', 'https://google.meet/anjay', 'jelek nih', 'mata mu', '2025-05-01 21:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `consultation_replies`
--

CREATE TABLE `consultation_replies` (
  `id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `parent_reply_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consultation_replies`
--

INSERT INTO `consultation_replies` (`id`, `consultation_id`, `user_id`, `message`, `parent_reply_id`, `created_at`) VALUES
(1, 43, 13, 'yoi bangku', NULL, '2025-05-01 21:17:59'),
(2, 43, 13, 'keren ya bangku', NULL, '2025-05-01 21:18:08'),
(3, 43, 13, 'bisa gini jir', NULL, '2025-05-01 21:18:52'),
(4, 43, 13, 'mantap', NULL, '2025-05-01 21:18:58'),
(5, 43, 14, 'yoi', 2, '2025-05-01 21:26:23');

-- --------------------------------------------------------

--
-- Table structure for table `consultation_reports`
--

CREATE TABLE `consultation_reports` (
  `report_id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `counselor_id` int NOT NULL,
  `report_text` text,
  `follow_up_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counselors`
--

CREATE TABLE `counselors` (
  `counselor_id` int NOT NULL,
  `user_id` int NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `qualifications` text NOT NULL,
  `experience` text,
  `availability` text,
  `rating` decimal(3,1) DEFAULT '0.0',
  `session_count` int DEFAULT '0',
  `hourly_rate` decimal(10,2) DEFAULT '0.00',
  `certificate_path` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `counselors`
--

INSERT INTO `counselors` (`counselor_id`, `user_id`, `specialization`, `qualifications`, `experience`, `availability`, `rating`, `session_count`, `hourly_rate`, `certificate_path`, `photo`) VALUES
(10, 11, 'cium mencium', 'esema', '2006', 'sedia', 3.1, 0, 5000000.00, NULL, 'assets/images/profiles/photo_2025-03-08_16-50-38.jpg'),
(13, 14, 'penghilang depresi', 'teka', '2000', 'bisa', 3.3, 0, 8000000.00, NULL, 'assets/images/profiles/1730106865069.jpg'),
(15, 15, 'sodok menyodok', 'esde', '2009', 'anjay', 0.0, 0, 2000000.00, NULL, 'assets/images/profiles/IMG-20250421-WA0003.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `counselor_applications`
--

CREATE TABLE `counselor_applications` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `qualifications` text,
  `experience` int DEFAULT NULL,
  `certificate_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counselor_availability`
--

CREATE TABLE `counselor_availability` (
  `availability_id` int NOT NULL,
  `counselor_id` int NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_anonymous` tinyint(1) DEFAULT '1',
  `category` enum('curhat','tanya','dukungan','cerita') NOT NULL,
  `view_count` int DEFAULT '0',
  `reply_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image_path` varchar(255) DEFAULT NULL,
  `voice_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`post_id`, `user_id`, `title`, `content`, `is_anonymous`, `category`, `view_count`, `reply_count`, `created_at`, `updated_at`, `image_path`, `voice_path`) VALUES
(4, 1, 'test', 'lorem ipsum dit dolor', 1, 'tanya', 1, 0, '2025-04-19 13:24:17', '2025-04-24 10:24:49', NULL, NULL),
(9, 1, 'test2', 'lorem', 0, 'curhat', 1, 0, '2025-04-19 13:30:32', '2025-04-24 10:16:22', NULL, NULL),
(18, 1, 'test3', 'lorem ipsum', 1, 'dukungan', 22, 0, '2025-04-19 13:44:30', '2025-04-24 13:12:13', NULL, NULL),
(30, 11, '12', '3deswds', 1, 'cerita', 31, 4, '2025-04-24 13:12:45', '2025-04-24 14:39:01', NULL, NULL),
(34, 11, 'hhyhy000', '', 1, 'curhat', 6, 0, '2025-04-24 15:00:47', '2025-04-27 04:39:52', NULL, 'assets/voices/1745506847.webm'),
(37, 8, 'bbb', '', 1, 'dukungan', 116, 0, '2025-04-27 03:53:28', '2025-07-26 07:09:38', NULL, 'assets/voices/1745726008.webm');

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `reply_id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `is_anonymous` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `voice_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`reply_id`, `post_id`, `user_id`, `content`, `is_anonymous`, `created_at`, `voice_path`) VALUES
(8, 30, 1, 'sabar ya kak', 1, '2025-04-24 13:13:17', NULL),
(9, 30, 1, 'sabar ya bre', 0, '2025-04-24 13:13:37', NULL),
(10, 30, 11, 'ya guys', 0, '2025-04-24 13:14:34', NULL),
(11, 30, 11, 'tes', 1, '2025-04-24 13:19:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `sender` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `consultation_id`, `sender`, `message`, `created_at`) VALUES
(1, 23, 'counselor', 'hai', '2025-04-28 00:50:55');

-- --------------------------------------------------------

--
-- Table structure for table `mood_tracker`
--

CREATE TABLE `mood_tracker` (
  `entry_id` int NOT NULL,
  `user_id` int NOT NULL,
  `mood` enum('sangat_bahagia','bahagia','netral','sedih','sangat_sedih','marah','cemas') NOT NULL,
  `notes` text,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mood_tracker`
--

INSERT INTO `mood_tracker` (`entry_id`, `user_id`, `mood`, `notes`, `date`, `created_at`) VALUES
(1, 1, 'sangat_bahagia', '', '2025-04-19', '2025-04-19 14:04:23'),
(2, 1, 'netral', 'b aja', '2025-04-20', '2025-04-19 14:44:47'),
(3, 1, 'netral', NULL, '2025-04-18', '2025-04-19 14:51:08'),
(5, 13, 'netral', NULL, '2025-04-24', '2025-04-23 17:45:44'),
(6, 13, 'sedih', NULL, '2025-04-23', '2025-04-24 07:30:25'),
(7, 13, 'bahagia', NULL, '2025-04-25', '2025-04-24 07:30:31'),
(8, 13, 'sangat_bahagia', NULL, '2025-04-22', '2025-04-24 09:20:38'),
(9, 13, 'bahagia', NULL, '2025-05-01', '2025-05-01 11:14:33'),
(10, 13, 'sangat_bahagia', NULL, '2025-05-02', '2025-05-01 11:14:42'),
(11, 13, 'sangat_bahagia', NULL, '2025-05-03', '2025-05-01 11:15:02'),
(12, 13, 'sangat_bahagia', NULL, '2025-05-04', '2025-05-01 11:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_logs`
--

CREATE TABLE `password_reset_logs` (
  `log_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `action_time` datetime DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `status` enum('requested','used','failed') DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `action`, `user_id`, `description`, `created_at`) VALUES
(1, 'login', 0, 'User ID  login.', '2025-04-20 06:43:12'),
(2, 'login', 0, 'User ID 8 (cecep) berhasil login.', '2025-04-20 06:45:12'),
(3, 'create_article', 0, 'Admin ID 1 membuat artikel baru berjudul \"sasasasasasa\".', '2025-04-20 06:47:16'),
(4, 'Edit Artikel', 1, 'Artikel dengan ID 12 telah diedit.', '2025-04-20 06:54:11'),
(5, 'Edit Artikel', 1, 'Artikel dengan ID 12 telah diedit.', '2025-04-20 07:01:29'),
(6, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-20 07:13:02'),
(7, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-20 07:13:24'),
(8, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-21 10:06:52'),
(9, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-21 10:08:29'),
(10, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-21 11:20:40'),
(11, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-21 11:21:17'),
(12, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-21 11:27:10'),
(13, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-21 11:30:32'),
(14, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-21 11:36:13'),
(15, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-21 11:40:39'),
(16, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-21 11:44:59'),
(17, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-21 11:48:50'),
(18, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-21 11:51:49'),
(19, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-21 12:46:00'),
(20, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-21 13:49:18'),
(21, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-21 13:50:25'),
(22, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-21 13:51:19'),
(23, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-21 13:52:37'),
(24, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-21 13:52:57'),
(25, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-21 13:53:24'),
(26, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-21 13:54:21'),
(27, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-21 13:56:29'),
(28, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-22 05:35:59'),
(29, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-22 08:12:22'),
(30, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-22 08:28:03'),
(31, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-22 08:33:12'),
(32, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-22 09:28:08'),
(33, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-22 10:28:32'),
(34, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 07:48:14'),
(35, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 08:48:56'),
(36, 'login', 7, 'User ID 7 (conselor) berhasil login.', '2025-04-23 08:55:27'),
(37, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-23 08:55:49'),
(38, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-23 08:56:10'),
(39, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 08:57:22'),
(40, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-23 09:04:23'),
(41, 'login', 7, 'User ID 7 (conselor) berhasil login.', '2025-04-23 09:05:18'),
(42, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-23 09:05:38'),
(43, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-23 09:05:49'),
(44, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-23 10:03:29'),
(45, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-23 10:06:19'),
(46, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 11:10:58'),
(47, 'login', 10, 'User ID 10 (conselor1) berhasil login.', '2025-04-23 11:14:18'),
(48, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-23 11:16:09'),
(49, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 11:58:29'),
(50, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-23 12:24:14'),
(51, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 12:29:35'),
(52, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-23 12:41:58'),
(53, 'login', 9, 'User ID 9 (cacap) berhasil login.', '2025-04-23 12:42:10'),
(54, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-23 12:43:17'),
(55, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 12:43:52'),
(56, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 12:45:05'),
(57, 'login', 12, 'User ID 12 (conselor) berhasil login.', '2025-04-23 12:45:20'),
(58, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-23 12:51:19'),
(59, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-23 12:52:50'),
(60, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 12:53:25'),
(61, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-23 12:54:12'),
(62, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 13:36:39'),
(63, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-23 13:40:34'),
(64, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-23 13:41:19'),
(65, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-23 17:33:25'),
(66, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-23 17:51:50'),
(67, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-24 05:38:42'),
(68, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-24 05:40:17'),
(69, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-24 05:40:42'),
(70, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-24 05:41:10'),
(71, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-24 05:42:31'),
(72, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-24 05:44:52'),
(73, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-24 05:45:06'),
(74, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-24 05:45:58'),
(75, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-24 05:47:20'),
(76, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-24 05:51:35'),
(77, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-24 05:52:11'),
(78, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-04-24 05:52:44'),
(79, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-24 05:54:10'),
(80, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-24 05:56:06'),
(81, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-04-24 06:03:25'),
(82, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-24 06:07:23'),
(83, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-24 11:32:47'),
(84, 'Edit Artikel', 11, 'Artikel dengan ID 7 telah diedit.', '2025-04-24 11:38:38'),
(85, 'create_article', 11, 'Admin ID 11 membuat artikel baru berjudul \"broken home\".', '2025-04-24 11:39:05'),
(86, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-27 02:11:13'),
(87, 'login', 8, 'User ID 8 (cecep) berhasil login.', '2025-04-27 03:51:41'),
(88, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 04:42:55'),
(89, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 04:48:07'),
(90, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 06:35:09'),
(91, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 06:35:38'),
(92, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 06:39:04'),
(93, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 06:39:27'),
(94, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 06:42:04'),
(95, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 06:42:29'),
(96, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 06:46:25'),
(97, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 06:46:58'),
(98, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 06:53:02'),
(99, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 06:53:23'),
(100, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 07:03:32'),
(101, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 07:05:48'),
(102, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-04-27 07:06:02'),
(103, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-04-27 17:57:40'),
(104, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 09:43:53'),
(105, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-05-01 09:46:57'),
(106, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 09:53:28'),
(107, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 10:05:55'),
(108, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 10:06:48'),
(109, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 10:07:05'),
(110, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 10:07:48'),
(111, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-05-01 10:24:34'),
(112, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 10:25:47'),
(113, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-05-01 10:26:54'),
(114, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 10:27:07'),
(115, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 10:27:31'),
(116, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-05-01 10:27:47'),
(117, 'login', 4, 'User ID 4 (admun) berhasil login.', '2025-05-01 10:28:56'),
(118, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 10:29:32'),
(119, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 10:34:14'),
(120, 'login', 15, 'User ID 15 (suripto) berhasil login.', '2025-05-01 10:34:49'),
(121, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 10:50:31'),
(122, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-05-01 11:06:18'),
(123, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 11:14:20'),
(124, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-05-01 12:47:35'),
(125, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 12:47:59'),
(126, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 12:48:08'),
(127, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 12:49:20'),
(128, 'login', 11, 'User ID 11 (user2) berhasil login.', '2025-05-01 12:49:30'),
(129, 'login', 1, 'User ID 1 (admin) berhasil login.', '2025-05-01 12:49:43'),
(130, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 12:54:08'),
(131, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 12:55:25'),
(132, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 13:15:12'),
(133, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 13:23:29'),
(134, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 13:24:00'),
(135, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 13:24:45'),
(136, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 13:28:37'),
(137, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 13:38:33'),
(138, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:11:26'),
(139, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 14:13:23'),
(140, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:18:17'),
(141, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 14:18:43'),
(142, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:21:04'),
(143, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:39:17'),
(144, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 14:40:08'),
(145, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:40:46'),
(146, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 14:41:17'),
(147, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:41:43'),
(148, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 14:45:46'),
(149, 'login', 14, 'User ID 14 (ahmad) berhasil login.', '2025-05-01 14:47:21'),
(150, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-01 14:52:16'),
(151, 'login', 13, 'User ID 13 (rianek) berhasil login.', '2025-05-28 12:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `test_results`
--

CREATE TABLE `test_results` (
  `result_id` int NOT NULL,
  `user_id` int NOT NULL,
  `test_type` enum('PHQ-9','GAD-7') NOT NULL,
  `score` int NOT NULL,
  `severity` enum('ringan','sedang','parah') NOT NULL,
  `recommendations` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `test_results`
--

INSERT INTO `test_results` (`result_id`, `user_id`, `test_type`, `score`, `severity`, `recommendations`, `created_at`) VALUES
(2, 1, 'PHQ-9', 9, 'ringan', 'Gejala depresi ringan. Pertimbangkan untuk melakukan konsultasi dengan profesional atau mencoba teknik self-care seperti journaling dan meditasi.', '2025-04-19 12:41:26'),
(3, 1, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-19 16:08:35'),
(4, 1, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-19 16:09:16'),
(5, 1, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-19 16:09:22'),
(6, 1, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-19 16:09:30'),
(7, 8, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-20 10:08:26'),
(8, 8, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-20 10:08:48'),
(9, 11, 'PHQ-9', 13, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-23 13:35:16'),
(10, 13, 'PHQ-9', 10, 'sedang', 'Gejala depresi sedang. Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental. Anda mungkin mendapat manfaat dari terapi atau dukungan lainnya.', '2025-04-23 16:23:50'),
(11, 13, 'PHQ-9', 20, 'parah', 'Gejala depresi parah. Segera cari bantuan profesional. Anda dapat menghubungi layanan darurat kesehatan mental atau rumah sakit terdekat.', '2025-04-24 11:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('L','P','Lainnya') DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT '1',
  `is_counselor` tinyint(1) DEFAULT '0',
  `bio` text,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  `remember_token` varchar(64) DEFAULT NULL,
  `remember_token_expiry` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `reset_attempts` int DEFAULT '0',
  `status` enum('active','pending') DEFAULT 'active',
  `profile_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `full_name`, `birth_date`, `gender`, `is_anonymous`, `is_counselor`, `bio`, `profile_pic`, `created_at`, `updated_at`, `is_admin`, `remember_token`, `remember_token_expiry`, `reset_token`, `reset_token_expiry`, `reset_attempts`, `status`, `profile_image`) VALUES
(1, 'admin', 'paddddhil@gmail.com', '$2y$10$P5rQtCGLvwEIIVfD4hoayej8i0FoGtlMNj/fCyyUXaOkOVl6y0oGW', 'mimin', '2220-02-10', 'L', 1, 1, '\\r\\nDeprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in C:\\\\laragon\\\\www\\\\RuangEmosi\\\\user\\\\profile.php on line 165\\r\\n', NULL, '2025-04-19 12:22:40', '2025-04-20 07:07:38', 1, 'd8c932218d686d71fcd0c7fcba1b8751c66d559323eb30c68d04ff535ceb8dfa', '2025-05-19 19:31:34', NULL, NULL, 0, 'active', 'default.png'),
(8, 'cecep', 'tsets@gmail.com', '$2y$10$u4.kNI4vj85kJlqbJkmvL.BdNwlcy5nqWicJkj2q5KZDX9qbHsj.u', 'emiliano', '2999-02-01', 'L', 1, 1, 'anjay', NULL, '2025-04-20 05:42:25', '2025-04-24 05:47:03', 0, NULL, NULL, NULL, NULL, 0, 'active', 'default.png'),
(11, 'user2', 'eksperimencoding@gmail.com', '$2y$10$S4zXao64miRCLiLuVtVxquSq//.fVF0nyrRr3TJG3y73n9qqTI7ku', '', NULL, NULL, 1, 1, NULL, NULL, '2025-04-23 12:41:26', '2025-04-23 12:53:30', 0, NULL, NULL, NULL, NULL, 0, 'active', 'default.png'),
(13, 'rianek', 'rianpirade052@gmail.com', '$2y$10$81b08gYHhwNMPqrsZqcxb.xJ3m06dB.dZeYt41FNs88MxTxIT8x2u', 'rianekkk', '7777-07-07', 'L', 1, 0, 'ghgh', NULL, '2025-04-23 12:52:37', '2025-05-01 14:47:12', 0, NULL, NULL, NULL, NULL, 0, 'active', 'default.png'),
(14, 'ahmad', 'sundala@gmail.com', '$2y$10$mTclvpWxuoSGobs4IB31peqfbYzTY0Q/BMdwNouVzy6VkDTbe.E8S', 'ahmad syariat', NULL, NULL, 1, 1, NULL, NULL, '2025-05-01 10:04:49', '2025-05-01 10:06:14', 0, NULL, NULL, NULL, NULL, 0, 'active', 'default.png'),
(15, 'suripto', 'hormonauxin@gmail.com', '$2y$10$Zk2/jRRisbcw1kLX1Yttoe3ZsBpuSymUTIxTXMKPyog59qxLQPDzW', 'suripto mangkokusumo', '2222-02-22', 'L', 1, 1, 'anjay', NULL, '2025-05-01 10:32:45', '2025-05-01 10:34:22', 0, NULL, NULL, NULL, NULL, 0, 'active', 'default.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `counselor_id` (`counselor_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`consultation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_counselor_reply` (`counselor_id`,`reply_date`);

--
-- Indexes for table `consultation_replies`
--
ALTER TABLE `consultation_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `consultation_reports`
--
ALTER TABLE `consultation_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `counselor_id` (`counselor_id`);

--
-- Indexes for table `counselors`
--
ALTER TABLE `counselors`
  ADD PRIMARY KEY (`counselor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `counselor_applications`
--
ALTER TABLE `counselor_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `counselor_id` (`counselor_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `consultation_id` (`consultation_id`);

--
-- Indexes for table `mood_tracker`
--
ALTER TABLE `mood_tracker`
  ADD PRIMARY KEY (`entry_id`),
  ADD UNIQUE KEY `unique_mood_entry` (`user_id`,`date`);

--
-- Indexes for table `password_reset_logs`
--
ALTER TABLE `password_reset_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `consultation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `consultation_replies`
--
ALTER TABLE `consultation_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `consultation_reports`
--
ALTER TABLE `consultation_reports`
  MODIFY `report_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counselors`
--
ALTER TABLE `counselors`
  MODIFY `counselor_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `counselor_applications`
--
ALTER TABLE `counselor_applications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  MODIFY `availability_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `post_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `reply_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mood_tracker`
--
ALTER TABLE `mood_tracker`
  MODIFY `entry_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_reset_logs`
--
ALTER TABLE `password_reset_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `result_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`counselor_id`) REFERENCES `counselors` (`counselor_id`);

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`counselor_id`) REFERENCES `counselors` (`counselor_id`) ON DELETE CASCADE;

--
-- Constraints for table `consultation_replies`
--
ALTER TABLE `consultation_replies`
  ADD CONSTRAINT `consultation_replies_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`consultation_id`),
  ADD CONSTRAINT `consultation_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `consultation_reports`
--
ALTER TABLE `consultation_reports`
  ADD CONSTRAINT `consultation_reports_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`consultation_id`),
  ADD CONSTRAINT `consultation_reports_ibfk_2` FOREIGN KEY (`counselor_id`) REFERENCES `counselors` (`counselor_id`);

--
-- Constraints for table `counselors`
--
ALTER TABLE `counselors`
  ADD CONSTRAINT `counselors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `counselor_applications`
--
ALTER TABLE `counselor_applications`
  ADD CONSTRAINT `counselor_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  ADD CONSTRAINT `counselor_availability_ibfk_1` FOREIGN KEY (`counselor_id`) REFERENCES `counselors` (`counselor_id`);

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`consultation_id`) ON DELETE CASCADE;

--
-- Constraints for table `mood_tracker`
--
ALTER TABLE `mood_tracker`
  ADD CONSTRAINT `mood_tracker_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
