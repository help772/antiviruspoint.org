/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_edd_tax_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `amount` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `scope` varchar(20) NOT NULL DEFAULT 'country',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `source` varchar(20) NOT NULL DEFAULT 'manual',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `country_state` (`country`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
