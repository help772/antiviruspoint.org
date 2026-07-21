/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_taxjar_record_queue` (
  `queue_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_id` bigint(20) unsigned NOT NULL,
  `record_type` varchar(200) NOT NULL,
  `force_push` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(200) NOT NULL DEFAULT 'new',
  `batch_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `retry_count` smallint(4) NOT NULL DEFAULT 0,
  `last_error` text NOT NULL DEFAULT '',
  PRIMARY KEY (`queue_id`),
  KEY `record_id` (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
