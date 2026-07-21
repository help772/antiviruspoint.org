/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_gla_shipping_rates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `country` varchar(2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `rate` double NOT NULL DEFAULT 0,
  `options` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country` (`country`),
  KEY `currency` (`currency`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_gla_shipping_rates` (`id`, `country`, `currency`, `rate`, `options`) VALUES (1,'CA','USD',0,'[]');
INSERT INTO `fqsi_gla_shipping_rates` (`id`, `country`, `currency`, `rate`, `options`) VALUES (2,'US','USD',0,'[]');
