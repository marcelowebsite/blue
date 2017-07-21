-- MySQL dump 10.13  Distrib 5.5.32, for Linux (x86_64)
--
-- Host: localhost    Database: blueprintsprograms
-- ------------------------------------------------------
-- Server version	5.5.32

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
-- Table structure for table `citation`
--
-- add later:   FOREIGN KEY (`bp_editor_id`) REFERENCES `bp_editor` (`bp_editor_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
-- element_id needs to be changed to int, here and elsewhere

DROP TABLE IF EXISTS `citation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citation` (
  `citation_id` int(11) NOT NULL AUTO_INCREMENT,
  `study_id` int(11) NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `citation_text` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `citation_brief` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`citation_id`),
  KEY `study_id` (`study_id`),
  KEY `element_id` (`element_id`),
  KEY `bp_editor_id` (`bp_editor_id`),
  FOREIGN KEY (`study_id`) REFERENCES `study` (`study_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY (`element_id`) REFERENCES `element` (`element_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `study`
--
-- add later:   FOREIGN KEY (`bp_editor_id`) REFERENCES `bp_editor` (`bp_editor_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
-- element_id needs to be changed to int, here and elsewhere

DROP TABLE IF EXISTS `study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study` (
  `study_id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_id` int(11) NOT NULL DEFAULT 1,
  `program_id` int(11) NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`study_id`),
  UNIQUE KEY (`study_id`,`sort_id`),
  KEY `program_id` (`program_id`),
  KEY `element_id` (`element_id`),
  KEY `bp_editor_id` (`bp_editor_id`),
  FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY (`element_id`) REFERENCES `element` (`element_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-04 19:05:37
