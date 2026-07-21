/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_gla_merchant_price_benchmarks` (
  `product_id` bigint(20) NOT NULL,
  `mc_product_id` varchar(255) NOT NULL,
  `mc_product_offer_id` varchar(255) NOT NULL,
  `mc_product_price_micros` varchar(64) NOT NULL,
  `mc_product_currency_code` varchar(3) NOT NULL,
  `mc_price_country_code` varchar(2) NOT NULL,
  `mc_price_benchmark_price_micros` varchar(64) NOT NULL,
  `mc_price_benchmark_price_currency_code` varchar(3) NOT NULL,
  `mc_insights_suggested_price_micros` varchar(64) NOT NULL,
  `mc_insights_suggested_price_currency_code` varchar(3) NOT NULL,
  `mc_insights_predicted_impressions_change_fraction` decimal(10,6) NOT NULL,
  `mc_insights_predicted_clicks_change_fraction` decimal(10,6) NOT NULL,
  `mc_insights_predicted_conversions_change_fraction` decimal(10,6) NOT NULL,
  `mc_insights_effectiveness` tinyint(1) NOT NULL,
  `mc_metrics_clicks` varchar(64) NOT NULL,
  `mc_metrics_impressions` varchar(64) NOT NULL,
  `mc_metrics_ctr` int(20) NOT NULL,
  `mc_metrics_conversions` int(20) NOT NULL,
  `price_compared_with_benchmark` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `mc_product_id` (`mc_product_id`),
  KEY `mc_insights_effectiveness` (`mc_insights_effectiveness`),
  KEY `price_compared_with_benchmark` (`price_compared_with_benchmark`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_gla_merchant_price_benchmarks` (`product_id`, `mc_product_id`, `mc_product_offer_id`, `mc_product_price_micros`, `mc_product_currency_code`, `mc_price_country_code`, `mc_price_benchmark_price_micros`, `mc_price_benchmark_price_currency_code`, `mc_insights_suggested_price_micros`, `mc_insights_suggested_price_currency_code`, `mc_insights_predicted_impressions_change_fraction`, `mc_insights_predicted_clicks_change_fraction`, `mc_insights_predicted_conversions_change_fraction`, `mc_insights_effectiveness`, `mc_metrics_clicks`, `mc_metrics_impressions`, `mc_metrics_ctr`, `mc_metrics_conversions`, `price_compared_with_benchmark`) VALUES (90007188,'online:en:CA:gla_90007188','gla_90007188','49990000','USD','US','44001154','USD','','',0.000000,0.000000,0.000000,0,'0','0',0,0,3);
INSERT INTO `fqsi_gla_merchant_price_benchmarks` (`product_id`, `mc_product_id`, `mc_product_offer_id`, `mc_product_price_micros`, `mc_product_currency_code`, `mc_price_country_code`, `mc_price_benchmark_price_micros`, `mc_price_benchmark_price_currency_code`, `mc_insights_suggested_price_micros`, `mc_insights_suggested_price_currency_code`, `mc_insights_predicted_impressions_change_fraction`, `mc_insights_predicted_clicks_change_fraction`, `mc_insights_predicted_conversions_change_fraction`, `mc_insights_effectiveness`, `mc_metrics_clicks`, `mc_metrics_impressions`, `mc_metrics_ctr`, `mc_metrics_conversions`, `price_compared_with_benchmark`) VALUES (90007229,'online:en:CA:gla_90007229','gla_90007229','17950000','USD','US','25920701','USD','','',0.000000,0.000000,0.000000,0,'0','0',0,0,1);
