/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_prl_tracking_conversions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deployment_id` bigint(20) unsigned NOT NULL,
  `engine_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `product_qty` int(10) unsigned NOT NULL,
  `location_hash` char(7) NOT NULL,
  `source_hash` char(32) DEFAULT '',
  `order_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `added_to_cart_time` int(10) unsigned NOT NULL,
  `ordered_time` int(10) unsigned NOT NULL,
  `total` double DEFAULT NULL,
  `total_tax` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deployment_id` (`deployment_id`),
  KEY `engine_id` (`engine_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
