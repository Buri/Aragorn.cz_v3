CREATE DATABASE  IF NOT EXISTS `aragorncz01` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `aragorncz01`;
-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: aragorncz01
-- ------------------------------------------------------
-- Server version	5.5.31-1~dotdeb.0-log

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
-- Table structure for table `3_chat_users`
--

DROP TABLE IF EXISTS `3_chat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_users` (
  `uid` smallint(5) unsigned NOT NULL,
  `rid` smallint(5) unsigned NOT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `odesel` tinyint(2) NOT NULL DEFAULT '0',
  `prava` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `uid_rid` (`uid`,`rid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_help`
--

DROP TABLE IF EXISTS `3_help`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_help` (
  `uid` smallint(6) unsigned NOT NULL,
  `keywords` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_admin`
--

DROP TABLE IF EXISTS `3_chat_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_admin` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `uid` smallint(6) DEFAULT '0',
  `typ` smallint(6) DEFAULT '0',
  `cas` int(11) unsigned DEFAULT '0',
  `adminid` smallint(5) unsigned DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`,`typ`)
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_post_new`
--

DROP TABLE IF EXISTS `3_post_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_post_new` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parent` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `mid` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `whis` varchar(255) DEFAULT NULL,
  `whisstav` varchar(50) DEFAULT NULL,
  `cas` int(10) unsigned NOT NULL DEFAULT '0',
  `stavfrom` enum('1','3') NOT NULL DEFAULT '1',
  `stavto` enum('0','1','3') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `stavto` (`stavto`),
  KEY `tid` (`tid`),
  KEY `stavfrom` (`stavfrom`),
  KEY `fid` (`fid`),
  KEY `mid` (`mid`)
) ENGINE=MyISAM AUTO_INCREMENT=888072 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC COMMENT='data zprav posty';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_cave_mess`
--

DROP TABLE IF EXISTS `3_cave_mess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_cave_mess` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `komu` varchar(255) NOT NULL DEFAULT '',
  `komuText` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `komu` (`cid`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=171102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_galerie`
--

DROP TABLE IF EXISTS `3_galerie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_galerie` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(80) NOT NULL,
  `nazev_rew` varchar(80) NOT NULL,
  `autor` smallint(5) unsigned NOT NULL DEFAULT '0',
  `source` varchar(80) NOT NULL,
  `thumb` varchar(80) NOT NULL,
  `x` smallint(5) unsigned NOT NULL DEFAULT '0',
  `y` smallint(5) unsigned NOT NULL DEFAULT '0',
  `schvalenotime` int(10) unsigned NOT NULL DEFAULT '0',
  `odeslanotime` int(10) unsigned NOT NULL DEFAULT '0',
  `schvaleno` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `popis` text NOT NULL,
  `hodnoceni` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `hodnotilo` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`schvaleno`,`schvalenotime`,`autor`,`nazev_rew`,`nazev`,`thumb`,`id`),
  KEY `nazev_rew` (`schvaleno`,`nazev_rew`),
  KEY `autor` (`autor`),
  KEY `id_2` (`id`,`nazev`,`nazev_rew`,`thumb`),
  KEY `schvaleno` (`schvaleno`,`schvalenotime`)
) ENGINE=MyISAM AUTO_INCREMENT=2767 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_postava_orp`
--

DROP TABLE IF EXISTS `3_herna_postava_orp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_postava_orp` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ico` varchar(50) DEFAULT NULL,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `jmeno` varchar(80) NOT NULL,
  `jmeno_rew` varchar(80) NOT NULL,
  `zivotopis` text,
  `popis` text NOT NULL,
  `atributy` text,
  `by_pj` mediumtext,
  `inventar` text,
  `kouzla` text,
  `aktivita` int(11) unsigned NOT NULL DEFAULT '0',
  `schvaleno` enum('0','1') NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `schvaleno` (`schvaleno`,`cid`,`uid`),
  KEY `jmeno_rew` (`jmeno_rew`),
  KEY `cid` (`cid`,`uid`),
  KEY `jmeno` (`cid`,`jmeno`,`jmeno_rew`,`uid`),
  KEY `uid` (`uid`,`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=21668 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_sekce_prava`
--

DROP TABLE IF EXISTS `3_sekce_prava`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_sekce_prava` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `prava` tinyint(3) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `uid_2` (`uid`,`sid`,`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_users_settings`
--

DROP TABLE IF EXISTS `3_users_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_users_settings` (
  `uid` smallint(5) unsigned NOT NULL,
  `serialized` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_maps`
--

DROP TABLE IF EXISTS `3_herna_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_maps` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `size` int(11) NOT NULL DEFAULT '0',
  `povrch` varchar(255) NOT NULL DEFAULT '',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `soubor` varchar(255) NOT NULL DEFAULT '',
  `datas` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=4857 DEFAULT CHARSET=utf8 COMMENT='mapy, soubor=js, kdyz je JSmade';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_4`
--

DROP TABLE IF EXISTS `3_comm_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_4` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `whispering` varchar(255) DEFAULT NULL,
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid_2` (`uid`),
  KEY `aid` (`aid`,`uid`,`whispering`)
) ENGINE=MyISAM AUTO_INCREMENT=1043831 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_cave_users`
--

DROP TABLE IF EXISTS `3_cave_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_cave_users` (
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `login` varchar(80) NOT NULL DEFAULT '',
  `login_rew` varchar(80) NOT NULL DEFAULT '',
  `jmeno` varchar(80) NOT NULL DEFAULT '',
  `jmeno_rew` varchar(80) NOT NULL DEFAULT '',
  `pozice` char(1) NOT NULL DEFAULT 'g',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `cid_2` (`cid`,`uid`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `cid_3` (`cid`,`login_rew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_2`
--

DROP TABLE IF EXISTS `3_comm_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_2` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=30523 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_sets_open`
--

DROP TABLE IF EXISTS `3_herna_sets_open`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_sets_open` (
  `cid` int(11) NOT NULL DEFAULT '0',
  `struktura` text,
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Struktura atributu OPEN systemu';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_filters`
--

DROP TABLE IF EXISTS `3_chat_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_filters` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) unsigned DEFAULT NULL,
  `rid` smallint(5) unsigned DEFAULT NULL,
  `fid` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_ankety_data`
--

DROP TABLE IF EXISTS `3_ankety_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_ankety_data` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ank_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hlas` tinyint(3) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `ank_id` (`ank_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_kouzla`
--

DROP TABLE IF EXISTS `3_herna_kouzla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_kouzla` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `typ` enum('h','k') NOT NULL DEFAULT 'h',
  `nazev` varchar(80) NOT NULL DEFAULT '',
  `popis` text NOT NULL,
  `magy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  KEY `id` (`id`),
  KEY `typ` (`typ`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_diskuze_groups`
--

DROP TABLE IF EXISTS `3_diskuze_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_diskuze_groups` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(40) NOT NULL DEFAULT '',
  `nazev_rew` varchar(40) NOT NULL DEFAULT '',
  `popis` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `nazev` (`nazev`),
  KEY `nazev_rew` (`nazev_rew`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 PACK_KEYS=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_long_login`
--

DROP TABLE IF EXISTS `3_long_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_long_login` (
  `nick` varchar(40) CHARACTER SET ascii NOT NULL,
  `hash` char(40) CHARACTER SET ascii NOT NULL,
  `chck` char(40) CHARACTER SET ascii NOT NULL,
  UNIQUE KEY `nick` (`nick`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_kalendar`
--

DROP TABLE IF EXISTS `3_kalendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_kalendar` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `public` smallint(6) unsigned NOT NULL,
  `uid` smallint(6) unsigned NOT NULL,
  `weekly` smallint(6) DEFAULT NULL,
  `yearly` smallint(6) DEFAULT NULL,
  `monthly` smallint(6) DEFAULT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `nazev` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `text` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_votes`
--

DROP TABLE IF EXISTS `3_chat_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `vote` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `uidrid` (`uid`,`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_3`
--

DROP TABLE IF EXISTS `3_comm_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_3` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=548061 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_roz_xp_stats`
--

DROP TABLE IF EXISTS `3_roz_xp_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_roz_xp_stats` (
  `uid` int(11) NOT NULL COMMENT 'unikátní identifikátor uživatele',
  `m0` tinyint(4) NOT NULL COMMENT 'počet hodnocení 0 body xp',
  `m1` tinyint(4) NOT NULL COMMENT 'počet hodnocení 1 bodem xp',
  `m2` tinyint(4) NOT NULL COMMENT 'počet hodnocení 2 body xp',
  `m3` tinyint(4) NOT NULL COMMENT 'počet hodnocení 3 body xp',
  `m4` tinyint(4) NOT NULL COMMENT 'počet hodnocení 4 body xp',
  `m5` tinyint(4) NOT NULL COMMENT 'počet hodnocení 5 body xp',
  `level` set('0','1','2','3','4','5') COLLATE utf8_czech_ci NOT NULL COMMENT 'medián použitý jako level uživatele',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'poslední aktualizace xp',
  `sum` smallint(6) NOT NULL COMMENT 'celkový počet získaných xp',
  UNIQUE KEY `uid` (`uid`),
  KEY `level` (`level`),
  KEY `sum` (`sum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Statistika xp rozcestí';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_post_text`
--

DROP TABLE IF EXISTS `3_post_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_post_text` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `compressed` enum('0','1') NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `hash` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`(8))
) ENGINE=MyISAM AUTO_INCREMENT=710067 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC COMMENT='texty zprav posty';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_1_texts`
--

DROP TABLE IF EXISTS `3_comm_1_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_1_texts` (
  `text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text_content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11554 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_users_about`
--

DROP TABLE IF EXISTS `3_users_about`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_users_about` (
  `uid` smallint(5) unsigned NOT NULL,
  `about_me` text NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_save_data`
--

DROP TABLE IF EXISTS `3_chat_save_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_save_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromId` int(10) unsigned NOT NULL DEFAULT '0',
  `toId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeStart` int(10) unsigned NOT NULL DEFAULT '0',
  `timeEnd` int(10) unsigned NOT NULL DEFAULT '0',
  `aktivni` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `rid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `aktivni` (`aktivni`,`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_1`
--

DROP TABLE IF EXISTS `3_comm_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_1` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=11554 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_postava_drd`
--

DROP TABLE IF EXISTS `3_herna_postava_drd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_postava_drd` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ico` varchar(40) DEFAULT NULL,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `jmeno` varchar(80) NOT NULL,
  `jmeno_rew` varchar(80) NOT NULL,
  `rasa` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `povolani` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `presvedceni` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `uroven` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xp` int(10) unsigned NOT NULL DEFAULT '0',
  `sila` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `obratnost` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `odolnost` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `inteligence` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `charisma` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `vyska` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vaha` smallint(5) unsigned NOT NULL DEFAULT '0',
  `zivoty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `zivoty_max` smallint(5) unsigned NOT NULL DEFAULT '0',
  `magy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `magy_max` smallint(5) unsigned NOT NULL DEFAULT '0',
  `penize` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `upravy_pjem` text,
  `schopnosti` text,
  `zivotopis` mediumtext,
  `popis` text NOT NULL,
  `inventar` text,
  `kouzla` text,
  `aktivita` int(10) unsigned NOT NULL DEFAULT '0',
  `schvaleno` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `schvaleno` (`schvaleno`,`cid`,`uid`),
  KEY `jmeno_rew` (`jmeno_rew`),
  KEY `uid` (`uid`,`cid`),
  KEY `cid` (`cid`,`uid`),
  KEY `jmeno` (`cid`,`jmeno`,`jmeno_rew`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=21457 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_ankety`
--

DROP TABLE IF EXISTS `3_ankety`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_ankety` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `aktiv` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `otazka` varchar(255) NOT NULL DEFAULT '',
  `dis` smallint(6) NOT NULL DEFAULT '0',
  `odpoved` text NOT NULL,
  `counts` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dis` (`dis`,`aktiv`)
) ENGINE=MyISAM AUTO_INCREMENT=2537 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_notes`
--

DROP TABLE IF EXISTS `3_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_notes` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text COLLATE utf8_czech_ci NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci PACK_KEYS=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_all`
--

DROP TABLE IF EXISTS `3_herna_all`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_all` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `spendlik` tinyint(3) unsigned DEFAULT '0',
  `uid` smallint(6) NOT NULL DEFAULT '0',
  `ico` varchar(40) NOT NULL DEFAULT '',
  `typ` int(1) NOT NULL DEFAULT '0',
  `subtyp` tinyint(4) NOT NULL DEFAULT '0',
  `nazev` varchar(80) NOT NULL DEFAULT '',
  `nazev_rew` varchar(80) NOT NULL DEFAULT '',
  `popis` text,
  `keywords` varchar(255) DEFAULT NULL,
  `pro_adminy` text,
  `nastenka` text,
  `poznamky` text,
  `hraci_pocet` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `hraci_hleda` text,
  `obchod` char(1) NOT NULL DEFAULT '1',
  `shoped` text NOT NULL,
  `povolreg` tinyint(1) NOT NULL DEFAULT '1',
  `aktivita` int(10) unsigned NOT NULL DEFAULT '0',
  `aktivitapj` int(10) unsigned NOT NULL DEFAULT '0',
  `zalozeno` int(10) unsigned NOT NULL DEFAULT '0',
  `ohrozeni` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `schvaleno` tinyint(1) NOT NULL DEFAULT '0',
  `kdoschvalil` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nazev_rew_all` (`id`,`nazev`,`nazev_rew`,`uid`),
  KEY `nazev_rew_2` (`nazev_rew`,`id`),
  KEY `schvaleno` (`schvaleno`,`nazev_rew`),
  KEY `spendlik` (`spendlik`),
  KEY `nazev_rew` (`schvaleno`,`typ`,`nazev_rew`),
  KEY `uid` (`uid`),
  FULLTEXT KEY `keywords` (`keywords`,`nazev`,`popis`,`hraci_hleda`)
) ENGINE=MyISAM AUTO_INCREMENT=9335 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_challenges`
--

DROP TABLE IF EXISTS `3_challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_challenges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `used_r` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=49495 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_mess`
--

DROP TABLE IF EXISTS `3_chat_mess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_mess` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) unsigned DEFAULT NULL,
  `rid` tinyint(3) unsigned DEFAULT NULL,
  `wh` smallint(11) unsigned NOT NULL DEFAULT '0',
  `login_from` varchar(80) NOT NULL DEFAULT '',
  `login_to` varchar(80) NOT NULL DEFAULT '',
  `time` int(10) unsigned DEFAULT NULL,
  `text` text,
  `color` char(12) NOT NULL DEFAULT 'white',
  `type` smallint(4) unsigned NOT NULL DEFAULT '0',
  `special` smallint(5) unsigned NOT NULL DEFAULT '0',
  `special2` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rid_2` (`rid`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=639848 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_clanky`
--

DROP TABLE IF EXISTS `3_clanky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_clanky` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(80) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `nazev_rew` varchar(80) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `autor` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sekce` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL DEFAULT '0',
  `anotace` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `admins` text,
  `schvaleno` tinyint(1) NOT NULL DEFAULT '0',
  `schvalenotime` int(10) unsigned NOT NULL DEFAULT '0',
  `odeslanotime` int(10) unsigned NOT NULL DEFAULT '0',
  `hodnoceni` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `hodnotilo` smallint(6) unsigned NOT NULL DEFAULT '0',
  `kdoschvalil` smallint(5) unsigned NOT NULL DEFAULT '0',
  `compressed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `autor` (`autor`),
  KEY `schvaleno` (`schvaleno`),
  KEY `id` (`nazev_rew`,`nazev`,`id`),
  KEY `schvalenotime` (`schvaleno`,`schvalenotime`),
  KEY `sekce` (`schvaleno`,`sekce`,`schvalenotime`)
) ENGINE=MyISAM AUTO_INCREMENT=2364 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_rooms`
--

DROP TABLE IF EXISTS `3_chat_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(40) NOT NULL DEFAULT '',
  `popis` varchar(255) DEFAULT NULL,
  `locked` char(1) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '0',
  `category` enum('','Fantasy','Sci-fi') NOT NULL,
  `staticka` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `elite` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `saving` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vote_uid` int(11) NOT NULL,
  `vote_situation` int(11) DEFAULT NULL,
  `vote_time` int(11) NOT NULL,
  `need_admin` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `saving` (`saving`),
  KEY `nazev` (`nazev`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_2_texts`
--

DROP TABLE IF EXISTS `3_comm_2_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_2_texts` (
  `text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text_content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30523 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_pj`
--

DROP TABLE IF EXISTS `3_herna_pj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_pj` (
  `cid` smallint(5) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `ico` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `nastenka` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `poznamky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mapy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `postavy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `obchod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `prispevky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktivita` int(10) unsigned NOT NULL DEFAULT '0',
  `schvaleno` tinyint(1) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `cid` (`cid`,`uid`),
  KEY `schvaleno` (`schvaleno`,`cid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_visited_4`
--

DROP TABLE IF EXISTS `3_visited_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_visited_4` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bookmark` tinyint(1) NOT NULL DEFAULT '0',
  `lastid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `news` mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY `uid_2` (`uid`),
  KEY `uid_aid_sid_bkmrk_lid` (`bookmark`,`uid`,`aid`,`lastid`),
  KEY `aid` (`aid`),
  KEY `bookmark` (`bookmark`,`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_visited_2`
--

DROP TABLE IF EXISTS `3_visited_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_visited_2` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bookmark` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `news` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `bookmark` (`bookmark`),
  KEY `uid_aid_sid_bkmrk_lid` (`bookmark`,`uid`,`aid`,`lastid`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_visited_3`
--

DROP TABLE IF EXISTS `3_visited_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_visited_3` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bookmark` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `news` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `uid_aid_sid_bkmrk_lid` (`bookmark`,`uid`,`aid`,`lastid`),
  KEY `aid` (`aid`),
  KEY `lastid` (`lastid`,`aid`),
  KEY `bookmark` (`bookmark`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_visited_1`
--

DROP TABLE IF EXISTS `3_visited_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_visited_1` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bookmark` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `news` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `bookmark` (`bookmark`),
  KEY `uid_2` (`uid`),
  KEY `uid_aid_sid_bkmrk_lid` (`bookmark`,`uid`,`aid`,`lastid`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_u_comm`
--

DROP TABLE IF EXISTS `3_u_comm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_u_comm` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  UNIQUE KEY `uid_2` (`uid`,`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_diskuze_prava`
--

DROP TABLE IF EXISTS `3_diskuze_prava`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_diskuze_prava` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_dis` smallint(5) unsigned NOT NULL DEFAULT '0',
  `prava` enum('reader','writer','moderator','admin','hide') NOT NULL DEFAULT 'reader',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_user` (`id_user`,`id_dis`,`prava`)
) ENGINE=MyISAM AUTO_INCREMENT=2928 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vilda`
--

DROP TABLE IF EXISTS `vilda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vilda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(40) COLLATE utf8_czech_ci DEFAULT NULL,
  `autor` varchar(40) COLLATE utf8_czech_ci DEFAULT NULL,
  `anotace` text COLLATE utf8_czech_ci,
  `time` int(11) DEFAULT NULL,
  `text` mediumtext COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_rating`
--

DROP TABLE IF EXISTS `3_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_rating` (
  `uid` smallint(5) unsigned NOT NULL,
  `aid` smallint(5) unsigned NOT NULL,
  `sid` enum('1','2') NOT NULL,
  `rate` decimal(4,2) DEFAULT '0.00',
  UNIQUE KEY `uid` (`sid`,`uid`,`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_users`
--

DROP TABLE IF EXISTS `3_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_users` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `login_rew` varchar(30) NOT NULL,
  `pass` varchar(40) DEFAULT NULL,
  `mail` varchar(80) NOT NULL,
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ico` varchar(20) NOT NULL DEFAULT 'default.jpg',
  `name` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `icq` varchar(20) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT '0',
  `login_count` smallint(5) unsigned DEFAULT '0',
  `reg_code` int(11) unsigned DEFAULT '0',
  `account_created` int(10) unsigned DEFAULT '0',
  `bonus_created` int(10) unsigned DEFAULT '0',
  `bonus_expired` int(10) unsigned DEFAULT '0',
  `informed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `chat_color` varchar(20) NOT NULL DEFAULT 'white',
  `chat_ref` tinyint(2) unsigned DEFAULT '15',
  `chat_time` enum('0','1') NOT NULL DEFAULT '0',
  `chat_order` enum('desc','asc') NOT NULL DEFAULT 'desc',
  `chat_font` tinyint(2) unsigned DEFAULT '12',
  `chat_sys` enum('0','1') DEFAULT '1',
  `chat_warn_roz` enum('0','1') NOT NULL DEFAULT '1',
  `chat_warn_ajax` enum('0','1') NOT NULL DEFAULT '1',
  `chat_warn_other` enum('0','1') NOT NULL DEFAULT '1',
  `roz_name` varchar(40) DEFAULT NULL,
  `roz_popis` varchar(255) DEFAULT NULL,
  `roz_ico` varchar(30) DEFAULT NULL,
  `roz_exp` tinyint(2) NOT NULL DEFAULT '0',
  `set_titles` enum('0','1') NOT NULL DEFAULT '0',
  `roz_pj` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `reg_code` (`reg_code`),
  KEY `roz_exp` (`roz_exp`),
  KEY `timestamp` (`timestamp`,`login_rew`),
  KEY `online` (`online`,`timestamp`),
  KEY `id` (`id`,`login_rew`,`login`,`level`,`ico`,`chat_color`),
  KEY `login_rew` (`login_rew`,`login`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM AUTO_INCREMENT=32349 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Zaznamy uzivatelu pro system';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_admin_blog`
--

DROP TABLE IF EXISTS `3_admin_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_admin_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `headline` text,
  `anotace` text NOT NULL,
  `content` text,
  `vydano` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=218 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_4_texts`
--

DROP TABLE IF EXISTS `3_comm_4_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_4_texts` (
  `text_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `text_content` text COLLATE utf8_czech_ci NOT NULL,
  `text_whisText` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1043917 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_ban`
--

DROP TABLE IF EXISTS `3_ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_ban` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned DEFAULT NULL,
  `fid` smallint(5) unsigned DEFAULT '0',
  `time` int(10) unsigned DEFAULT NULL,
  `assignedin` int(10) unsigned DEFAULT NULL,
  `reason` text,
  `ipe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ipe` (`ipe`)
) ENGINE=MyISAM AUTO_INCREMENT=1620 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_post`
--

DROP TABLE IF EXISTS `3_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_post` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `tid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `oid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `r` enum('0','1') NOT NULL DEFAULT '0',
  `text` text,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oid_2` (`oid`,`r`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='pridavani ukonceno Auto_Indexem 143 049';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_bestie`
--

DROP TABLE IF EXISTS `3_herna_bestie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_bestie` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `popis` text NOT NULL,
  `ziv` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `uc` tinyint(4) NOT NULL DEFAULT '0',
  `oc` tinyint(4) NOT NULL DEFAULT '0',
  `od` tinyint(4) NOT NULL DEFAULT '0',
  `vel` varchar(10) NOT NULL DEFAULT '',
  `boj` tinyint(4) NOT NULL DEFAULT '0',
  `zr` varchar(255) NOT NULL DEFAULT '',
  `it` tinyint(4) NOT NULL DEFAULT '0',
  `pok` smallint(6) NOT NULL DEFAULT '0',
  `zk` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_roz_situace`
--

DROP TABLE IF EXISTS `3_roz_situace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_roz_situace` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category` enum('','Fantasy','Sci-fi') NOT NULL DEFAULT 'Fantasy',
  `nazev` varchar(255) DEFAULT NULL,
  `nadrazena` smallint(5) unsigned DEFAULT NULL,
  `popis` text,
  `lastedit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nadrazena` (`nadrazena`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=376 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_friends`
--

DROP TABLE IF EXISTS `3_friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_friends` (
  `uid` smallint(6) unsigned NOT NULL,
  `fid` smallint(6) unsigned NOT NULL,
  KEY `uid` (`uid`),
  KEY `fid` (`fid`),
  KEY `uid_2` (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_diskuze_topics`
--

DROP TABLE IF EXISTS `3_diskuze_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_diskuze_topics` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `okruh` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `nazev` varchar(60) NOT NULL DEFAULT '',
  `nazev_rew` varchar(60) NOT NULL DEFAULT '',
  `owner` smallint(5) unsigned NOT NULL DEFAULT '0',
  `popis` varchar(255) NOT NULL DEFAULT '',
  `nastenka` blob,
  `nastenka_compressed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `schvaleno` enum('1','0') NOT NULL DEFAULT '0',
  `schvalenotime` int(10) unsigned NOT NULL DEFAULT '0',
  `closed` enum('0','1') NOT NULL DEFAULT '0',
  `prava_reg` enum('read','write','hide') NOT NULL DEFAULT 'write',
  `prava_guest` enum('read','hide') NOT NULL DEFAULT 'read',
  PRIMARY KEY (`id`),
  KEY `nazev_rew` (`schvaleno`,`okruh`,`nazev_rew`,`nazev`,`owner`),
  KEY `schvaleno` (`schvaleno`,`nazev_rew`),
  KEY `id` (`id`,`nazev`,`nazev_rew`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM AUTO_INCREMENT=1675 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_roz_xp`
--

DROP TABLE IF EXISTS `3_roz_xp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_roz_xp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unikátní identifikátor položky v tabulce',
  `player` int(11) NOT NULL COMMENT 'unikátní identifikátor uživatele, kterému xp patří',
  `count` tinyint(4) NOT NULL COMMENT 'počet xp, které bylo přiřazeno',
  `admin` varchar(30) NOT NULL COMMENT 'login uživatele, který xp přiřadil',
  `comment` varchar(255) DEFAULT NULL COMMENT 'důvod, proč bylo xp přiřazeno',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'časové razítko udělení xp',
  PRIMARY KEY (`id`),
  KEY `player` (`player`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Obsahuje záznamy o přidávání xp';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_items`
--

DROP TABLE IF EXISTS `3_herna_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_items` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `typ` enum('i','s','w','z','a') NOT NULL,
  `hands` tinyint(4) NOT NULL DEFAULT '0',
  `nazev` varchar(60) NOT NULL DEFAULT '',
  `popis` text NOT NULL,
  `cena` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `sila` tinyint(4) DEFAULT '0',
  `obrana` tinyint(4) DEFAULT '0',
  `oprava` varchar(255) DEFAULT NULL,
  `vaha` smallint(5) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `typ` (`typ`)
) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_save_text`
--

DROP TABLE IF EXISTS `3_chat_save_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_save_text` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mid` int(10) unsigned NOT NULL DEFAULT '0',
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `cas` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`,`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_comm_3_texts`
--

DROP TABLE IF EXISTS `3_comm_3_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_comm_3_texts` (
  `text_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `text_content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=548061 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_admin_prava`
--

DROP TABLE IF EXISTS `3_admin_prava`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_admin_prava` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) unsigned NOT NULL,
  `ban` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blog` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `clanky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `galerie` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `herna` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hernaneakt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `chat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `doplnky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ankety` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reklamy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `redaktorina` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_chat_advert`
--

DROP TABLE IF EXISTS `3_chat_advert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_chat_advert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text CHARACTER SET utf8 NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cykle` int(11) NOT NULL DEFAULT '0',
  `last` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_ajax_chat`
--

DROP TABLE IF EXISTS `3_ajax_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_ajax_chat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `room` smallint(5) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned DEFAULT NULL,
  `serialized` blob,
  PRIMARY KEY (`id`),
  KEY `room` (`room`),
  KEY `time` (`time`)
) ENGINE=InnoDB AUTO_INCREMENT=118972 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `3_herna_poznamky`
--

DROP TABLE IF EXISTS `3_herna_poznamky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `3_herna_poznamky` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_postava` int(11) DEFAULT NULL,
  `poznamka` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5359 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'aragorncz01'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-27 22:54:42
