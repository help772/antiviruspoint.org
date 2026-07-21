/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_gla_shipping_times` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `country` varchar(2) NOT NULL,
  `time` bigint(20) NOT NULL DEFAULT 0,
  `max_time` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `country` (`country`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_gla_shipping_times` (`id`, `country`, `time`, `max_time`) VALUES (1,'CA',1,1);
INSERT INTO `fqsi_gla_shipping_times` (`id`, `country`, `time`, `max_time`) VALUES (2,'US',1,1);
