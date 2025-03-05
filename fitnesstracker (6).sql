-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 11:47 AM
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
-- Database: `fitnesstracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `deleted_at`) VALUES
(1, 'Breakfast', NULL),
(2, 'Lunch', NULL),
(3, 'Dinner', NULL),
(4, 'Snacks', NULL),
(5, 'Supplements', NULL),
(6, 'Strength', NULL),
(7, 'Cardio', NULL),
(8, 'Flexibility', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `one_rm` decimal(5,1) DEFAULT 0.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_predefined` tinyint(1) DEFAULT 0,
  `video_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `user_id`, `name`, `one_rm`, `created_at`, `updated_at`, `deleted_at`, `is_predefined`, `video_url`) VALUES
(1, 1, 'Squat', 150.0, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(2, 1, 'Bench', 100.0, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(3, 1, 'Deadlift', 180.0, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(4, 1, 'Bent Over Row', 80.0, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(5, 1, 'Incline Bench', 82.5, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(6, 1, 'Romanian Deadlift', 140.0, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(7, 1, 'Overhead Press', 50.0, '2025-02-22 12:18:41', '2025-02-22 16:31:18', NULL, 1, NULL),
(8, 1, 'Incline Bench Press', 0.0, '2025-02-23 12:25:05', '2025-02-23 12:25:05', NULL, 1, NULL),
(9, 1, 'Squat', 0.0, '2025-02-23 12:25:05', '2025-02-23 12:25:05', NULL, 1, NULL),
(10, 1, 'Squat', 150.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 1, 'https://youtube.com/watch?v=squat_demo'),
(11, 1, 'Bench Press', 100.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 1, 'https://youtube.com/watch?v=bench_demo'),
(12, 1, 'Deadlift', 180.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 1, 'https://youtube.com/watch?v=deadlift_demo'),
(13, 2, 'Pull-Up', 80.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 0, NULL),
(14, 3, 'Dumbbell Curl', 50.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 0, NULL),
(15, 4, 'Leg Press', 120.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 1, 'https://youtube.com/watch?v=legpress_demo'),
(16, 5, 'Overhead Press', 60.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 1, 'https://youtube.com/watch?v=overhead_demo'),
(17, 1, 'Lunges', 70.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 0, NULL),
(18, 2, 'Plank', 0.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 0, NULL),
(19, 3, 'Rowing', 90.0, '2025-02-23 15:15:08', '2025-02-23 15:15:08', NULL, 1, 'https://youtube.com/watch?v=rowing_demo'),
(20, 10, 'Barbell Squat', 160.0, '2025-02-02 15:00:00', '2025-02-02 15:00:00', NULL, 0, 'https://youtube.com/watch?v=tunis_squat'),
(21, 11, 'Cat-Cow Stretch', 0.0, '2025-02-06 06:30:00', '2025-02-06 06:30:00', NULL, 0, 'https://youtube.com/watch?v=tunis_yoga'),
(22, 12, 'Outdoor Run', 0.0, '2025-02-11 05:45:00', '2025-02-11 05:45:00', NULL, 0, NULL),
(23, 13, 'Quad Stretch', 0.0, '2025-02-16 17:00:00', '2025-02-16 17:00:00', NULL, 0, NULL),
(24, 14, 'Bench Press', 130.0, '2025-02-21 16:00:00', '2025-02-21 16:00:00', NULL, 0, 'https://youtube.com/watch?v=tunis_bench'),
(25, 10, 'Treadmill Sprint', 0.0, '2025-03-01 07:00:00', '2025-03-01 07:00:00', NULL, 0, NULL),
(26, 11, 'Russian Twist', 0.0, '2025-03-02 14:30:00', '2025-03-02 14:30:00', NULL, 0, 'https://youtube.com/watch?v=tunis_twist'),
(27, 12, 'Breaststroke', 0.0, '2025-03-03 08:00:00', '2025-03-03 08:00:00', NULL, 0, NULL),
(28, 13, 'Pilates Roll-Up', 0.0, '2025-03-04 06:45:00', '2025-03-04 06:45:00', NULL, 0, 'https://youtube.com/watch?v=tunis_rollup'),
(29, 14, 'Shoulder Press', 90.0, '2025-03-05 15:00:00', '2025-03-05 15:00:00', NULL, 0, 'https://youtube.com/watch?v=tunis_shoulder');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `target_weight` decimal(5,1) NOT NULL,
  `attempts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attempts`)),
  `achieved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `exercise_id`, `target_weight`, `attempts`, `achieved`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 200.0, '[[\"2025-02-22\", 150.0], [\"2025-02-23\", 160.0]]', 0, '2025-02-23 15:15:18', '2025-02-23 15:15:18', NULL),
