/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_funnel_sequences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `funnel_id` bigint(20) unsigned DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT 0,
  `action_name` varchar(192) DEFAULT NULL,
  `condition_type` varchar(192) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'sequence',
  `title` varchar(192) DEFAULT NULL,
  `description` varchar(192) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `conditions` text DEFAULT NULL,
  `settings` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `delay` int(10) unsigned DEFAULT NULL,
  `c_delay` int(10) unsigned DEFAULT NULL,
  `sequence` int(10) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_fq__fs_idx` (`status`),
  KEY `fqsi_fc_fq__fid_idx` (`funnel_id`),
  KEY `c_delay` (`c_delay`),
  KEY `sequence` (`sequence`),
  KEY `action_name` (`action_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
