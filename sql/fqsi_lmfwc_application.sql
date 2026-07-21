/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_lmfwc_application` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `stable_release_id` bigint(20) unsigned DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `documentation` longtext DEFAULT NULL,
  `support` longtext DEFAULT NULL,
  `gallery` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT 'Creation Date',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL COMMENT 'Update Date',
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_lmfwc_application` (`id`, `name`, `type`, `stable_release_id`, `description`, `documentation`, `support`, `gallery`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES (1,'Antiviruspoint.org','wordpress',0,NULL,NULL,NULL,'null','2025-06-24 20:29:58',12,'2025-07-04 16:57:12',12);
