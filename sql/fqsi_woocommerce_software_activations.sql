/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_software_activations` (
  `activation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key_id` bigint(20) NOT NULL,
  `instance` varchar(200) NOT NULL,
  `activation_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation_active` int(1) NOT NULL DEFAULT 1,
  `activation_platform` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`activation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
