/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT 0,
  `display_name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (1,'dikshit@thecodeyogi.com','$wp$2y$10$JoXGiGYFr7N/gIlXRWUIh.BwIjgVgQTILDum/dwPA4DL6Gk77abM2','dikshitthecodeyogi-com','infinity@antiviruspoint.org','https://antiviruspoint.org','2025-01-16 10:21:03','',0,'Lokendra');
INSERT INTO `fqsi_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (12,'madav6310','$wp$2y$10$nvfKtxT7x0UFTujdrG28juA6GIXFNLLw0eLhDkThsD9C9.KtgHb2C','antiviruspointorg','madav6310@gmail.com','https://Antiviruspoint.org','2025-06-19 05:46:36','',0,'Antiviruspoint.org');
INSERT INTO `fqsi_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (16,'lokendra07','$wp$2y$10$Tfd6okCdDJzWvKN.9f0MieEd415aCRbbreXcbV9JOn8qlj6N6uMIa','lokendra07','Lokendra07@outlook.com','','2025-06-27 19:16:35','1751411132:$generic$EHj_BAdjRCbmVR9engmFV9GXrJExfmkK4ncTc2Nx',0,'lokendra07');
INSERT INTO `fqsi_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (26,'chatgpt-helper','$wp$2y$10$yKezaGKsxqdZpVYA2jE.9e/t7SsthCbW8llLUW9m1V4ta5Q2.nw3u','chatgpt-helper','Loken07@yahoo.com','https://antiviruspoint.org/','2025-09-09 23:46:55','',0,'ChatGPT Helpe');
INSERT INTO `fqsi_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (27,'Loken','$wp$2y$10$CqObC2Mnm2ECJjIqj1fSx.QgY0s0RmUvHxPe3CNsAoBUaJzRdXXAO','loken','loken9350@gmail.com','','2025-09-10 00:05:42','',0,'ChatGPT Helper');
INSERT INTO `fqsi_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (29,'chagptuser','$wp$2y$10$0YLqIkJ7DVg0ruX1Et6VL.XYk1r.a88nfkpc9TcgeCjzzBsco/ctG','chagptuser','xdevoty@gmail.com','','2025-09-10 03:38:44','',0,'Chagpt User');
