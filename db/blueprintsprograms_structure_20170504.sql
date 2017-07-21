CREATE DATABASE IF NOT EXISTS `blueprintsprograms`
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blueprintsprograms`;

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
-- Table structure for table `attribute`
--

DROP TABLE IF EXISTS `attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute` (
  `attribute_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_value` smallint(3) NOT NULL DEFAULT '0',
  `attribute_label` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute_sort` smallint(4) NOT NULL DEFAULT '0',
  `control_id` smallint(6) NOT NULL DEFAULT '0',
  `active` smallint(6) NOT NULL DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attribute_id`),
  KEY `control_id` (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_editor`
--

DROP TABLE IF EXISTS `bp_editor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_editor` (
  `bp_editor_id` int(11) NOT NULL,
  `editor_email` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `editor_pass` char(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `editor_newpass` char(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_newpass_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor_token` char(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `editor_email_token` char(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_email_token_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor_email_authenticated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor_role` smallint(4) unsigned NOT NULL DEFAULT '0',
  `editor_fname` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_lname` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_editor_id`),
  UNIQUE KEY `editor_email` (`editor_email`(128))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_editor_session`
--

DROP TABLE IF EXISTS `bp_editor_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_editor_session` (
  `bp_editor_session_id` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_name` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bp_editor_id` int(11) unsigned DEFAULT NULL,
  `session_ip_address` int(11) unsigned DEFAULT NULL,
  `session_page_id` smallint(4) unsigned DEFAULT NULL,
  `session_page_token` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_editor_session_id`),
  KEY `bp_editor_id` (`bp_editor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_search`
--

DROP TABLE IF EXISTS `bp_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_search` (
  `bp_search_pid` int(11) NOT NULL AUTO_INCREMENT,
  `bp_search_id` int(11) unsigned NOT NULL,
  `bp_search_element` smallint(5) unsigned NOT NULL,
  `bp_search_attribute` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`bp_search_pid`),
  UNIQUE KEY `bp_search_id` (`bp_search_id`,`bp_search_element`,`bp_search_attribute`),
  KEY `bp_search_id_2` (`bp_search_id`),
  KEY `bp_search_element` (`bp_search_element`),
  KEY `bp_search_attribute` (`bp_search_attribute`)
) ENGINE=InnoDB AUTO_INCREMENT=43771 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_search_keyword`
--

DROP TABLE IF EXISTS `bp_search_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_search_keyword` (
  `bp_search_id` int(11) unsigned NOT NULL,
  `search_keyword_limit` tinyint(2) unsigned DEFAULT NULL,
  `bp_search_text` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`bp_search_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_search_save`
--

DROP TABLE IF EXISTS `bp_search_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_search_save` (
  `bp_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bp_search_save_id` int(11) unsigned NOT NULL,
  `bp_search_id` int(11) unsigned NOT NULL,
  `bp_search_name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bp_search_notes` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `search_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_user_id`,`bp_search_save_id`),
  KEY `bp_user_id` (`bp_user_id`),
  KEY `bp_search_save_id` (`bp_search_save_id`),
  KEY `bp_search_id` (`bp_search_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_search_user`
--

DROP TABLE IF EXISTS `bp_search_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_search_user` (
  `bp_search_user_pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bp_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bp_search_id` int(11) unsigned NOT NULL,
  `is_keyword_search` tinyint(1) DEFAULT NULL,
  `is_step_search` tinyint(1) DEFAULT NULL,
  `bp_session_id` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_user_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_search_user_pid`),
  UNIQUE KEY `bp_user_id` (`bp_user_id`,`bp_search_id`,`bp_session_id`),
  KEY `bp_user_id_2` (`bp_user_id`),
  KEY `bp_search_id` (`bp_search_id`),
  KEY `bp_session_id` (`bp_session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13825 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_session`
--

DROP TABLE IF EXISTS `bp_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_session` (
  `bp_session_id` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_name` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bp_user_id` int(11) unsigned DEFAULT NULL,
  `session_ip_address` int(11) unsigned DEFAULT NULL,
  `session_page_id` smallint(4) unsigned DEFAULT NULL,
  `session_page_token` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_session_id`),
  KEY `bp_user_id` (`bp_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_user`
--

DROP TABLE IF EXISTS `bp_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_user` (
  `bp_user_id` int(11) unsigned NOT NULL,
  `user_email` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_pass` char(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_newpass` char(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_newpass_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_token` char(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email_token` char(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email_token_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_email_authenticated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_fname` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_lname` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_organization` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_job_title` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_address` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_address2` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_city` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_state` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_country` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_zip` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_mailing_list` tinyint(1) unsigned DEFAULT NULL,
  `user_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_user_id`),
  UNIQUE KEY `user_email` (`user_email`(128))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_user_min`
--

DROP TABLE IF EXISTS `bp_user_min`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_user_min` (
  `bp_user_id` int(11) unsigned NOT NULL,
  `user_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bp_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `control`
--

DROP TABLE IF EXISTS `control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `control` (
  `control_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `control_type` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `control_width` smallint(4) NOT NULL DEFAULT '0',
  `control_height` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`control_id`),
  KEY `control_type` (`control_type`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain`
--

DROP TABLE IF EXISTS `domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domain` (
  `domain_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_label` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` smallint(6) NOT NULL DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_member`
--

DROP TABLE IF EXISTS `domain_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domain_member` (
  `domain_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`domain_id`,`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edit_page`
--

DROP TABLE IF EXISTS `edit_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edit_page` (
  `edit_page_id` smallint(4) NOT NULL DEFAULT '0',
  `edit_page_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_page_filename` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`edit_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `element`
--

DROP TABLE IF EXISTS `element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element` (
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `element_label` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `element_sort` smallint(6) NOT NULL DEFAULT '0',
  `edit_page_id` smallint(4) NOT NULL DEFAULT '0',
  `control_id` smallint(6) DEFAULT '0',
  `active` smallint(6) NOT NULL DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`element_id`),
  KEY `control_id` (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `message_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `target_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `target_type` smallint(6) NOT NULL DEFAULT '0',
  `message_type` smallint(6) NOT NULL DEFAULT '0',
  `message_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `target_id` (`target_id`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_name`
--

DROP TABLE IF EXISTS `meta_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_name` (
  `meta_name_id` smallint(6) NOT NULL,
  `meta_name_label` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`meta_name_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_item`
--

DROP TABLE IF EXISTS `news_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_item` (
  `news_item_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `author` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_posted` date NOT NULL DEFAULT '0000-00-00',
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_item_id`),
  KEY `slug` (`slug`(191))
) ENGINE=InnoDB AUTO_INCREMENT=1015 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `page_id` smallint(4) NOT NULL DEFAULT '0',
  `page_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_element`
--

DROP TABLE IF EXISTS `page_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_element` (
  `page_id` smallint(4) NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_element_sort` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`,`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program`
--

DROP TABLE IF EXISTS `program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `program_slug` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` smallint(6) NOT NULL DEFAULT '1',
  `bp_editor_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`program_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1317 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program_added`
--

DROP TABLE IF EXISTS `program_added`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_added` (
  `program_id` int(11) NOT NULL,
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `added_at` datetime DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`program_id`),
  CONSTRAINT `program_added_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program_attribute`
--

DROP TABLE IF EXISTS `program_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_attribute` (
  `program_id` int(11) NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `attribute_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`program_id`,`element_id`,`attribute_id`),
  KEY `program_id` (`program_id`),
  KEY `element_id` (`element_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program_meta`
--

DROP TABLE IF EXISTS `program_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_meta` (
  `program_meta_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `meta_name_id` smallint(5) NOT NULL DEFAULT '0',
  `meta_content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`program_meta_id`),
  KEY `program_id` (`program_id`),
  KEY `meta_name_id` (`meta_name_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program_text`
--

DROP TABLE IF EXISTS `program_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_text` (
  `program_id` int(11) NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `bp_editor_id` int(11) NOT NULL DEFAULT '99',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`program_id`,`element_id`),
  KEY `program_id` (`program_id`),
  KEY `element_id` (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section` (
  `section_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_label` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` smallint(6) NOT NULL DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `section_element`
--

DROP TABLE IF EXISTS `section_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section_element` (
  `section_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `element_id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`section_id`,`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'blueprintsprograms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-04 19:05:37
