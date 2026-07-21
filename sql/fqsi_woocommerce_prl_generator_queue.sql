/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_prl_generator_queue` (
  `item_key` varchar(191) NOT NULL,
  `deployment_id` int(10) unsigned NOT NULL,
  `data` longtext DEFAULT NULL,
  `added_time` int(10) unsigned NOT NULL,
  `iterations` int(10) unsigned NOT NULL,
  PRIMARY KEY (`item_key`),
  KEY `deployment_id` (`deployment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
