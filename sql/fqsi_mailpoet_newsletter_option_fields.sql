/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_newsletter_option_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `newsletter_type` varchar(90) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_newsletter_type` (`newsletter_type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (1,'isScheduled','standard',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (2,'scheduledAt','standard',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (3,'event','welcome',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (4,'segment','welcome',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (5,'role','welcome',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (6,'afterTimeNumber','welcome',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (7,'afterTimeType','welcome',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (8,'intervalType','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (9,'timeOfDay','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (10,'weekDay','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (11,'monthDay','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (12,'nthWeekDay','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (13,'schedule','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (14,'group','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (15,'group','automation',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (16,'group','automation_transactional',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (17,'event','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (18,'event','automation',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (19,'event','automation_transactional',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (20,'sendTo','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (21,'segment','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (22,'afterTimeNumber','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (23,'afterTimeType','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (24,'meta','automatic',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (25,'afterTimeNumber','re_engagement',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (26,'afterTimeType','re_engagement',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (27,'automationId','automation',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (28,'automationStepId','automation',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (29,'filterSegmentId','standard',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (30,'filterSegmentId','re_engagement',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (31,'filterSegmentId','notification',NULL,'2025-06-19 19:41:23');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (32,'scheduleMode','standard',NULL,'2026-05-05 15:15:10');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (33,'scheduledLocalDate','standard',NULL,'2026-05-05 15:15:10');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (34,'scheduledLocalTime','standard',NULL,'2026-05-05 15:15:10');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (35,'shareVisibility','standard',NULL,'2026-05-19 14:53:36');
INSERT INTO `fqsi_mailpoet_newsletter_option_fields` (`id`, `name`, `newsletter_type`, `created_at`, `updated_at`) VALUES (36,'excludeFromArchive','standard',NULL,'2026-06-02 20:13:27');
