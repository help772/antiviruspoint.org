/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_quotes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbsq_id_override` varchar(128) DEFAULT NULL,
  `zbsq_title` varchar(255) DEFAULT NULL,
  `zbsq_currency` varchar(4) NOT NULL DEFAULT '-1',
  `zbsq_value` decimal(18,2) DEFAULT 0.00,
  `zbsq_date` int(14) NOT NULL,
  `zbsq_template` varchar(200) DEFAULT NULL,
  `zbsq_content` longtext DEFAULT NULL,
  `zbsq_notes` longtext DEFAULT NULL,
  `zbsq_hash` varchar(64) DEFAULT NULL,
  `zbsq_send_attachments` tinyint(1) NOT NULL DEFAULT -1,
  `zbsq_lastviewed` int(14) DEFAULT -1,
  `zbsq_viewed_count` int(10) DEFAULT 0,
  `zbsq_accepted` int(14) DEFAULT -1,
  `zbsq_acceptedsigned` varchar(200) DEFAULT NULL,
  `zbsq_acceptedip` varchar(64) DEFAULT NULL,
  `zbsq_created` int(14) NOT NULL,
  `zbsq_lastupdated` int(14) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `title` (`zbsq_title`),
  KEY `dateint` (`zbsq_date`),
  KEY `hash` (`zbsq_hash`),
  KEY `created` (`zbsq_created`),
  KEY `accepted` (`zbsq_accepted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
