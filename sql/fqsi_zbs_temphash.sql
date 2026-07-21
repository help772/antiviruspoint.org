/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_temphash` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbstemphash_status` int(11) DEFAULT -1,
  `zbstemphash_objtype` varchar(50) NOT NULL,
  `zbstemphash_objid` int(11) DEFAULT NULL,
  `zbstemphash_objhash` varchar(256) DEFAULT NULL,
  `zbstemphash_created` int(14) NOT NULL,
  `zbstemphash_lastupdated` int(14) NOT NULL,
  `zbstemphash_expiry` int(14) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
