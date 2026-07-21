/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_dlm_licenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `license_key` longtext NOT NULL COMMENT 'Encrypted License Key',
  `hash` longtext NOT NULL COMMENT 'Hashed License Key ID	',
  `valid_for` int(10) unsigned DEFAULT NULL COMMENT 'Valid for X time (when ordered from stock)',
  `expires_at` datetime DEFAULT NULL COMMENT 'Expiration Date',
  `source` varchar(255) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `activations_limit` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT 'Creation Date',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL COMMENT 'Update Date',
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
