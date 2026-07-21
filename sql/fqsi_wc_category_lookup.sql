/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wc_category_lookup` (
  `category_tree_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`category_tree_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (15,15);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (59,59);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (60,60);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (60,64);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (61,61);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (62,62);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (63,63);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (64,64);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (68,68);
INSERT INTO `fqsi_wc_category_lookup` (`category_tree_id`, `category_id`) VALUES (97,97);
