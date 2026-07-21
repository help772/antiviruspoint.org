/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_tlwp_activity_logs_meta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `activity_log_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `activity_log_id` (`activity_log_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (1,1,'message','TLWP user logged out');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (2,1,'email','');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (3,2,'message','TLWP user logged in');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (4,2,'email','xdevoty@gmail.com');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (5,3,'message','TLWP user logged out');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (6,3,'email','xdevoty@gmail.com');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (7,4,'message','TLWP user logged in');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (8,4,'email','xdevoty@gmail.com');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (9,5,'message','TLWP user logged out');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (10,5,'email','xdevoty@gmail.com');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (11,6,'message','TLWP user logged in');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (12,6,'email','xdevoty@gmail.com');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (13,7,'message','LiteSpeed Cache');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (14,7,'version','7.4');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (15,7,'author','<a href=\"https://www.litespeedtech.com\">LiteSpeed Technologies</a>');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (16,8,'message','LiteSpeed Cache');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (17,8,'version','7.4');
INSERT INTO `fqsi_tlwp_activity_logs_meta` (`meta_id`, `activity_log_id`, `meta_key`, `meta_value`) VALUES (18,8,'author','<a href=\"https://www.litespeedtech.com\">LiteSpeed Technologies</a>');
