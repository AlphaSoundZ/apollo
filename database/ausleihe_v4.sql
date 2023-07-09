-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 15. Jun 2023 um 09:51
-- Server-Version: 10.5.12-MariaDB-0+deb11u1
-- PHP-Version: 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ausleihe_v4`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `devices`
--

CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL,
  `device_type` int(11) NOT NULL,
  `device_uid` text NOT NULL,
  `device_lend_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `devices`
--

INSERT INTO `devices` (`device_id`, `device_type`, `device_uid`, `device_lend_user_id`) VALUES
(2, 3, '8846fce', 1),
(3, 3, '884d6cc', NULL),
(4, 3, '8849ec9', NULL),
(5, 3, '884d5cc', NULL),
(6, 3, '884bfc9', NULL),
(7, 3, '884c4c9', NULL),
(8, 3, '884c2c9', NULL),
(9, 3, '884bac9', NULL),
(10, 3, '884c5ca', NULL),
(11, 3, '88472ce', NULL),
(12, 3, '88473ce', 66),
(13, 3, '88459cd', NULL),
(14, 3, '8846ace', NULL),
(15, 3, '884bec9', NULL),
(16, 3, '8846ece', 66),
(17, 3, '884c3c9', NULL),
(18, 3, '884c0ca', NULL),
(19, 3, '884b9c9', NULL),
(20, 3, '8846bce', NULL),
(21, 3, '88496c9', NULL),
(22, 3, '884c1ca', NULL),
(23, 3, '884b2ca', NULL),
(24, 3, '8849ac9', NULL),
(25, 3, '884b1ca', NULL),
(26, 3, '88499c9', NULL),
(27, 3, '884b6ca', NULL),
(28, 3, '884b5ca', 66),
(29, 3, '88495c9', NULL),
(30, 3, '884b9ca', NULL),
(31, 3, '884baca', NULL),
(34, 3, '884d9cc', 66),
(35, 3, '884bdca', NULL),
(36, 3, '884bcca', NULL),
(38, 3, '88484cd', NULL),
(39, 3, '8847fcd', NULL),
(40, 3, '8847ecb', NULL),
(41, 3, '8847bcb', NULL),
(42, 3, '8847acb', NULL),
(43, 3, '88476cd', 66),
(44, 3, '88472cd', NULL),
(45, 3, '88471cd', NULL),
(46, 3, '8846ecd', NULL),
(47, 3, '8846dcd', NULL),
(48, 3, '8846bcb', NULL),
(49, 3, '8846acb', NULL),
(50, 3, '88472cb', 19),
(51, 3, '8846ecb', NULL),
(52, 3, '8846fcb', NULL),
(53, 3, '884ddcc', NULL),
(54, 3, '88445cc', NULL),
(55, 3, '88473cb', NULL),
(56, 3, '8848cb', NULL),
(57, 3, '884dcb', NULL),
(58, 3, '884ebca', NULL),
(59, 3, '884eaca', NULL),
(60, 3, '884efca', NULL),
(61, 3, '884eeca', 66),
(62, 3, '884f3ca', NULL),
(63, 3, '88475cd', NULL),
(64, 3, '884f2ca', NULL),
(65, 3, '8847ccc', NULL),
(66, 3, '8847ecd', NULL),
(67, 3, '88483cd', NULL),
(68, 3, '88487cd', NULL),
(69, 3, '88488cd', 66),
(70, 3, '8847bcc', NULL),
(71, 3, '8842ecc', NULL),
(74, 3, '8844cb', NULL),
(76, 3, '8843cb', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `event_user_id` int(11) NOT NULL,
  `event_device_id` int(11) NOT NULL,
  `event_begin` timestamp NULL DEFAULT current_timestamp(),
  `event_end` timestamp NULL DEFAULT current_timestamp(),
  `event_multi_booking_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `event`
--

INSERT INTO `event` (`event_id`, `event_user_id`, `event_device_id`, `event_begin`, `event_end`, `event_multi_booking_id`) VALUES
(1, 1, 70, '2022-12-21 11:04:00', '2022-12-21 11:05:07', 1),
(2, 19, 26, '2023-01-09 12:58:03', '2023-01-09 14:28:31', 2),
(3, 19, 70, '2023-01-09 12:58:44', '2023-01-25 10:45:38', 2),
(4, 19, 12, '2023-01-09 13:00:25', '2023-01-09 14:28:40', 2),
(5, 19, 27, '2023-01-09 13:34:30', '2023-01-09 14:29:13', 2),
(6, 19, 43, '2023-01-25 09:58:12', '2023-01-25 10:44:54', 2),
(7, 66, 11, '2023-01-26 08:44:51', '2023-01-26 09:36:46', 3),
(8, 66, 45, '2023-01-26 08:45:02', '2023-01-26 09:36:50', 3),
(9, 66, 66, '2023-01-26 08:45:26', '2023-01-26 09:36:27', 3),
(10, 66, 7, '2023-01-26 08:45:40', '2023-01-26 09:36:06', 3),
(11, 66, 6, '2023-01-26 08:45:48', '2023-01-26 09:36:40', 3),
(12, 66, 71, '2023-01-26 09:35:53', '2023-01-26 09:35:58', 3),
(13, 19, 31, '2023-01-30 09:56:08', '2023-02-03 10:44:02', 4),
(14, 19, 34, '2023-02-01 08:53:35', '2023-03-03 10:45:05', 4),
(15, 19, 68, '2023-02-01 11:01:32', '2023-02-01 12:23:40', 4),
(16, 19, 54, '2023-02-01 12:24:48', '2023-02-01 12:24:55', 4),
(17, 4, 54, '2023-02-01 12:43:01', '2023-02-01 12:43:23', 5),
(18, 1, 68, '2023-02-01 12:44:36', '2023-02-01 12:44:49', 6),
(19, 1, 54, '2023-02-01 12:44:59', '2023-02-01 12:45:01', 7),
(20, 66, 3, '2023-02-02 09:50:55', '2023-02-02 09:51:14', 8),
(21, 1, 2, '2023-02-02 11:33:03', '2023-02-02 11:33:09', 9),
(22, 19, 40, '2023-02-02 13:19:16', '2023-02-02 14:28:27', 4),
(23, 19, 28, '2023-02-02 13:19:58', '2023-02-02 14:29:08', 4),
(24, 19, 22, '2023-02-02 13:20:18', '2023-02-02 14:29:00', 4),
(25, 19, 20, '2023-02-02 13:20:42', '2023-02-06 10:39:35', 4),
(26, 19, 71, '2023-02-02 13:21:04', '2023-02-02 14:28:53', 4),
(27, 19, 70, '2023-02-02 13:21:11', '2023-02-02 14:29:17', 4),
(28, 19, 15, '2023-02-02 13:21:40', '2023-02-02 14:28:32', 4),
(29, 19, 54, '2023-02-03 07:09:01', '2023-02-03 08:39:15', 4),
(30, 19, 51, '2023-02-03 07:09:15', '2023-02-03 10:43:37', 4),
(31, 19, 68, '2023-02-03 07:09:23', '2023-02-03 10:43:24', 4),
(32, 19, 36, '2023-02-03 07:09:31', '2023-02-03 08:39:25', 4),
(33, 66, 45, '2023-02-03 08:51:42', '2023-02-03 10:45:26', 10),
(34, 66, 55, '2023-02-03 08:52:04', '2023-02-03 10:45:11', 10),
(35, 66, 3, '2023-02-03 08:52:19', '2023-02-03 10:44:48', 10),
(36, 66, 7, '2023-02-03 08:52:26', '2023-02-03 10:44:38', 10),
(37, 66, 43, '2023-02-03 08:52:31', '2023-02-03 10:44:57', 10),
(38, 66, 24, '2023-02-03 08:52:39', '2023-02-03 10:44:22', 10),
(39, 66, 67, '2023-02-03 08:52:43', '2023-02-03 10:44:12', 10),
(40, 66, 62, '2023-02-03 08:53:16', '2023-02-03 10:45:49', 10),
(41, 66, 14, '2023-02-03 08:53:23', '2023-02-03 10:45:37', 10),
(42, 19, 6, '2023-02-06 09:56:55', '2023-02-06 10:39:46', 4),
(43, 19, 15, '2023-02-06 09:57:04', '2023-02-06 10:39:57', 4),
(44, 19, 66, '2023-02-06 09:57:12', '2023-02-06 10:39:24', 4),
(45, 19, 71, '2023-02-06 09:57:19', '2023-02-06 10:39:06', 4),
(46, 19, 18, '2023-02-06 09:57:34', '2023-02-06 10:39:13', 4),
(47, 19, 24, '2023-02-06 13:12:22', '2023-02-06 14:27:21', 4),
(48, 19, 7, '2023-02-06 13:12:49', '2023-02-06 14:27:33', 4),
(49, 19, 66, '2023-02-08 08:54:30', '2023-02-08 09:29:54', 4),
(50, 19, 6, '2023-02-08 08:54:50', '2023-02-08 09:29:25', 4),
(51, 19, 7, '2023-02-08 08:55:08', '2023-02-08 09:29:43', 4),
(52, 19, 3, '2023-02-08 08:55:16', '2023-02-08 09:28:50', 4),
(53, 19, 42, '2023-02-08 08:55:33', '2023-02-08 09:29:10', 4),
(54, 19, 28, '2023-02-08 08:55:43', '2023-02-08 09:29:34', 4),
(55, 19, 24, '2023-02-08 08:55:52', '2023-02-08 09:29:00', 4),
(56, 19, 12, '2023-02-08 08:56:05', '2023-02-08 09:34:18', 4),
(57, 66, 26, '2023-02-09 09:40:31', '2023-02-09 10:55:58', 11),
(58, 66, 24, '2023-02-09 09:40:40', '2023-02-09 10:56:22', 11),
(59, 66, 12, '2023-02-09 09:41:10', NULL, 11),
(60, 66, 28, '2023-02-09 09:41:17', '2023-02-09 10:53:06', 11),
(61, 66, 18, '2023-02-09 09:41:24', '2023-02-09 10:54:59', 11),
(62, 66, 14, '2023-02-09 09:41:31', '2023-02-09 11:00:15', 11),
(63, 66, 20, '2023-02-09 09:41:37', '2023-02-09 10:59:35', 11),
(64, 66, 67, '2023-02-09 09:41:43', '2023-02-09 10:59:24', 11),
(65, 66, 69, '2023-02-09 09:41:51', NULL, 11),
(66, 66, 60, '2023-02-09 09:42:01', '2023-02-09 10:58:14', 11),
(67, 66, 27, '2023-02-09 09:42:30', '2023-02-09 10:54:51', 11),
(68, 66, 70, '2023-02-09 09:42:37', '2023-02-09 10:51:42', 11),
(69, 66, 55, '2023-02-09 09:42:51', '2023-02-09 10:51:17', 11),
(70, 66, 61, '2023-02-09 09:42:57', '2023-02-09 10:59:06', 11),
(71, 66, 62, '2023-02-09 09:43:05', '2023-02-09 10:55:45', 11),
(72, 66, 43, '2023-02-09 09:43:11', '2023-02-09 10:55:09', 11),
(73, 66, 54, '2023-02-09 09:43:17', '2023-02-09 10:58:55', 11),
(74, 66, 3, '2023-02-09 09:43:27', '2023-02-09 10:53:15', 11),
(75, 66, 42, '2023-02-09 09:44:00', '2023-02-09 11:00:02', 11),
(76, 66, 2, '2023-02-09 09:44:27', '2023-02-09 10:51:30', 11),
(77, 66, 8, '2023-02-09 09:44:33', '2023-02-09 10:59:48', 11),
(78, 66, 7, '2023-02-09 09:44:48', '2023-02-09 10:52:53', 11),
(79, 66, 18, '2023-02-09 11:01:03', '2023-02-09 11:01:28', 11),
(80, 19, 55, '2023-02-09 12:57:57', '2023-02-09 14:29:43', 4),
(81, 19, 2, '2023-02-09 12:58:08', '2023-02-09 14:29:32', 4),
(82, 19, 70, '2023-02-09 12:58:20', '2023-02-09 14:29:36', 4),
(83, 19, 68, '2023-02-09 12:59:00', '2023-02-09 14:30:54', 4),
(84, 19, 23, '2023-02-09 12:59:12', '2023-02-09 14:29:27', 4),
(85, 66, 28, '2023-02-10 08:49:27', '2023-02-10 10:43:42', 11),
(86, 66, 3, '2023-02-10 08:50:10', '2023-02-10 10:42:11', 11),
(87, 66, 43, '2023-02-10 08:50:46', '2023-02-10 10:42:19', 11),
(88, 66, 55, '2023-02-10 08:50:55', '2023-02-10 10:42:32', 11),
(89, 66, 70, '2023-02-10 08:51:05', '2023-02-10 10:41:55', 11),
(90, 66, 18, '2023-02-10 08:51:27', '2023-02-10 10:42:40', 11),
(91, 66, 68, '2023-02-10 08:51:49', '2023-02-10 10:43:30', 11),
(92, 66, 67, '2023-02-10 08:52:07', '2023-02-10 10:43:07', 11),
(93, 19, 63, '2023-02-15 07:06:33', '2023-02-24 10:43:10', 4),
(94, 19, 15, '2023-02-15 07:06:44', '2023-02-24 10:42:28', 4),
(95, 19, 6, '2023-02-15 07:06:51', '2023-02-24 10:41:52', 4),
(96, 19, 14, '2023-02-15 07:07:05', '2023-04-14 09:47:48', 4),
(97, 66, 53, '2023-02-17 08:43:04', '2023-02-17 10:41:59', 11),
(98, 66, 51, '2023-02-17 08:43:09', '2023-02-17 10:42:06', 11),
(99, 66, 20, '2023-02-17 08:43:38', '2023-02-17 10:41:26', 11),
(100, 66, 2, '2023-02-17 08:43:48', '2023-02-17 10:43:07', 11),
(101, 66, 54, '2023-02-17 08:43:55', '2023-02-17 10:41:16', 11),
(102, 66, 28, '2023-02-17 08:44:04', '2023-02-17 10:43:33', 11),
(103, 66, 31, '2023-02-17 08:44:14', '2023-02-17 10:43:47', 11),
(104, 66, 67, '2023-02-17 08:44:19', '2023-02-17 10:43:41', 11),
(105, 66, 40, '2023-02-17 08:44:22', '2023-02-17 10:42:13', 11),
(106, 66, 10, '2023-02-17 08:53:24', '2023-02-17 10:41:47', 11),
(107, 19, 7, '2023-02-17 10:59:13', '2023-02-17 11:17:46', 4),
(108, 66, 51, '2023-02-24 08:41:01', '2023-03-03 08:41:25', 11),
(109, 66, 70, '2023-02-24 08:41:26', '2023-04-14 09:46:44', 11),
(110, 66, 66, '2023-02-24 08:41:32', '2023-04-28 07:46:09', 11),
(111, 66, 36, '2023-02-24 08:41:40', '2023-03-03 08:41:17', 11),
(112, 66, 28, '2023-02-24 08:41:47', NULL, 11),
(113, 66, 20, '2023-02-24 08:41:55', '2023-02-24 10:42:49', 11),
(114, 66, 43, '2023-02-24 08:42:13', NULL, 11),
(115, 66, 23, '2023-02-24 08:42:21', '2023-02-24 10:42:10', 11),
(116, 66, 62, '2023-02-24 08:42:49', '2023-04-14 09:48:03', 11),
(117, 66, 67, '2023-02-24 08:44:03', '2023-03-03 10:42:39', 11),
(118, 19, 40, '2023-03-02 07:55:41', '2023-03-02 07:57:40', 4),
(119, 19, 40, '2023-03-02 07:57:49', '2023-03-02 07:58:06', 4),
(120, 4, 40, '2023-03-02 07:58:40', '2023-03-02 07:58:45', 12),
(121, 1, 2, '2023-03-02 10:40:58', '2023-03-02 10:41:07', 13),
(122, 4, 27, '2023-03-03 08:40:00', '2023-03-03 08:40:23', 14),
(123, 4, 27, '2023-03-03 08:40:45', '2023-03-03 08:41:29', 15),
(124, 4, 31, '2023-03-03 08:41:07', '2023-03-03 08:41:12', 15),
(125, 66, 36, '2023-03-03 08:48:55', '2023-03-03 10:42:32', 11),
(126, 66, 8, '2023-03-03 08:49:09', '2023-03-03 10:45:25', 11),
(127, 66, 53, '2023-03-03 08:49:31', '2023-03-03 10:44:13', 11),
(128, 66, 23, '2023-03-03 08:49:59', '2023-03-03 10:45:39', 11),
(129, 66, 40, '2023-03-03 08:50:19', '2023-03-03 10:44:40', 11),
(130, 66, 19, '2023-03-03 08:50:32', '2023-03-03 10:45:14', 11),
(131, 66, 54, '2023-03-03 08:50:59', '2023-03-03 10:42:50', 11),
(132, 66, 6, '2023-03-03 08:51:06', '2023-03-03 10:45:49', 11),
(133, 66, 63, '2023-03-03 08:51:24', '2023-03-03 10:44:52', 11),
(134, 66, 67, '2023-04-14 07:47:30', '2023-04-14 09:47:04', 11),
(135, 66, 34, '2023-04-14 07:47:37', '2023-04-14 09:46:28', 11),
(136, 66, 15, '2023-04-14 07:47:42', '2023-04-14 09:46:09', 11),
(137, 66, 38, '2023-04-14 07:47:51', '2023-04-14 09:47:31', 11),
(138, 66, 53, '2023-04-14 07:48:43', '2023-04-14 09:47:23', 11),
(139, 66, 27, '2023-04-14 07:49:01', '2023-04-14 09:47:42', 11),
(140, 66, 61, '2023-04-14 07:49:08', '2023-04-14 09:47:20', 11),
(141, 1, 2, '2023-04-26 10:39:24', '2023-04-26 10:39:30', 16),
(142, 1, 2, '2023-04-26 10:39:35', NULL, 17),
(143, 66, 61, '2023-04-28 07:43:35', '2023-04-28 09:35:30', 11),
(144, 66, 57, '2023-04-28 07:43:41', '2023-04-28 09:36:01', 11),
(145, 66, 41, '2023-04-28 07:43:46', '2023-04-28 09:35:38', 11),
(146, 66, 34, '2023-04-28 07:44:08', '2023-04-28 09:36:04', 11),
(147, 66, 59, '2023-04-28 07:44:22', '2023-04-28 09:36:47', 11),
(148, 66, 16, '2023-04-28 07:44:34', NULL, 11),
(149, 66, 64, '2023-04-28 07:45:12', '2023-04-28 09:36:30', 11),
(150, 66, 34, '2023-05-05 07:52:30', NULL, 11),
(151, 66, 52, '2023-05-05 07:52:42', '2023-05-05 09:35:34', 11),
(152, 66, 11, '2023-05-05 07:52:45', '2023-05-05 09:34:42', 11),
(153, 66, 20, '2023-05-05 07:53:28', '2023-05-05 09:35:58', 11),
(154, 66, 41, '2023-05-05 07:53:35', '2023-05-05 09:35:52', 11),
(155, 66, 53, '2023-05-12 07:55:19', '2023-05-12 09:42:13', 11),
(156, 66, 52, '2023-05-12 07:55:29', '2023-05-12 09:40:56', 11),
(157, 66, 11, '2023-05-12 07:56:02', '2023-05-12 09:40:55', 11),
(158, 66, 61, '2023-05-12 07:56:07', NULL, 11),
(159, 66, 15, '2023-05-12 07:56:21', '2023-05-12 09:40:43', 11),
(160, 66, 7, '2023-05-12 07:56:25', '2023-05-12 09:41:09', 11);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_class`
--

CREATE TABLE `property_class` (
  `class_id` int(11) NOT NULL,
  `class_name` text NOT NULL,
  `multi_booking` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `property_class`
--

INSERT INTO `property_class` (`class_id`, `class_name`, `multi_booking`) VALUES
(1, 'Lehrer', 1),
(2, 'Schüler', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_device_type`
--

CREATE TABLE `property_device_type` (
  `device_type_id` int(11) NOT NULL,
  `device_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `property_device_type`
--

INSERT INTO `property_device_type` (`device_type_id`, `device_type_name`) VALUES
(1, 'Laptop'),
(2, 'Ipad'),
(3, 'Surface Book');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_token_permissions`
--

CREATE TABLE `property_token_permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `property_token_permissions`
--

INSERT INTO `property_token_permissions` (`permission_id`, `permission_text`) VALUES
(1, 'book');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_usercard_type`
--

CREATE TABLE `property_usercard_type` (
  `usercard_type_id` int(11) NOT NULL,
  `usercard_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `property_usercard_type`
--

INSERT INTO `property_usercard_type` (`usercard_type_id`, `usercard_type_name`) VALUES
(1, 'RFID Chip');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `token`
--

CREATE TABLE `token` (
  `token_id` int(11) NOT NULL,
  `token_username` text NOT NULL,
  `token_password` text NOT NULL,
  `token_last_change` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `token`
--

INSERT INTO `token` (`token_id`, `token_username`, `token_password`, `token_last_change`) VALUES
(1, 'booking_account', '$2a$12$EqOR4hUyJxmkYv.nX.sm9ua0RQRMPkx4gxSxEVGWYgPu4UWRGqX3a', '2022-08-26 22:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `token_link_permissions`
--

CREATE TABLE `token_link_permissions` (
  `link_permission_id` int(11) NOT NULL,
  `link_token_id` int(11) NOT NULL,
  `link_token_permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `token_link_permissions`
--

INSERT INTO `token_link_permissions` (`link_permission_id`, `link_token_id`, `link_token_permission_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_firstname` text NOT NULL,
  `user_lastname` text NOT NULL,
  `user_class` int(11) NOT NULL,
  `user_token_id` int(11) DEFAULT NULL,
  `user_usercard_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_class`, `user_token_id`, `user_usercard_id`) VALUES
(1, 'Test Vorname', 'Test Nachname', 1, 1, 1),
(2, 'Pia', 'Brüntrup', 1, NULL, 32),
(4, 'Max', 'Heilmann', 1, NULL, 37),
(5, 'Emils', 'Fischerei', 1, NULL, 72),
(6, 'Samio', 'Mohammed', 1, NULL, 73),
(7, 'Nils', 'Cleven', 1, NULL, 75),
(8, 'Alain', 'Regidor', 1, NULL, 77),
(9, 'Alexander', 'Potrykus', 1, NULL, 33),
(10, 'Alexandra', 'Wilson', 1, NULL, 78),
(11, 'Anja', 'Woitalla', 1, NULL, 79),
(12, 'Annabrit', 'Evert', 1, NULL, 81),
(13, 'Anne', 'Möbert', 1, NULL, 80),
(14, 'Antonella', 'Frisina', 1, NULL, NULL),
(15, 'Beatrice', 'Asare-Lartey', 1, NULL, 129),
(16, 'Betül', 'Aslan', 1, NULL, 82),
(17, 'Britta', 'Letzner', 1, NULL, 83),
(18, 'Christian', 'Laarz', 1, NULL, NULL),
(19, 'Christian', 'Müller', 1, NULL, 130),
(20, 'Christine', 'Velmede', 1, NULL, 84),
(21, 'Eva', 'Voermanek', 1, NULL, 85),
(22, 'Eva', 'Kreuzeder', 1, NULL, 86),
(23, 'Felix', 'Bunzel', 1, NULL, 87),
(24, 'Finja', 'Lucassen', 1, NULL, 88),
(25, 'Florian', 'Baumert', 1, NULL, 89),
(26, 'Helena', 'Zeller', 1, NULL, 125),
(27, 'Inga', 'Sönnichsen', 1, NULL, 90),
(28, 'Janne', 'Meyn', 1, NULL, 91),
(29, 'Jasmin', 'Bolwin', 1, NULL, 92),
(30, 'Joannis', 'Stassinopoulos', 1, NULL, 93),
(31, 'Jörg', 'Neuwerth', 1, NULL, 94),
(32, 'Jörn', 'Krönert', 1, NULL, 95),
(33, 'Josephine', 'Reinefarth', 1, NULL, 96),
(34, 'Julia', 'Hornbostel', 1, NULL, 126),
(35, 'Juliane', 'Brunner', 1, NULL, 97),
(36, 'Katharina', 'Weiland', 1, NULL, 127),
(37, 'Katrin', 'Carstens', 1, NULL, 98),
(38, 'Katrin', 'Zeng', 1, NULL, 99),
(39, 'Kristin', 'Schulz', 1, NULL, 100),
(40, 'Leonie Isabelle', 'Schüler', 1, NULL, 101),
(41, 'Lina', 'Minners', 1, NULL, 102),
(42, 'Lisa', 'Straßer', 1, NULL, 103),
(43, 'Lynn', 'Kreipe', 1, NULL, 104),
(44, 'Manuel', 'Bamming', 1, NULL, 105),
(45, 'Marcel', 'Stemmler-Nemcic', 1, NULL, 106),
(46, 'Mareike', 'Mönnich', 1, NULL, 107),
(47, 'Maren', 'Dellbrügger', 1, NULL, 108),
(48, 'Markus', 'Heimbach', 1, NULL, 109),
(49, 'Maya Antonia', 'Paasch', 1, NULL, 110),
(50, 'Mike', 'Korherr', 1, NULL, 111),
(51, 'Miriam-Sarah', 'Nöldner', 1, NULL, 112),
(52, 'Nils', 'Lenzen', 1, NULL, NULL),
(53, 'Nino', 'Ehlers', 1, NULL, 127),
(55, 'Rainer', 'Munck', 1, NULL, 113),
(56, 'Rita', 'Wolf', 1, NULL, 114),
(57, 'Sarah', 'Müller', 1, NULL, 115),
(58, 'Sarah', 'Mirlacher', 1, NULL, 128),
(59, 'Sascha', 'Haffer', 1, NULL, 116),
(60, 'Simon', 'Löer', 1, NULL, 117),
(61, 'Steffen', 'Kaminsky', 1, NULL, 118),
(62, 'Susanne', 'Wagner', 1, NULL, 119),
(63, 'Sven', 'Surup', 1, NULL, 120),
(64, 'Svenja', 'Blum', 1, NULL, NULL),
(65, 'Timo', 'Trunk', 1, NULL, 121),
(66, 'Tobias', 'Drechsel', 1, NULL, 122),
(67, 'VincentRouven', 'Dobrick', 1, NULL, 123),
(68, 'Wiebke', 'Suchanek', 1, NULL, 124);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `usercard`
--

CREATE TABLE `usercard` (
  `usercard_id` int(11) NOT NULL,
  `usercard_type` int(11) NOT NULL,
  `usercard_uid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `usercard`
--

INSERT INTO `usercard` (`usercard_id`, `usercard_type`, `usercard_uid`) VALUES
(1, 1, '2e9ab22'),
(32, 1, '884eea2'),
(33, 1, '884a5f4'),
(37, 1, 'eed5d22'),
(72, 1, 'a2937623'),
(73, 1, 'aea6822'),
(75, 1, 'b9c4651'),
(77, 1, '884b9e9'),
(78, 1, '88497c3'),
(79, 1, '884b4ab'),
(80, 1, '8842ce6'),
(81, 1, '88482b7'),
(82, 1, '884428d'),
(83, 1, '8846e9'),
(84, 1, '8846b93'),
(85, 1, '88477ea'),
(86, 1, '8843e6'),
(87, 1, '88435d8'),
(88, 1, '8848c8d'),
(89, 1, '8847ff4'),
(90, 1, '88479f4'),
(91, 1, '884b6ab'),
(92, 1, '88442e7'),
(93, 1, '884e98a'),
(94, 1, '8844bea'),
(95, 1, '8846fb7'),
(96, 1, '88456ea'),
(97, 1, '8847d8'),
(98, 1, '8842283'),
(99, 1, '8841d85'),
(100, 1, '884885'),
(101, 1, '884e6e9'),
(102, 1, '884aa9'),
(103, 1, '8846d8'),
(104, 1, '8848e9f'),
(105, 1, '884498b'),
(106, 1, '884c7a1'),
(107, 1, '8845c8d'),
(108, 1, '884a2e9'),
(109, 1, '884ead8'),
(110, 1, '8842be6'),
(111, 1, '884f8d8'),
(112, 1, '884d8d8'),
(113, 1, '8841dd8'),
(114, 1, '88457e7'),
(115, 1, '8844af5'),
(116, 1, '88499f4'),
(117, 1, '88433e6'),
(118, 1, '8842de7'),
(119, 1, '88472c3'),
(120, 1, '884e9e6'),
(121, 1, '8841fca'),
(122, 1, '8848787'),
(123, 1, '884ca8c'),
(124, 1, '884b1c'),
(125, 1, '88483e1'),
(126, 1, '88424f5'),
(127, 1, '884b5c'),
(128, 1, '8845ce7'),
(129, 1, '9a34701a'),
(130, 1, '884c8c');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_id`),
  ADD KEY `type_constraint` (`device_type`),
  ADD KEY `user_constraint` (`device_lend_user_id`);

--
-- Indizes für die Tabelle `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `device_constraint` (`event_device_id`),
  ADD KEY `FK_event_user` (`event_user_id`);

--
-- Indizes für die Tabelle `property_class`
--
ALTER TABLE `property_class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indizes für die Tabelle `property_device_type`
--
ALTER TABLE `property_device_type`
  ADD PRIMARY KEY (`device_type_id`);

--
-- Indizes für die Tabelle `property_token_permissions`
--
ALTER TABLE `property_token_permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indizes für die Tabelle `property_usercard_type`
--
ALTER TABLE `property_usercard_type`
  ADD PRIMARY KEY (`usercard_type_id`);

--
-- Indizes für die Tabelle `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`token_id`);

--
-- Indizes für die Tabelle `token_link_permissions`
--
ALTER TABLE `token_link_permissions`
  ADD PRIMARY KEY (`link_permission_id`),
  ADD KEY `token_constraint` (`link_token_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `class_constraint` (`user_class`),
  ADD KEY `user_token_constraint` (`user_token_id`),
  ADD KEY `user_usercard_constraint` (`user_usercard_id`);

--
-- Indizes für die Tabelle `usercard`
--
ALTER TABLE `usercard`
  ADD PRIMARY KEY (`usercard_id`),
  ADD KEY `usecard_type_constraint` (`usercard_type`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT für Tabelle `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT für Tabelle `property_class`
--
ALTER TABLE `property_class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `property_device_type`
--
ALTER TABLE `property_device_type`
  MODIFY `device_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `property_token_permissions`
--
ALTER TABLE `property_token_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `property_usercard_type`
--
ALTER TABLE `property_usercard_type`
  MODIFY `usercard_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `token`
--
ALTER TABLE `token`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `token_link_permissions`
--
ALTER TABLE `token_link_permissions`
  MODIFY `link_permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT für Tabelle `usercard`
--
ALTER TABLE `usercard`
  MODIFY `usercard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `type_constraint` FOREIGN KEY (`device_type`) REFERENCES `property_device_type` (`device_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_constraint` FOREIGN KEY (`device_lend_user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_event_user` FOREIGN KEY (`event_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `device_constraint` FOREIGN KEY (`event_device_id`) REFERENCES `devices` (`device_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `token_link_permissions`
--
ALTER TABLE `token_link_permissions`
  ADD CONSTRAINT `permission_constraint` FOREIGN KEY (`link_permission_id`) REFERENCES `property_token_permissions` (`permission_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `token_constraint` FOREIGN KEY (`link_token_id`) REFERENCES `token` (`token_id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `class_constraint` FOREIGN KEY (`user_class`) REFERENCES `property_class` (`class_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_token_constraint` FOREIGN KEY (`user_token_id`) REFERENCES `token` (`token_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_usercard_constraint` FOREIGN KEY (`user_usercard_id`) REFERENCES `usercard` (`usercard_id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `usercard`
--
ALTER TABLE `usercard`
  ADD CONSTRAINT `usecard_type_constraint` FOREIGN KEY (`usercard_type`) REFERENCES `property_usercard_type` (`usercard_type_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
