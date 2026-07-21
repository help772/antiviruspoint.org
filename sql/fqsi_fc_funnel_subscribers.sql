/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_funnel_subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `funnel_id` bigint(20) unsigned DEFAULT NULL,
  `starting_sequence_id` bigint(20) unsigned DEFAULT NULL,
  `next_sequence` bigint(20) unsigned DEFAULT NULL,
  `subscriber_id` bigint(20) unsigned DEFAULT NULL,
  `last_sequence_id` bigint(20) unsigned DEFAULT NULL,
  `next_sequence_id` bigint(20) unsigned DEFAULT NULL,
  `last_sequence_status` varchar(50) DEFAULT 'pending',
  `status` varchar(50) DEFAULT 'active',
  `type` varchar(50) DEFAULT 'funnel',
  `last_executed_time` timestamp NULL DEFAULT NULL,
  `next_execution_time` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `source_trigger_name` varchar(192) DEFAULT NULL,
  `source_ref_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_fsx__fidx` (`funnel_id`),
  KEY `fqsi_fc_fsx__fsq_idx` (`subscriber_id`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `next_execution_time` (`next_execution_time`),
  KEY `next_sequence` (`next_sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
