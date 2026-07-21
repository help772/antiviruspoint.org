/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_yith_wcas_query_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT 0,
  `query` varchar(200) NOT NULL,
  `search_date` datetime NOT NULL,
  `num_results` int(11) DEFAULT 0,
  `clicked_product` bigint(20) DEFAULT 0,
  `lang` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `index_query` (`query`),
  KEY `index_query_lang_search_date` (`query`,`lang`,`search_date` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
