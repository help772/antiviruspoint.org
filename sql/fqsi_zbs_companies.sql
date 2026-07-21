/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_companies` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbsco_status` varchar(50) DEFAULT NULL,
  `zbsco_name` varchar(100) DEFAULT NULL,
  `zbsco_email` varchar(200) DEFAULT NULL,
  `zbsco_addr1` varchar(200) DEFAULT NULL,
  `zbsco_addr2` varchar(200) DEFAULT NULL,
  `zbsco_city` varchar(200) DEFAULT NULL,
  `zbsco_county` varchar(200) DEFAULT NULL,
  `zbsco_country` varchar(200) DEFAULT NULL,
  `zbsco_postcode` varchar(50) DEFAULT NULL,
  `zbsco_secaddr1` varchar(200) DEFAULT NULL,
  `zbsco_secaddr2` varchar(200) DEFAULT NULL,
  `zbsco_seccity` varchar(200) DEFAULT NULL,
  `zbsco_seccounty` varchar(200) DEFAULT NULL,
  `zbsco_seccountry` varchar(200) DEFAULT NULL,
  `zbsco_secpostcode` varchar(50) DEFAULT NULL,
  `zbsco_maintel` varchar(40) DEFAULT NULL,
  `zbsco_sectel` varchar(40) DEFAULT NULL,
  `zbsco_wpid` int(11) DEFAULT NULL,
  `zbsco_avatar` varchar(300) DEFAULT NULL,
  `zbsco_tw` varchar(100) DEFAULT NULL,
  `zbsco_li` varchar(300) DEFAULT NULL,
  `zbsco_fb` varchar(200) DEFAULT NULL,
  `zbsco_created` int(14) NOT NULL,
  `zbsco_lastupdated` int(14) NOT NULL,
  `zbsco_lastcontacted` int(14) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `wpid` (`zbsco_wpid`),
  KEY `name` (`zbsco_name`),
  KEY `email` (`zbsco_email`),
  KEY `created` (`zbsco_created`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_zbs_companies` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsco_status`, `zbsco_name`, `zbsco_email`, `zbsco_addr1`, `zbsco_addr2`, `zbsco_city`, `zbsco_county`, `zbsco_country`, `zbsco_postcode`, `zbsco_secaddr1`, `zbsco_secaddr2`, `zbsco_seccity`, `zbsco_seccounty`, `zbsco_seccountry`, `zbsco_secpostcode`, `zbsco_maintel`, `zbsco_sectel`, `zbsco_wpid`, `zbsco_avatar`, `zbsco_tw`, `zbsco_li`, `zbsco_fb`, `zbsco_created`, `zbsco_lastupdated`, `zbsco_lastcontacted`) VALUES (1,1,1,12,'Customer','thecodeyogi','','','','','','','','','','','','','','','',0,'','','','',1737041830,1757010379,0);
INSERT INTO `fqsi_zbs_companies` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsco_status`, `zbsco_name`, `zbsco_email`, `zbsco_addr1`, `zbsco_addr2`, `zbsco_city`, `zbsco_county`, `zbsco_country`, `zbsco_postcode`, `zbsco_secaddr1`, `zbsco_secaddr2`, `zbsco_seccity`, `zbsco_seccounty`, `zbsco_seccountry`, `zbsco_secpostcode`, `zbsco_maintel`, `zbsco_sectel`, `zbsco_wpid`, `zbsco_avatar`, `zbsco_tw`, `zbsco_li`, `zbsco_fb`, `zbsco_created`, `zbsco_lastupdated`, `zbsco_lastcontacted`) VALUES (2,1,1,12,'Customer','KNACKHELP CONSULTING SERVICES PRIVATE LIMITED','','','','','','','','','','','','','','','',0,'','','','',1737365723,1757010381,0);
INSERT INTO `fqsi_zbs_companies` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsco_status`, `zbsco_name`, `zbsco_email`, `zbsco_addr1`, `zbsco_addr2`, `zbsco_city`, `zbsco_county`, `zbsco_country`, `zbsco_postcode`, `zbsco_secaddr1`, `zbsco_secaddr2`, `zbsco_seccity`, `zbsco_seccounty`, `zbsco_seccountry`, `zbsco_secpostcode`, `zbsco_maintel`, `zbsco_sectel`, `zbsco_wpid`, `zbsco_avatar`, `zbsco_tw`, `zbsco_li`, `zbsco_fb`, `zbsco_created`, `zbsco_lastupdated`, `zbsco_lastcontacted`) VALUES (3,1,1,12,'Customer','Test','','','','','','','','','','','','','','','',0,'','','','',1750230661,1757010384,0);
INSERT INTO `fqsi_zbs_companies` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsco_status`, `zbsco_name`, `zbsco_email`, `zbsco_addr1`, `zbsco_addr2`, `zbsco_city`, `zbsco_county`, `zbsco_country`, `zbsco_postcode`, `zbsco_secaddr1`, `zbsco_secaddr2`, `zbsco_seccity`, `zbsco_seccounty`, `zbsco_seccountry`, `zbsco_secpostcode`, `zbsco_maintel`, `zbsco_sectel`, `zbsco_wpid`, `zbsco_avatar`, `zbsco_tw`, `zbsco_li`, `zbsco_fb`, `zbsco_created`, `zbsco_lastupdated`, `zbsco_lastcontacted`) VALUES (4,1,1,12,'Customer','internet bull','','','','','','','','','','','','','','','',0,'','','','',1751051105,1757010385,0);
