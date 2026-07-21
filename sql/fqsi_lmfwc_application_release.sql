/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_lmfwc_application_release` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `version` varchar(255) NOT NULL,
  `download_type` varchar(255) NOT NULL,
  `download_file` text NOT NULL,
  `changelog` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT 'Creation Date',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL COMMENT 'Update Date',
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
