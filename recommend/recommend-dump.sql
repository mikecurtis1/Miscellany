-- MySQL dump 10.13  Distrib 5.1.61, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: recommend
-- ------------------------------------------------------
-- Server version	5.1.61-0ubuntu0.11.04.1

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
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `items_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person` varchar(6) NOT NULL DEFAULT '??????',
  `item` varchar(13) NOT NULL DEFAULT '#############',
  `rating` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `items_id` (`items_id`),
  KEY `items_id_2` (`items_id`),
  KEY `person` (`person`),
  KEY `item` (`item`),
  KEY `rating` (`rating`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'a','1',NULL),(2,'a','2',NULL),(3,'a','3',NULL),(4,'b','1',NULL),(5,'b','2',NULL),(6,'b','4',NULL),(7,'c','1',NULL),(8,'c','5',NULL),(9,'c','6',NULL),(10,'d','1',NULL),(11,'d','2',NULL),(12,'d','3',NULL),(13,'e','7',NULL),(14,'f','1',NULL),(15,'g','1',NULL),(16,'g','8',NULL),(17,'h','9',NULL),(18,'h','10',NULL),(19,'h','11',NULL),(20,'h','12',NULL),(21,'h','13',NULL),(22,'h','14',NULL),(23,'h','15',NULL),(24,'h','16',NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-12 14:24:27
