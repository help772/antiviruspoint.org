/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_actionscheduler_claims` (
  `claim_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date_created_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`claim_id`),
  KEY `date_created_gmt` (`date_created_gmt`)
) ENGINE=InnoDB AUTO_INCREMENT=450126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (137260,'2025-09-23 17:20:54');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (140306,'2025-09-26 15:30:58');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (183280,'2025-11-07 10:05:49');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (216631,'2025-12-11 10:45:12');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (265589,'2026-01-29 22:55:52');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (284787,'2026-02-17 15:59:57');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (336983,'2026-04-14 15:49:53');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (364551,'2026-05-11 14:00:31');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (403408,'2026-06-18 13:30:44');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (412944,'2026-06-26 13:45:43');
INSERT INTO `fqsi_actionscheduler_claims` (`claim_id`, `date_created_gmt`) VALUES (428855,'2026-07-07 00:05:51');
