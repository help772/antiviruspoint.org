/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_subscriber_segment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) unsigned NOT NULL,
  `segment_id` int(11) unsigned NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'subscribed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_segment` (`subscriber_id`,`segment_id`),
  KEY `segment_id` (`segment_id`),
  KEY `segment_id_status` (`segment_id`,`status`,`subscriber_id`)
) ENGINE=InnoDB AUTO_INCREMENT=90086 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (1,3,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (2,4,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (3,1,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (4,6,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (5,8,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (6,9,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (7,5,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (8,2,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (9,7,1,'subscribed','2025-06-19 19:41:23','2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90024,2,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90025,4,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90026,5,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90027,6,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90028,8,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90029,9,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90030,16,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90031,17,2,'subscribed','2025-06-19 23:08:45','2025-06-19 23:08:45');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90034,19,1,'subscribed','2025-09-05 03:52:56','2025-09-05 03:52:56');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90036,20,1,'subscribed','2025-09-09 23:46:55','2025-09-09 23:46:55');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90037,21,1,'subscribed','2025-09-10 00:05:43','2025-09-10 00:05:43');
INSERT INTO `fqsi_mailpoet_subscriber_segment` (`id`, `subscriber_id`, `segment_id`, `status`, `created_at`, `updated_at`) VALUES (90038,22,1,'subscribed','2025-09-10 03:38:45','2025-09-10 03:38:45');
