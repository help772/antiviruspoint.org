/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_prl_frequencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  `context` varchar(32) NOT NULL DEFAULT 'order',
  `product_id` bigint(20) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `base_total` int(10) unsigned NOT NULL,
  `expire_date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
