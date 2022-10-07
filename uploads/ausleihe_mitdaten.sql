-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.20-MariaDB - mariadb.org binary distribution
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


-- Exportiere Datenbank Struktur für ausleihe
DROP DATABASE IF EXISTS `ausleihe`;
CREATE DATABASE IF NOT EXISTS `ausleihe` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `ausleihe`;

-- Exportiere Struktur von Tabelle ausleihe.devices
DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type` int(11) NOT NULL,
  `device_uid` text NOT NULL,
  `device_lend_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.devices: ~130 rows (ungefähr)
DELETE FROM `devices`;
INSERT INTO `devices` (`device_id`, `device_type`, `device_uid`, `device_lend_user_id`) VALUES
	(1, 1, '2e9ab22', 0),
	(2, 3, '8846fce', 0),
	(3, 3, '884d6cc', 0),
	(4, 3, '8849ec9', 0),
	(5, 3, '884d5cc', 0),
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
	(32, 1, '884eea2', 0),
	(33, 1, '884a5f4', 0),
	(34, 3, '884d9cc', 0),
	(35, 3, '884bdca', 0),
	(36, 3, '884bcca', 0),
	(37, 1, 'eed5d22', 0),
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
	(72, 1, 'a2937623', 0),
	(73, 1, 'aea6822', 0),
	(74, 3, '8844cb', 0),
	(75, 1, 'b9c4651', 0),
	(76, 3, '8843cb', 0),
	(77, 1, '884b9e9', 0),
	(78, 1, '88497c3', 0),
	(79, 1, '884b4ab', 0),
	(80, 1, '8842ce6', 0),
	(81, 1, '88482b7', 0),
	(82, 1, '884428d', 0),
	(83, 1, '8846e9', 0),
	(84, 1, '8846b93', 0),
	(85, 1, '88477ea', 0),
	(86, 1, '8843e6', 0),
	(87, 1, '88435d8', 0),
	(88, 1, '8848c8d', 0),
	(89, 1, '8847ff4', 0),
	(90, 1, '88479f4', 0),
	(91, 1, '884b6ab', 0),
	(92, 1, '88442e7', 0),
	(93, 1, '884e98a', 0),
	(94, 1, '8844bea', 0),
	(95, 1, '8846fb7', 0),
	(96, 1, '88456ea', 0),
	(97, 1, '8847d8', 0),
	(98, 1, '8842283', 0),
	(99, 1, '8841d85', 0),
	(100, 1, '884885', 0),
	(101, 1, '884e6e9', 0),
	(102, 1, '884aa9', 0),
	(103, 1, '8846d8', 0),
	(104, 1, '8848e9f', 0),
	(105, 1, '884498b', 0),
	(106, 1, '884c7a1', 0),
	(107, 1, '8845c8d', 0),
	(108, 1, '884a2e9', 0),
	(109, 1, '884ead8', 0),
	(110, 1, '8842be6', 0),
	(111, 1, '884f8d8', 0),
	(112, 1, '884d8d8', 0),
	(113, 1, '8841dd8', 0),
	(114, 1, '88457e7', 0),
	(115, 1, '8844af5', 0),
	(116, 1, '88499f4', 0),
	(117, 1, '88433e6', 0),
	(118, 1, '8842de7', 0),
	(119, 1, '88472c3', 0),
	(120, 1, '884e9e6', 0),
	(121, 1, '8841fca', 0),
	(122, 1, '8848787', 0),
	(123, 1, '884ca8c', 0),
	(124, 1, '884b1c', 0),
	(125, 1, '88483e1', 0),
	(126, 1, '88424f5', 0),
	(127, 1, '884b5c', 0),
	(128, 1, '8845ce7', 0),
	(129, 1, '9a34701a', 0),
	(130, 1, '884c8c', NULL);

-- Exportiere Struktur von Tabelle ausleihe.event
DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_user_id` int(11) NOT NULL,
  `event_device_id` int(11) NOT NULL,
  `event_begin` timestamp NULL DEFAULT current_timestamp(),
  `event_end` timestamp NULL DEFAULT current_timestamp(),
  `event_multi_booking_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.event: ~0 rows (ungefähr)
