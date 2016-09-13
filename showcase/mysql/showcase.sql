-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Sep 13, 2016 at 06:03 AM
-- Server version: 5.7.14
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `showcase`
--

-- --------------------------------------------------------

--
-- Table structure for table `alarm`
--

CREATE TABLE `alarm` (
  `id` bigint(20) NOT NULL,
  `alarm_regel` bigint(20) NOT NULL,
  `observatie` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `alarm_notificatie`
--

CREATE TABLE `alarm_notificatie` (
  `id` int(11) NOT NULL,
  `alarm_regel` bigint(20) NOT NULL,
  `kanaal` varchar(45) DEFAULT NULL,
  `p1` varchar(45) DEFAULT NULL,
  `p2` varchar(45) DEFAULT NULL,
  `p3` varchar(45) DEFAULT NULL,
  `p4` varchar(45) DEFAULT NULL,
  `meldingtekst` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `alarm_notificatie`
--

INSERT INTO `alarm_notificatie` (`id`, `alarm_regel`, `kanaal`, `p1`, `p2`, `p3`, `p4`, `meldingtekst`) VALUES
(1, 1, 'slack', 'koffer_1', NULL, NULL, NULL, 'Temperatuur te laag'),
(2, 2, 'slack', 'koffer_1', NULL, NULL, NULL, 'Temperatuur te hoog'),
(3, 5, 'slack', 'koffer_1', NULL, NULL, NULL, 'TEST: Temperatuur te hoog'),
(4, 6, 'slack', 'koffer_1', NULL, NULL, NULL, 'TEST: Temperatuur te laag'),
(5, 7, 'slack', 'koffer_1', NULL, NULL, NULL, 'TEST: Al 10 seconden geen heartbeat ontvangen');

-- --------------------------------------------------------

--
-- Table structure for table `alarm_regel`
--

CREATE TABLE `alarm_regel` (
  `id` bigint(20) NOT NULL,
  `node` varchar(255) NOT NULL,
  `sensor` varchar(255) NOT NULL,
  `alarm_trigger` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `alarm_regel`
--

INSERT INTO `alarm_regel` (`id`, `node`, `sensor`, `alarm_trigger`) VALUES
(1, '000000007FEE6E5B', '2', 'x<5'),
(2, '000000007FEE6E5B', '2', 'x>75'),
(3, '000000007FEE6E5B', '0', '30'),
(4, '000000007FEE6E5B', '1', 'true'),
(5, '00000000D1EC2A39', '2', 'x>75'),
(6, '00000000D1EC2A39', '2', 'x<5'),
(7, '00000000D1EC2A39', '0', '10');

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE `node` (
  `dev_eui` varchar(255) NOT NULL,
  `omschrijving` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`dev_eui`, `omschrijving`) VALUES
('0000000002015EFF', 'Sodaq Mbili Demo en Productie'),
('0000000029B1A8A0', 'Sodaq Mbili Test en Acceptatie'),
('000000002DABCBC8', 'Sodaq Mbili Ontwikkel omgeving'),
('00000000518C9EB3', 'Sodaq One Ontwikkel omgeving'),
('00000000556C1CB9', 'Sodaq One Demo en Productie'),
('000000007FEE6E5B', 'Sodaq One Test en Acceptatie'),
('00000000A4F5F80F', 'Sodaq One Ontwikkel omgeving'),
('00000000D1EC2A39', 'gesimuleerde lokale test');

-- --------------------------------------------------------

--
-- Table structure for table `observatie`
--

CREATE TABLE `observatie` (
  `id` bigint(20) NOT NULL,
  `node` varchar(255) NOT NULL,
  `sensor` varchar(255) NOT NULL,
  `datum_tijd_aangemaakt` datetime NOT NULL,
  `waarde` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sensor`
--

CREATE TABLE `sensor` (
  `sensor_id` varchar(255) NOT NULL,
  `omschrijving` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sensor`
--

INSERT INTO `sensor` (`sensor_id`, `omschrijving`) VALUES
('0', 'Heartbeat'),
('1', 'Schakelaar'),
('2', 'Temperatuur'),
('3', 'Luchtvochtigheid'),
('4', 'Spanning (Volt)'),
('5', 'Stroomverbruik (Ah)');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alarm`
--
ALTER TABLE `alarm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_qqgttvcq7u148nkqjhx2hsbdi` (`alarm_regel`),
  ADD KEY `FK_27v5pji13cutepjuv9ox0glwp` (`observatie`);

--
-- Indexes for table `alarm_notificatie`
--
ALTER TABLE `alarm_notificatie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alarm_notificatie_alarm_regel_idx` (`alarm_regel`);

--
-- Indexes for table `alarm_regel`
--
ALTER TABLE `alarm_regel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_4stgr2ch3nidujfk8pial5sdv` (`node`),
  ADD KEY `FK_afhhe5d4s0if67l0h6fxmdj08` (`sensor`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`dev_eui`);

--
-- Indexes for table `observatie`
--
ALTER TABLE `observatie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_smi270lm0koqq55tj5bfisawt` (`node`),
  ADD KEY `FK_3vtmlnui6re2o9jq4vqpa2t06` (`sensor`);

--
-- Indexes for table `sensor`
--
ALTER TABLE `sensor`
  ADD PRIMARY KEY (`sensor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alarm`
--
ALTER TABLE `alarm`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `alarm_notificatie`
--
ALTER TABLE `alarm_notificatie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `alarm_regel`
--
ALTER TABLE `alarm_regel`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `observatie`
--
ALTER TABLE `observatie`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `alarm`
--
ALTER TABLE `alarm`
  ADD CONSTRAINT `FK_27v5pji13cutepjuv9ox0glwp` FOREIGN KEY (`observatie`) REFERENCES `observatie` (`id`),
  ADD CONSTRAINT `FK_qqgttvcq7u148nkqjhx2hsbdi` FOREIGN KEY (`alarm_regel`) REFERENCES `alarm_regel` (`id`);

--
-- Constraints for table `alarm_notificatie`
--
ALTER TABLE `alarm_notificatie`
  ADD CONSTRAINT `fk_alarm_notificatie_alarm_regel` FOREIGN KEY (`alarm_regel`) REFERENCES `alarm_regel` (`id`);

--
-- Constraints for table `alarm_regel`
--
ALTER TABLE `alarm_regel`
  ADD CONSTRAINT `FK_4stgr2ch3nidujfk8pial5sdv` FOREIGN KEY (`node`) REFERENCES `node` (`dev_eui`),
  ADD CONSTRAINT `FK_afhhe5d4s0if67l0h6fxmdj08` FOREIGN KEY (`sensor`) REFERENCES `sensor` (`sensor_id`);

--
-- Constraints for table `observatie`
--
ALTER TABLE `observatie`
  ADD CONSTRAINT `FK_3vtmlnui6re2o9jq4vqpa2t06` FOREIGN KEY (`sensor`) REFERENCES `sensor` (`sensor_id`),
  ADD CONSTRAINT `FK_smi270lm0koqq55tj5bfisawt` FOREIGN KEY (`node`) REFERENCES `node` (`dev_eui`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
