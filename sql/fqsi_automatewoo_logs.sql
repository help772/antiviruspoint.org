/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_automatewoo_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `tracking_enabled` int(1) NOT NULL DEFAULT 0,
  `conversion_tracking_enabled` int(1) NOT NULL DEFAULT 0,
  `has_errors` int(1) NOT NULL DEFAULT 0,
  `has_blocked_emails` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `date` (`date`),
  KEY `workflow_id_date` (`workflow_id`,`date`),
  KEY `tracking_blocked_date` (`tracking_enabled`,`date`),
  KEY `conversion_tracking_date` (`conversion_tracking_enabled`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_automatewoo_logs` (`id`, `workflow_id`, `date`, `tracking_enabled`, `conversion_tracking_enabled`, `has_errors`, `has_blocked_emails`) VALUES (15,90007689,'2025-08-27 02:44:53',0,0,0,1);
INSERT INTO `fqsi_automatewoo_logs` (`id`, `workflow_id`, `date`, `tracking_enabled`, `conversion_tracking_enabled`, `has_errors`, `has_blocked_emails`) VALUES (16,90007689,'2025-09-04 12:50:04',0,0,0,1);
INSERT INTO `fqsi_automatewoo_logs` (`id`, `workflow_id`, `date`, `tracking_enabled`, `conversion_tracking_enabled`, `has_errors`, `has_blocked_emails`) VALUES (17,90007689,'2025-09-06 12:55:11',0,0,0,1);
