/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_aka` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `aka_type` int(11) DEFAULT NULL,
  `aka_id` int(11) NOT NULL,
  `aka_alias` varchar(200) NOT NULL,
  `aka_created` int(14) DEFAULT NULL,
  `aka_lastupdated` int(14) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `aka_id` (`aka_id`,`aka_alias`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_zbs_aka` (`ID`, `aka_type`, `aka_id`, `aka_alias`, `aka_created`, `aka_lastupdated`) VALUES (1,1,2,'anuraghandique.dev@gmail.com',1757010381,1757010381);
INSERT INTO `fqsi_zbs_aka` (`ID`, `aka_type`, `aka_id`, `aka_alias`, `aka_created`, `aka_lastupdated`) VALUES (2,1,10,'sxqrscym6@mozmail.com',1757010385,1757010385);
