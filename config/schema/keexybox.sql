-- MySQL dump 10.17  Distrib 10.3.22-MariaDB, for debian-linux-gnueabihf (armv8l)
--
-- Host: localhost    Database: keexybox
-- ------------------------------------------------------
-- Server version	10.3.22-MariaDB-0+deb10u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actives_connections`
--

DROP TABLE IF EXISTS `actives_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actives_connections` (
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(20) NOT NULL DEFAULT 0,
  `device_id` int(20) NOT NULL DEFAULT 0,
  `name_id` int(20) NOT NULL DEFAULT 0,
  `profile_id` int(20) NOT NULL,
  `type` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mac` varchar(17) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_details` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` decimal(15,3) unsigned NOT NULL,
  `end_time` decimal(15,3) unsigned NOT NULL,
  `display_start_time` datetime NOT NULL,
  `display_end_time` datetime NOT NULL,
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip` (`ip`),
  KEY `end_time` (`end_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `param` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `type` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`param`),
  UNIQUE KEY `variable` (`param`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connections_history`
--

DROP TABLE IF EXISTS `connections_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connections_history` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_id` int(20) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `device_id` int(10) NOT NULL DEFAULT 0,
  `profile_id` int(20) NOT NULL,
  `type` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mac` varchar(17) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_details` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` decimal(15,3) unsigned NOT NULL,
  `end_time` decimal(15,3) unsigned NOT NULL,
  `duration` int(20) NOT NULL,
  `display_start_time` datetime NOT NULL,
  `display_end_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `end_time` (`end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `devicename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mac` varchar(17) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dhcp_reservation_ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lang` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` int(1) NOT NULL,
  `profile_id` int(20) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER device_insert_timestamp BEFORE INSERT ON devices FOR EACH ROW SET NEW.created = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER device_update_timestamp BEFORE UPDATE ON devices FOR EACH ROW SET NEW.modified = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;

--
-- Table structure for table `dns_cache`
--

DROP TABLE IF EXISTS `dns_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dns_cache` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `fqdn` varchar(255) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `timestamp` int(20) NOT NULL,
  `c_timestamp` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `fw_rules`
--

DROP TABLE IF EXISTS `fw_rules`;
/*!50001 DROP VIEW IF EXISTS `fw_rules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `fw_rules` (
  `default_ipfilter` tinyint NOT NULL,
  `id` tinyint NOT NULL,
  `rule_number` tinyint NOT NULL,
  `profile_id` tinyint NOT NULL,
  `dest_ip_type` tinyint NOT NULL,
  `dest_ip` tinyint NOT NULL,
  `dest_ip_mask` tinyint NOT NULL,
  `dest_iprange_first` tinyint NOT NULL,
  `dest_iprange_last` tinyint NOT NULL,
  `dest_hostname` tinyint NOT NULL,
  `protocol` tinyint NOT NULL,
  `dest_ports` tinyint NOT NULL,
  `target` tinyint NOT NULL,
  `enabled` tinyint NOT NULL,
  `dest_hostname_ip` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL,
  `c_timestamp` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profilename` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `default_routing` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bypass',
  `default_ipfilter` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `use_ttdnsd` tinyint(1) NOT NULL DEFAULT 0,
  `safesearch_google` tinyint(1) NOT NULL DEFAULT 0,
  `safesearch_bing` tinyint(1) NOT NULL DEFAULT 0,
  `safesearch_youtube` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER profile_insert_timestamp BEFORE INSERT ON profiles FOR EACH ROW SET NEW.created = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER profile_update_timestamp BEFORE UPDATE ON profiles FOR EACH ROW SET NEW.modified = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;

--
-- Table structure for table `profiles_blacklists`
--

DROP TABLE IF EXISTS `profiles_blacklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles_blacklists` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profiles_ipfilters`
--

DROP TABLE IF EXISTS `profiles_ipfilters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles_ipfilters` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `rule_number` int(20) NOT NULL,
  `profile_id` int(20) NOT NULL,
  `dest_ip_type` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dest_ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dest_ip_mask` int(2) NOT NULL DEFAULT 32,
  `dest_iprange_first` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dest_iprange_last` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dest_hostname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `protocol` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dest_ports` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profiles_routing`
--

DROP TABLE IF EXISTS `profiles_routing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles_routing` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `routing` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_id` int(20) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profiles_times`
--

DROP TABLE IF EXISTS `profiles_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles_times` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `daysofweek` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timerange` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timeid` (`daysofweek`,`timerange`,`profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!50001 DROP VIEW IF EXISTS `routes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `routes` (
  `profile_id` tinyint NOT NULL,
  `default_routing` tinyint NOT NULL,
  `fqdn` tinyint NOT NULL,
  `dstip` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL,
  `c_timestamp` tinyint NOT NULL,
  `routing` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `displayname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_US',
  `profile_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `expiration` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER user_creation_timestamp BEFORE INSERT ON users  FOR EACH ROW SET NEW.created = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER user_update_timestamp BEFORE UPDATE ON users  FOR EACH ROW SET NEW.modified = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `keexybox` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;

--
-- Final view structure for view `fw_rules`
--

/*!50001 DROP TABLE IF EXISTS `fw_rules`*/;
/*!50001 DROP VIEW IF EXISTS `fw_rules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `fw_rules` AS select `p`.`default_ipfilter` AS `default_ipfilter`,`pi`.`id` AS `id`,`pi`.`rule_number` AS `rule_number`,`pi`.`profile_id` AS `profile_id`,`pi`.`dest_ip_type` AS `dest_ip_type`,`pi`.`dest_ip` AS `dest_ip`,`pi`.`dest_ip_mask` AS `dest_ip_mask`,`pi`.`dest_iprange_first` AS `dest_iprange_first`,`pi`.`dest_iprange_last` AS `dest_iprange_last`,`pi`.`dest_hostname` AS `dest_hostname`,`pi`.`protocol` AS `protocol`,`pi`.`dest_ports` AS `dest_ports`,`pi`.`target` AS `target`,`pi`.`enabled` AS `enabled`,`dc`.`ip` AS `dest_hostname_ip`,`dc`.`timestamp` AS `timestamp`,`dc`.`c_timestamp` AS `c_timestamp` from ((`dns_cache` `dc` left join `profiles_ipfilters` `pi` on(`dc`.`fqdn` = `pi`.`dest_hostname`)) left join `profiles` `p` on(`pi`.`profile_id` = `p`.`id`)) where `pi`.`enabled` = 1 union select `p`.`default_ipfilter` AS `default_ipfilter`,`pi`.`id` AS `id`,`pi`.`rule_number` AS `rule_number`,`pi`.`profile_id` AS `profile_id`,`pi`.`dest_ip_type` AS `dest_ip_type`,`pi`.`dest_ip` AS `dest_ip`,`pi`.`dest_ip_mask` AS `dest_ip_mask`,`pi`.`dest_iprange_first` AS `dest_iprange_first`,`pi`.`dest_iprange_last` AS `dest_iprange_last`,`pi`.`dest_hostname` AS `dest_hostname`,`pi`.`protocol` AS `protocol`,`pi`.`dest_ports` AS `dest_ports`,`pi`.`target` AS `target`,`pi`.`enabled` AS `enabled`,`dc`.`ip` AS `dest_hostname_ip`,`dc`.`timestamp` AS `timestamp`,`dc`.`c_timestamp` AS `c_timestamp` from (`profiles` `p` left join (`profiles_ipfilters` `pi` left join `dns_cache` `dc` on(`dc`.`fqdn` = `pi`.`dest_hostname`)) on(`pi`.`profile_id` = `p`.`id`)) where `pi`.`enabled` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `routes`
--

/*!50001 DROP TABLE IF EXISTS `routes`*/;
/*!50001 DROP VIEW IF EXISTS `routes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `routes` AS select `p`.`id` AS `profile_id`,`p`.`default_routing` AS `default_routing`,`dc`.`fqdn` AS `fqdn`,`dc`.`ip` AS `dstip`,`dc`.`timestamp` AS `timestamp`,`dc`.`c_timestamp` AS `c_timestamp`,`pr`.`routing` AS `routing` from ((`dns_cache` `dc` join `profiles_routing` `pr` on(`dc`.`fqdn` = `pr`.`address`)) join `profiles` `p` on(`pr`.`profile_id` = `p`.`id`)) where `pr`.`enabled` = 1 */;
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

-- Dump completed on 2020-07-16 13:08:01
