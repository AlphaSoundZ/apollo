-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.28-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportiere Datenbank Struktur für ausleihe_v6
CREATE DATABASE IF NOT EXISTS `ausleihe_v6` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `ausleihe_v6`;

-- Exportiere Struktur von Tabelle ausleihe_v6.devices
CREATE TABLE IF NOT EXISTS `devices` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type` int(11) NOT NULL,
  `device_uid` text NOT NULL,
  `device_lend_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `device_uid` (`device_uid`) USING HASH,
  KEY `type_constraint` (`device_type`),
  KEY `user_constraint` (`device_lend_user_id`),
  CONSTRAINT `type_constraint` FOREIGN KEY (`device_type`) REFERENCES `property_device_type` (`device_type_id`) ON UPDATE CASCADE,
  CONSTRAINT `user_constraint` FOREIGN KEY (`device_lend_user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.devices: ~69 rows (ungefähr)
INSERT INTO `devices` (`device_id`, `device_type`, `device_uid`, `device_lend_user_id`) VALUES
	(2, 3, '8846fce', NULL),
	(3, 3, '884d6cc', 1),
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
	(28, 3, '884b5ca', NULL),
	(29, 3, '88495c9', NULL),
	(30, 3, '884b9ca', NULL),
	(31, 3, '884baca', NULL),
	(34, 3, '884d9cc', NULL),
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
	(61, 3, '884eeca', NULL),
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

-- Exportiere Struktur von Tabelle ausleihe_v6.event
CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_user_id` int(11) NOT NULL,
  `event_device_id` int(11) NOT NULL,
  `event_begin` timestamp NULL DEFAULT current_timestamp(),
  `event_end` timestamp NULL DEFAULT current_timestamp(),
  `event_multi_booking_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `device_constraint` (`event_device_id`),
  KEY `FK_event_user` (`event_user_id`),
  CONSTRAINT `FK_event_user` FOREIGN KEY (`event_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `device_constraint` FOREIGN KEY (`event_device_id`) REFERENCES `devices` (`device_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.event: ~163 rows (ungefähr)
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
	(112, 66, 28, '2023-02-24 08:41:47', '2023-07-07 08:48:21', 11),
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
	(142, 1, 2, '2023-04-26 10:39:35', '2023-07-03 07:55:52', 17),
	(143, 66, 61, '2023-04-28 07:43:35', '2023-04-28 09:35:30', 11),
	(144, 66, 57, '2023-04-28 07:43:41', '2023-04-28 09:36:01', 11),
	(145, 66, 41, '2023-04-28 07:43:46', '2023-04-28 09:35:38', 11),
	(146, 66, 34, '2023-04-28 07:44:08', '2023-04-28 09:36:04', 11),
	(147, 66, 59, '2023-04-28 07:44:22', '2023-04-28 09:36:47', 11),
	(148, 66, 16, '2023-04-28 07:44:34', NULL, 11),
	(149, 66, 64, '2023-04-28 07:45:12', '2023-04-28 09:36:30', 11),
	(150, 66, 34, '2023-05-05 07:52:30', '2023-07-07 08:47:12', 11),
	(151, 66, 52, '2023-05-05 07:52:42', '2023-05-05 09:35:34', 11),
	(152, 66, 11, '2023-05-05 07:52:45', '2023-05-05 09:34:42', 11),
	(153, 66, 20, '2023-05-05 07:53:28', '2023-05-05 09:35:58', 11),
	(154, 66, 41, '2023-05-05 07:53:35', '2023-05-05 09:35:52', 11),
	(155, 66, 53, '2023-05-12 07:55:19', '2023-05-12 09:42:13', 11),
	(156, 66, 52, '2023-05-12 07:55:29', '2023-05-12 09:40:56', 11),
	(157, 66, 11, '2023-05-12 07:56:02', '2023-05-12 09:40:55', 11),
	(158, 66, 61, '2023-05-12 07:56:07', '2023-07-07 08:48:07', 11),
	(159, 66, 15, '2023-05-12 07:56:21', '2023-05-12 09:40:43', 11),
	(160, 66, 7, '2023-05-12 07:56:25', '2023-05-12 09:41:09', 11),
	(161, 1, 3, '2023-07-03 07:55:15', NULL, 17),
	(162, 1, 15, '2023-07-06 09:49:56', '2023-07-06 09:50:04', 17),
	(163, 1, 70, '2023-07-06 11:53:01', '2023-07-06 11:53:55', 17);

-- Exportiere Struktur von Tabelle ausleihe_v6.prebook
CREATE TABLE IF NOT EXISTS `prebook` (
  `prebook_id` int(11) NOT NULL AUTO_INCREMENT,
  `prebook_user_id` int(11) NOT NULL,
  `prebook_amount` int(11) NOT NULL,
  `prebook_begin` timestamp NOT NULL DEFAULT current_timestamp(),
  `prebook_end` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`prebook_id`),
  KEY `FK_prebook_user` (`prebook_user_id`),
  CONSTRAINT `FK_prebook_user` FOREIGN KEY (`prebook_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.prebook: ~0 rows (ungefähr)

-- Exportiere Struktur von Tabelle ausleihe_v6.property_class
CREATE TABLE IF NOT EXISTS `property_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` text NOT NULL,
  `multi_booking` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class_name` (`class_name`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_class: ~2 rows (ungefähr)
INSERT INTO `property_class` (`class_id`, `class_name`, `multi_booking`) VALUES
	(1, 'Lehrer', b'1'),
	(2, 'Schüler', b'0');

-- Exportiere Struktur von Tabelle ausleihe_v6.property_device_type
CREATE TABLE IF NOT EXISTS `property_device_type` (
  `device_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type_name` text NOT NULL,
  PRIMARY KEY (`device_type_id`),
  UNIQUE KEY `device_type_name` (`device_type_name`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_device_type: ~3 rows (ungefähr)
INSERT INTO `property_device_type` (`device_type_id`, `device_type_name`) VALUES
	(1, 'Laptop'),
	(2, 'Ipad'),
	(3, 'Surface Book');

-- Exportiere Struktur von Tabelle ausleihe_v6.property_token_permissions
CREATE TABLE IF NOT EXISTS `property_token_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_text` varchar(50) NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permission_text` (`permission_text`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_token_permissions: ~11 rows (ungefähr)
INSERT INTO `property_token_permissions` (`permission_id`, `permission_text`) VALUES
	(2, 'add_csv'),
	(1, 'book'),
	(5, 'CRUD_device'),
	(7, 'CRUD_device_type'),
	(11, 'CRUD_prebook'),
	(9, 'CRUD_token'),
	(3, 'CRUD_user'),
	(4, 'CRUD_usercard'),
	(8, 'CRUD_usercard_type'),
	(6, 'CRUD_user_class'),
	(10, 'delete_event');

-- Exportiere Struktur von Tabelle ausleihe_v6.property_usercard_type
CREATE TABLE IF NOT EXISTS `property_usercard_type` (
  `usercard_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `usercard_type_name` text NOT NULL,
  PRIMARY KEY (`usercard_type_id`),
  UNIQUE KEY `usercard_type_name` (`usercard_type_name`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_usercard_type: ~1 rows (ungefähr)
INSERT INTO `property_usercard_type` (`usercard_type_id`, `usercard_type_name`) VALUES
	(1, 'RFID Chip');

-- Exportiere Struktur von Tabelle ausleihe_v6.token
CREATE TABLE IF NOT EXISTS `token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `token_username` text NOT NULL,
  `token_password` text NOT NULL,
  `token_last_change` timestamp NOT NULL DEFAULT current_timestamp(),
  `token_user_id` int(11) NOT NULL,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `token_user_id` (`token_user_id`),
  UNIQUE KEY `token_username` (`token_username`) USING HASH,
  CONSTRAINT `FK_token_user` FOREIGN KEY (`token_user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.token: ~4 rows (ungefähr)
INSERT INTO `token` (`token_id`, `token_username`, `token_password`, `token_last_change`, `token_user_id`) VALUES
	(1, 'booking_account', '$2a$12$EqOR4hUyJxmkYv.nX.sm9ua0RQRMPkx4gxSxEVGWYgPu4UWRGqX3a', '2022-08-26 22:00:00', 4),
	(7, 'test', '$2y$10$fHiWUDrcl.rbDfIpOy.mUekRc500Dg8ozNLk/j.eMh/qNCDamx9DG', '2023-08-24 14:48:29', 1),
	(8, 'abc', '$2y$10$dHuq.RwzF5KqDm4bq/oEu.Z/tvgnIbZ8mmjcBYvoUP7AIwkDVLLl6', '2023-08-23 19:03:31', 2),
	(12, 'admin', '$2y$10$vxY.4fe74l1v955Mq632n.6PPAzle23yImSX2mv9lbvyVkuhTsjES', '2023-08-24 16:12:41', 19);

-- Exportiere Struktur von Tabelle ausleihe_v6.token_link_permissions
CREATE TABLE IF NOT EXISTS `token_link_permissions` (
  `link_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_token_id` int(11) NOT NULL,
  `link_token_permission_id` int(11) NOT NULL,
  PRIMARY KEY (`link_permission_id`),
  UNIQUE KEY `UNIQUE` (`link_token_permission_id`,`link_token_id`) USING BTREE,
  KEY `token_constraint` (`link_token_id`),
  KEY `permission_constraint` (`link_token_permission_id`),
  CONSTRAINT `permission_constraint` FOREIGN KEY (`link_token_permission_id`) REFERENCES `property_token_permissions` (`permission_id`) ON UPDATE CASCADE,
  CONSTRAINT `token_constraint` FOREIGN KEY (`link_token_id`) REFERENCES `token` (`token_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.token_link_permissions: ~14 rows (ungefähr)
INSERT INTO `token_link_permissions` (`link_permission_id`, `link_token_id`, `link_token_permission_id`) VALUES
	(1, 1, 1),
	(52, 7, 1),
	(79, 8, 1),
	(53, 7, 2),
	(54, 7, 3),
	(55, 7, 4),
	(56, 7, 5),
	(57, 7, 6),
	(81, 7, 7),
	(82, 7, 8),
	(83, 7, 9),
	(84, 7, 10),
	(85, 7, 11),
	(80, 8, 11);

-- Exportiere Struktur von Tabelle ausleihe_v6.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_firstname` text NOT NULL,
  `user_lastname` text NOT NULL,
  `user_class` int(11) NOT NULL,
  `user_usercard_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_firstname_user_lastname` (`user_firstname`,`user_lastname`) USING HASH,
  KEY `class_constraint` (`user_class`),
  KEY `user_usercard_constraint` (`user_usercard_id`),
  CONSTRAINT `class_constraint` FOREIGN KEY (`user_class`) REFERENCES `property_class` (`class_id`) ON UPDATE CASCADE,
  CONSTRAINT `user_usercard_constraint` FOREIGN KEY (`user_usercard_id`) REFERENCES `usercard` (`usercard_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.user: ~68 rows (ungefähr)
INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_class`, `user_usercard_id`) VALUES
	(1, 'Test Vorname', 'Test Nachname', 1, 1),
	(2, 'Pia', 'Brüntrup', 1, 32),
	(4, 'Test', 'Test', 1, 37),
	(5, 'Emils', 'Fischerei', 1, 72),
	(6, 'Samio', 'Mohammed', 1, 73),
	(7, 'Nils', 'Cleven', 1, 75),
	(8, 'Alain', 'Regidor', 1, 77),
	(9, 'Alexander', 'Potrykus', 1, 33),
	(10, 'Alexandra', 'Wilson', 1, 78),
	(11, 'Anja', 'Woitalla', 1, 79),
	(12, 'Annabrit', 'Evert', 1, 81),
	(13, 'Anne', 'Möbert', 1, 80),
	(14, 'Antonella', 'Frisina', 1, NULL),
	(15, 'Beatrice', 'Asare-Lartey', 1, 129),
	(16, 'Betül', 'Aslan', 1, 82),
	(17, 'Britta', 'Letzner', 1, 83),
	(18, 'Christian', 'Laarz', 1, NULL),
	(19, 'Christian', 'Müller', 1, 130),
	(20, 'Christine', 'Velmede', 1, 84),
	(21, 'Eva', 'Voermanek', 1, 85),
	(22, 'Eva', 'Kreuzeder', 1, 86),
	(23, 'Felix', 'Bunzel', 1, 87),
	(24, 'Finja', 'Lucassen', 1, 88),
	(25, 'Florian', 'Baumert', 1, 89),
	(26, 'Helena', 'Zeller', 1, 125),
	(27, 'Inga', 'Sönnichsen', 1, 90),
	(28, 'Janne', 'Meyn', 1, 91),
	(29, 'Jasmin', 'Bolwin', 1, 92),
	(30, 'Joannis', 'Stassinopoulos', 1, 93),
	(31, 'Jörg', 'Neuwerth', 1, 94),
	(32, 'Jörn', 'Krönert', 1, 95),
	(33, 'Josephine', 'Reinefarth', 1, 96),
	(34, 'Julia', 'Hornbostel', 1, 126),
	(35, 'Juliane', 'Brunner', 1, 97),
	(36, 'Katharina', 'Weiland', 1, 127),
	(37, 'Katrin', 'Carstens', 1, 98),
	(38, 'Katrin', 'Zeng', 1, 99),
	(39, 'Kristin', 'Schulz', 1, 100),
	(40, 'Leonie Isabelle', 'Schüler', 1, 101),
	(41, 'Lina', 'Minners', 1, 102),
	(42, 'Lisa', 'Straßer', 1, 103),
	(43, 'Lynn', 'Kreipe', 1, 104),
	(44, 'Manuel', 'Bamming', 1, 105),
	(45, 'Marcel', 'Stemmler-Nemcic', 1, 106),
	(46, 'Mareike', 'Mönnich', 1, 107),
	(47, 'Maren', 'Dellbrügger', 1, 108),
	(48, 'Markus', 'Heimbach', 1, 109),
	(49, 'Maya Antonia', 'Paasch', 1, 110),
	(50, 'Mike', 'Korherr', 1, 111),
	(51, 'Miriam-Sarah', 'Nöldner', 1, 112),
	(52, 'Nils', 'Lenzen', 1, NULL),
	(53, 'Nino', 'Ehlers', 1, 127),
	(55, 'Rainer', 'Munck', 1, 113),
	(56, 'Rita', 'Wolf', 1, 114),
	(57, 'Sarah', 'Müller', 1, 115),
	(58, 'Sarah', 'Mirlacher', 1, 128),
	(59, 'Sascha', 'Haffer', 1, 116),
	(60, 'Simon', 'Löer', 1, 117),
	(61, 'Steffen', 'Kaminsky', 1, 118),
	(62, 'Susanne', 'Wagner', 1, 119),
	(63, 'Sven', 'Surup', 1, 120),
	(64, 'Svenja', 'Blum', 1, NULL),
	(65, 'Timo', 'Trunk', 1, 121),
	(66, 'Tobias', 'Drechsel', 1, 122),
	(67, 'VincentRouven', 'Dobrick', 1, 123),
	(68, 'Wiebke', 'Suchanek', 1, 124),
	(69, 'Ruben', 'Eversmeier', 1, 131),
	(71, 'Admin', 'Admin', 1, NULL);

-- Exportiere Struktur von Tabelle ausleihe_v6.usercard
CREATE TABLE IF NOT EXISTS `usercard` (
  `usercard_id` int(11) NOT NULL AUTO_INCREMENT,
  `usercard_type` int(11) NOT NULL,
  `usercard_uid` text NOT NULL,
  PRIMARY KEY (`usercard_id`),
  UNIQUE KEY `usercard_uid` (`usercard_uid`) USING HASH,
  KEY `usecard_type_constraint` (`usercard_type`),
  CONSTRAINT `usecard_type_constraint` FOREIGN KEY (`usercard_type`) REFERENCES `property_usercard_type` (`usercard_type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.usercard: ~62 rows (ungefähr)
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
	(130, 1, '884c8c'),
	(131, 1, '3e553b22');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
