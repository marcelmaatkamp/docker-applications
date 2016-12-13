-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: localhost    Database: showcase
-- ------------------------------------------------------
-- Server version	5.7.15

drop database if exists showcase;
create database showcase;
use showcase;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alarm`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `alarm` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `alarm_regel` bigint(20) NOT NULL,
  `observatie` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_alarm_alarm_regel_idx` (`alarm_regel`),
  KEY `FK_alarm_observatie_idx` (`observatie`),
  CONSTRAINT `FK_alarm_alarm_regel` FOREIGN KEY (`alarm_regel`) REFERENCES `alarm_regel` (`id`),
  CONSTRAINT `FK_alarm_observatie` FOREIGN KEY (`observatie`) REFERENCES `observatie` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=382 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alarm`
--


--
-- Table structure for table `alarm_notificatie`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `alarm_notificatie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alarm_regel` bigint(20) NOT NULL,
  `kanaal` varchar(45) DEFAULT NULL,
  `p1` varchar(45) DEFAULT NULL,
  `p2` varchar(45) DEFAULT NULL,
  `p3` varchar(45) DEFAULT NULL,
  `p4` varchar(45) DEFAULT NULL,
  `meldingtekst` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_alarm_notificatie_alarm_regel_idx` (`alarm_regel`),
  CONSTRAINT `fk_alarm_notificatie_alarm_regel` FOREIGN KEY (`alarm_regel`) REFERENCES `alarm_regel` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alarm_notificatie`
--

INSERT INTO `alarm_notificatie` (`id`, `alarm_regel`, `kanaal`, `p1`, `p2`, `p3`, `p4`, `meldingtekst`) VALUES (1,1,'RabbitMQ','AT','OT','','','Temp is hier dan 80%'),(2,3,'rabbitmq','OT','AT','','','humidity > 95'),(3,5,'slack','koffer_1',NULL,NULL,NULL,'TEST: Temperatuur te hoog: %v %t'),(4,6,'slack','koffer_1',NULL,NULL,NULL,'TEST: Temperatuur te laag: %v %t'),(5,7,'slack','koffer_1',NULL,NULL,NULL,'TEST: Al 10 seconden geen heartbeat ontvangen'),(6,8,'slack','koffer_1',NULL,NULL,NULL,'TEST: Deksel dicht'),(7,9,'slack','koffer_1',NULL,NULL,NULL,'TEST: Deksel open'),(8,10,'slack','koffer_1','','','','Er staat een paard in de gang'),(9,11,'slack','koffer_1','','','','koffer 1 open'),(10,12,'slack','koffer_1','','','','koffer 1 dicht'),(11,11,'telegram','','','','','koffer is open dicht test'),(12,13,'slack','koffer_1','','','','Koffer danst'),(13,16,'slack','koffer_kpn','','','','Deurtje open via KPN');

--
-- Table structure for table `alarm_regel`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `alarm_regel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node` varchar(255) NOT NULL,
  `sensor` varchar(255) NOT NULL,
  `alarm_trigger` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_alarm_regel_node_idx` (`node`),
  KEY `FK_alarm_regel_sensor_idx` (`sensor`),
  CONSTRAINT `FK_alarm_regel_node` FOREIGN KEY (`node`) REFERENCES `node` (`dev_eui`),
  CONSTRAINT `FK_alarm_regel_sensor` FOREIGN KEY (`sensor`) REFERENCES `sensor` (`sensor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alarm_regel`
--

INSERT INTO `alarm_regel` (`id`, `node`, `sensor`, `alarm_trigger`) VALUES (1,'00000000D1EC2A39','1','X > 80'),(2,'00000000D1EC2A39','2','x > 90'),(3,'00000000D1EC2A39','2','x > 95'),(4,'000000007FEE6E5B','1','true'),(5,'00000000D1EC2A39','2','x>75'),(6,'00000000D1EC2A39','2','x < 5'),(7,'00000000D1EC2A39','0','10'),(8,'00000000D1EC2A39','1','x == 0'),(9,'00000000D1EC2A39','1','x == 1'),(10,'00000TEST','2','X > 90'),(11,'000000007FEE6E5B','1','x == 0'),(12,'000000007FEE6E5B','1','x == 1'),(13,'000000007FEE6E5B','7','x == 1'),(15,'000000007FEE6E5B','2','x > 240'),(16,'0059AC000018041B','1','x == 1');

--
-- Temporary view structure for view `alarm_report`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `alarm_report` AS SELECT
 1 AS `Id`,
 1 AS `Node`,
 1 AS `Sensor`,
 1 AS `AlarmTrigger`,
 1 AS `ObservatieWaarde`,
 1 AS `Observatietijdstip`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `laatste_observatie_per_node_sensor`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `laatste_observatie_per_node_sensor` AS SELECT
 1 AS `ObservatieId`,
 1 AS `Node`,
 1 AS `Sensor`,
 1 AS `ObservatieWaarde`,
 1 AS `ObservatieDatum`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `node`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `node` (
  `dev_eui` varchar(255) NOT NULL,
  `omschrijving` varchar(255) NOT NULL,
  PRIMARY KEY (`dev_eui`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`dev_eui`, `omschrijving`) VALUES ('0000000002015EFF','Sodaq Mbili Demo en Productie'),('0000000029B1A8A0','Sodaq Mbili Test en Acceptatie'),('000000002DABCBC8','Sodaq Mbili Ontwikkel omgeving'),('00000000518C9EB3','Sodaq One Ontwikkel omgeving XXX'),('00000000556C1CB9','Sodaq One Demo en Productie'),('000000007FEE6E5B','Node 3'),('00000000A4F5F80F','Sodaq One Ontwikkel omgeving'),('00000000D1EC2A39','Node 1'),('00000TEST','TESTNODE_JON'),('0059AC000018041B','KPN');

--
-- Table structure for table `observatie`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `observatie` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node` varchar(255) NOT NULL,
  `sensor` varchar(255) NOT NULL,
  `datum_tijd_aangemaakt` datetime NOT NULL,
  `waarde` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_observatie_node_idx` (`node`),
  KEY `FK_observatie_sensor_idx` (`sensor`),
  CONSTRAINT `FK_observatie_node` FOREIGN KEY (`node`) REFERENCES `node` (`dev_eui`),
  CONSTRAINT `FK_observatie_sensor` FOREIGN KEY (`sensor`) REFERENCES `sensor` (`sensor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9034 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `observatie`
--

INSERT INTO `observatie` (`id`, `node`, `sensor`, `datum_tijd_aangemaakt`, `waarde`) VALUES (9000,'0059AC000018041B','4','2016-10-04 13:29:26','0'),(9001,'0059AC000018041B','5','2016-10-04 13:29:26','0'),(9002,'0059AC000018041B','6','2016-10-04 13:29:26','0'),(9003,'0059AC000018041B','0','2016-10-04 13:29:26','1'),(9004,'0059AC000018041B','2','2016-10-04 13:29:26','-10'),(9005,'0059AC000018041B','3','2016-10-04 13:29:26','-1'),(9006,'0059AC000018041B','4','2016-10-04 13:29:26','0'),(9007,'0059AC000018041B','5','2016-10-04 13:29:26','0'),(9008,'0059AC000018041B','6','2016-10-04 13:29:26','0'),(9009,'0059AC000018041B','0','2016-10-04 13:29:26','1'),(9010,'0059AC000018041B','2','2016-10-04 13:29:26','-10'),(9011,'0059AC000018041B','3','2016-10-04 13:29:26','-1'),(9012,'0059AC000018041B','4','2016-10-04 13:29:26','0'),(9013,'0059AC000018041B','5','2016-10-04 13:29:26','0'),(9014,'0059AC000018041B','6','2016-10-04 13:29:26','0'),(9015,'0059AC000018041B','0','2016-10-04 13:29:26','1'),(9016,'0059AC000018041B','2','2016-10-04 13:29:26','-10'),(9017,'0059AC000018041B','3','2016-10-04 13:29:26','-1'),(9018,'0059AC000018041B','4','2016-10-04 13:29:26','0'),(9019,'0059AC000018041B','5','2016-10-04 13:29:26','0'),(9020,'0059AC000018041B','6','2016-10-04 13:29:26','0'),(9021,'0059AC000018041B','0','2016-10-04 13:29:26','1'),(9022,'0059AC000018041B','2','2016-10-04 13:29:26','-10'),(9023,'0059AC000018041B','3','2016-10-04 13:29:00','-1'),(9025,'0059AC000018041B','5','2016-10-04 13:29:26','0'),(9026,'0059AC000018041B','6','2016-10-04 13:29:26','0'),(9027,'0059AC000018041B','0','2016-10-04 13:29:26','1'),(9028,'0059AC000018041B','2','2016-10-04 13:29:26','-10'),(9029,'0059AC000018041B','3','2016-10-04 13:29:26','-1'),(9030,'0059AC000018041B','4','2016-10-04 13:29:26','0'),(9031,'0059AC000018041B','5','2016-10-04 13:29:26','0'),(9032,'0059AC000018041B','6','2016-10-04 13:29:26','0');

--
-- Table structure for table `role`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `role` (
  `r_id` int(10) NOT NULL AUTO_INCREMENT,
  `r_name` varchar(45) NOT NULL,
  `r_can_admin` tinyint(4) DEFAULT '0',
  `r_can_edit` tinyint(4) DEFAULT '0',
  `r_can_write` tinyint(4) DEFAULT '0',
  `r_can_read` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`r_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`r_id`, `r_name`, `r_can_admin`, `r_can_edit`, `r_can_write`, `r_can_read`) VALUES (1,'Reader',0,0,0,1),(2,'Author',0,0,1,1),(3,'Editor',0,1,1,1),(4,'Admin',1,1,1,1);

--
-- Table structure for table `sensor`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `sensor` (
  `sensor_id` varchar(255) NOT NULL,
  `omschrijving` varchar(255) NOT NULL,
  `eenheid` varchar(255) DEFAULT NULL,
  `omrekenfactor` varchar(255) DEFAULT NULL,
  `presentatie` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sensor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensor`
--

INSERT INTO `sensor` (`sensor_id`, `omschrijving`, `eenheid`, `omrekenfactor`, `presentatie`) VALUES ('-1','Skipped message',NULL,NULL,NULL),('0','Keepalive',NULL,NULL,NULL),('1','Schakelaar',NULL,NULL,NULL),('2','Temperatuur','C','x/10',''),('3','Luchtvochtigheid','%','',''),('4','Spanning','V','x/1000',''),('5','Stroom','A','x/1000',''),('6','Verbruik','uA/h','',''),('7','Beweging','(1=beweging)','',''),('8','Retry Counter',NULL,NULL,NULL);

--
-- Temporary view structure for view `sensor_node_observation`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `sensor_node_observation` AS SELECT
 1 AS `Node`,
 1 AS `DevEui`,
 1 AS `Sensor`,
 1 AS `SensorId`,
 1 AS `ObservatieWaarde`,
 1 AS `ObservatieDatum`,
 1 AS `ObservatieId`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `user` (
  `a_id` int(10) NOT NULL AUTO_INCREMENT,
  `a_role_id` int(10) DEFAULT NULL,
  `a_username` varchar(150) NOT NULL,
  `a_password` varchar(150) NOT NULL,
  `a_first_name` varchar(45) NOT NULL,
  `a_last_name` varchar(45) NOT NULL,
  PRIMARY KEY (`a_id`),
  KEY `u_role_idx` (`a_role_id`),
  CONSTRAINT `u_role` FOREIGN KEY (`a_role_id`) REFERENCES `role` (`r_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`a_id`, `a_role_id`, `a_username`, `a_password`, `a_first_name`, `a_last_name`) VALUES (2,4,'admin','!!!$2y$10$MOEhiKLYZqd6qH.IPSAFwuaQAHBSmqEnN5sKqxcPZ8fpFU.t5s7eO','Admin','User'),(6,1,'Rob','!!!$2y$10$hdQOzyfqJZszSJJbEwXFie3sJHbWbnWfJVi7nRa7sz3ijMsmSXO5e','Rob','Robson'),(7,3,'Ed','!!!$2y$10$j.JHG.rLMQNZxPRxGNjUKOad1LC4oJotDbP9sNGqyGmEdpSuWh/3G','Ed','Edson'),(8,4,'Andre','!!!$2y$10$TVmON/yXWiiNPow96EquT.zzXR6NSQxXe6s6fKdGd2ecb.04UTUQC','Andre','Adson');

--
-- Final view structure for view `alarm_report`
--

/*!50001 DROP VIEW IF EXISTS `alarm_report`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `alarm_report` AS select `alarm`.`id` AS `Id`,`node`.`omschrijving` AS `Node`,`sensor`.`omschrijving` AS `Sensor`,`alarm_regel`.`alarm_trigger` AS `AlarmTrigger`,`observatie`.`waarde` AS `ObservatieWaarde`,`observatie`.`datum_tijd_aangemaakt` AS `Observatietijdstip` from ((((`alarm` join `alarm_regel` on((`alarm`.`alarm_regel` = `alarm_regel`.`id`))) join `node` on((`alarm_regel`.`node` = `node`.`dev_eui`))) join `sensor` on((`alarm_regel`.`sensor` = `sensor`.`sensor_id`))) join `observatie` on((`alarm`.`observatie` = `observatie`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `laatste_observatie_per_node_sensor`
--

/*!50001 DROP VIEW IF EXISTS `laatste_observatie_per_node_sensor`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `laatste_observatie_per_node_sensor` AS select `sno`.`ObservatieId` AS `ObservatieId`,`sno`.`Node` AS `Node`,`sno`.`Sensor` AS `Sensor`,`sno`.`ObservatieWaarde` AS `ObservatieWaarde`,`sno`.`ObservatieDatum` AS `ObservatieDatum` from (`showcase`.`sensor_node_observation` `sno` join (select `sensor_node_observation`.`Node` AS `Node`,`sensor_node_observation`.`Sensor` AS `Sensor`,max(`sensor_node_observation`.`ObservatieId`) AS `ObservatieId` from `showcase`.`sensor_node_observation` group by `sensor_node_observation`.`Node`,`sensor_node_observation`.`Sensor`) `max` on(((`sno`.`Node` = `max`.`Node`) and (`sno`.`Sensor` = `max`.`Sensor`) and (`sno`.`ObservatieId` = `max`.`ObservatieId`)))) order by `sno`.`Node`,`sno`.`Sensor`,`sno`.`ObservatieId` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sensor_node_observation`
--

/*!50001 DROP VIEW IF EXISTS `sensor_node_observation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `sensor_node_observation` AS select `node`.`omschrijving` AS `Node`,`node`.`dev_eui` AS `DevEui`,`sensor`.`omschrijving` AS `Sensor`,`sensor`.`sensor_id` AS `SensorId`,`observatie`.`waarde` AS `ObservatieWaarde`,`observatie`.`datum_tijd_aangemaakt` AS `ObservatieDatum`,`observatie`.`id` AS `ObservatieId` from ((`observatie` join `node` on((`observatie`.`node` = `node`.`dev_eui`))) join `sensor` on((`observatie`.`sensor` = `sensor`.`sensor_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;