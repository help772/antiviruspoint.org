/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_gc_activity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `object_id` bigint(20) unsigned NOT NULL,
  `gc_id` bigint(20) unsigned NOT NULL,
  `gc_code` varchar(128) NOT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `date` int(10) unsigned NOT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `user_id` (`user_id`),
  KEY `object_id` (`object_id`),
  KEY `gc_id` (`gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
