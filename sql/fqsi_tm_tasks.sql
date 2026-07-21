/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_tm_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `type` varchar(300) NOT NULL,
  `class_identifier` varchar(300) DEFAULT '0',
  `attempts` int(11) DEFAULT 0,
  `description` varchar(300) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_locked_at` bigint(20) DEFAULT 0,
  `status` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (42,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org','2025-07-02 14:39:24',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (43,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/','2025-07-02 14:39:24',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (44,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/shop/','2025-07-02 14:39:24',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (45,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/2025/','2025-07-02 14:39:24',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (46,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/2025/07/','2025-07-02 14:39:24',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (47,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/2025/07/02/','2025-07-02 14:39:24',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (48,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org','2025-07-02 19:37:39',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (49,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/','2025-07-02 19:37:39',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (50,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/shop/','2025-07-02 19:37:39',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (51,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/2025/','2025-07-02 19:37:39',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (52,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/2025/07/','2025-07-02 19:37:39',0,'active');
INSERT INTO `fqsi_tm_tasks` (`id`, `user_id`, `type`, `class_identifier`, `attempts`, `description`, `time_created`, `last_locked_at`, `status`) VALUES (53,12,'load-url-task','WP_Optimize_Load_Url_Task',0,'Preload - https://antiviruspoint.org/2025/07/02/','2025-07-02 19:37:39',0,'active');
