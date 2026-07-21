/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_campaign_emails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `email_type` varchar(50) DEFAULT 'campaign',
  `subscriber_id` bigint(20) unsigned DEFAULT NULL,
  `email_subject_id` bigint(20) unsigned DEFAULT NULL,
  `email_address` varchar(192) NOT NULL,
  `email_subject` varchar(192) DEFAULT NULL,
  `email_body` longtext DEFAULT NULL,
  `email_headers` text DEFAULT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 0,
  `is_parsed` tinyint(1) NOT NULL DEFAULT 0,
  `click_counter` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `note` text DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `email_hash` varchar(192) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_cam__cid_idx` (`campaign_id`),
  KEY `fqsi_fc_cam__sid_idx` (`subscriber_id`),
  KEY `fqsi_fc_cam__et_idx` (`email_type`),
  KEY `fqsi_fc_cam__estidx` (`status`),
  KEY `fqsi_fc_cam__emtidx` (`email_hash`),
  KEY `fqsi_fc_cam__scheduled_at` (`scheduled_at`),
  KEY `fqsi_fc_cam__updated_at` (`updated_at`),
  KEY `fqsi_fc_cam_sc_at_status` (`scheduled_at`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
