/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_litespeed_img_optming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `optm_status` tinyint(4) NOT NULL DEFAULT 0,
  `src` varchar(1000) NOT NULL DEFAULT '',
  `server_info` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `optm_status` (`optm_status`),
  KEY `src` (`src`(191))
) ENGINE=InnoDB AUTO_INCREMENT=1588 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
