/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_fc_term_relations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned DEFAULT NULL,
  `object_type` varchar(192) NOT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `settings` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fqsi_fc_tmr__tm_idx` (`term_id`),
  KEY `fqsi_fc_tmr__tm_id_type` (`object_type`),
  KEY `fqsi_fc_tmr__tm_id_idx` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
