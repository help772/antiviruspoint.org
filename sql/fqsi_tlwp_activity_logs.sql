/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_tlwp_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` bigint(20) DEFAULT NULL,
  `object` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `user_roles` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `post_status` varchar(255) NOT NULL,
  `post_type` varchar(255) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `created_on` double NOT NULL,
  `client_ip` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (1,NULL,'User','Logout','administrator','tlwp-user-i5nrvvfbjqrvo1l',28,'','',0,1757475592,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (2,NULL,'User','Login','administrator','chagptuser',29,'','',0,1757475592,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (3,NULL,'User','Logout','administrator','chagptuser',29,'','',0,1757476323,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (4,NULL,'User','Login','administrator','chagptuser',29,'','',0,1757476338,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (5,NULL,'User','Logout','administrator','chagptuser',29,'','',0,1757476386,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (6,NULL,'User','Login','administrator','chagptuser',29,'','',0,1757476415,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (7,NULL,'Plugin','Activated','administrator','chagptuser',29,'','plugin',0,1757478171,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
INSERT INTO `fqsi_tlwp_activity_logs` (`id`, `alert_id`, `object`, `action`, `user_roles`, `username`, `user_id`, `post_status`, `post_type`, `post_id`, `created_on`, `client_ip`, `user_agent`) VALUES (8,NULL,'Plugin','Activated','administrator','chagptuser',29,'','plugin',0,1757478172,'104.28.225.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
