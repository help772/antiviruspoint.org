/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_ff_scheduled_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) DEFAULT NULL,
  `form_id` bigint(20) unsigned DEFAULT NULL,
  `origin_id` bigint(20) unsigned DEFAULT NULL,
  `feed_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) DEFAULT 'submission_action',
  `status` varchar(255) DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `note` tinytext DEFAULT NULL,
  `retry_count` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
