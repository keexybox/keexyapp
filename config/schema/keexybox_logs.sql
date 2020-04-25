-- MySQL dump 10.17  Distrib 10.3.22-MariaDB, for debian-linux-gnueabihf (armv8l)
--
-- Host: localhost    Database: keexybox_logs
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
-- Table structure for table `dns_log`
--

DROP TABLE IF EXISTS `dns_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dns_log` (
  `id` varchar(65) NOT NULL,
  `logfile_name` varchar(30) DEFAULT NULL,
  `line_number` int(20) NOT NULL,
  `proxy_host` varchar(30) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `client_ip` char(15) DEFAULT NULL,
  `profile_id` int(11) NOT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `blocked` tinyint(1) NOT NULL,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_dnslog` (`blocked`,`category`,`date_time`,`client_ip`,`profile_id`,`domain`,`logfile_name`,`line_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `keexybox_profiles`
--

DROP TABLE IF EXISTS `keexybox_profiles`;
/*!50001 DROP VIEW IF EXISTS `keexybox_profiles`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `keexybox_profiles` (
  `id` tinyint NOT NULL,
  `profilename` tinyint NOT NULL,
  `enabled` tinyint NOT NULL,
  `default_routing` tinyint NOT NULL,
  `default_ipfilter` tinyint NOT NULL,
  `log_enabled` tinyint NOT NULL,
  `use_ttdnsd` tinyint NOT NULL,
  `safesearch_google` tinyint NOT NULL,
  `safesearch_bing` tinyint NOT NULL,
  `safesearch_youtube` tinyint NOT NULL,
  `created` tinyint NOT NULL,
  `modified` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `keexybox_profiles`
--

/*!50001 DROP TABLE IF EXISTS `keexybox_profiles`*/;
/*!50001 DROP VIEW IF EXISTS `keexybox_profiles`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `keexybox_profiles` AS select `keexybox`.`profiles`.`id` AS `id`,`keexybox`.`profiles`.`profilename` AS `profilename`,`keexybox`.`profiles`.`enabled` AS `enabled`,`keexybox`.`profiles`.`default_routing` AS `default_routing`,`keexybox`.`profiles`.`default_ipfilter` AS `default_ipfilter`,`keexybox`.`profiles`.`log_enabled` AS `log_enabled`,`keexybox`.`profiles`.`use_ttdnsd` AS `use_ttdnsd`,`keexybox`.`profiles`.`safesearch_google` AS `safesearch_google`,`keexybox`.`profiles`.`safesearch_bing` AS `safesearch_bing`,`keexybox`.`profiles`.`safesearch_youtube` AS `safesearch_youtube`,`keexybox`.`profiles`.`created` AS `created`,`keexybox`.`profiles`.`modified` AS `modified` from `keexybox`.`profiles` */;
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

-- Dump completed on 2020-04-10 10:53:55
