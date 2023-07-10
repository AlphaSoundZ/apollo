-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.27-MariaDB - mariadb.org binary distribution
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
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.devices: ~69 rows (ungefähr)
INSERT INTO `devices` (`device_id`, `device_type`, `device_uid`, `device_lend_user_id`) VALUES
	(2, 3, '8846fce', NULL),
	(3, 3, '884d6cc', NULL),
	(4, 3, '8849ec9', NULL),
	(5, 3, '884d5cc', NULL),
	(6, 3, '884bfc9', 0),
	(7, 3, '884c4c9', 0),
	(8, 3, '884c2c9', 0),
	(9, 3, '884bac9', 0),
	(10, 3, '884c5ca', 0),
	(11, 3, '88472ce', 0),
	(12, 3, '88473ce', 0),
	(13, 3, '88459cd', 0),
	(14, 3, '8846ace', 0),
	(15, 3, '884bec9', 0),
	(16, 3, '8846ece', 0),
	(17, 3, '884c3c9', 0),
	(18, 3, '884c0ca', 0),
	(19, 3, '884b9c9', 0),
	(20, 3, '8846bce', 0),
	(21, 3, '88496c9', 0),
	(22, 3, '884c1ca', 0),
	(23, 3, '884b2ca', 0),
	(24, 3, '8849ac9', 0),
	(25, 3, '884b1ca', 0),
	(26, 3, '88499c9', 0),
	(27, 3, '884b6ca', 0),
	(28, 3, '884b5ca', 0),
	(29, 3, '88495c9', 0),
	(30, 3, '884b9ca', 0),
	(31, 3, '884baca', 0),
	(34, 3, '884d9cc', 0),
	(35, 3, '884bdca', 0),
	(36, 3, '884bcca', 0),
	(38, 3, '88484cd', 0),
	(39, 3, '8847fcd', 0),
	(40, 3, '8847ecb', 0),
	(41, 3, '8847bcb', 0),
	(42, 3, '8847acb', 0),
	(43, 3, '88476cd', 0),
	(44, 3, '88472cd', 0),
	(45, 3, '88471cd', 0),
	(46, 3, '8846ecd', 0),
	(47, 3, '8846dcd', 0),
	(48, 3, '8846bcb', 0),
	(49, 3, '8846acb', 0),
	(50, 3, '88472cb', 0),
	(51, 3, '8846ecb', 0),
	(52, 3, '8846fcb', 0),
	(53, 3, '884ddcc', 0),
	(54, 3, '88445cc', 0),
	(55, 3, '88473cb', 0),
	(56, 3, '8848cb', 0),
	(57, 3, '884dcb', 0),
	(58, 3, '884ebca', 0),
	(59, 3, '884eaca', 0),
	(60, 3, '884efca', 0),
	(61, 3, '884eeca', 0),
	(62, 3, '884f3ca', 0),
	(63, 3, '88475cd', 0),
	(64, 3, '884f2ca', 0),
	(65, 3, '8847ccc', 0),
	(66, 3, '8847ecd', 0),
	(67, 3, '88483cd', 0),
	(68, 3, '88487cd', 0),
	(69, 3, '88488cd', 0),
	(70, 3, '8847bcc', 0),
	(71, 3, '8842ecc', 0),
	(74, 3, '8844cb', 0),
	(76, 3, '8843cb', 0);

-- Exportiere Struktur von Tabelle ausleihe_v6.event
CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_user_id` int(11) NOT NULL,
  `event_device_id` int(11) NOT NULL,
  `event_begin` timestamp NULL DEFAULT current_timestamp(),
  `event_end` timestamp NULL DEFAULT current_timestamp(),
  `event_multi_booking_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.event: ~11 rows (ungefähr)
INSERT INTO `event` (`event_id`, `event_user_id`, `event_device_id`, `event_begin`, `event_end`, `event_multi_booking_id`) VALUES
	(1, 1, 2, '2023-03-08 17:49:04', '2023-06-10 15:43:25', 1),
	(2, 1, 3, '2023-06-10 15:43:18', '2023-06-10 15:43:37', 1),
	(3, 1, 3, '2023-06-10 15:43:43', '2023-06-10 15:43:51', 2),
	(6, 1, 3, '2023-07-08 16:50:21', '2023-07-08 16:50:26', 3),
	(7, 1, 3, '2023-07-08 21:37:16', '2023-07-08 21:37:50', 4),
	(8, 1, 2, '2023-07-08 21:43:29', '2023-07-08 21:44:44', 5),
	(9, 1, 3, '2023-07-08 21:43:34', '2023-07-08 21:44:13', 5),
	(10, 1, 4, '2023-07-08 21:43:43', '2023-07-08 21:44:51', 5),
	(11, 1, 5, '2023-07-08 21:44:35', '2023-07-09 10:47:40', 5),
	(12, 1, 4, '2023-07-09 10:46:13', '2023-07-09 10:47:06', 5),
	(13, 1, 5, '2023-07-09 10:48:04', '2023-07-09 10:48:11', 6);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.prebook: ~3 rows (ungefähr)
INSERT INTO `prebook` (`prebook_id`, `prebook_user_id`, `prebook_amount`, `prebook_begin`, `prebook_end`) VALUES
	(2, 5, 39, '2023-07-09 21:15:37', '2023-07-09 22:59:37'),
	(3, 1, 30, '2023-07-12 08:00:00', '2023-07-12 08:30:00'),
	(5, 1, 30, '2023-07-14 08:00:00', '2023-07-14 08:30:00');

