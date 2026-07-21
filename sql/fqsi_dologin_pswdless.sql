/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_dologin_pswdless` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT 0,
  `hash` varchar(255) NOT NULL DEFAULT '',
  `src` varchar(255) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT 0,
  `dateline` int(11) NOT NULL DEFAULT 0,
  `last_used_at` int(11) NOT NULL DEFAULT 0,
  `expired_at` int(11) NOT NULL DEFAULT 0,
  `onetime` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_dologin_pswdless` (`id`, `user_id`, `hash`, `src`, `count`, `dateline`, `last_used_at`, `expired_at`, `onetime`, `active`) VALUES (1,12,'OM0fp7m6zVAlBCcKlNM6UoHac15z8hAz','Litespeed Report-Antiviruspoint.org',0,1751485326,0,1752090126,1,1);
