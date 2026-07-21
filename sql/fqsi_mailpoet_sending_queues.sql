/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_sending_queues` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL,
  `newsletter_id` int(11) unsigned DEFAULT NULL,
  `newsletter_rendered_body` longtext DEFAULT NULL,
  `newsletter_rendered_subject` varchar(250) DEFAULT NULL,
  `subscribers` longtext DEFAULT NULL,
  `count_total` int(11) unsigned NOT NULL DEFAULT 0,
  `count_processed` int(11) unsigned NOT NULL DEFAULT 0,
  `count_to_process` int(11) unsigned NOT NULL DEFAULT 0,
  `meta` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `newsletter_id` (`newsletter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
