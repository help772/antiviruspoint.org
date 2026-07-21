/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_api_keys` (
  `key_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `permissions` varchar(10) NOT NULL,
  `consumer_key` char(64) NOT NULL,
  `consumer_secret` char(43) NOT NULL,
  `nonces` longtext DEFAULT NULL,
  `truncated_key` char(7) NOT NULL,
  `last_access` datetime DEFAULT NULL,
  PRIMARY KEY (`key_id`),
  KEY `consumer_key` (`consumer_key`),
  KEY `consumer_secret` (`consumer_secret`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_woocommerce_api_keys` (`key_id`, `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `nonces`, `truncated_key`, `last_access`) VALUES (2,12,'Antiviruspoint.org','read_write','0375ca273e36a4b92192a62831b3585b5ec09418174b4b61fea70bd503d0e65b','cs_6b8174af5a82aeb3022dbf6081830ad4335dc479',NULL,'1a9d69c',NULL);
INSERT INTO `fqsi_woocommerce_api_keys` (`key_id`, `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `nonces`, `truncated_key`, `last_access`) VALUES (3,12,'WooCommerce By Meta - API (2025-08-01 08:43:16)','read_write','60ae8ac439b0761738aba3c1c345a398648f96e48dae8ade9596e9602b545ae3','cs_1848ce277595d7bd24f9b713697396ffc43becfd',NULL,'8bdaf09','2025-08-01 08:43:19');
INSERT INTO `fqsi_woocommerce_api_keys` (`key_id`, `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `nonces`, `truncated_key`, `last_access`) VALUES (4,12,'Predis App - API (2025-08-03 15:28:09)','read','4ea5748fbc919a9ca9392f375b6e10bcfa429b262c42ad9519334e62b20020cc','cs_bbc44457a0fe0d7538049b9dee601dd34ef6c18c',NULL,'d796ec6','2025-08-03 15:59:15');
INSERT INTO `fqsi_woocommerce_api_keys` (`key_id`, `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `nonces`, `truncated_key`, `last_access`) VALUES (5,12,'Klaviyo - API (2025-08-10 04:36:15)','read_write','fc1d986323f8e98e721fe7c6a18acf6c94d17d559527061e795618171c46ae9f','cs_18e3058e4594f29347c7059d594426922cb68c58',NULL,'d63cb2f','2025-09-09 18:43:22');
INSERT INTO `fqsi_woocommerce_api_keys` (`key_id`, `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `nonces`, `truncated_key`, `last_access`) VALUES (6,12,'Effortless Marketing - API (2025-09-05 03:26:59)','read','de3f72b0e3b7f448de170c3df960c7b43e4d0be33312b9b4c8e0e1ae7c41a38f','cs_f8159054e059fa97c78bfbb387f714eaabeaf92c',NULL,'3432c7f','2025-09-06 03:57:36');
