/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_tm_taskmeta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `meta_key` (`meta_key`(191)),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (42,42,'task_options','a:3:{s:3:\"url\";s:26:\"https://antiviruspoint.org\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (43,43,'task_options','a:3:{s:3:\"url\";s:27:\"https://antiviruspoint.org/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (44,44,'task_options','a:3:{s:3:\"url\";s:32:\"https://antiviruspoint.org/shop/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (45,45,'task_options','a:3:{s:3:\"url\";s:32:\"https://antiviruspoint.org/2025/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (46,46,'task_options','a:3:{s:3:\"url\";s:35:\"https://antiviruspoint.org/2025/07/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (47,47,'task_options','a:3:{s:3:\"url\";s:38:\"https://antiviruspoint.org/2025/07/02/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (48,48,'task_options','a:3:{s:3:\"url\";s:26:\"https://antiviruspoint.org\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (49,49,'task_options','a:3:{s:3:\"url\";s:27:\"https://antiviruspoint.org/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (50,50,'task_options','a:3:{s:3:\"url\";s:32:\"https://antiviruspoint.org/shop/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (51,51,'task_options','a:3:{s:3:\"url\";s:32:\"https://antiviruspoint.org/2025/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (52,52,'task_options','a:3:{s:3:\"url\";s:35:\"https://antiviruspoint.org/2025/07/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
INSERT INTO `fqsi_tm_taskmeta` (`meta_id`, `task_id`, `meta_key`, `meta_value`) VALUES (53,53,'task_options','a:3:{s:3:\"url\";s:38:\"https://antiviruspoint.org/2025/07/02/\";s:12:\"preload_type\";s:6:\"manual\";s:22:\"anonymous_user_allowed\";b:0;}');
