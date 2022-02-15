-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 12. Feb 2022 um 20:17
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
-- Tabellenstruktur für Tabelle `eventtype`
--

CREATE TABLE `eventtype` (
  `event_type_id` int(11) NOT NULL,
  `name` text NOT NULL DEFAULT 'UNKNOWN',
  `description` text NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `eventtype`
--

INSERT INTO `eventtype` (`event_type_id`, `name`, `description`) VALUES
(1, 'RFID_READ', 'Ein Lesezugriff'),
(2, 'RFID_WRITE', 'Schreibzugriff auf RFID-Device');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klassen`
--

CREATE TABLE `klassen` (
  `id` int(11) NOT NULL,
  `klassen_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `klassen`
--

INSERT INTO `klassen` (`id`, `klassen_name`) VALUES
(1, 'Lehrer'),
(2, '5a'),
(3, '5b'),
(4, '5c'),
(5, '5d'),
(6, '9a'),
(7, '9b'),
(8, '9c'),
(9, '10b');

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
-- Tabellenstruktur für Tabelle `rfid_devices`
--

CREATE TABLE `rfid_devices` (
  `device_id` int(11) NOT NULL,
  `device_type` int(11) NOT NULL,
  `rfid_code` text NOT NULL,
  `lend_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `rfid_devices`
--

INSERT INTO `rfid_devices` (`device_id`, `device_type`, `rfid_code`, `lend_id`) VALUES
(1, 1, '6815.510528921625', 0),
(2, 2, '6406.586651137644', 0),
(3, 2, '6815.510528921111', 0),
(4, 2, '6815.123412341234', 0),
(5, 1, '6815.510528922222', 0),
(6, 3, '6815.123498761543', 0),
(7, 2, '0000.000000000000', 0),
(8, 2, '0000.000000000001', 0),
(9, 2, '0000.000000000002', 0),
(10, 2, '0000.000000000003', 0),
(11, 2, '0000.568845734567', 0),
(12, 2, '6406.586651137688', 0),
(13, 2, '6406.586651137699', 0),
(14, 2, '6406.580951137644', 0),
(15, 2, '6815.510528924059', 0),
(16, 2, '6815.510528922228', 0),
(17, 2, '6815.510528922229', 0),
(18, 2, '6406.586651135034', 0),
(19, 2, '6815.510528938813', 0),
(20, 2, '0001.000100010001', 0),
(21, 2, '6406.586651130195', 0),
(22, 2, '6406.574829386750', 0),
(23, 2, '1215.420691870073', 0),
(24, 1, 'a8fg419z', 0),
(26, 2, 'jklj', 0),
(27, 2, 'jkljlkj', 0),
(28, 2, 'jikljlö', 0),
(29, 2, 'hklhjklh', 0),
(30, 2, 'hkhkhj', 0),
(31, 2, 'jkjlkjljlkö', 0),
(32, 2, 'jklöj', 0),
(33, 2, '457476', 0),
(34, 2, 'sfg', 0),
(35, 2, 'jkhkljg20', 0),
(36, 2, 'jköhjölj', 0),
(37, 2, 'jkljlökjw423', 0),
(38, 2, 'Rfidcode123', 0),
(39, 2, '840932rjkfsdh', 0),
(40, 2, 'dfksfhsdk', 0),
(41, 2, 'dfkdh23', 0),
(42, 2, 'Hjkh1323409', 0),
(43, 2, 'hlhk', 0),
(44, 2, 'jköhk', 0),
(45, 2, 'Jkölhkjhklj', 0),
(46, 2, 'Going', 0),
(47, 2, 'testrfidcode', 0),
(48, 2, 'Bruhdatacode', 0),
(49, 2, 'Kp', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rfid_device_type`
--

CREATE TABLE `rfid_device_type` (
  `device_type_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `rfid_device_type`
--

INSERT INTO `rfid_device_type` (`device_type_id`, `name`) VALUES
(1, 'Ipad'),
(2, 'UserCard'),
(3, 'Surface Book');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rfid_event`
--

CREATE TABLE `rfid_event` (
  `id` int(11) NOT NULL,
  `event_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `rfid_event`
--

INSERT INTO `rfid_event` (`id`, `event_type_id`, `user_id`, `device_id`, `status`, `time_stamp`) VALUES
(90, 1, 2, 5, 1, '2021-12-12 11:44:36'),
(91, 1, 2, 5, 0, '2021-12-12 11:44:44'),
(92, 1, 2, 5, 1, '2021-12-12 22:34:18'),
(93, 1, 2, 5, 0, '2021-12-12 22:36:30'),
(94, 1, 2, 24, 1, '2021-12-12 22:37:03'),
(95, 1, 10, 5, 1, '2021-12-13 12:22:57'),
(96, 1, 10, 5, 0, '2021-12-13 12:23:13'),
(97, 1, 10, 5, 1, '2021-12-13 12:23:20'),
(98, 1, 1, 1, 1, '2022-02-10 14:14:50'),
(99, 1, 1, 1, 1, '2022-02-10 14:16:35'),
(100, 1, 1, 1, 1, '2022-02-10 14:17:43'),
(101, 1, 1, 1, 1, '2022-02-11 08:35:13'),
(102, 1, 1, 1, 1, '2022-02-11 08:35:50'),
(103, 1, 1, 1, 1, '2022-02-11 09:18:36'),
(104, 1, 1, 1, 1, '2022-02-11 09:22:34'),
(105, 1, 1, 1, 1, '2022-02-11 09:24:32'),
(106, 1, 1, 1, 1, '2022-02-11 09:52:36'),
(107, 1, 1, 1, 0, '2022-02-11 09:52:40'),
(108, 1, 1, 1, 1, '2022-02-11 09:52:48'),
(109, 1, 1, 1, 0, '2022-02-11 09:52:50'),
(110, 1, 1, 1, 1, '2022-02-11 10:49:54'),
(111, 1, 1, 1, 0, '2022-02-11 10:49:57'),
(112, 1, 1, 1, 1, '2022-02-11 10:50:12'),
(113, 1, 1, 1, 1, '2022-02-11 10:54:24'),
(114, 1, 1, 1, 0, '2022-02-11 10:54:25'),
(115, 1, 1, 1, 1, '2022-02-11 11:21:23'),
(116, 1, 1, 1, 0, '2022-02-11 11:21:24'),
(117, 1, 1, 1, 1, '2022-02-11 11:21:29'),
(118, 1, 1, 1, 0, '2022-02-11 11:27:55'),
(119, 1, 1, 1, 1, '2022-02-11 11:28:00'),
(120, 1, 1, 1, 0, '2022-02-11 11:28:04'),
(121, 1, 1, 1, 1, '2022-02-11 11:28:05'),
(122, 1, 1, 1, 0, '2022-02-11 11:28:07'),
(123, 1, 1, 1, 1, '2022-02-11 11:28:48'),
(124, 1, 1, 1, 0, '2022-02-11 11:28:59'),
(125, 1, 1, 1, 1, '2022-02-11 11:30:21'),
(126, 1, 1, 1, 0, '2022-02-11 11:31:15'),
(127, 1, 1, 1, 1, '2022-02-11 11:31:17'),
(128, 1, 1, 1, 0, '2022-02-11 11:31:21'),
(129, 1, 1, 1, 1, '2022-02-11 11:31:39'),
(130, 1, 1, 1, 0, '2022-02-11 11:31:40');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `vorname` text NOT NULL,
  `name` text NOT NULL,
  `klasse` int(11) NOT NULL,
  `rfid_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `vorname`, `name`, `klasse`, `rfid_code`) VALUES
(1, 'Paul', 'Peter', 9, 2),
(2, 'Henri', 'Frische', 8, 3),
(3, 'Paul 2', 'Pater', 7, 4),
(5, 'Test', 'Test 3', 7, NULL),
(6, 'test', 'Lehrer', 1, NULL),
(7, 'Maria', 'DB', 7, NULL),
(8, 'test', 'Lehrer2', 1, NULL),
(9, 'Maria2', 'DB2', 7, NULL),
(10, 'Jan', 'Holst', 9, 7),
(11, 'Emil', 'Test', 2, 10),
(12, 'Peter', 'Wedemann', 1, 11),
(13, 'Femil', 'Eischer', 1, 12),
(14, 'User1', 'User1', 1, 13),
(16, 'Test', 'lol', 6, NULL),
(18, 'Fritz', 'Fritzchen', 2, 14),
(19, 'User1', 'Nachname1', 7, 15),
(20, 'Lukas', 'Peter', 5, 16),
(21, 'Lukasso', 'Peta', 5, 17),
(22, 'A', 'B', 1, 18),
(23, 'Leonoard', 'Westfalewski', 8, 19),
(24, 'Fred', 'Peter', 5, 20),
(25, 'Person100', 'Lastnameforchristmas', 1, 21),
(26, 'Testacc2', 'NachnameTestacc2', 9, 22),
(27, 'Frederick', 'Herbert', 2, 23),
(28, 'Jl', 'Jlk', 1, NULL),
(29, 'Jlk', 'Jjkljkl', 1, NULL),
(30, 'Lkjklj', 'Jkljl', 3, NULL),
(31, 'Jlkjö', 'Jklöjlö', 2, NULL),
(32, 'Jlk', 'Jlkjlk', 3, 29),
(33, 'Jlöj', 'Kjlöj', 3, 30),
(34, 'Jkljö', 'Jkljlö', 4, 31),
(35, 'Jklöj', 'Jlökjl', 3, 32),
(36, 'Jlkö', 'Kjlö', 2, 26),
(37, 'Izuioz', 'Ziuoz', 2, 33),
(38, 'Jklöjklö', 'Jlökjlk', 2, 34),
(39, 'Jklö', 'Jlökjlk', 3, 35),
(40, 'Lkjölj', 'Ljklö', 3, 36),
(41, 'Jlköjl', 'Öjlkjlöj', 3, 37),
(42, 'TestUser', 'TestUserNachname', 1, 38),
(43, 'TestAcc', 'TestAcc', 4, 39),
(44, 'Test', 'Test', 3, 40),
(45, 'Person 1', 'Person 1', 3, 41),
(46, 'Bla', 'BlaBla', 3, 42),
(47, 'Jljlö', 'Jlköjklkj', 3, 43),
(48, 'Jlökj', 'Jlk', 3, 44),
(49, 'Jöklj', 'Ljklöjö', 3, 45),
(50, 'Test', 'Okokkoö', 3, NULL),
(51, 'Jlökj', 'Jkljl', 2, NULL),
(52, 'E', 'J', 3, NULL),
(53, 'J', 'P', 2, NULL),
(54, 'Ok', 'Lets', 2, NULL),
(55, 'Jklh', 'Jkhklhjkl', 1, NULL),
(56, '9090', '9090', 4, NULL),
(57, 'Jkl809', 'Jkl8080', 1, NULL),
(58, '68768', '68768', 1, NULL),
(59, '808', '09890', 1, NULL),
(60, '98i0po', '890ipo', 1, NULL),
(61, 'Ok', 'Letsse', 1, NULL),
(62, 'Oke', 'Letse', 1, 46),
(68, 'Paull bruh 420', 'Peter', 2, 47),
(69, 'Bruh', 'Bruh', 2, 48),
(70, 'Test 69', 'Test nachname', 2, 49);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `eventtype`
--
ALTER TABLE `eventtype`
  ADD PRIMARY KEY (`event_type_id`);

--
-- Indizes für die Tabelle `klassen`
--
ALTER TABLE `klassen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rfid_devices`
--
ALTER TABLE `rfid_devices`
  ADD PRIMARY KEY (`device_id`),
  ADD KEY `device_type` (`device_type`);

--
-- Indizes für die Tabelle `rfid_device_type`
--
ALTER TABLE `rfid_device_type`
  ADD PRIMARY KEY (`device_type_id`);

--
-- Indizes für die Tabelle `rfid_event`
--
ALTER TABLE `rfid_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_type_id` (`event_type_id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `klasse` (`klasse`),
  ADD KEY `rfid_user_index` (`rfid_code`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `eventtype`
--
ALTER TABLE `eventtype`
  MODIFY `event_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `klassen`
--
ALTER TABLE `klassen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `rfid_devices`
--
ALTER TABLE `rfid_devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT für Tabelle `rfid_device_type`
--
ALTER TABLE `rfid_device_type`
  MODIFY `device_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `rfid_event`
--
ALTER TABLE `rfid_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `rfid_devices`
--
ALTER TABLE `rfid_devices`
  ADD CONSTRAINT `rfid_devices_ibfk_1` FOREIGN KEY (`device_type`) REFERENCES `rfid_device_type` (`device_type_id`);

--
-- Constraints der Tabelle `rfid_event`
--
ALTER TABLE `rfid_event`
  ADD CONSTRAINT `rfid_event_ibfk_1` FOREIGN KEY (`event_type_id`) REFERENCES `eventtype` (`event_type_id`),
  ADD CONSTRAINT `rfid_event_ibfk_2` FOREIGN KEY (`device_id`) REFERENCES `rfid_devices` (`device_id`);

--
-- Constraints der Tabelle `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`klasse`) REFERENCES `klassen` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`rfid_code`) REFERENCES `rfid_devices` (`device_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
