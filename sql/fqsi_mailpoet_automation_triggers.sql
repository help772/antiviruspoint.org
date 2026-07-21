/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_automation_triggers` (
  `automation_id` int(11) unsigned NOT NULL,
  `trigger_key` varchar(191) NOT NULL,
  PRIMARY KEY (`automation_id`,`trigger_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_automation_triggers` (`automation_id`, `trigger_key`) VALUES (1,'mailpoet:someone-subscribes');
