/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_automation_run_subjects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `automation_run_id` int(11) unsigned NOT NULL,
  `key` varchar(191) DEFAULT NULL,
  `args` longtext DEFAULT NULL,
  `hash` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_run_id` (`automation_run_id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
