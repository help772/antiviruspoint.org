/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wfstatus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ctime` double(17,6) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `type` char(5) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `k1` (`ctime`),
  KEY `k2` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (1,1750446082.157264,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (2,1750448024.657052,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (3,1750448024.725575,2,'info','Rescheduled missing hourly cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (4,1750448025.745049,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (5,1750448025.821512,2,'info','Rescheduled missing hourly cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (6,1750529627.045215,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (7,1750529627.129417,2,'info','Rescheduled missing hourly cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (8,1751941533.097631,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (9,1751941533.201848,2,'info','Rescheduled missing hourly cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (10,1751941533.249810,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (11,1751941533.737128,2,'info','Rescheduled missing daily cron');
INSERT INTO `fqsi_wfstatus` (`id`, `ctime`, `level`, `type`, `msg`) VALUES (12,1751941533.803638,2,'info','Rescheduled missing hourly cron');
