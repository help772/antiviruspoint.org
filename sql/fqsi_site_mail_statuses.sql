/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_site_mail_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` varchar(3) DEFAULT 'to' COMMENT 'to|cc|bcc',
  `status` varchar(255) DEFAULT 'pending' COMMENT 'pending|accepted|processed|delivered|bounce|dropped|deferred|not sent|rate limit|not valid|unsubscribed',
  `opened` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `log_id` (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
