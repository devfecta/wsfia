-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: wsfia
-- ------------------------------------------------------
-- Server version	5.7.27-0ubuntu0.19.04.1

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
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `businesses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `station` varchar(24) DEFAULT NULL,
  `streetAddress` varchar(64) NOT NULL,
  `city` varchar(24) NOT NULL,
  `state` tinyint(2) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `phone` varchar(14) NOT NULL,
  `url` varchar(256) DEFAULT NULL,
  `services` varchar(1024) DEFAULT NULL,
  `type` varchar(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES (1,'Fitchburg Fire','2','123 Test Road','Fitchburg',49,'53719','(608) 123-4567',NULL,NULL,'Combination'),(2,'Verona Fire','4','456 Test Circle','Verona',49,'53700','(608) 456-4567',NULL,NULL,'Volunteer'),(3,'Madison Fire','5','789 Test Street','Madison',49,'53704','(608) 789-4567',NULL,NULL,'Career'),(25,'Test School',NULL,'123 Test Lane','McFarland',49,'53725','(608) 654-4567',NULL,NULL,'None'),(32,'Test Company',NULL,'123 Test Avenue','Oregon',49,'53710','(608) 321-4567','www.test.com','Test Services','None');
/*!40000 ALTER TABLE `businesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lineItems`
--

DROP TABLE IF EXISTS `lineItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lineItems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `itemDescription` varchar(512) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orderId_fk` (`orderId`),
  CONSTRAINT `orderId_fk` FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lineItems`
--

