/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_automation_run_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `automation_run_id` int(11) unsigned NOT NULL,
  `step_id` varchar(191) NOT NULL,
  `step_type` varchar(255) NOT NULL,
  `step_key` varchar(255) NOT NULL,
  `status` varchar(191) NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `run_number` int(11) NOT NULL,
  `data` longtext NOT NULL,
  `error` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `automation_run_id_step_id` (`automation_run_id`,`step_id`),
  KEY `status` (`status`),
  KEY `step_id` (`step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
