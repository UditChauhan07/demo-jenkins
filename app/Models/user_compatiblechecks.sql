-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2022 at 12:19 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `astar`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_compatiblechecks`
--

CREATE TABLE `user_compatiblechecks` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL COMMENT '(1->car, 2->one to one, 3->business, 4->property, 5->partner)',
  `type_name` text DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `number` varchar(100) DEFAULT NULL,
  `postalcode` int(11) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `type_dates` int(11) DEFAULT NULL COMMENT '(1->incorporation_date, 2->profession start date, 3-> dob\r\n)',
  `dates` date DEFAULT NULL,
  `no_of_partner` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_compatiblechecks`
--

INSERT INTO `user_compatiblechecks` (`id`, `user_id`, `type`, `type_name`, `name`, `gender`, `email`, `number`, `postalcode`, `city`, `type_dates`, `dates`, `no_of_partner`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 30, 1, 'personal', 'tata', NULL, NULL, 'DL3CCF0619', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2022-07-28 07:48:52', '2022-07-28 07:48:52'),
(2, 30, 2, NULL, 'Anubhav', 'male', 'anubhav123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2022-07-28 07:48:57', '2022-07-28 07:48:57'),
(3, 30, 3, 'information technology', 'DesignerX', NULL, NULL, NULL, NULL, NULL, 1, '2015-01-01', NULL, 1, NULL, '2022-07-28 07:49:04', '2022-07-28 07:49:04'),
(4, 30, 4, 'residental', NULL, NULL, NULL, '675', 140308, 'mohali', NULL, NULL, NULL, 1, NULL, '2022-07-28 07:49:08', '2022-07-28 07:49:08'),
(5, 30, 5, 'profession', 'web designer', NULL, NULL, NULL, NULL, NULL, 2, '2022-01-01', NULL, 1, NULL, '2022-07-28 07:49:12', '2022-07-28 07:49:12'),
(6, 30, 3, 'information technology', 'DesignerX', NULL, NULL, NULL, NULL, NULL, 1, '2015-01-01', NULL, 1, NULL, '2022-08-02 04:20:36', '2022-08-02 04:20:36'),
(7, 30, 3, 'information technology', 'DesignerX', NULL, NULL, NULL, NULL, NULL, 1, '2015-01-01', NULL, 1, NULL, '2022-08-02 04:21:39', '2022-08-02 04:21:39'),
(8, 30, 3, 'information technology', 'DesignerX', NULL, NULL, NULL, NULL, NULL, 1, '2015-01-01', NULL, 1, NULL, '2022-08-02 04:22:35', '2022-08-02 04:22:35'),
(9, 30, 1, 'personal', 'tata altroz', NULL, NULL, 'DL3CCF0619', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2022-08-02 04:23:35', '2022-08-02 04:23:35'),
(10, 30, 1, 'personal', 'tata altroz', NULL, NULL, 'DL3CCF0619', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2022-08-02 04:23:58', '2022-08-02 04:23:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_compatiblechecks`
--
ALTER TABLE `user_compatiblechecks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_compatiblechecks`
--
ALTER TABLE `user_compatiblechecks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
