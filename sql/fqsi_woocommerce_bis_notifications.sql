/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_bis_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(128) NOT NULL DEFAULT 'one-time',
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_email` varchar(191) NOT NULL,
  `create_date` int(10) unsigned NOT NULL DEFAULT 0,
  `subscribe_date` int(10) unsigned NOT NULL DEFAULT 0,
  `last_notified_date` int(10) unsigned NOT NULL DEFAULT 0,
  `is_queued` char(3) NOT NULL DEFAULT 'off',
  `is_active` char(3) NOT NULL DEFAULT 'off',
  `is_verified` char(3) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `user_email` (`user_email`),
  KEY `is_queued` (`is_queued`),
  KEY `is_active` (`is_active`),
  KEY `is_verified` (`is_verified`),
  KEY `idx_product_active_queue` (`product_id`,`is_active`,`is_queued`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
