/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'campaign',
  `title` varchar(192) NOT NULL,
  `available_urls` text DEFAULT NULL,
  `slug` varchar(192) NOT NULL,
  `status` varchar(50) NOT NULL,
  `template_id` bigint(20) unsigned DEFAULT NULL,
  `email_subject` varchar(192) DEFAULT NULL,
  `email_pre_header` varchar(192) DEFAULT NULL,
  `email_body` longtext NOT NULL,
  `recipients_count` int(11) NOT NULL DEFAULT 0,
  `delay` int(11) DEFAULT 0,
  `utm_status` tinyint(1) DEFAULT 0,
  `utm_source` varchar(192) DEFAULT NULL,
  `utm_medium` varchar(192) DEFAULT NULL,
  `utm_campaign` varchar(192) DEFAULT NULL,
  `utm_term` varchar(192) DEFAULT NULL,
  `utm_content` varchar(192) DEFAULT NULL,
  `design_template` varchar(192) DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `settings` longtext DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
