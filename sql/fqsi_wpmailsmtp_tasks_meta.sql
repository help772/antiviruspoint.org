/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wpmailsmtp_tasks_meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wpmailsmtp_tasks_meta` (`id`, `action`, `data`, `date`) VALUES (1,'wp_mail_smtp_admin_notifications_update','W10=','2025-01-17 05:56:13');
INSERT INTO `fqsi_wpmailsmtp_tasks_meta` (`id`, `action`, `data`, `date`) VALUES (2,'wp_mail_smtp_admin_notifications_update','W10=','2025-06-20 05:07:33');