(2, 2, 2, 120.0, '[[\"2025-02-22\", 90.0]]', 0, '2025-02-23 15:15:18', '2025-02-23 15:15:18', NULL),
(3, 3, 3, 220.0, '[[\"2025-02-23\", 180.0]]', 0, '2025-02-23 15:15:18', '2025-02-23 15:15:18', NULL),
(4, 4, 4, 100.0, '[[\"2025-02-22\", 80.0], [\"2025-02-23\", 90.0]]', 0, '2025-02-23 15:15:18', '2025-02-23 15:15:18', NULL),
(5, 5, 5, 60.0, '[[\"2025-02-23\", 50.0]]', 0, '2025-02-23 15:15:18', '2025-02-23 15:15:18', NULL),
(6, 10, 20, 180.0, '[[\"2025-02-05\", 160.0]]', 0, '2025-02-05 15:00:00', '2025-02-05 15:00:00', NULL),
(7, 11, 21, 0.0, '[[\"2025-02-10\", 0.0]]', 1, '2025-02-10 06:30:00', '2025-03-02 14:30:00', NULL),
(8, 12, 22, 0.0, '[[\"2025-02-15\", 0.0]]', 0, '2025-02-15 05:45:00', '2025-02-15 05:45:00', NULL),
(9, 13, 23, 0.0, '[[\"2025-02-20\", 0.0]]', 1, '2025-02-20 17:00:00', '2025-03-04 06:45:00', NULL),
(10, 14, 24, 150.0, '[[\"2025-02-25\", 130.0]]', 0, '2025-02-25 16:00:00', '2025-02-25 16:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `attempt_time`, `success`) VALUES
(1, 'admin@gmail.com', '2025-02-23 15:05:31', 0),
(2, 'user1@example.com', '2025-02-23 15:00:31', 0),
(3, 'user2@example.com', '2025-02-23 15:10:31', 0),
(4, 'user3@example.com', '2025-02-23 14:55:31', 0),
(5, 'user4@example.com', '2025-02-23 14:45:31', 0),
(6, 'admin@gmail.com', '2025-03-04 11:39:39', 0),
(7, 'admin@gmail.com', '2025-03-04 11:40:00', 0),
(8, 'admin@gmail.com', '2025-03-04 11:41:59', 0),
(9, 'admin@gmail.com', '2025-03-04 11:42:45', 0),
(10, 'admin@gmail.com', '2025-03-04 11:43:56', 0),
(11, 'admin@gmail.com', '2025-03-04 12:07:08', 0),
(12, 'admin@gmail.com', '2025-03-04 12:28:43', 0),
(13, 'youssefdhib28@gmail.com', '2025-03-04 13:21:41', 0),
(14, 'hafedh.mezni@gmail.com', '2025-02-02 07:45:00', 1),
(15, 'imen.sassi@yahoo.com', '2025-02-06 08:00:00', 1),
(16, 'tarek.bouazizi@outlook.com', '2025-02-11 12:30:00', 1),
(17, 'sonia.khaledi@gmail.com', '2025-02-16 07:15:00', 1),
(18, 'riadh.jebali@hotmail.com', '2025-02-21 14:00:00', 1),
(19, 'hafedh.mezni@gmail.com', '2025-03-04 13:00:00', 1),
(20, 'tarek.bouazizi@outlook.com', '2025-03-05 06:45:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `calories` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_predefined` tinyint(1) DEFAULT 0,
  `carbs` decimal(5,2) DEFAULT NULL,
  `protein` decimal(5,2) DEFAULT NULL,
  `fat` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`id`, `user_id`, `name`, `calories`, `category_id`, `created_at`, `deleted_at`, `updated_at`, `is_predefined`, `carbs`, `protein`, `fat`) VALUES
(1, 1, 'Oatmeal', 150, 1, '2025-02-12 15:35:10', NULL, '2025-02-22 11:47:43', 0, NULL, NULL, NULL),
(2, 1, 'Chicken Salad', 350, 2, '2025-02-12 15:35:10', NULL, '2025-02-22 11:47:43', 0, NULL, NULL, NULL),
(3, 1, 'Steak', 500, 3, '2025-02-12 15:35:10', NULL, '2025-02-22 11:47:43', 0, NULL, NULL, NULL),
(4, 1, 'Protein Shake', 200, 4, '2025-02-12 15:35:10', NULL, '2025-02-22 11:47:43', 0, NULL, NULL, NULL),
(5, 1, 'Oatmeal', 150, 1, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 27.00, 5.00, 3.00),
(6, 2, 'Chicken Salad', 350, 2, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 10.00, 30.00, 20.00),
(7, 3, 'Steak Dinner', 500, 3, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 5.00, 40.00, 35.00),
(8, 4, 'Protein Shake', 200, 4, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 15.00, 25.00, 5.00),
(9, 5, 'Apple Snack', 95, 4, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 25.00, 0.50, 0.30),
(10, 1, 'Scrambled Eggs', 140, 1, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 2.00, 12.00, 10.00),
(11, 2, 'Turkey Sandwich', 300, 2, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 30.00, 20.00, 8.00),
(12, 3, 'Salmon Dinner', 450, 3, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 5.00, 35.00, 30.00),
(13, 4, 'Almonds', 160, 4, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 6.00, 6.00, 14.00),
(14, 5, 'Whey Protein', 120, 5, '2025-02-23 15:15:40', NULL, '2025-02-23 15:15:40', 0, 3.00, 20.00, 2.00),
(15, 8, 'oats', 30, 1, '2025-03-04 23:21:06', '2025-03-04 23:34:42', '2025-03-04 23:34:42', 0, 10.00, 20.00, 22.00),
(16, 8, 'oats', 300, 1, '2025-03-04 23:34:55', NULL, '2025-03-04 23:34:55', 0, 10.00, 20.00, 22.00),
(17, 10, 'Brik au Thon', 320, 1, '2025-02-02 06:00:00', NULL, '2025-02-02 06:00:00', 0, 25.00, 18.00, 15.00),
(18, 11, 'Couscous au Poisson', 550, 3, '2025-02-06 17:30:00', NULL, '2025-02-06 17:30:00', 0, 65.00, 30.00, 20.00),
(19, 12, 'Lablabi', 260, 2, '2025-02-11 11:45:00', NULL, '2025-02-11 11:45:00', 0, 45.00, 10.00, 8.00),
(20, 13, 'Ojja aux Crevettes', 300, 3, '2025-02-16 18:00:00', NULL, '2025-02-16 18:00:00', 0, 10.00, 20.00, 22.00),
(21, 14, 'Slata Mechouia', 120, 2, '2025-02-21 12:15:00', NULL, '2025-02-21 12:15:00', 0, 15.00, 3.00, 6.00),
(22, 10, 'Fricassé', 350, 4, '2025-02-25 14:30:00', NULL, '2025-02-25 14:30:00', 0, 40.00, 12.00, 18.00),
(23, 11, 'Chakchouka', 210, 1, '2025-03-01 06:30:00', NULL, '2025-03-01 06:30:00', 0, 20.00, 10.00, 12.00),
(24, 12, 'Tajine Tunisien', 340, 3, '2025-03-02 17:00:00', NULL, '2025-03-02 17:00:00', 0, 25.00, 20.00, 18.00),
(25, 13, 'Dates with Milk', 180, 4, '2025-03-03 15:00:00', NULL, '2025-03-03 15:00:00', 0, 40.00, 5.00, 2.00),
(26, 14, 'Protein Shake Tunis', 130, 5, '2025-03-04 08:00:00', NULL, '2025-03-04 08:00:00', 0, 4.00, 25.00, 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

CREATE TABLE `meal_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_plans`
--

INSERT INTO `meal_plans` (`id`, `user_id`, `name`, `created_at`) VALUES
(1, 1, 'Weekly Meal Plan', '2025-02-12 15:35:10'),
(2, 1, 'Weekly Meal Plan 1', '2025-02-23 15:15:50'),
(3, 2, 'Daily Diet Plan', '2025-02-23 15:15:50'),
(4, 3, 'Low-Carb Plan', '2025-02-23 15:15:50'),
(5, 4, 'High-Protein Plan', '2025-02-23 15:15:50'),
(6, 5, 'Balanced Diet', '2025-02-23 15:15:50'),
(7, 10, 'Tunisian Bulk Plan', '2025-02-02 07:00:00'),
(8, 11, 'Daily Healthy Tunis', '2025-02-06 09:00:00'),
(9, 12, 'Runner\'s Diet', '2025-02-11 12:00:00'),
(10, 13, 'Yoga Nutrition', '2025-02-16 08:30:00'),
(11, 14, 'Strength Meal Plan', '2025-02-21 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `meal_plan_items`
--

CREATE TABLE `meal_plan_items` (
  `id` int(11) NOT NULL,
  `meal_plan_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_plan_items`
--

INSERT INTO `meal_plan_items` (`id`, `meal_plan_id`, `meal_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 1),
(6, 1, 2),
(7, 1, 3),
(8, 2, 4),
(9, 2, 5),
(10, 2, 6),
(11, 3, 7),
(12, 3, 8),
(13, 3, 9),
(14, 4, 1),
(15, 4, 3),
(16, 4, 10),
(17, 5, 2),
(18, 5, 4),
(19, 5, 8),
(20, 7, 17),
(21, 7, 18),
(22, 7, 22),
(23, 8, 23),
(24, 8, 19),
(25, 8, 20),
(26, 9, 19),
(27, 9, 24),
(28, 9, 25),
(29, 10, 23),
(30, 10, 20),
(31, 10, 25),
(32, 11, 17),
(33, 11, 21),
(34, 11, 26);

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `body_fat` decimal(5,2) DEFAULT NULL,
  `muscle_mass` decimal(5,2) DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`id`, `user_id`, `weight`, `body_fat`, `muscle_mass`, `date`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 1, 75.50, 15.20, 40.00, '2025-02-20', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(2, 2, 68.30, 12.50, 35.00, '2025-02-21', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(3, 3, 82.00, 18.00, 45.00, '2025-02-22', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(4, 4, 60.20, 10.80, 30.00, '2025-02-23', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(5, 5, 70.10, 14.30, 38.00, '2025-02-19', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(6, 1, 76.00, 15.00, 40.50, '2025-02-18', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(7, 2, 68.80, 12.70, 35.20, '2025-02-17', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(8, 3, 81.50, 17.80, 44.50, '2025-02-16', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(9, 4, 60.50, 10.50, 30.50, '2025-02-15', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(10, 5, 70.50, 14.50, 38.50, '2025-02-14', '2025-02-23 15:16:12', NULL, '2025-02-23 15:16:12'),
(11, 8, 50.00, 12.00, 50.00, '2025-03-04', '2025-03-04 21:00:09', '2025-03-04 21:13:35', '2025-03-04 21:13:35'),
(12, 8, 90.00, 12.00, 40.00, '2025-03-04', '2025-03-04 21:15:52', NULL, '2025-03-04 21:15:52'),
(13, 8, 70.00, 9.00, 50.00, '2025-03-20', '2025-03-05 01:22:10', NULL, '2025-03-05 01:22:10'),
(14, 10, 70.80, 15.80, 38.00, '2025-02-05', '2025-02-05 09:00:00', NULL, '2025-02-05 09:00:00'),
(15, 11, 56.20, 13.50, 30.50, '2025-02-10', '2025-02-10 11:30:00', NULL, '2025-02-10 11:30:00'),
(16, 12, 82.40, 17.20, 44.00, '2025-02-15', '2025-02-15 14:00:00', NULL, '2025-02-15 14:00:00'),
(17, 13, 62.00, 14.00, 33.00, '2025-02-20', '2025-02-20 08:00:00', NULL, '2025-02-20 08:00:00'),
(18, 14, 85.50, 16.50, 46.00, '2025-02-25', '2025-02-25 16:30:00', NULL, '2025-02-25 16:30:00'),
(19, 10, 70.50, 15.60, 38.20, '2025-03-01', '2025-03-01 07:30:00', NULL, '2025-03-01 07:30:00'),
(20, 11, 55.90, 13.30, 30.70, '2025-03-02', '2025-03-02 15:00:00', NULL, '2025-03-02 15:00:00'),
(21, 12, 82.00, 17.00, 44.30, '2025-03-03', '2025-03-03 08:30:00', NULL, '2025-03-03 08:30:00'),
(22, 13, 61.80, 13.80, 33.20, '2025-03-04', '2025-03-04 07:00:00', NULL, '2025-03-04 07:00:00'),
(23, 14, 85.20, 16.30, 46.50, '2025-03-05', '2025-03-05 15:30:00', NULL, '2025-03-05 15:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `remember_me_tokens`
--

CREATE TABLE `remember_me_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_me_tokens`
--

INSERT INTO `remember_me_tokens` (`id`, `user_id`, `token`, `expires_at`) VALUES
(2, 2, 'xyz789pqr123stu456', '2025-03-25 15:16:19'),
(3, 3, 'klm456nop789jkl012', '2025-03-25 15:16:19'),
(4, 4, 'def123ghi456jkl789', '2025-03-25 15:16:19'),
(5, 5, 'stu789xyz123pqr456', '2025-03-25 15:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') DEFAULT 'user',
  `verification_token` varchar(32) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'inactive',
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`, `verification_token`, `deleted_at`, `updated_at`, `status`, `last_activity`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$QHmmWOCyWyflRpRjDd/NQO8T2TlLF5i0jYYVQsNzg19GGUyrJBOJS', '2025-02-12 15:35:10', 'admin', NULL, NULL, '2025-03-04 12:34:11', 'active', '2025-02-23 16:14:32'),
(2, 'youssef', 'youssefdhib28@gmail.com', '$2y$10$COQPe1h//8W4XJ2/9IDwOehXluwVAEtgAQVoHgyfSaGwN0FOz2jH6', '2025-02-23 15:12:53', 'user', NULL, NULL, '2025-03-04 12:57:42', 'active', '2025-02-23 16:12:53'),
(3, 'user2', 'user2@example.com', '$2y$10$YOUR_HASH_HERE', '2025-02-23 15:12:53', 'user', NULL, NULL, '2025-02-23 15:12:53', 'active', '2025-02-23 16:12:53'),
(4, 'user3', 'user3@example.com', '$2y$10$YOUR_HASH_HERE', '2025-02-23 15:12:53', 'user', NULL, NULL, '2025-02-23 15:12:53', 'active', '2025-02-23 16:12:53'),
(5, 'user4', 'user4@example.com', '$2y$10$YOUR_HASH_HERE', '2025-02-23 15:12:53', 'user', NULL, NULL, '2025-02-23 15:12:53', 'active', '2025-02-23 16:12:53'),
(6, 'user5', 'user5@example.com', '$2y$10$YOUR_HASH_HERE', '2025-02-23 15:12:53', 'user', NULL, NULL, '2025-02-23 15:12:53', 'active', '2025-02-23 16:12:53'),
(8, 'MOHAMED Y', 'youssefdhib25@gmail.com', '$2y$12$1ABiNIGsrhbI./JO6W16butqP0BMsx1Qc/sQmYW/Bt1Mi44voRUeO', '2025-03-04 15:06:38', 'user', NULL, '2025-03-05 01:27:07', '2025-03-05 01:27:07', 'inactive', NULL),
(9, 'MOHAMED YOUSSEF', 'youssefdhib26@gmail.com', '$2y$12$01YTfMySk3DwT4HcyiI34OQNNmz33NS7ZaIkUg0iw48OY9ygq3lIu', '2025-03-04 15:11:55', 'user', NULL, NULL, '2025-03-04 15:11:55', 'inactive', NULL),
(10, 'HafedhFit', 'hafedh.mezni@gmail.com', '$2y$10$YOUR_HASH_HERE', '2025-02-01 08:30:00', 'user', NULL, NULL, '2025-03-04 14:00:00', 'active', '2025-03-04 16:00:00'),
(11, 'ImenActive', 'imen.sassi@yahoo.com', '$2y$10$YOUR_HASH_HERE', '2025-02-05 11:00:00', 'user', NULL, NULL, '2025-03-03 09:45:00', 'active', '2025-03-03 11:45:00'),
(12, 'TarekRun', 'tarek.bouazizi@outlook.com', '$2y$10$YOUR_HASH_HERE', '2025-02-10 13:15:00', 'user', NULL, NULL, '2025-03-05 07:30:00', 'active', '2025-03-05 09:30:00'),
(13, 'SoniaYoga', 'sonia.khaledi@gmail.com', '$2y$10$YOUR_HASH_HERE', '2025-02-15 07:00:00', 'user', NULL, NULL, '2025-03-02 12:20:00', 'active', '2025-03-02 14:20:00'),
(14, 'RiadhPower', 'riadh.jebali@hotmail.com', '$2y$10$YOUR_HASH_HERE', '2025-02-20 15:30:00', 'user', NULL, NULL, '2025-03-04 16:00:00', 'active', '2025-03-04 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `body_fat` decimal(5,2) DEFAULT NULL,
  `activity_level` enum('Sedentary','Light','Moderate','Active','Very Active') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `age`, `height`, `weight`, `body_fat`, `activity_level`, `created_at`, `updated_at`) VALUES
(1, 1, 30, 175.50, 75.50, 15.20, 'Active', '2025-02-23 15:16:27', '2025-02-23 15:16:27'),
(2, 2, 25, 168.20, 68.30, 12.50, 'Moderate', '2025-02-23 15:16:27', '2025-02-23 15:16:27'),
(3, 3, 35, 180.00, 82.00, 18.00, 'Very Active', '2025-02-23 15:16:27', '2025-02-23 15:16:27'),
(4, 4, 28, 165.30, 60.20, 10.80, 'Light', '2025-02-23 15:16:27', '2025-02-23 15:16:27'),
(5, 5, 32, 170.10, 70.10, 14.30, 'Active', '2025-02-23 15:16:27', '2025-02-23 15:16:27'),
(6, 10, 29, 172.00, 70.80, 15.80, 'Moderate', '2025-02-01 08:30:00', '2025-03-04 14:00:00'),
(7, 11, 26, 160.50, 56.20, 13.50, 'Active', '2025-02-05 11:00:00', '2025-03-03 09:45:00'),
(8, 12, 33, 179.30, 82.40, 17.20, 'Very Active', '2025-02-10 13:15:00', '2025-03-05 07:30:00'),
(9, 13, 31, 164.80, 62.00, 14.00, 'Light', '2025-02-15 07:00:00', '2025-03-02 12:20:00'),
(10, 14, 28, 181.00, 85.50, 16.50, 'Active', '2025-02-20 15:30:00', '2025-03-04 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `verification_tokens`
--

CREATE TABLE `verification_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(32) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_tokens`
--

INSERT INTO `verification_tokens` (`id`, `email`, `token`, `expires_at`) VALUES
(1, 'youssefdhib26@gmail.com', 'b0e18df1dae40e4014a5292df8d90923', '2025-03-05 15:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_predefined` tinyint(1) DEFAULT 0,
  `duration` int(11) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `is_custom` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`id`, `user_id`, `name`, `description`, `category_id`, `created_at`, `deleted_at`, `updated_at`, `is_predefined`, `duration`, `calories`, `is_custom`) VALUES
(1, 1, 'Beginner Full Body Workout', 'A starter workout for beginners', NULL, '2025-02-22 16:25:30', NULL, '2025-02-22 16:25:30', 1, NULL, NULL, 0),
(2, 1, '4-Day Upper 1', 'Upper body workout for Day 1', NULL, '2025-02-23 12:25:05', NULL, '2025-02-23 12:25:05', 1, NULL, NULL, 0),
(3, 1, '4-Day Lower 1', 'Lower body workout for Day 2', NULL, '2025-02-23 12:25:05', NULL, '2025-02-23 12:25:05', 1, NULL, NULL, 0),
(4, 1, '5-Day Chest', 'Chest workout for Monday', NULL, '2025-02-23 12:25:05', NULL, '2025-02-23 12:25:05', 1, NULL, NULL, 0),
(5, 1, 'Morning Cardio', '30-min treadmill run', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 30, 300, 0),
(6, 2, 'Strength Training', 'Upper body with weights', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 60, 450, 0),
(7, 3, 'HIIT Session', 'High-intensity interval training', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 45, 400, 0),
(8, 4, 'Yoga Flow', 'Relaxing yoga for flexibility', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 60, 250, 0),
(9, 5, 'Lower Body Workout', 'Legs and glutes focus', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 50, 350, 0),
(10, 1, 'Advanced Lifting', 'Heavy squats and deadlifts', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 75, 600, 0),
(11, 2, 'Cycling Outdoor', '2-hour bike ride', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 120, 800, 0),
(12, 3, 'Core Workout', 'Ab and core strengthening', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 40, 200, 0),
(13, 4, 'Swimming Laps', 'Lap swimming for endurance', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 45, 350, 0),
(14, 5, 'CrossFit Routine', 'Varied functional exercises', NULL, '2025-02-23 15:13:12', NULL, '2025-02-23 15:13:12', 0, 60, 500, 0),
(15, 1, 'Morning Cardio', '30-min treadmill run', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 30, 300, 0),
(16, 2, 'Strength Training', 'Upper body with weights', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 60, 450, 0),
(17, 3, 'HIIT Session', 'High-intensity interval training', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 45, 400, 0),
(18, 4, 'Yoga Flow', 'Relaxing yoga for flexibility', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 60, 250, 0),
(19, 5, 'Lower Body Workout', 'Legs and glutes focus', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 50, 350, 0),
(20, 1, 'Advanced Lifting', 'Heavy squats and deadlifts', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 75, 600, 0),
(21, 2, 'Cycling Outdoor', '2-hour bike ride', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 120, 800, 0),
(22, 3, 'Core Workout', 'Ab and core strengthening', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 40, 200, 0),
(23, 4, 'Swimming Laps', 'Lap swimming for endurance', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 45, 350, 0),
(24, 5, 'CrossFit Routine', 'Varied functional exercises', NULL, '2025-02-23 15:16:35', NULL, '2025-02-23 15:16:35', 0, 60, 500, 0),
(25, 8, 'legs', 'legs', NULL, '2025-03-04 20:01:14', '2025-03-04 20:17:39', '2025-03-04 20:17:39', 0, 120, NULL, 0),
(26, 8, 'legs', 'legs', NULL, '2025-03-04 20:20:27', '2025-03-04 20:36:54', '2025-03-04 20:36:54', 0, 160, 300, 0),
(27, 8, 'legs', 'legs', NULL, '2025-03-04 20:21:51', '2025-03-04 20:36:58', '2025-03-04 20:36:58', 0, 160, 300, 0),
(28, 8, 'legs', 'legs', NULL, '2025-03-04 20:23:06', '2025-03-04 20:37:02', '2025-03-04 20:37:02', 0, 160, 300, 0),
(29, 8, 'legs', 'legs', NULL, '2025-03-04 20:33:47', '2025-03-04 20:37:07', '2025-03-04 20:37:07', 0, 160, 300, 0),
(30, 8, 'legs', 'legs', NULL, '2025-03-04 20:34:32', '2025-03-04 20:37:23', '2025-03-04 20:37:23', 0, 160, 300, 0),
(31, 8, 'legs', 'legs', NULL, '2025-03-04 20:36:40', NULL, '2025-03-04 20:36:40', 0, 160, 300, 0),
(32, 10, 'Sousse Strength', 'Full-body gym session', 6, '2025-02-02 15:00:00', NULL, '2025-02-02 15:00:00', 0, 60, 500, 0),
(33, 11, 'Tunis Yoga Flow', 'Morning flexibility', 8, '2025-02-06 06:30:00', NULL, '2025-02-06 06:30:00', 0, 50, 200, 0),
(34, 12, 'Medina Run', '10K city run', 7, '2025-02-11 05:45:00', NULL, '2025-02-11 05:45:00', 0, 60, 550, 0),
(35, 13, 'Hammamet Stretch', 'Post-workout stretch', 8, '2025-02-16 17:00:00', NULL, '2025-02-16 17:00:00', 0, 30, 150, 0),
(36, 14, 'Bizerte Power', 'Heavy lifting', 6, '2025-02-21 16:00:00', NULL, '2025-02-21 16:00:00', 0, 75, 600, 0),
(37, 10, 'Carthage Cardio', 'Treadmill intervals', 7, '2025-03-01 07:00:00', NULL, '2025-03-01 07:00:00', 0, 45, 400, 0),
(38, 11, 'Kairouan Core', 'Abs and planks', 6, '2025-03-02 14:30:00', NULL, '2025-03-02 14:30:00', 0, 40, 250, 0),
(39, 12, 'Djerba Swim', 'Pool endurance', 7, '2025-03-03 08:00:00', NULL, '2025-03-03 08:00:00', 0, 50, 350, 0),
(40, 13, 'Sfax Pilates', 'Core stability', 8, '2025-03-04 06:45:00', NULL, '2025-03-04 06:45:00', 0, 45, 200, 0),
(41, 14, 'Tunis Push Day', 'Chest and shoulders', 6, '2025-03-05 15:00:00', NULL, '2025-03-05 15:00:00', 0, 60, 450, 0);

-- --------------------------------------------------------

--
-- Table structure for table `workout_exercises`
--

CREATE TABLE `workout_exercises` (
  `id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` varchar(50) DEFAULT NULL,
  `rest_time` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_exercises`
--

INSERT INTO `workout_exercises` (`id`, `workout_id`, `exercise_id`, `sets`, `reps`, `rest_time`, `notes`, `deleted_at`) VALUES
(1, 1, 1, 3, '8-10', '60s', 'Focus on form', NULL),
(2, 1, 1, 3, '6-8', '60s', NULL, NULL),
(3, 2, 2, 3, '8-10', '60s', NULL, NULL),
(4, 1, 1, 3, '8-10', '60s', 'Focus on form', NULL),
(5, 2, 2, 4, '6-8', '90s', 'Heavy weights', NULL),
(6, 3, 3, 3, '10-12', '60s', NULL, NULL),
(7, 4, 4, 3, '12-15', '45s', 'Hold poses', NULL),
(8, 5, 1, 4, '8-10', '75s', 'Leg focus', NULL),
(9, 6, 2, 5, '3', '10-12', 'Upper focus', NULL),
(10, 7, 3, 4, '6-8', '90s', 'Cardio push', NULL),
(11, 8, 4, 5, '3', '12-15', 'Core stability', NULL),
(12, 9, 5, 3, '8-10', '60s', 'Endurance', NULL),
(13, 10, 1, 4, '10-12', '75s', 'Strength build', NULL),
(14, 11, 2, 3, '6-8', '90s', 'Power lift', NULL),
(15, 12, 3, 3, '10-12', '60s', 'Speed work', NULL),
(16, 13, 4, 4, '12-15', '45s', 'Flexibility', NULL),
(17, 14, 5, 2, '8-10', '60s', 'Full body', NULL),
(18, 26, 10, 4, '12', '60s', NULL, '2025-03-04 20:36:54'),
(19, 26, 6, 3, '10', '60s', NULL, '2025-03-04 20:36:54'),
(20, 26, 15, 4, '8', '60s', NULL, '2025-03-04 20:36:54'),
(21, 27, 1, 4, '12', '60s', NULL, '2025-03-04 20:36:58'),
(22, 28, 1, 4, '12', '60s', NULL, '2025-03-04 20:37:02'),
(23, 29, 9, 4, '12', '60s', NULL, '2025-03-04 20:37:07'),
(24, 30, 9, 4, '12', '60s', NULL, '2025-03-04 20:37:23'),
(25, 31, 9, 4, '12', '60s', NULL, NULL),
(26, 32, 20, 4, '8-10', '90s', 'Keep back straight', NULL),
(27, 33, 21, 3, 'Hold 30s', '30s', 'Flow smoothly', NULL),
(28, 34, 22, 1, '10K', '0s', 'Maintain pace', NULL),
(29, 35, 23, 3, 'Hold 45s', '15s', 'Stretch deeply', NULL),
(30, 36, 24, 4, '6-8', '120s', 'Heavy load', NULL),
(31, 37, 25, 5, '20s sprint', '40s', 'Max effort', NULL),
(32, 38, 26, 3, '20 per side', '30s', 'Engage abs', NULL),
(33, 39, 27, 1, '40 laps', '0s', 'Steady swim', NULL),
(34, 40, 28, 4, '10-12', '30s', 'Control movement', NULL),
(35, 41, 29, 4, '8-10', '90s', 'Strict form', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `workout_sessions`
--

CREATE TABLE `workout_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_sessions`
--

INSERT INTO `workout_sessions` (`id`, `user_id`, `workout_id`, `date`, `duration`, `calories_burned`, `created_at`) VALUES
(1, 1, 1, '2025-02-20', 30, 300, '2025-02-23 15:16:53'),
(2, 2, 2, '2025-02-21', 60, 450, '2025-02-23 15:16:53'),
(3, 3, 3, '2025-02-22', 45, 400, '2025-02-23 15:16:53'),
(4, 4, 4, '2025-02-23', 60, 250, '2025-02-23 15:16:53'),
(5, 5, 5, '2025-02-19', 50, 350, '2025-02-23 15:16:53'),
(6, 10, 32, '2025-02-05', 60, 500, '2025-02-05 15:30:00'),
(7, 11, 33, '2025-02-10', 50, 200, '2025-02-10 07:00:00'),
(8, 12, 34, '2025-02-15', 60, 550, '2025-02-15 06:15:00'),
(9, 13, 35, '2025-02-20', 30, 150, '2025-02-20 17:30:00'),
(10, 14, 36, '2025-02-25', 75, 600, '2025-02-25 16:45:00'),
(11, 10, 37, '2025-03-01', 45, 400, '2025-03-01 07:45:00'),
(12, 11, 38, '2025-03-02', 40, 250, '2025-03-02 15:00:00'),
(13, 12, 39, '2025-03-03', 50, 350, '2025-03-03 08:30:00'),
(14, 13, 40, '2025-03-04', 45, 200, '2025-03-04 07:15:00'),
(15, 14, 41, '2025-03-05', 60, 450, '2025-03-05 15:30:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_goals_exercise_id` (`exercise_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `meal_plan_items`
--
ALTER TABLE `meal_plan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meal_plan_id` (`meal_plan_id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_id` (`workout_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `workout_sessions`
--
ALTER TABLE `workout_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `workout_id` (`workout_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `meal_plan_items`
--
ALTER TABLE `meal_plan_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `workout_sessions`
--
ALTER TABLE `workout_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `goals_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`);

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `meals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meals_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD CONSTRAINT `meal_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_plan_items`
--
ALTER TABLE `meal_plan_items`
  ADD CONSTRAINT `meal_plan_items_ibfk_1` FOREIGN KEY (`meal_plan_id`) REFERENCES `meal_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meal_plan_items_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  ADD CONSTRAINT `remember_me_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  ADD CONSTRAINT `verification_tokens_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workouts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  ADD CONSTRAINT `workout_exercises_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`),
  ADD CONSTRAINT `workout_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`);

--
-- Constraints for table `workout_sessions`
--
ALTER TABLE `workout_sessions`
  ADD CONSTRAINT `workout_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workout_sessions_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
