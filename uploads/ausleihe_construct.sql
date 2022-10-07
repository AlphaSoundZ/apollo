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

-- Daten Export vom Benutzer nicht ausgewählt

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle ausleihe.prebook
DROP TABLE IF EXISTS `prebook`;
CREATE TABLE IF NOT EXISTS `prebook` (
  `prebook_id` int(11) NOT NULL AUTO_INCREMENT,
  `prebook_user_id` int(11) NOT NULL,
  `prebook_amount` int(11) NOT NULL,
  `prebook_begin` int(11) NOT NULL,
  `prebook_end` int(11) NOT NULL,
  PRIMARY KEY (`prebook_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle ausleihe.property_class
DROP TABLE IF EXISTS `property_class`;
CREATE TABLE IF NOT EXISTS `property_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` text NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle ausleihe.property_device_type
DROP TABLE IF EXISTS `property_device_type`;
CREATE TABLE IF NOT EXISTS `property_device_type` (
  `device_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_type_name` text NOT NULL,
  PRIMARY KEY (`device_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle ausleihe.property_token_permissions
DROP TABLE IF EXISTS `property_token_permissions`;
CREATE TABLE IF NOT EXISTS `property_token_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_text` text NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

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

-- Daten Export vom Benutzer nicht ausgewählt

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

-- Daten Export vom Benutzer nicht ausgewählt

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
