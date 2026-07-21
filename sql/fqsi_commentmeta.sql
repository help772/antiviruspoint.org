/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (15,28,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (16,28,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (17,29,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (18,29,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (19,30,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (20,30,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (21,31,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (22,31,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (23,32,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (24,32,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (25,33,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (26,33,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (27,34,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (28,34,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (29,35,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (30,35,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (31,36,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (32,36,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (33,37,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (34,37,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (35,38,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (36,38,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (37,39,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (38,39,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (39,40,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (40,40,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (41,41,'rating','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (42,41,'verified','0');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (43,85,'verified','1');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (44,41,'akismet_history','a:3:{s:4:\"time\";d:1750405335.343189;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (45,40,'akismet_history','a:3:{s:4:\"time\";d:1750405335.603516;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (46,39,'akismet_history','a:3:{s:4:\"time\";d:1750405335.703071;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (47,38,'akismet_history','a:3:{s:4:\"time\";d:1750405335.802942;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (48,37,'akismet_history','a:3:{s:4:\"time\";d:1750405336.059655;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (49,34,'akismet_history','a:3:{s:4:\"time\";d:1750405336.163035;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (50,33,'akismet_history','a:3:{s:4:\"time\";d:1750405336.267339;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (51,32,'akismet_history','a:3:{s:4:\"time\";d:1750405336.53187;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (52,31,'akismet_history','a:3:{s:4:\"time\";d:1750405337.043547;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (53,30,'akismet_history','a:3:{s:4:\"time\";d:1750405337.14738;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (54,29,'akismet_history','a:3:{s:4:\"time\";d:1750405337.251511;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (55,28,'akismet_history','a:3:{s:4:\"time\";d:1750405337.540162;s:5:\"event\";s:15:\"status-approved\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (56,37,'akismet_history','a:3:{s:4:\"time\";d:1751247746.896265;s:5:\"event\";s:12:\"status-trash\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (59,30,'akismet_history','a:3:{s:4:\"time\";d:1751247896.712383;s:5:\"event\";s:12:\"status-trash\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (62,41,'akismet_history','a:3:{s:4:\"time\";d:1752029420.67621;s:5:\"event\";s:12:\"status-trash\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (65,39,'akismet_history','a:3:{s:4:\"time\";d:1752029426.679714;s:5:\"event\";s:12:\"status-trash\";s:4:\"user\";s:9:\"madav6310\";}');
INSERT INTO `fqsi_commentmeta` (`meta_id`, `comment_id`, `meta_key`, `meta_value`) VALUES (68,34,'akismet_history','a:3:{s:4:\"time\";d:1752029432.220174;s:5:\"event\";s:12:\"status-trash\";s:4:\"user\";s:9:\"madav6310\";}');
