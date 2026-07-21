/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_segments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbsseg_name` varchar(120) NOT NULL,
  `zbsseg_slug` varchar(45) NOT NULL,
  `zbsseg_matchtype` varchar(10) NOT NULL,
  `zbsseg_created` int(14) NOT NULL,
  `zbsseg_lastupdated` int(14) NOT NULL,
  `zbsseg_compilecount` int(11) DEFAULT 0,
  `zbsseg_lastcompiled` int(14) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
