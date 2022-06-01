-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 23. Mai 2022 um 20:59
-- Server-Version: 10.4.20-MariaDB
-- PHP-Version: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ausleihe`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `event_user_id` int(11) NOT NULL,
  `event_device_id` int(11) NOT NULL,
  `event_begin` timestamp NULL DEFAULT current_timestamp(),
  `event_end` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `event`
--

INSERT INTO `event` (`event_id`, `event_user_id`, `event_device_id`, `event_begin`, `event_end`) VALUES
(33, 12, 5, '2022-03-02 21:51:04', '2022-03-02 21:51:04'),
(34, 12, 5, '2022-03-02 22:26:03', '2022-03-02 22:26:03'),
(35, 12, 5, '2022-03-02 22:26:08', '2022-03-02 22:26:08'),
(36, 12, 5, '2022-03-02 22:54:16', '2022-03-02 23:02:52');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_class`
--

CREATE TABLE `property_class` (
  `class_id` int(11) NOT NULL,
  `class_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `property_class`
--

INSERT INTO `property_class` (`class_id`, `class_name`) VALUES
(1, 'Lehrer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `login`
--

CREATE TABLE `login` (
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `login`
--

INSERT INTO `login` (`username`, `password`) VALUES
('admin', 'abc12345');

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
(1, 1, '123', NULL);

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
(1, 'Ipad'),
(2, 'UserCard'),
(3, 'Surface Book'),
(4, 'Laptop');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_firstname` text NOT NULL,
  `user_lastname` text NOT NULL,
  `user_class` int(11) NOT NULL,
  `user_usercard_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_class`, `user_usercard_id`) VALUES
(1, 'user1', 'user11', 9, NULL),
(2, 'user2', 'user22', 9, NULL),
(3, 'user3', 'user33', 9, NULL),
(4, 'user4', 'user44', 9, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);

--
-- Indizes für die Tabelle `property_class`
--
ALTER TABLE `property_class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indizes für die Tabelle `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_id`);

--
-- Indizes für die Tabelle `property_device_type`
--
ALTER TABLE `property_device_type`
  ADD PRIMARY KEY (`device_type_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT für Tabelle `property_class`
--
ALTER TABLE `property_class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT für Tabelle `property_device_type`
--
ALTER TABLE `property_device_type`
  MODIFY `device_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Constraints der exportierten Tabellen
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
