/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_tracking` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbst_contactid` int(11) NOT NULL,
  `zbst_action` varchar(50) NOT NULL,
  `zbst_action_detail` longtext NOT NULL,
  `zbst_referrer` varchar(300) NOT NULL,
  `zbst_utm_source` varchar(200) NOT NULL,
  `zbst_utm_medium` varchar(200) NOT NULL,
  `zbst_utm_name` varchar(200) NOT NULL,
  `zbst_utm_term` varchar(200) NOT NULL,
  `zbst_utm_content` varchar(200) NOT NULL,
  `zbst_created` int(14) NOT NULL,
  `zbst_lastupdated` int(14) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
