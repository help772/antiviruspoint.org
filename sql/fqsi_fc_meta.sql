/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_type` varchar(50) NOT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `key` varchar(192) NOT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_mt__mt_idx` (`object_type`),
  KEY `fqsi_fc_mt__mto_id_idx` (`object_id`),
  KEY `fqsi_fc_mt__mto_id_key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_fc_meta` (`id`, `object_type`, `object_id`, `key`, `value`, `created_at`, `updated_at`) VALUES (1,'option',NULL,'fluentcrm_is_sending_emails_last_called','1750541815','2025-06-21 20:39:50','2025-06-21 21:36:55');
