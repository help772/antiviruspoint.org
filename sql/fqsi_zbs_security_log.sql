/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_security_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbssl_reqtype` varchar(20) NOT NULL,
  `zbssl_ip` varchar(200) DEFAULT NULL,
  `zbssl_reqhash` varchar(128) DEFAULT NULL,
  `zbssl_reqid` int(11) DEFAULT NULL,
  `zbssl_loggedin_id` int(11) DEFAULT NULL,
  `zbssl_reqstatus` int(1) DEFAULT NULL,
  `zbssl_reqtime` int(14) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
