/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_customfields` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbscf_objtype` int(4) NOT NULL,
  `zbscf_objid` int(32) NOT NULL,
  `zbscf_objkey` varchar(100) NOT NULL,
  `zbscf_objval` varchar(2000) DEFAULT NULL,
  `zbscf_created` int(14) NOT NULL,
  `zbscf_lastupdated` int(14) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `TYPEIDKEY` (`zbscf_objtype`,`zbscf_objid`,`zbscf_objkey`),
  FULLTEXT KEY `search` (`zbscf_objval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
