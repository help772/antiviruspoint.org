/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_subscribers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wp_user_id` bigint(20) DEFAULT NULL,
  `is_woocommerce_user` int(1) NOT NULL DEFAULT 0,
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'unconfirmed',
  `subscribed_ip` varchar(45) DEFAULT NULL,
  `confirmed_ip` varchar(45) DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `last_subscribed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `unconfirmed_data` longtext DEFAULT NULL,
  `source` enum('form','imported','administrator','api','wordpress_user','wordpress_user_deleted','woocommerce_user','woocommerce_checkout','unknown') DEFAULT 'unknown',
  `count_confirmations` int(11) unsigned NOT NULL DEFAULT 0,
  `unsubscribe_token` char(15) DEFAULT NULL,
  `link_token` char(32) DEFAULT NULL,
  `engagement_score` float unsigned DEFAULT NULL,
  `engagement_score_updated_at` timestamp NULL DEFAULT NULL,
  `last_engagement_at` timestamp NULL DEFAULT NULL,
  `woocommerce_synced_at` timestamp NULL DEFAULT NULL,
  `email_count` int(11) unsigned NOT NULL DEFAULT 0,
  `last_sending_at` timestamp NULL DEFAULT NULL,
  `last_open_at` timestamp NULL DEFAULT NULL,
  `last_click_at` timestamp NULL DEFAULT NULL,
  `last_purchase_at` timestamp NULL DEFAULT NULL,
  `last_page_view_at` timestamp NULL DEFAULT NULL,
  `last_confirmation_email_sent_at` timestamp NULL DEFAULT NULL,
  `time_zone` varchar(64) DEFAULT NULL,
  `time_zone_source` varchar(32) DEFAULT NULL,
  `time_zone_confidence` int(11) DEFAULT NULL,
  `time_zone_updated_at` timestamp NULL DEFAULT NULL,
  `segments_count` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `unsubscribe_token` (`unsubscribe_token`),
  KEY `wp_user_id` (`wp_user_id`),
  KEY `updated_at` (`updated_at`),
  KEY `status_deleted_at` (`status`,`deleted_at`),
  KEY `last_subscribed_at` (`last_subscribed_at`),
  KEY `engagement_score_updated_at` (`engagement_score_updated_at`),
  KEY `link_token` (`link_token`),
  KEY `first_name` (`first_name`(10)),
  KEY `last_name` (`last_name`(10)),
  KEY `last_sending_at` (`last_sending_at`),
  KEY `last_open_at` (`last_open_at`),
  KEY `last_click_at` (`last_click_at`),
  KEY `last_purchase_at` (`last_purchase_at`),
  KEY `last_page_view_at` (`last_page_view_at`),
  KEY `idx_sub_cleanup_legacy` (`status`,`deleted_at`,`wp_user_id`,`is_woocommerce_user`,`last_confirmation_email_sent_at`,`last_subscribed_at`,`created_at`,`id`),
  KEY `deleted_at_created` (`deleted_at`,`created_at`),
  KEY `segments_count_status_deleted_at` (`segments_count`,`status`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (3,1,0,'Anurag','Handique','infinity@antiviruspoint.org','unconfirmed',NULL,NULL,NULL,NULL,'2025-06-19 19:41:23','2026-07-20 16:34:07',NULL,NULL,'wordpress_user',0,'8f6019e7f034fa5','4736e4',NULL,'2026-07-09 17:55:42','2026-07-20 16:33:07',NULL,0,NULL,NULL,NULL,'2025-02-13 10:12:28','2026-07-20 16:33:07',NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (7,12,0,'madav6310','Saingar','madav6310@gmail.com','unconfirmed',NULL,NULL,NULL,NULL,'2025-06-19 19:41:23','2026-07-09 17:55:42',NULL,NULL,'wordpress_user',0,'5fe6c94b5bd901d','e4a793',NULL,'2026-07-09 17:55:42','2025-09-10 06:23:27',NULL,0,NULL,NULL,NULL,NULL,'2025-09-10 06:23:27',NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (16,NULL,1,'Michael','Crose','Mandrewc@comcast.net','subscribed',NULL,NULL,NULL,'2025-06-19 23:08:45','2025-06-19 23:08:45','2026-07-09 17:55:42',NULL,NULL,'woocommerce_user',0,'bbac7359ddc8cbe','cfafc5',NULL,'2026-07-09 17:55:42',NULL,'2025-06-19 23:08:45',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (17,NULL,1,'frank','test','frankd@gmail.com','subscribed',NULL,NULL,NULL,'2025-06-19 23:08:45','2025-06-19 23:08:45','2026-07-09 17:55:42',NULL,NULL,'woocommerce_user',0,'ef49329ba0fed0e','502a12',NULL,'2026-07-09 17:55:42',NULL,'2025-06-19 23:08:45',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (19,16,0,'LOKENDRA','SAINGAR','Lokendra07@outlook.com','unconfirmed',NULL,NULL,NULL,NULL,'2025-09-05 03:52:56','2026-07-09 17:55:42',NULL,NULL,'wordpress_user',0,'1fb1a169d66c7f2','60be2a',NULL,'2026-07-09 17:55:42',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (20,26,0,'ChatGPT','Helpe','Loken07@yahoo.com','unconfirmed',NULL,NULL,NULL,NULL,'2025-09-09 23:46:55','2026-07-18 03:46:01',NULL,NULL,'administrator',0,'681d0b71a647b9b','7acd9c',NULL,'2026-07-18 03:46:01',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (21,27,0,'ChatGPT','Helper','loken9350@gmail.com','unconfirmed',NULL,NULL,NULL,NULL,'2025-09-10 00:05:43','2026-07-18 03:46:01',NULL,NULL,'administrator',0,'6a60c23324a4769','485bee',NULL,'2026-07-18 03:46:01','2025-09-10 06:00:55',NULL,0,NULL,NULL,NULL,NULL,'2025-09-10 06:00:55',NULL,NULL,NULL,NULL,NULL,1);
INSERT INTO `fqsi_mailpoet_subscribers` (`id`, `wp_user_id`, `is_woocommerce_user`, `first_name`, `last_name`, `email`, `status`, `subscribed_ip`, `confirmed_ip`, `confirmed_at`, `last_subscribed_at`, `created_at`, `updated_at`, `deleted_at`, `unconfirmed_data`, `source`, `count_confirmations`, `unsubscribe_token`, `link_token`, `engagement_score`, `engagement_score_updated_at`, `last_engagement_at`, `woocommerce_synced_at`, `email_count`, `last_sending_at`, `last_open_at`, `last_click_at`, `last_purchase_at`, `last_page_view_at`, `last_confirmation_email_sent_at`, `time_zone`, `time_zone_source`, `time_zone_confidence`, `time_zone_updated_at`, `segments_count`) VALUES (22,29,0,'Chagpt','User','xdevoty@gmail.com','unconfirmed',NULL,NULL,NULL,NULL,'2025-09-10 03:38:45','2026-07-18 03:46:01',NULL,NULL,'wordpress_user',0,'bcf503654ca1a87','25de9b',NULL,'2026-07-18 03:46:01','2025-09-10 06:24:17',NULL,0,NULL,NULL,NULL,NULL,'2025-09-10 06:24:17',NULL,NULL,NULL,NULL,NULL,1);
