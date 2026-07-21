/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_migrations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `retries` int(11) unsigned NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (1,'Migration_20221028_105818','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (2,'Migration_20221110_151621','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (3,'Migration_20230111_120000','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (4,'Migration_20230111_130000','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (5,'Migration_20230215_050813','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (6,'Migration_20230221_200520','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (7,'Migration_20230421_135915','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (8,'Migration_20230503_210945','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (9,'Migration_20230605_174836','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (10,'Migration_20230703_105957','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (11,'Migration_20230716_130221_Db','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (12,'Migration_20230824_054259_Db','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (13,'Migration_20230831_124214_Db','2025-06-19 19:41:21','2025-06-19 19:41:21',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (14,'Migration_20230831_143755_Db','2025-06-19 19:41:21','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (15,'Migration_20240119_113943_Db','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (16,'Migration_20240617_122847_Db','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (17,'Migration_20240725_182318_Db','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (18,'Migration_20241007_170437_Db','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (19,'Migration_20241108_103249_Db','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (20,'Migration_20221028_105818_App','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (21,'Migration_20230109_144830','2025-06-19 19:41:22','2025-06-19 19:41:22',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (22,'Migration_20230131_121621','2025-06-19 19:41:22','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (23,'Migration_20230419_080000','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (24,'Migration_20230425_211517','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (25,'Migration_20230712_180341','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (26,'Migration_20230803_200413_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (27,'Migration_20230825_093531_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (28,'Migration_20231128_120355_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (29,'Migration_20240202_130053_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (30,'Migration_20240207_105912_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (31,'Migration_20240322_110443_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (32,'Migration_20240730_212419_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (33,'Migration_20241015_105511_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (34,'Migration_20241128_114257_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (35,'Migration_20250120_094614_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (36,'Migration_20250501_114655_App','2025-06-19 19:41:23','2025-06-19 19:41:23',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (37,'Migration_20250903_151331_Db','2025-09-09 16:33:02','2025-09-09 16:33:02',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (38,'Migration_20250926_153050_Db','2025-10-01 15:24:48','2025-10-01 15:24:48',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (39,'Migration_20260421_155908_App','2026-04-28 15:00:27','2026-04-28 15:00:27',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (40,'Migration_20260415_090055_Db','2026-05-05 15:15:10','2026-05-05 15:15:10',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (41,'Migration_20260427_100000','2026-05-05 15:15:10','2026-05-05 15:15:10',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (42,'Migration_20260428_120000','2026-05-05 15:15:10','2026-05-05 15:15:10',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (43,'Migration_20260430_103000_Db','2026-05-05 15:15:10','2026-05-05 15:15:10',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (44,'Migration_20260430_120000','2026-05-05 15:15:10','2026-05-05 15:15:10',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (45,'Migration_20260504_120000_Db','2026-05-12 14:50:30','2026-05-12 14:50:30',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (46,'Migration_20260514_120000_Db','2026-05-19 14:53:36','2026-05-19 14:53:36',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (47,'Migration_20260515_120000_App','2026-05-19 14:53:36','2026-05-19 14:53:36',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (48,'Migration_20260609_120000_Db','2026-06-17 15:10:42','2026-06-17 15:10:42',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (49,'Migration_20260610_120000_Db','2026-06-24 15:00:44','2026-06-24 15:00:44',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (50,'Migration_20260622_120000_Db','2026-07-07 14:50:47','2026-07-07 14:50:47',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (51,'Migration_20260623_120000_App','2026-07-07 14:50:47','2026-07-07 14:50:47',0,NULL);
INSERT INTO `fqsi_mailpoet_migrations` (`id`, `name`, `started_at`, `completed_at`, `retries`, `error`) VALUES (52,'Migration_20260709_120000_Db','2026-07-14 14:51:08','2026-07-14 14:51:08',0,NULL);
