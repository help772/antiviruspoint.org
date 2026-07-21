/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `taxonomy_name` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `title` text DEFAULT NULL,
  `position` decimal(10,2) NOT NULL DEFAULT 1.00,
  `description` longtext DEFAULT NULL,
  `settings` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_tms__tm_idx` (`taxonomy_name`),
  KEY `fqsi_fc_tms__tm_id_slug` (`slug`),
  KEY `fqsi_fc_tms__tm_id_pid` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
