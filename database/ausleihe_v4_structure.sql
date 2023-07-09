-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 15. Jun 2023 um 09:52
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_class`
--

CREATE TABLE `property_class` (
  `class_id` int(11) NOT NULL,
  `class_name` text NOT NULL,
  `multi_booking` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_device_type`
--

CREATE TABLE `property_device_type` (
  `device_type_id` int(11) NOT NULL,
  `device_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_token_permissions`
--

CREATE TABLE `property_token_permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_usercard_type`
--

CREATE TABLE `property_usercard_type` (
  `usercard_type_id` int(11) NOT NULL,
  `usercard_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `token_link_permissions`
--

CREATE TABLE `token_link_permissions` (
  `link_permission_id` int(11) NOT NULL,
  `link_token_id` int(11) NOT NULL,
  `link_token_permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `property_class`
--
ALTER TABLE `property_class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `property_device_type`
--
ALTER TABLE `property_device_type`
  MODIFY `device_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `property_token_permissions`
--
ALTER TABLE `property_token_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `property_usercard_type`
--
ALTER TABLE `property_usercard_type`
  MODIFY `usercard_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `token`
--
ALTER TABLE `token`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `token_link_permissions`
--
ALTER TABLE `token_link_permissions`
  MODIFY `link_permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `usercard`
--
ALTER TABLE `usercard`
  MODIFY `usercard_id` int(11) NOT NULL AUTO_INCREMENT;

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