LOCK TABLES `lineItems` WRITE;
/*!40000 ALTER TABLE `lineItems` DISABLE KEYS */;
INSERT INTO `lineItems` VALUES (1,1,1,'Description 1',3.99),(2,1,2,'Description 2',5.99),(3,2,2,'Description 2',5.99),(4,3,3,'Description 3',2.99);
/*!40000 ALTER TABLE `lineItems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` varchar(24) NOT NULL,
  `userId` int(11) NOT NULL,
  `jobTitle` varchar(48) DEFAULT NULL,
  `departments` json NOT NULL,
  `areas` json DEFAULT NULL,
  `expirationDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) DEFAULT '1',
  `sinceDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `studentId` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_user_fk` (`userId`),
  CONSTRAINT `member_user_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES ('WSFIA-13020312',130,'Fire Inspector','[{\"id\": \"32\", \"url\": \"www.test.com\", \"city\": \"Oregon\", \"name\": \"Test Company\", \"type\": \"None\", \"phone\": \"(608) 321-4567\", \"state\": \"49\", \"station\": null, \"zipcode\": \"53710\", \"services\": \"Test Services\", \"streetAddress\": \"123 Test Avenue\"}, {\"id\": \"25\", \"url\": null, \"city\": \"McFarland\", \"name\": \"Test School\", \"type\": \"None\", \"phone\": \"(608) 654-4567\", \"state\": \"49\", \"station\": null, \"zipcode\": \"53725\", \"services\": null, \"streetAddress\": \"123 Test Lane\"}]','[\"Area 1\", \"Area 2\", \"Area 7\", \"Area 11\"]','2020-12-31 00:00:00',1,'2020-03-12 00:11:44','std123'),('WSFIA-13120312',131,'Fire Inspector1','[{\"id\": \"2\", \"url\": null, \"city\": \"Verona\", \"name\": \"Verona Fire\", \"type\": \"Volunteer\", \"phone\": \"(608) 456-4567\", \"state\": \"49\", \"station\": \"4\", \"zipcode\": \"53700\", \"services\": null, \"streetAddress\": \"456 Test Circle\"}]','[\"Area 2\"]','2020-12-31 00:00:00',1,'2020-03-12 00:14:36',''),('WSFIA-13220312',132,'Fire Inspector2','[{\"id\": \"25\", \"url\": null, \"city\": \"McFarland\", \"name\": \"Test School\", \"type\": \"None\", \"phone\": \"(608) 654-4567\", \"state\": \"49\", \"station\": null, \"zipcode\": \"53725\", \"services\": null, \"streetAddress\": \"123 Test Lane\"}]','[\"Area 1\", \"Area 11\"]','2020-12-31 00:00:00',1,'2020-03-12 00:14:36','std123');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderAddOns`
--

DROP TABLE IF EXISTS `orderAddOns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderAddOns` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `description` varchar(64) NOT NULL,
  `price` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderAddOns`
--

LOCK TABLES `orderAddOns` WRITE;
/*!40000 ALTER TABLE `orderAddOns` DISABLE KEYS */;
/*!40000 ALTER TABLE `orderAddOns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderOptions`
--

DROP TABLE IF EXISTS `orderOptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderOptions` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `type` varchar(16) NOT NULL,
  `description` varchar(64) NOT NULL,
  `inventory` tinyint(2) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderOptions`
--

LOCK TABLES `orderOptions` WRITE;
/*!40000 ALTER TABLE `orderOptions` DISABLE KEYS */;
INSERT INTO `orderOptions` VALUES (1,'Membership','WSFIA Membership',0,40.00),(2,'Conference','WSFIA Conference Registration (1 Day)',0,225.00),(3,'Conference','WSFIA Conference Registration (2 Day)',0,265.00),(4,'Conference','WSFIA Conference Registration (3-4 Day)',0,300.00),(5,'Conference','WSFIA Conference Vendor Night',0,18.00),(6,'Conference','WSFIA Conference Banquet',0,25.00);
/*!40000 ALTER TABLE `orderOptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionId` varchar(64) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `orderDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'123abc',1,0,'2020-02-05 23:19:25'),(2,'456abc',2,1,'2020-02-05 23:19:25'),(3,'789abc',1,1,'2020-02-05 23:19:25');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `speakers`
--

DROP TABLE IF EXISTS `speakers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `speakers` (
  `id` varchar(24) NOT NULL,
  `userId` int(11) NOT NULL,
  `jobTitle` varchar(48) DEFAULT NULL,
  `companies` varchar(32) NOT NULL,
  `bio` varchar(4096) DEFAULT NULL,
  `photo` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `speaker_user_fk` (`userId`),
  CONSTRAINT `speaker_user_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `speakers`
--

LOCK TABLES `speakers` WRITE;
/*!40000 ALTER TABLE `speakers` DISABLE KEYS */;
/*!40000 ALTER TABLE `speakers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `stateId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `stateAbbreviation` varchar(2) NOT NULL,
  `stateName` varchar(30) NOT NULL,
  PRIMARY KEY (`stateId`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,'AL','Alabama'),(2,'AK','Alaska'),(3,'AZ','Arizona'),(4,'AR','Arkansas'),(5,'CA','California'),(6,'CO','Colorado'),(7,'CT','Connecticut'),(8,'DE','Delaware'),(9,'FL','Florida'),(10,'GA','Georgia'),(11,'HI','Hawaii'),(12,'ID','Idaho'),(13,'IL','Illinois'),(14,'IN','Indiana'),(15,'IA','Iowa'),(16,'KS','Kansas'),(17,'KY','Kentucky'),(18,'LA','Louisiana'),(19,'ME','Maine'),(20,'MD','Maryland'),(21,'MA','Massachusetts'),(22,'MI','Michigan'),(23,'MN','Minnesota'),(24,'MS','Mississippi'),(25,'MO','Missouri'),(26,'MT','Montana'),(27,'NE','Nebraska'),(28,'NV','Nevada'),(29,'NH','New Hampshire'),(30,'NJ','New Jersey'),(31,'NM','New Mexico'),(32,'NY','New York'),(33,'NC','North Carolina'),(34,'ND','North Dakota'),(35,'OH','Ohio'),(36,'OK','Oklahoma'),(37,'OR','Oregon'),(38,'PA','Pennsylvania'),(39,'RI','Rhode Island'),(40,'SC','South Carolina'),(41,'SD','South Dakota'),(42,'TN','Tennessee'),(43,'TX','Texas'),(44,'UT','Utah'),(45,'VT','Vermont'),(46,'VA','Virginia'),(47,'WA','Washington'),(48,'WV','West Virginia'),(49,'WI','Wisconsin'),(50,'WY','Wyoming');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `statusId` int(11) NOT NULL AUTO_INCREMENT,
  `statusName` varchar(16) NOT NULL,
  PRIMARY KEY (`statusId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'Active'),(2,'Awaiting Payment'),(3,'Suspended'),(4,'Deceased');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userSessions`
--

DROP TABLE IF EXISTS `userSessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userSessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionId` varchar(256) NOT NULL,
  `registration` json NOT NULL,
  `registrationDate` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userSessions`
--

LOCK TABLES `userSessions` WRITE;
/*!40000 ALTER TABLE `userSessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `userSessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` json NOT NULL,
  `password` varchar(256) NOT NULL,
  `firstName` varchar(24) NOT NULL,
  `lastName` varchar(24) NOT NULL,
  `emailAddress` varchar(128) NOT NULL,
  `lastLoginDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (130,'{\"2\": \"Member\"}','$2y$10$GaIq6BRClnEYjq.UnR75XOw4MpEsiIW8ky71/c0MMmDOIhqncYk.m','FirstName','LastName','testing@wsfia.org','2020-03-12 00:11:44'),(131,'{\"2\": \"Member\"}','$2y$10$Jap4OQ8iBh0Jbj.k3qY6k.BcqmPVab.DhcUmD7JVM.2PAOeyh.81a','FirstName1','LastName1','testing1@wsfia.org','2020-03-12 00:14:36'),(132,'{\"2\": \"Member\"}','$2y$10$72rpBzpIHzjmIVuz6bbrMufcijt5a3DDCWKNEqASnZX/LnNkP0p4a','FirstName2','LastName2','testing2@wsfia.org','2020-03-12 00:14:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendors`
--

DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendors` (
  `id` varchar(24) NOT NULL,
  `userId` int(11) NOT NULL,
  `jobTitle` varchar(48) DEFAULT NULL,
  `companies` varchar(32) NOT NULL,
  `representatives` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_user_fk` (`userId`),
  CONSTRAINT `vendor_user_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendors`
--

LOCK TABLES `vendors` WRITE;
/*!40000 ALTER TABLE `vendors` DISABLE KEYS */;
/*!40000 ALTER TABLE `vendors` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-16 18:06:34