-- Exportiere Struktur von Tabelle ausleihe_v6.property_class
CREATE TABLE IF NOT EXISTS `property_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` text NOT NULL,
  `multi_booking` tinyint(1) NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_class: ~2 rows (ungefähr)
INSERT INTO `property_class` (`class_id`, `class_name`, `multi_booking`) VALUES
	(1, 'Lehrer', 1),
	(2, 'Schüler', 0);

-- Exportiere Struktur von Tabelle ausleihe_v6.property_device_type
CREATE TABLE IF NOT EXISTS `property_device_type` (
  `device_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type_name` text NOT NULL,
  PRIMARY KEY (`device_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_device_type: ~3 rows (ungefähr)
INSERT INTO `property_device_type` (`device_type_id`, `device_type_name`) VALUES
	(1, 'Laptop'),
	(2, 'Ipad'),
	(3, 'Surface Book');

-- Exportiere Struktur von Tabelle ausleihe_v6.property_token_permissions
CREATE TABLE IF NOT EXISTS `property_token_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_text` text NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.property_token_permissions: ~2 rows (ungefähr)
INSERT INTO `property_token_permissions` (`permission_id`, `permission_text`) VALUES
	(1, 'booking'),
	(2, 'prebook');

-- Exportiere Struktur von Tabelle ausleihe_v6.property_usercard_type
CREATE TABLE IF NOT EXISTS `property_usercard_type` (
  `usercard_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `usercard_type_name` text NOT NULL,
  PRIMARY KEY (`usercard_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  CONSTRAINT `FK_token_user` FOREIGN KEY (`token_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.token: ~2 rows (ungefähr)
INSERT INTO `token` (`token_id`, `token_username`, `token_password`, `token_last_change`, `token_user_id`) VALUES
	(5, 'abc', '$2y$10$r60cZt7ZsiVp8tV0v8l1Y.u88qOP90ATT1Df0Q8bpu7srrDEkFqXW', '2023-07-10 21:09:55', 1),
	(9, 'abcd', '$2y$10$OFdGEilQpMugPtPUKZhzS.v.KzyBo7Bpr/a8kuDBhAygMopchm5Lq', '2023-07-10 21:11:12', 2);

-- Exportiere Struktur von Tabelle ausleihe_v6.token_link_permissions
CREATE TABLE IF NOT EXISTS `token_link_permissions` (
  `link_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_token_id` int(11) NOT NULL,
  `link_token_permission_id` int(11) NOT NULL,
  PRIMARY KEY (`link_permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.token_link_permissions: ~6 rows (ungefähr)
INSERT INTO `token_link_permissions` (`link_permission_id`, `link_token_id`, `link_token_permission_id`) VALUES
	(4, 2, 1),
	(5, 2, 2),
	(6, 5, 1),
	(7, 5, 2),
	(8, 9, 1),
	(9, 9, 2);

-- Exportiere Struktur von Tabelle ausleihe_v6.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_firstname` text NOT NULL,
  `user_lastname` text NOT NULL,
  `user_class` int(11) NOT NULL,
  `user_usercard_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.user: ~67 rows (ungefähr)
INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_class`, `user_usercard_id`) VALUES
	(1, 'Test Vorname', 'Test Nachname', 1, 1),
	(2, 'Pia', 'Brüntrup', 1, 32),
	(4, 'Max', 'Heilmann', 1, 37),
	(5, 'Emils', 'Fischerei', 1, 72),
	(6, 'Samio', 'Mohammed', 1, 73),
	(7, 'Nils', 'Cleven', 1, 75),
	(8, 'Alain', 'Regidor', 1, 77),
	(9, 'Alexander', 'Potrykus', 1, 33),
	(10, 'Alexandra', 'Wilson', 1, 78),
	(11, 'Anja', 'Woitalla', 1, 79),
	(12, 'Annabrit', 'Evert', 1, 81),
	(13, 'Anne', 'Möbert', 1, 80),
	(14, 'Antonella', 'Frisina', 1, 0),
	(15, 'Beatrice', 'Asare-Lartey', 1, 129),
	(16, 'Betül', 'Aslan', 1, 82),
	(17, 'Britta', 'Letzner', 1, 83),
	(18, 'Christian', 'Laarz', 1, 0),
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
	(52, 'Nils', 'Lenzen', 1, 0),
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
	(64, 'Svenja', 'Blum', 1, 0),
	(65, 'Timo', 'Trunk', 1, 121),
	(66, 'Tobias', 'Drechsel', 1, 122),
	(67, 'VincentRouven', 'Dobrick', 1, 123),
	(68, 'Wiebke', 'Suchanek', 1, 124),
	(74, 'jj', 'holst', 1, NULL);

-- Exportiere Struktur von Tabelle ausleihe_v6.usercard
CREATE TABLE IF NOT EXISTS `usercard` (
  `usercard_id` int(11) NOT NULL AUTO_INCREMENT,
  `usercard_type` int(11) NOT NULL,
  `usercard_uid` text NOT NULL,
  PRIMARY KEY (`usercard_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle ausleihe_v6.usercard: ~61 rows (ungefähr)
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

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
