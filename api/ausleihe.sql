-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Jun 2022 um 07:45
-- Server-Version: 10.4.24-MariaDB
-- PHP-Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Datenbank: `ausleihe`
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
(1, 2, '123', NULL),
(63, 1, '456', 0),
(64, 1, '789', 0),
(65, 3, '321', 0),
(66, 3, '654', 0),
(67, 2, '1234', NULL),
(68, 2, '12345', NULL);

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
(1, 1, 1, '2022-05-29 10:33:15', '2022-05-29 10:33:15');

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
('21232f297a57a5a743894a0e4a801fc3', 'd6b0ab7f1c8ab8f514db9a6d85de160a');

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
(1, 'add_device'),
(2, 'add_user'),
(3, 'search'),
(4, 'login');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `token`
--

CREATE TABLE `token` (
  `token_id` int(11) NOT NULL,
  `token_hash` text NOT NULL,
  `token_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`token_permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `token`
--

INSERT INTO `token` (`token_id`, `token_hash`, `token_permissions`) VALUES
(1, 'dd114d62493532d3a5615550796229a8', '[1, 2, 3, 4]');

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
(1, 'user1', 'user11', 1, 1),
(2, 'user2', 'user22', 1, NULL),
(3, 'user3', 'user33', 1, NULL),
(4, 'user4', 'user44', 1, NULL),
(80, 'Test', 'Test', 1, 0),
(81, 'Test494', 'Test484', 1, 67),
(82, 'Test4904', 'Test4804', 1, 68);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_id`);

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
-- Indizes für die Tabelle `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`token_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT für Tabelle `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `property_class`
--
ALTER TABLE `property_class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `property_device_type`
--
ALTER TABLE `property_device_type`
  MODIFY `device_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `property_token_permissions`
--
ALTER TABLE `property_token_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `token`
--
ALTER TABLE `token`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;
COMMIT;
