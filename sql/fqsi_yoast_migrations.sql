/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_yoast_migrations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fqsi_yoast_migrations_version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (1,'20171228151840');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (2,'20171228151841');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (3,'20190529075038');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (25,'20190715101200');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (4,'20191011111109');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (5,'20200408101900');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (6,'20200420073606');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (7,'20200428123747');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (8,'20200428194858');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (9,'20200429105310');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (10,'20200430075614');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (11,'20200430150130');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (12,'20200507054848');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (13,'20200513133401');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (14,'20200609154515');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (15,'20200616130143');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (16,'20200617122511');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (17,'20200702141921');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (18,'20200728095334');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (19,'20201202144329');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (20,'20201216124002');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (21,'20201216141134');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (22,'20210817092415');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (26,'20210827093024');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (23,'20211020091404');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (24,'20230417083836');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (27,'20260105111111');
INSERT INTO `fqsi_yoast_migrations` (`id`, `version`) VALUES (28,'20260325155530');
