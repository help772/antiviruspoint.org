/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_aiowps_login_lockdown` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_login` varchar(150) NOT NULL,
  `lockdown_date` datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
  `created` int(10) unsigned DEFAULT NULL,
  `release_date` datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
  `released` int(10) unsigned DEFAULT NULL,
  `failed_login_ip` varchar(100) NOT NULL DEFAULT '',
  `lock_reason` varchar(128) NOT NULL DEFAULT '',
  `unlock_key` varchar(128) NOT NULL DEFAULT '',
  `is_lockout_email_sent` tinyint(1) NOT NULL DEFAULT 1,
  `backtrace_log` text NOT NULL,
  `ip_lookup_result` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_login_ip` (`failed_login_ip`),
  KEY `is_lockout_email_sent` (`is_lockout_email_sent`),
  KEY `unlock_key` (`unlock_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
