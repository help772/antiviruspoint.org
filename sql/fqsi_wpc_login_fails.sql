/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wpc_login_fails` (
  `login_attempt_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `login_attempt_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_attempt_IP` varchar(100) NOT NULL DEFAULT '',
  `failed_user` varchar(200) NOT NULL DEFAULT '',
  `failed_pass` varchar(200) NOT NULL DEFAULT '',
  `reason` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`login_attempt_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
