/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_actionscheduler_groups` (
  `group_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `slug` (`slug`(191))
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (1,'action-scheduler-migration');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (2,'');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (3,'woocommerce-db-updates');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (4,'wc_update_product_default_cat');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (5,'wc-admin-data');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (6,'wp_mail_smtp');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (7,'wp_mail_smtp');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (8,'wpforms');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (9,'woocommerce_payments');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (10,'woocommerce-remote-inbox-engine');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (11,'count');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (12,'gla');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (13,'mailpoet-cron');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (14,'wc_batch_processes');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (15,'wc_prl_generator');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (16,'automatewoo');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (17,'wc_delete_related_product_transients_group');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (18,'image-optimization/migration');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (19,'image-optimization/cleanup');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (20,'image-optimization/optimize');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (29,'data_index_lookup');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (30,'data_index_taxonomy');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (33,'cybersource');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (34,'woocommerce-webhooks');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (35,'facebook-for-woocommerce');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (36,'mc-woocommerce');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (37,'woocommerce-product-feeds');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (38,'ActionScheduler');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (39,'woocommerce');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (40,'wcs_batch_processes');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (41,'edd');
INSERT INTO `fqsi_actionscheduler_groups` (`group_id`, `slug`) VALUES (42,'wc_facebook_log_batch');
