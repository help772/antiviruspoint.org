/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_funnels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT 'funnel',
  `title` varchar(192) NOT NULL,
  `trigger_name` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `conditions` text DEFAULT NULL,
  `settings` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_fn__f_idx` (`status`),
  KEY `fqsi_fc_fn__ft_idx` (`trigger_name`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
