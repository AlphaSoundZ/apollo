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
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `device` int(11) NOT NULL,
  `begin` timestamp NULL DEFAULT current_timestamp(),
  `end` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `event`
--

INSERT INTO `event` (`id`, `user`, `device`, `begin`, `end`) VALUES
(33, 12, 5, '2022-03-02 21:51:04', '2022-03-02 21:51:04'),
(34, 12, 5, '2022-03-02 22:26:03', '2022-03-02 22:26:03'),
(35, 12, 5, '2022-03-02 22:26:08', '2022-03-02 22:26:08'),
(36, 12, 5, '2022-03-02 22:54:16', '2022-03-02 23:02:52'),
(37, 12, 24, '2022-03-02 23:03:21', NULL),
(39, 12, 24, '2022-03-02 23:04:22', NULL),
(40, 13, 1, '2022-03-02 23:05:23', NULL),
(41, 12, 5, '2022-03-02 23:06:25', NULL);

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
(1, 1, '6815.510528921625', 11),
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
(49, 2, 'Kp', 0),
(50, 2, '3842057489andas', 0),
(51, 2, '', 0),
(52, 2, '43211234', 0),
(53, 2, '432112341', 0),
(54, 2, '432112342', 0),
(55, 2, '1234567876543', 0),
(56, 2, '0000.0000123000001', 0),
(57, 3, '0000.00001230000012', 0),
(58, 3, '0000.000012300000122', 0),
(59, 3, '0000.0000112300000122', 0),
(60, 3, '0000.0000112300000132', 0),
(61, 3, '0000000', 0),
(62, 3, '884b5c', 0);

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
(3, 'Surface Book'),
(4, 'Laptop');

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
(70, 'Test 69', 'Test nachname', 2, 49),
(71, 'TestVorname', 'Lollllll', 4, 50),
(72, 'TestVorkname', 'Lojllllll', 4, 51),
(73, '1Test', '1Test', 7, 52),
(74, '2Test', '2Test', 8, 53),
(75, '3Test', '3Test', 8, 54),
(76, 'Testttt', 'Dfdslkf', 4, 55),
(78, 'testnamee', 'testnachnamee', 1, 1),
(79, 'testnamee', 'testnachnamee', 1, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT für Tabelle `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT für Tabelle `klassen`
--
ALTER TABLE `klassen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `rfid_devices`
--
ALTER TABLE `rfid_devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT für Tabelle `rfid_device_type`
--
ALTER TABLE `rfid_device_type`
  MODIFY `device_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `rfid_devices`
--
ALTER TABLE `rfid_devices`
  ADD CONSTRAINT `rfid_devices_ibfk_1` FOREIGN KEY (`device_type`) REFERENCES `rfid_device_type` (`device_type_id`);

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
