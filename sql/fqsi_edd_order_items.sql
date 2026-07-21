/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_edd_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `order_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `product_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `product_name` text NOT NULL DEFAULT '',
  `price_id` bigint(20) unsigned DEFAULT NULL,
  `cart_index` bigint(20) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'download',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `subtotal` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `discount` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `tax` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `total` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `rate` decimal(10,5) NOT NULL DEFAULT 1.00000,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
  `uuid` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `order_product_price_id` (`order_id`,`product_id`,`price_id`),
  KEY `type_status` (`type`,`status`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
