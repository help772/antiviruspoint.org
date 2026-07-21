/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_segments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `type` varchar(90) NOT NULL DEFAULT 'default',
  `description` varchar(250) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `average_engagement_score` float unsigned DEFAULT NULL,
  `average_engagement_score_updated_at` timestamp NULL DEFAULT NULL,
  `display_in_manage_subscription_page` tinyint(1) NOT NULL DEFAULT 0,
  `confirmation_email_id` int(11) unsigned DEFAULT NULL,
  `confirmation_page_id` int(11) unsigned DEFAULT NULL,
  `public_description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `average_engagement_score_updated_at` (`average_engagement_score_updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_segments` (`id`, `name`, `type`, `description`, `created_at`, `updated_at`, `deleted_at`, `average_engagement_score`, `average_engagement_score_updated_at`, `display_in_manage_subscription_page`, `confirmation_email_id`, `confirmation_page_id`, `public_description`) VALUES (1,'WordPress Users','wp_users','This list contains all of your WordPress users.','2025-06-19 19:41:23','2026-07-19 16:11:05',NULL,NULL,'2026-07-19 16:11:05',0,NULL,NULL,'');
INSERT INTO `fqsi_mailpoet_segments` (`id`, `name`, `type`, `description`, `created_at`, `updated_at`, `deleted_at`, `average_engagement_score`, `average_engagement_score_updated_at`, `display_in_manage_subscription_page`, `confirmation_email_id`, `confirmation_page_id`, `public_description`) VALUES (2,'WooCommerce Customers','woocommerce_users','This list contains all of your WooCommerce customers.','2025-06-19 19:41:23','2026-07-19 16:11:05',NULL,NULL,'2026-07-19 16:11:05',0,NULL,NULL,'');
INSERT INTO `fqsi_mailpoet_segments` (`id`, `name`, `type`, `description`, `created_at`, `updated_at`, `deleted_at`, `average_engagement_score`, `average_engagement_score_updated_at`, `display_in_manage_subscription_page`, `confirmation_email_id`, `confirmation_page_id`, `public_description`) VALUES (3,'Newsletter mailing list','default','This list is automatically created when you install MailPoet.','2025-06-19 19:41:23','2026-07-19 16:11:05','2025-06-20 04:39:20',NULL,'2026-07-19 16:11:05',0,NULL,NULL,'');
INSERT INTO `fqsi_mailpoet_segments` (`id`, `name`, `type`, `description`, `created_at`, `updated_at`, `deleted_at`, `average_engagement_score`, `average_engagement_score_updated_at`, `display_in_manage_subscription_page`, `confirmation_email_id`, `confirmation_page_id`, `public_description`) VALUES (4,'New','default','10000','2025-06-20 04:41:45','2026-07-19 16:11:05',NULL,NULL,'2026-07-19 16:11:05',1,NULL,NULL,'');
