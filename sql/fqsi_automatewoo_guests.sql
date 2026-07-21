/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_automatewoo_guests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `tracking_key` varchar(32) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `language` varchar(10) NOT NULL DEFAULT '',
  `most_recent_order` bigint(20) NOT NULL DEFAULT 0,
  `version` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tracking_key` (`tracking_key`),
  KEY `email` (`email`(191)),
  KEY `most_recent_order` (`most_recent_order`),
  KEY `version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_automatewoo_guests` (`id`, `email`, `tracking_key`, `created`, `last_active`, `language`, `most_recent_order`, `version`) VALUES (3,'tomvancaster@yahoo.com','','2025-07-04 18:25:01',NULL,'',0,0);
INSERT INTO `fqsi_automatewoo_guests` (`id`, `email`, `tracking_key`, `created`, `last_active`, `language`, `most_recent_order`, `version`) VALUES (4,'aurorabnkwy@gmailbrt.com','','2025-07-09 02:50:20',NULL,'',0,0);
INSERT INTO `fqsi_automatewoo_guests` (`id`, `email`, `tracking_key`, `created`, `last_active`, `language`, `most_recent_order`, `version`) VALUES (5,'astitva@gmail.com','','2025-09-03 18:14:37','2025-09-03 18:14:41','',0,0);
