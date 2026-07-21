/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wc_download_log` (
  `download_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_ip_address` varchar(100) DEFAULT '',
  PRIMARY KEY (`download_log_id`),
  KEY `permission_id` (`permission_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wc_download_log` (`download_log_id`, `timestamp`, `permission_id`, `user_id`, `user_ip_address`) VALUES (1,'2025-01-17 12:06:53',1,2,'2401:4900:1f3a:74ea:1da9:e411:a948:b331');
INSERT INTO `fqsi_wc_download_log` (`download_log_id`, `timestamp`, `permission_id`, `user_id`, `user_ip_address`) VALUES (2,'2025-02-13 10:13:10',2,1,'223.233.64.37');
INSERT INTO `fqsi_wc_download_log` (`download_log_id`, `timestamp`, `permission_id`, `user_id`, `user_ip_address`) VALUES (3,'2025-02-13 10:13:42',2,1,'223.233.64.37');
INSERT INTO `fqsi_wc_download_log` (`download_log_id`, `timestamp`, `permission_id`, `user_id`, `user_ip_address`) VALUES (4,'2025-02-13 10:21:44',3,6,'223.233.64.37');
INSERT INTO `fqsi_wc_download_log` (`download_log_id`, `timestamp`, `permission_id`, `user_id`, `user_ip_address`) VALUES (5,'2025-02-14 11:04:20',6,9,'14.195.41.194');
