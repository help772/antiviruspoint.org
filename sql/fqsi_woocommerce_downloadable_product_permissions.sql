/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_downloadable_product_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `download_id` varchar(36) NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `order_key` varchar(200) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `downloads_remaining` varchar(9) DEFAULT NULL,
  `access_granted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_expires` datetime DEFAULT NULL,
  `download_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`permission_id`),
  KEY `download_order_key_product` (`product_id`,`order_id`,`order_key`(16),`download_id`),
  KEY `download_order_product` (`download_id`,`order_id`,`product_id`),
  KEY `order_id` (`order_id`),
  KEY `user_order_remaining_expires` (`user_id`,`order_id`,`downloads_remaining`,`access_expires`),
  KEY `idx_user_email` (`user_email`(100))
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (1,'ec3d4018-a0d2-415e-bc51-3876dadb8e86',90006987,90007181,'wc_order_J9bkXHY3ysJSN','divyanshu.tiwari@thecodeyogi.com',0,'','2025-01-17 00:00:00',NULL,1);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (2,'26999cd4-b527-4ccd-afc1-50fe21b7be78',90006994,90007474,'wc_order_c8ZpOSgblJ6BE','anuraghandique.dev@gmail.com',1,'','2025-02-13 00:00:00',NULL,2);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (3,'e6324928-d10b-4168-8e6b-cee8d5b94b2d',90007366,90007476,'wc_order_WmCr7c2Yvxn6E','anuraghandique.dev@gmail.com',0,'','2025-02-13 00:00:00',NULL,1);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (4,'e6324928-d10b-4168-8e6b-cee8d5b94b2d',90007366,90007477,'wc_order_EvlDpIo17j3c7','anuraghandique.dev@gmail.com',0,'','2025-02-13 00:00:00',NULL,0);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (5,'e6324928-d10b-4168-8e6b-cee8d5b94b2d',90007366,90007478,'wc_order_IVy9lGexQvUzK','anuraghandique.dev@gmail.com',0,'','2025-02-13 00:00:00',NULL,0);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (6,'e6324928-d10b-4168-8e6b-cee8d5b94b2d',90007366,90007481,'wc_order_i51wOwlo7KpvU','rajnish.k@thecodeyogi.com',0,'','2025-02-14 00:00:00',NULL,1);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (7,'e6324928-d10b-4168-8e6b-cee8d5b94b2d',90007366,90007487,'wc_order_baPUcWUV9LfIy','info@thecodeyogi.com',0,'','2025-02-14 00:00:00',NULL,0);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (8,'e6324928-d10b-4168-8e6b-cee8d5b94b2d',90007366,90007491,'wc_order_NuEF3ZRf1qh3H','anuraghandique.dev@gmail.com',0,'1','2025-06-18 00:00:00','2025-06-28 00:00:00',0);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (9,'ec3d4018-a0d2-415e-bc51-3876dadb8e86',90006987,90007023,'wc_order_9KKXphy6GxfX3','divyanshu.tiwari@thecodeyogi.com',0,'','2025-06-23 00:00:00',NULL,0);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (10,'16512170-cb78-4cff-bffc-a541f3ec0da0',90006997,90007023,'wc_order_9KKXphy6GxfX3','divyanshu.tiwari@thecodeyogi.com',0,'','2025-06-23 00:00:00',NULL,0);
INSERT INTO `fqsi_woocommerce_downloadable_product_permissions` (`permission_id`, `download_id`, `product_id`, `order_id`, `order_key`, `user_email`, `user_id`, `downloads_remaining`, `access_granted`, `access_expires`, `download_count`) VALUES (11,'72ff7961-7449-49be-9905-29642ce035ab',90006998,90007025,'wc_order_ckRKdqIJfUMRh','divyanshu.tiwari@thecodeyogi.com',0,'','2025-06-23 00:00:00',NULL,0);
