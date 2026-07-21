/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wpforms_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `types` varchar(255) NOT NULL,
  `create_at` datetime NOT NULL,
  `form_id` bigint(20) DEFAULT NULL,
  `entry_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wpforms_logs` (`id`, `title`, `message`, `types`, `create_at`, `form_id`, `entry_id`, `user_id`) VALUES (1,'Migration','Migration of WPForms to 1.9.3.2 is fully completed.','log','2025-01-28 19:12:27',0,0,0);
INSERT INTO `fqsi_wpforms_logs` (`id`, `title`, `message`, `types`, `create_at`, `form_id`, `entry_id`, `user_id`) VALUES (2,'Migration','Migration of WPForms to 1.9.6.1 is fully completed.','log','2025-06-18 19:13:25',0,0,0);
INSERT INTO `fqsi_wpforms_logs` (`id`, `title`, `message`, `types`, `create_at`, `form_id`, `entry_id`, `user_id`) VALUES (3,'Migration','Migration of WPForms Pro to 1.9.4 started.','log','2025-06-20 05:05:18',0,0,12);
INSERT INTO `fqsi_wpforms_logs` (`id`, `title`, `message`, `types`, `create_at`, `form_id`, `entry_id`, `user_id`) VALUES (4,'Migration','Migration of WPForms Pro to 1.9.4 completed.','log','2025-06-20 05:05:18',0,0,12);
INSERT INTO `fqsi_wpforms_logs` (`id`, `title`, `message`, `types`, `create_at`, `form_id`, `entry_id`, `user_id`) VALUES (5,'Migration','Migration of WPForms Pro to 1.9.6.1 is fully completed.','log','2025-06-20 05:05:18',0,0,12);
