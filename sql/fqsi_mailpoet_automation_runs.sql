/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_automation_runs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `automation_id` int(11) unsigned NOT NULL,
  `version_id` int(11) unsigned NOT NULL,
  `trigger_key` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `next_step_id` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_id` (`automation_id`,`status`),
  KEY `created_at` (`created_at`),
  KEY `version_id` (`version_id`),
  KEY `status` (`status`),
  KEY `next_step_id` (`next_step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
