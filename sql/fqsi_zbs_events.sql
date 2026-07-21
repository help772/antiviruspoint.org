/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_events` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbse_title` varchar(255) DEFAULT NULL,
  `zbse_desc` longtext DEFAULT NULL,
  `zbse_start` int(14) NOT NULL,
  `zbse_end` int(14) NOT NULL,
  `zbse_complete` tinyint(1) NOT NULL DEFAULT -1,
  `zbse_show_on_portal` tinyint(1) NOT NULL DEFAULT -1,
  `zbse_show_on_cal` tinyint(1) NOT NULL DEFAULT -1,
  `zbse_created` int(14) NOT NULL,
  `zbse_lastupdated` int(14) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `title` (`zbse_title`),
  KEY `startint` (`zbse_start`),
  KEY `endint` (`zbse_end`),
  KEY `created` (`zbse_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
