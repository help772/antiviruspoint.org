/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_automatewoo_abandoned_carts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) NOT NULL DEFAULT '',
  `user_id` bigint(20) NOT NULL DEFAULT 0,
  `guest_id` bigint(20) NOT NULL DEFAULT 0,
  `last_modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `items` longtext NOT NULL,
  `coupons` longtext NOT NULL,
  `fees` longtext NOT NULL,
  `shipping_tax_total` double NOT NULL DEFAULT 0,
  `shipping_total` double NOT NULL DEFAULT 0,
  `total` double NOT NULL DEFAULT 0,
  `token` varchar(32) NOT NULL DEFAULT '',
  `currency` varchar(8) NOT NULL DEFAULT '',
  `has_been_abandoned` tinyint(1) NOT NULL DEFAULT 0,
  `shipping_total_is_calculated` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `guest_id` (`guest_id`),
  KEY `last_modified` (`last_modified`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
