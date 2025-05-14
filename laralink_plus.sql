-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 14, 2025 at 08:48 AM
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
-- Database: `laralink_plus`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`id`, `api_key`, `name`, `is_active`, `created_at`, `last_used_at`) VALUES
(1, 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'LaraLink+ Mobile App', 1, '2025-05-11 10:39:06', '2025-05-14 08:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `api_logs`
--

CREATE TABLE `api_logs` (
  `log_id` int NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `method` varchar(10) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `request_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `api_logs`
--

INSERT INTO `api_logs` (`log_id`, `endpoint`, `method`, `ip_address`, `api_key`, `status`, `request_time`) VALUES
(1, '/laralink-api/reports/11', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:02'),
(2, '/laralink-api/reports/11', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:02'),
(3, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(4, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(5, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(6, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(7, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(8, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(9, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(10, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(11, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(12, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(13, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(14, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(15, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:14'),
(16, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:14'),
(17, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:19'),
(18, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:19'),
(19, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:19'),
(20, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:19'),
(21, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:20'),
(22, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:20'),
(23, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:20'),
(24, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:20'),
(25, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:20'),
(26, '/laralink-api/reports?user_id=4&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:20'),
(27, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:21'),
(28, '/laralink-api/contacts', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:41:21'),
(29, '/laralink-api/contacts', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:21'),
(30, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:41:21'),
(31, '/laralink-api/reports', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:42:13'),
(32, '/laralink-api/reports', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:42:13'),
(33, '/laralink-api/users', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(34, '/laralink-api/users', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(35, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(36, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(37, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(38, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(39, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(40, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(41, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(42, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(43, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(44, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(45, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:07'),
(46, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:07'),
(47, '/laralink-api/contacts', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:09'),
(48, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:09'),
(49, '/laralink-api/contacts', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:09'),
(50, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:09'),
(51, '/laralink-api/reports', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:38'),
(52, '/laralink-api/reports', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:38'),
(53, '/laralink-api/reports/2/media', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:38'),
(54, '/laralink-api/reports/2/media', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:38'),
(55, '/laralink-api/reports/2', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:55'),
(56, '/laralink-api/reports/2', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:55'),
(57, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:43:59'),
(58, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:43:59'),
(59, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:44:01'),
(60, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:44:01'),
(61, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:44:01'),
(62, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:44:01'),
(63, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:44:01'),
(64, '/laralink-api/reports?user_id=7&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:44:01'),
(65, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 12:44:01'),
(66, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-13 12:44:01'),
(67, '/laralink-api/users/1', 'GET', '::1', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 19:33:35'),
(68, '/laralink-api/users/1', 'GET', '::1', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'success', '2025-05-13 19:33:35'),
(69, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 20:06:11'),
(70, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-13 20:06:11'),
(71, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-13 20:09:48'),
(72, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-13 20:09:48'),
(73, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 07:36:33'),
(74, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 07:36:33'),
(75, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 07:42:48'),
(76, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 07:42:48'),
(77, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 07:50:03'),
(78, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 07:50:03'),
(79, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 07:54:24'),
(80, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 07:54:24'),
(81, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 07:56:23'),
(82, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 07:56:23'),
(83, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 07:59:13'),
(84, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 07:59:13'),
(85, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:03:33'),
(86, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:03:33'),
(87, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(88, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(89, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(90, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(91, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(92, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(93, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(94, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(95, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(96, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(97, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(98, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(99, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(100, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:48'),
(101, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(102, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:48'),
(103, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:10:49'),
(104, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:10:49'),
(105, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:33'),
(106, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:33'),
(107, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:47'),
(108, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:47'),
(109, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:47'),
(110, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:47'),
(111, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:47'),
(112, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:47'),
(113, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:48'),
(114, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:48'),
(115, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:48'),
(116, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:48'),
(117, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:48'),
(118, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:48'),
(119, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:48'),
(120, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:48'),
(121, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:13:48'),
(122, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:13:48'),
(123, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:18'),
(124, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:18'),
(125, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:18'),
(126, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:18'),
(127, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:18'),
(128, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:18'),
(129, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:18'),
(130, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:18'),
(131, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:18'),
(132, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:18'),
(133, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:19'),
(134, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:19'),
(135, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:19'),
(136, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:19'),
(137, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:20'),
(138, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:20'),
(139, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:21'),
(140, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:21'),
(141, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:21'),
(142, '/laralink-api/materials?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:21'),
(143, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:21'),
(144, '/laralink-api/violence-types', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:21'),
(145, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:21'),
(146, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:21'),
(147, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:21'),
(148, '/laralink-api/reports?user_id=1&page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:21'),
(149, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:21:21'),
(150, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:21:21'),
(151, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:23:21'),
(152, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:23:21'),
(153, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:23:21'),
(154, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:23:21'),
(155, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:23:21'),
(156, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:23:21'),
(157, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:26:39'),
(158, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:26:39'),
(159, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:26:40'),
(160, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:26:40'),
(161, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:26:40'),
(162, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:26:40'),
(163, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:27:13'),
(164, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:27:13'),
(165, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:29:03'),
(166, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:29:03'),
(167, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:29:03'),
(168, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:29:03'),
(169, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:29:03'),
(170, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:29:03'),
(171, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:29:06'),
(172, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:29:06'),
(173, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:29:06'),
(174, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:29:06'),
(175, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:29:06'),
(176, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:29:06'),
(177, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:42:09'),
(178, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:42:09'),
(179, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:42:10'),
(180, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:42:10'),
(181, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:42:17'),
(182, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:42:17'),
(183, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:42:18'),
(184, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:42:18'),
(185, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:42:18'),
(186, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:42:18'),
(187, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:45:30'),
(188, '/laralink-api/admin/login', 'POST', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:45:30'),
(189, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:45:31'),
(190, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:45:31'),
(191, '/laralink-api/reports?page=1&limit=10', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:45:31'),
(192, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:45:31'),
(193, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'lar4l1nk-8d92h3-9sdh2-s8dh2-d82jd93', 'Valid API key - Lara', '2025-05-14 08:45:31'),
(194, '/laralink-api/reports?page=1&limit=5', 'GET', '192.168.1.2', 'unknown', 'success', '2025-05-14 08:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `contact_id` int NOT NULL,
  `contact_type` varchar(50) NOT NULL,
  `contact_value` varchar(255) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`contact_id`, `contact_type`, `contact_value`, `description`, `is_active`) VALUES
(1, 'WhatsApp', '+62895339046899', 'Customer Service LaraLink+', 1),
(2, 'Hotline', '129', 'Telepon Pengaduan Kekerasan Terhadap Perempuan dan Anak', 1),
(3, 'Email', 'support@laralink.id', 'Email Dukungan', 1),
(4, 'Instagram', '@laralink_plus', 'Akun Media Sosial Resmi', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int NOT NULL,
  `user_id` int NOT NULL,
  `violence_type_id` int NOT NULL,
  `perpetrator` varchar(255) NOT NULL,
  `incident_date` datetime NOT NULL,
  `incident_location_lat` decimal(10,8) NOT NULL,
  `incident_location_lng` decimal(11,8) NOT NULL,
  `location_address` text,
  `description` text NOT NULL,
  `report_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('submitted','processing','investigating','resolved','closed') DEFAULT 'submitted',
  `is_anonymous` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `user_id`, `violence_type_id`, `perpetrator`, `incident_date`, `incident_location_lat`, `incident_location_lng`, `location_address`, `description`, `report_date`, `status`, `is_anonymous`) VALUES
(2, 7, 5, 'Yowis', '2025-05-13 12:43:07', -6.76542756, 108.15824081, '11, Tolengas, Sumedang, Jawa Barat', 'Gdf', '2025-05-13 12:43:38', 'submitted', 0);

-- --------------------------------------------------------

--
-- Table structure for table `report_media`
--

CREATE TABLE `report_media` (
  `media_id` int NOT NULL,
  `report_id` int NOT NULL,
  `media_type` enum('image','video') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `report_media`
--

INSERT INTO `report_media` (`media_id`, `report_id`, `media_type`, `file_path`, `uploaded_at`) VALUES
(1, 2, 'image', 'uploads/reports/2/68233e7ae7d37.jpeg', '2025-05-13 12:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `report_status_history`
--

CREATE TABLE `report_status_history` (
  `history_id` int NOT NULL,
  `report_id` int NOT NULL,
  `status` enum('submitted','processing','investigating','resolved','closed') NOT NULL,
  `notes` text,
  `changed_by` varchar(100) NOT NULL,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `report_status_history`
--

INSERT INTO `report_status_history` (`history_id`, `report_id`, `status`, `notes`, `changed_by`, `changed_at`) VALUES
(12, 2, 'submitted', 'Report submitted', 'System', '2025-05-13 12:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `password` varchar(255) DEFAULT NULL COMMENT 'Password hanya digunakan untuk admin',
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','blocked') DEFAULT 'active',
  `fcm_token` varchar(255) DEFAULT NULL COMMENT 'Firebase Cloud Messaging token untuk notifikasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `phone_number`, `name`, `role`, `password`, `registration_date`, `status`, `fcm_token`) VALUES
(1, '08123456789', 'Admin LaraLink+', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-11 10:39:06', 'active', NULL),
(7, '0895339046899', 'Gdt', 'user', NULL, '2025-05-13 12:43:07', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `violence_materials`
--

CREATE TABLE `violence_materials` (
  `material_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `violence_type_id` int DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `violence_materials`
--

INSERT INTO `violence_materials` (`material_id`, `title`, `content`, `violence_type_id`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'Mengenali Tanda-tanda Bullying', 'Bullying dapat berupa fisik, verbal, atau sosial. Berikut adalah tanda-tandarnya...', 3, NULL, '2025-05-11 10:39:06', '2025-05-11 10:39:06'),
(2, 'Cara Melaporkan Kekerasan Seksual', 'Jika Anda menjadi korban kekerasan seksual, penting untuk segera melaporkan kejadian tersebut...', 2, NULL, '2025-05-11 10:39:06', '2025-05-11 10:39:06'),
(3, 'Dampak Psikologis KDRT', 'Korban KDRT sering mengalami trauma berkepanjangan dan masalah kesehatan mental seperti...', 5, NULL, '2025-05-11 10:39:06', '2025-05-11 10:39:06'),
(4, 'Menghindari Cyberbullying', 'Langkah-langkah yang dapat diambil untuk melindungi diri dari kekerasan digital...', 6, NULL, '2025-05-11 10:39:06', '2025-05-11 10:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `violence_types`
--

CREATE TABLE `violence_types` (
  `violence_type_id` int NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `violence_types`
--

INSERT INTO `violence_types` (`violence_type_id`, `type_name`, `description`, `icon_url`) VALUES
(1, 'Kekerasan Fisik', 'Tindakan yang menyebabkan rasa sakit, cedera fisik, luka, atau penderitaan fisik', NULL),
(2, 'Kekerasan Seksual', 'Pemaksaan hubungan seksual atau tindakan seksual tanpa persetujuan', NULL),
(3, 'Bullying', 'Perilaku agresif yang disengaja yang melibatkan ketidakseimbangan kekuatan dan diulang dari waktu ke waktu', NULL),
(4, 'Kekerasan Verbal', 'Penggunaan kata-kata yang kasar, mengancam, merendahkan, atau mengintimidasi', NULL),
(5, 'KDRT', 'Kekerasan Dalam Rumah Tangga yang meliputi kekerasan fisik, psikologis, seksual, atau ekonomi', NULL),
(6, 'Kekerasan Digital', 'Pelecehan atau intimidasi melalui media elektronik atau internet', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`);

--
-- Indexes for table `api_logs`
--
ALTER TABLE `api_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `violence_type_id` (`violence_type_id`);

--
-- Indexes for table `report_media`
--
ALTER TABLE `report_media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `report_status_history`
--
ALTER TABLE `report_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `report_status_history_ibfk_1` (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `violence_materials`
--
ALTER TABLE `violence_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `violence_type_id` (`violence_type_id`);

--
-- Indexes for table `violence_types`
--
ALTER TABLE `violence_types`
  ADD PRIMARY KEY (`violence_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_logs`
--
ALTER TABLE `api_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `contact_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `report_media`
--
ALTER TABLE `report_media`
  MODIFY `media_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `report_status_history`
--
ALTER TABLE `report_status_history`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `violence_materials`
--
ALTER TABLE `violence_materials`
  MODIFY `material_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `violence_types`
--
ALTER TABLE `violence_types`
  MODIFY `violence_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`violence_type_id`) REFERENCES `violence_types` (`violence_type_id`);

--
-- Constraints for table `report_media`
--
ALTER TABLE `report_media`
  ADD CONSTRAINT `report_media_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`report_id`) ON DELETE CASCADE;

--
-- Constraints for table `report_status_history`
--
ALTER TABLE `report_status_history`
  ADD CONSTRAINT `report_status_history_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `violence_materials`
--
ALTER TABLE `violence_materials`
  ADD CONSTRAINT `violence_materials_ibfk_1` FOREIGN KEY (`violence_type_id`) REFERENCES `violence_types` (`violence_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
