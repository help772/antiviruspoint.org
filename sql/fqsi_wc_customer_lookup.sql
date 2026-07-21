/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wc_customer_lookup` (
  `customer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(60) NOT NULL DEFAULT '',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date_last_active` timestamp NULL DEFAULT NULL,
  `date_registered` timestamp NULL DEFAULT NULL,
  `country` char(2) NOT NULL DEFAULT '',
  `postcode` varchar(20) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `state` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (3,NULL,'','Michael','Crose','Mandrewc@comcast.net','2025-01-21 15:23:28',NULL,'US','98516-1426','Lacey','WA');
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (4,NULL,'','frank','test','frankd@gmail.com','2025-01-28 17:01:07',NULL,'US','48006','new york','CA');
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (15,12,'madav6310','Lokendra singh','Saingar','madav6310@gmail.com','2025-06-28 23:30:15','2025-06-19 05:46:36','US','45008','Midland','MI');
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (16,16,'lokendra07','LOKENDRA','SAINGAR','Lokendra07@outlook.com','2025-06-27 19:18:03','2025-06-27 19:16:35','US','45008','karauli','CO');
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (24,NULL,'','Thomas','VanCaster','tomvancaster@yahoo.com','2025-07-04 18:24:59',NULL,'US','','','');
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (28,NULL,'','Astitva','Pathak','astitva@gmail.com','2025-09-03 18:14:36',NULL,'US','90001','Raipur','CA');
INSERT INTO `fqsi_wc_customer_lookup` (`customer_id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `date_last_active`, `date_registered`, `country`, `postcode`, `city`, `state`) VALUES (29,1,'dikshit@thecodeyogi.com','Lokendra','Singar','infinity@antiviruspoint.org','2026-07-20 16:30:44','2025-01-16 10:21:03','','','','UP');
