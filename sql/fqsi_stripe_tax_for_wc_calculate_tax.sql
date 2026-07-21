/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_stripe_tax_for_wc_calculate_tax` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `time` bigint(20) NOT NULL,
  `calculate_tax_md5` char(32) NOT NULL,
  `tax_registrations_md5` char(32) NOT NULL,
  `tax_settings_md5` char(32) NOT NULL,
  `api_key_md5` char(32) NOT NULL,
  `request` longtext NOT NULL,
  `response` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `calculate_tax_uindex` (`calculate_tax_md5`,`tax_registrations_md5`,`tax_settings_md5`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
