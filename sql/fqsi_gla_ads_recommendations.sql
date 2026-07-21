/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_gla_ads_recommendations` (
  `recommendation_id` bigint(20) NOT NULL,
  `recommendation_type` varchar(64) NOT NULL,
  `recommendation_resource_name` varchar(255) NOT NULL,
  `recommendation_campaign_id` bigint(20) NOT NULL,
  `recommendation_campaign_name` varchar(255) NOT NULL,
  `recommendation_campaign_status` varchar(64) NOT NULL,
  `recommendation_customer_id` bigint(20) NOT NULL,
  `recommendation_last_synced` datetime NOT NULL,
  PRIMARY KEY (`recommendation_id`),
  KEY `recommendation_type` (`recommendation_type`),
  KEY `recommendation_campaign_id` (`recommendation_campaign_id`),
  KEY `recommendation_customer_id` (`recommendation_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
