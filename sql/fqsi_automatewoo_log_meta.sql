/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_automatewoo_log_meta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_id` bigint(20) DEFAULT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `log_id` (`log_id`),
  KEY `meta_key` (`meta_key`(191)),
  KEY `log_id_meta_key` (`log_id`,`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (57,15,'_data_layer_customer','1');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (58,15,'cart_id','51');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (59,15,'user_id','12');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (60,15,'notes','a:1:{i:0;s:59:\"Send Email: The recipient is not opted-in to this workflow.\";}');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (61,16,'_data_layer_customer','1');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (62,16,'cart_id','52');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (63,16,'user_id','12');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (64,16,'notes','a:1:{i:0;s:59:\"Send Email: The recipient is not opted-in to this workflow.\";}');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (65,17,'_data_layer_customer','1');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (66,17,'cart_id','54');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (67,17,'user_id','12');
INSERT INTO `fqsi_automatewoo_log_meta` (`meta_id`, `log_id`, `meta_key`, `meta_value`) VALUES (68,17,'notes','a:1:{i:0;s:59:\"Send Email: The recipient is not opted-in to this workflow.\";}');
