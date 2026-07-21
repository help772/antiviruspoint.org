/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_prl_deployments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `active` char(3) NOT NULL,
  `engine_id` bigint(20) unsigned NOT NULL,
  `engine_type` varchar(25) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `display_order` int(10) unsigned NOT NULL,
  `columns` int(10) unsigned NOT NULL,
  `limit` int(10) unsigned NOT NULL,
  `location_id` varchar(80) NOT NULL,
  `hook` varchar(191) NOT NULL,
  `conditions_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `engine_id` (`engine_id`),
  KEY `hook` (`hook`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
