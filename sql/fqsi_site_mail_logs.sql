/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_site_mail_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `api_id` varchar(255) DEFAULT '',
  `to` text DEFAULT NULL,
  `subject` varchar(768) DEFAULT '',
  `headers` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `activity` text DEFAULT NULL,
  `source` text NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `opened` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `api_id` (`api_id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