DELETE FROM `event`;

-- Exportiere Struktur von Tabelle ausleihe.prebook
DROP TABLE IF EXISTS `prebook`;
CREATE TABLE IF NOT EXISTS `prebook` (
  `prebook_id` int(11) NOT NULL AUTO_INCREMENT,
  `prebook_user_id` int(11) NOT NULL,
  `prebook_amount` int(11) NOT NULL,
  `prebook_begin` int(11) NOT NULL,
  `prebook_end` int(11) NOT NULL,
  PRIMARY KEY (`prebook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.prebook: ~0 rows (ungefähr)
DELETE FROM `prebook`;

-- Exportiere Struktur von Tabelle ausleihe.property_class
DROP TABLE IF EXISTS `property_class`;
CREATE TABLE IF NOT EXISTS `property_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` text NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.property_class: ~1 rows (ungefähr)
DELETE FROM `property_class`;
INSERT INTO `property_class` (`class_id`, `class_name`) VALUES
	(1, 'Lehrer');

-- Exportiere Struktur von Tabelle ausleihe.property_device_type
DROP TABLE IF EXISTS `property_device_type`;
CREATE TABLE IF NOT EXISTS `property_device_type` (
  `device_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type_name` text NOT NULL,
  PRIMARY KEY (`device_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.property_device_type: ~4 rows (ungefähr)
DELETE FROM `property_device_type`;
INSERT INTO `property_device_type` (`device_type_id`, `device_type_name`) VALUES
	(1, 'Usercard'),
	(2, 'Ipad'),
	(3, 'Surface Book'),
	(4, 'Laptop');

-- Exportiere Struktur von Tabelle ausleihe.property_token_permissions
DROP TABLE IF EXISTS `property_token_permissions`;
CREATE TABLE IF NOT EXISTS `property_token_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_text` text NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.property_token_permissions: ~10 rows (ungefähr)
DELETE FROM `property_token_permissions`;
INSERT INTO `property_token_permissions` (`permission_id`, `permission_text`) VALUES
	(1, 'add_device'),
	(2, 'add_user'),
	(3, 'search'),
	(4, 'login'),
	(5, 'reset'),
	(6, 'prebook'),
	(7, 'book'),
	(8, 'update'),
	(9, 'delete'),
	(10, 'add_csv');

-- Exportiere Struktur von Tabelle ausleihe.token
DROP TABLE IF EXISTS `token`;
CREATE TABLE IF NOT EXISTS `token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `token_username` text NOT NULL,
  `token_password` text NOT NULL,
  `token_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`token_permissions`)),
  `token_last_change` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.token: ~2 rows (ungefähr)
DELETE FROM `token`;
INSERT INTO `token` (`token_id`, `token_username`, `token_password`, `token_permissions`, `token_last_change`) VALUES
	(1, 'test_usr', '$2a$12$aDx9uuWfG5.GkV5gw132vOznCy1xEWDU7PYF9i886jZHz0j2LNoIu', '[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]', '2022-08-26 22:00:00'),
	(2, 'booking_account', '$2a$12$.is8OnwyAMIRvQ.jxTki3ubntBpOk9LCnbOdyWI7eaWw2bYNXDFsC', ' [7]', '2022-08-26 22:00:00');

-- Exportiere Struktur von Tabelle ausleihe.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_firstname` text NOT NULL,
  `user_lastname` text NOT NULL,
  `user_class` int(11) NOT NULL,
  `user_token_id` int(11) DEFAULT NULL,
  `user_usercard_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle ausleihe.user: ~67 rows (ungefähr)
DELETE FROM `user`;
INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_class`, `user_token_id`, `user_usercard_id`) VALUES
	(1, 'Test Vorname', 'Test Nachname', 1, 1, 1),
	(2, 'Pia', 'Brüntrup', 1, NULL, 32),
	(3, 'Test', 'Test', 1, NULL, NULL),
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

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
