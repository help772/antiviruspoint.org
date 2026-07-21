/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_edd_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `name` varchar(200) NOT NULL DEFAULT '',
  `code` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `scope` varchar(20) NOT NULL DEFAULT 'all',
  `amount_type` varchar(20) NOT NULL DEFAULT '',
  `amount` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `description` longtext NOT NULL DEFAULT '',
  `max_uses` bigint(20) unsigned NOT NULL DEFAULT 0,
  `use_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `once_per_customer` int(1) NOT NULL DEFAULT 0,
  `min_charge_amount` decimal(18,9) NOT NULL DEFAULT 0.000000000,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
  `uuid` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `type_status` (`type`,`status`),
  KEY `type_status_dates` (`type`,`status`,`start_date`,`end_date`),
  KEY `code` (`code`),
  KEY `date_created` (`date_created`),
  KEY `date_start_end` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
