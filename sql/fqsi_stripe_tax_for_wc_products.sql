/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_stripe_tax_for_wc_products` (
  `product_id` bigint(20) unsigned NOT NULL,
  `tax_code` char(13) NOT NULL DEFAULT '',
  `tax_behavior` enum('','exclusive','inclusive') NOT NULL DEFAULT '',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
