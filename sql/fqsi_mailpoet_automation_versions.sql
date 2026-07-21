/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_automation_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `automation_id` int(11) unsigned NOT NULL,
  `steps` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `automation_id` (`automation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_automation_versions` (`id`, `automation_id`, `steps`, `created_at`, `updated_at`) VALUES (1,1,'{\"root\":{\"id\":\"root\",\"type\":\"root\",\"key\":\"core:root\",\"args\":[],\"next_steps\":[{\"id\":\"1sl3u8xcmdwk4k80\"}],\"filters\":null},\"1sl3u8xcmdwk4k80\":{\"id\":\"1sl3u8xcmdwk4k80\",\"type\":\"trigger\",\"key\":\"mailpoet:someone-subscribes\",\"args\":[],\"next_steps\":[{\"id\":\"6dii76wjn1k4okww\"}],\"filters\":null},\"6dii76wjn1k4okww\":{\"id\":\"6dii76wjn1k4okww\",\"type\":\"action\",\"key\":\"core:delay\",\"args\":{\"delay_type\":\"MINUTES\",\"delay\":1},\"next_steps\":[{\"id\":\"2zp5bpwmbeiowc84\"}],\"filters\":null},\"2zp5bpwmbeiowc84\":{\"id\":\"2zp5bpwmbeiowc84\",\"type\":\"action\",\"key\":\"mailpoet:send-email\",\"args\":{\"name\":\"Send email\",\"subject\":\"Subject\",\"preheader\":\"\",\"sender_name\":\"Antiviruspoint\",\"sender_address\":\"infinity@antiviruspoint.org\"},\"next_steps\":[],\"filters\":null}}','2025-06-20 01:14:37','2025-06-20 01:14:37');
