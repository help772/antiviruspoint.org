/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_funnel_metrics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `funnel_id` bigint(20) unsigned DEFAULT NULL,
  `sequence_id` bigint(20) unsigned DEFAULT NULL,
  `subscriber_id` bigint(20) unsigned DEFAULT NULL,
  `benchmark_value` bigint(20) unsigned DEFAULT 0,
  `benchmark_currency` varchar(10) DEFAULT 'USD',
  `status` varchar(50) DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_fmx__m_idx` (`funnel_id`),
  KEY `fqsi_fc_fmx__ms__idx` (`subscriber_id`),
  KEY `sequence_id` (`sequence_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
