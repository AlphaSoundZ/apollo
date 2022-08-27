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
(1, 2, '2e9ab22', 0),
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
(32, 2, '884eea2', 0),
(33, 2, '884a5f4', 0),
(34, 3, '884d9cc', 0),
(35, 3, '884bdca', 0),
(36, 3, '884bcca', 0),
(37, 2, 'eed5d22', 0),
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
(72, 2, 'a2937623', 0),
(73, 2, 'aea6822', 0),
(74, 3, '8844cb', 0),
(75, 2, 'b9c4651', 0),
(76, 3, '8843cb', 0),
(77, 2, '884b9e9', 0),
(78, 2, '88497c3', 0),
(79, 2, '884b4ab', 0),
(80, 2, '8842ce6', 0),
(81, 2, '88482b7', 0),
(82, 2, '884428d', 0),
(83, 2, '8846e9', 0),
(84, 2, '8846b93', 0),
(85, 2, '88477ea', 0),
(86, 2, '8843e6', 0),
(87, 2, '88435d8', 0),
(88, 2, '8848c8d', 0),
(89, 2, '8847ff4', 0),
(90, 2, '88479f4', 0),
(91, 2, '884b6ab', 0),
(92, 2, '88442e7', 0),
(93, 2, '884e98a', 0),
(94, 2, '8844bea', 0),
(95, 2, '8846fb7', 0),
(96, 2, '88456ea', 0),
(97, 2, '8847d8', 0),
(98, 2, '8842283', 0),
(99, 2, '8841d85', 0),
(100, 2, '884885', 0),
(101, 2, '884e6e9', 0),
(102, 2, '884aa9', 0),
(103, 2, '8846d8', 0),
(104, 2, '8848e9f', 0),
(105, 2, '884498b', 0),
(106, 2, '884c7a1', 0),
(107, 2, '8845c8d', 0),
(108, 2, '884a2e9', 0),
(109, 2, '884ead8', 0),
(110, 2, '8842be6', 0),
(111, 2, '884f8d8', 0),
(112, 2, '884d8d8', 0),
(113, 2, '8841dd8', 0),
(114, 2, '88457e7', 0),
(115, 2, '8844af5', 0),
(116, 2, '88499f4', 0),
(117, 2, '88433e6', 0),
(118, 2, '8842de7', 0),
(119, 2, '88472c3', 0),
(120, 2, '884e9e6', 0),
(121, 2, '8841fca', 0),
(122, 2, '8848787', 0),
(123, 2, '884ca8c', 0),
(124, 2, '884b1c', 0),
(125, 2, '88483e1', 0),
(126, 2, '88424f5', 0),
(127, 2, '884b5c', 0),
(128, 2, '8845ce7', 0),
(129, 2, '9a34701a', 0),
(130, 2, '884c8c', NULL);

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
(1, 'Jan Jacob', 'Holst', 1, 1),
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
(68, 'Wiebke', 'Suchanek', 1, 124);

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
