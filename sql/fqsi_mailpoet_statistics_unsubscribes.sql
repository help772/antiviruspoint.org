/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_statistics_unsubscribes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter_id` int(11) unsigned DEFAULT NULL,
  `subscriber_id` int(11) unsigned NOT NULL,
  `queue_id` int(11) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `source` varchar(255) DEFAULT 'unknown',
  `meta` varchar(255) DEFAULT NULL,
  `method` varchar(40) NOT NULL DEFAULT 'unknown',
  `reason` varchar(80) DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `reason_submitted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `newsletter_id_subscriber_id` (`newsletter_id`,`subscriber_id`),
  KEY `queue_id` (`queue_id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `newsletter_id_reason` (`newsletter_id`,`reason`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
