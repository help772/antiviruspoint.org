/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wc_product_download_directories` (
  `url_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(256) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`url_id`),
  KEY `url` (`url`(191))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (1,'file:///srv/htdocs/wp-content/uploads/woocommerce_uploads/',1);
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (2,'https://antiviruspointorgdomainonly.wpcomstaging.com/wp-content/uploads/woocommerce_uploads/',1);
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (3,'https://antiviruspointorgdomainonly.wpcomstaging.com/wp-content/uploads/woocommerce_uploads/2023/05/',1);
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (4,'https://antiviruspointorgdomainonly.wpcomstaging.com/wp-content/uploads/woocommerce_uploads/2023/07/',1);
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (5,'https://antiviruspoint.org/wp-content/uploads/woocommerce_uploads/2025/01/',1);
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (6,'https://antiviruspoint.org/wp-content/uploads/woocommerce_uploads/2023/05/',1);
INSERT INTO `fqsi_wc_product_download_directories` (`url_id`, `url`, `enabled`) VALUES (7,'https://antiviruspoint.org/wp-content/uploads/woocommerce_uploads/2023/07/',1);
