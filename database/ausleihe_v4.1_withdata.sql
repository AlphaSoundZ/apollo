-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 09. Mrz 2023 um 14:32
-- Server-Version: 10.4.27-MariaDB
-- PHP-Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ausleihe_v4.1`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `devices`
--

INSERT INTO `devices` (`device_id`, `device_type`, `device_uid`, `device_lend_user_id`) VALUES
(2, 3, '8846fce', 1),
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
(14, 3, '8846ace', 19),
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
(36, 3, '884bcca', 4),
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
(50, 3, '88472cb', 19),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `event`
--

INSERT INTO `event` (`event_id`, `event_user_id`, `event_device_id`, `event_begin`, `event_end`, `event_multi_booking_id`) VALUES
(1, 1, 2, '2023-03-08 17:49:04', NULL, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prebook`
--

CREATE TABLE `prebook` (
  `prebook_id` int(11) NOT NULL,
  `prebook_user_id` int(11) NOT NULL,
  `prebook_amount` int(11) NOT NULL,
  `prebook_begin` int(11) NOT NULL,
  `prebook_end` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_class`
--

CREATE TABLE `property_class` (
  `class_id` int(11) NOT NULL,
  `class_name` text NOT NULL,
  `multi_booking` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `property_class`
--

INSERT INTO `property_class` (`class_id`, `class_name`, `multi_booking`) VALUES
(1, 'Lehrer', 0),
(2, 'Schüler', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_device_type`
--

CREATE TABLE `property_device_type` (
  `device_type_id` int(11) NOT NULL,
  `device_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property_usercard_type`
--

CREATE TABLE `property_usercard_type` (
  `usercard_type_id` int(11) NOT NULL,
  `usercard_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `token_link_permissions`
--

CREATE TABLE `token_link_permissions` (
  `link_permission_id` int(11) NOT NULL,
  `link_token_id` int(11) NOT NULL,
  `link_token_permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `token_link_permissions`
--

INSERT INTO `token_link_permissions` (`link_permission_id`, `link_token_id`, `link_token_permission_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_class`, `user_token_id`, `user_usercard_id`) VALUES
(1, 'Test Vorname', 'Test Nachname', 1, 1, 1),
(2, 'Pia', 'Brüntrup', 1, 0, 32),
(4, 'Max', 'Heilmann', 1, 0, 37),
(5, 'Emils', 'Fischerei', 1, 0, 72),
(6, 'Samio', 'Mohammed', 1, 0, 73),
(7, 'Nils', 'Cleven', 1, 0, 75),
(8, 'Alain', 'Regidor', 1, 0, 77),
(9, 'Alexander', 'Potrykus', 1, 0, 33),
(10, 'Alexandra', 'Wilson', 1, 0, 78),
(11, 'Anja', 'Woitalla', 1, 0, 79),
(12, 'Annabrit', 'Evert', 1, 0, 81),
(13, 'Anne', 'Möbert', 1, 0, 80),
(14, 'Antonella', 'Frisina', 1, 0, 0),
(15, 'Beatrice', 'Asare-Lartey', 1, 0, 129),
(16, 'Betül', 'Aslan', 1, 0, 82),
(17, 'Britta', 'Letzner', 1, 0, 83),
(18, 'Christian', 'Laarz', 1, 0, 0),
(19, 'Christian', 'Müller', 1, 0, 130),
(20, 'Christine', 'Velmede', 1, 0, 84),
(21, 'Eva', 'Voermanek', 1, 0, 85),
(22, 'Eva', 'Kreuzeder', 1, 0, 86),
(23, 'Felix', 'Bunzel', 1, 0, 87),
(24, 'Finja', 'Lucassen', 1, 0, 88),
(25, 'Florian', 'Baumert', 1, 0, 89),
(26, 'Helena', 'Zeller', 1, 0, 125),
(27, 'Inga', 'Sönnichsen', 1, 0, 90),
(28, 'Janne', 'Meyn', 1, 0, 91),
(29, 'Jasmin', 'Bolwin', 1, 0, 92),
(30, 'Joannis', 'Stassinopoulos', 1, 0, 93),
(31, 'Jörg', 'Neuwerth', 1, 0, 94),
(32, 'Jörn', 'Krönert', 1, 0, 95),
(33, 'Josephine', 'Reinefarth', 1, 0, 96),
(34, 'Julia', 'Hornbostel', 1, 0, 126),
(35, 'Juliane', 'Brunner', 1, 0, 97),
(36, 'Katharina', 'Weiland', 1, 0, 127),
(37, 'Katrin', 'Carstens', 1, 0, 98),
(38, 'Katrin', 'Zeng', 1, 0, 99),
(39, 'Kristin', 'Schulz', 1, 0, 100),
(40, 'Leonie Isabelle', 'Schüler', 1, 0, 101),
(41, 'Lina', 'Minners', 1, 0, 102),
(42, 'Lisa', 'Straßer', 1, 0, 103),
(43, 'Lynn', 'Kreipe', 1, 0, 104),
(44, 'Manuel', 'Bamming', 1, 0, 105),
(45, 'Marcel', 'Stemmler-Nemcic', 1, 0, 106),
(46, 'Mareike', 'Mönnich', 1, 0, 107),
(47, 'Maren', 'Dellbrügger', 1, 0, 108),
(48, 'Markus', 'Heimbach', 1, 0, 109),
(49, 'Maya Antonia', 'Paasch', 1, 0, 110),
(50, 'Mike', 'Korherr', 1, 0, 111),
(51, 'Miriam-Sarah', 'Nöldner', 1, 0, 112),
(52, 'Nils', 'Lenzen', 1, 0, 0),
(53, 'Nino', 'Ehlers', 1, 0, 127),
(55, 'Rainer', 'Munck', 1, 0, 113),
(56, 'Rita', 'Wolf', 1, 0, 114),
(57, 'Sarah', 'Müller', 1, 0, 115),
(58, 'Sarah', 'Mirlacher', 1, 0, 128),
(59, 'Sascha', 'Haffer', 1, 0, 116),
(60, 'Simon', 'Löer', 1, 0, 117),
(61, 'Steffen', 'Kaminsky', 1, 0, 118),
(62, 'Susanne', 'Wagner', 1, 0, 119),
(63, 'Sven', 'Surup', 1, 0, 120),
(64, 'Svenja', 'Blum', 1, 0, 0),
(65, 'Timo', 'Trunk', 1, 0, 121),
(66, 'Tobias', 'Drechsel', 1, 0, 122),
(67, 'VincentRouven', 'Dobrick', 1, 0, 123),
(68, 'Wiebke', 'Suchanek', 1, 0, 124);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `usercard`
--

CREATE TABLE `usercard` (
  `usercard_id` int(11) NOT NULL,
  `usercard_type` int(11) NOT NULL,
  `usercard_uid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD PRIMARY KEY (`device_id`);

--
-- Indizes für die Tabelle `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);

--
-- Indizes für die Tabelle `prebook`
--
ALTER TABLE `prebook`
  ADD PRIMARY KEY (`prebook_id`);

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
  ADD PRIMARY KEY (`link_permission_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indizes für die Tabelle `usercard`
--
ALTER TABLE `usercard`
  ADD PRIMARY KEY (`usercard_id`);

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
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `prebook`
--
ALTER TABLE `prebook`
  MODIFY `prebook_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `property_usercard_type`
--
ALTER TABLE `property_usercard_type`
  MODIFY `usercard_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `token`
--
ALTER TABLE `token`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `token_link_permissions`
--
ALTER TABLE `token_link_permissions`
  MODIFY `link_permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT für Tabelle `usercard`
--
ALTER TABLE `usercard`
  MODIFY `usercard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
