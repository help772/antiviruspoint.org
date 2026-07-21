/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_dologin_failure` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(128) NOT NULL DEFAULT '',
  `ip_geo` text NOT NULL,
  `username` varchar(128) NOT NULL DEFAULT '',
  `gateway` varchar(128) NOT NULL DEFAULT '',
  `dateline` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
