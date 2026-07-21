/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wc_webhooks` (
  `webhook_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(200) NOT NULL,
  `name` text NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `delivery_url` text NOT NULL,
  `secret` text NOT NULL,
  `topic` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `api_version` smallint(4) NOT NULL,
  `failure_count` smallint(10) NOT NULL DEFAULT 0,
  `pending_delivery` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`webhook_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wc_webhooks` (`webhook_id`, `status`, `name`, `user_id`, `delivery_url`, `secret`, `topic`, `date_created`, `date_created_gmt`, `date_modified`, `date_modified_gmt`, `api_version`, `failure_count`, `pending_delivery`) VALUES (15,'disabled','WooPayments woopay order status sync',12,'https://pay.woo.com/wp-json/platform-checkout/v1/merchant-notification','zh86nwGoWne4dTYqyCCMI5hY5AFwCWkwZpkPY8e7cTNyqGVy5l','order.updated','2025-06-24 22:21:19','2025-06-24 22:21:19','2025-08-21 01:50:16','2025-08-21 01:50:16',3,6,1);
INSERT INTO `fqsi_wc_webhooks` (`webhook_id`, `status`, `name`, `user_id`, `delivery_url`, `secret`, `topic`, `date_created`, `date_created_gmt`, `date_modified`, `date_modified_gmt`, `api_version`, `failure_count`, `pending_delivery`) VALUES (16,'disabled','order.updated',12,'https://a.klaviyo.com/api/webhook/integration/woocommerce?c=RTQSZk','cs_18e3058e4594f29347c7059d594426922cb68c58','order.updated','2025-08-10 04:36:51','2025-08-10 04:36:51','2026-05-05 21:44:59','2026-05-05 21:44:59',3,6,0);
INSERT INTO `fqsi_wc_webhooks` (`webhook_id`, `status`, `name`, `user_id`, `delivery_url`, `secret`, `topic`, `date_created`, `date_created_gmt`, `date_modified`, `date_modified_gmt`, `api_version`, `failure_count`, `pending_delivery`) VALUES (17,'active','order.created',12,'https://a.klaviyo.com/api/webhook/integration/woocommerce?c=RTQSZk','cs_18e3058e4594f29347c7059d594426922cb68c58','order.created','2025-08-10 04:36:55','2025-08-10 04:36:55','2025-09-03 18:14:50','2025-09-03 18:14:50',3,0,0);
INSERT INTO `fqsi_wc_webhooks` (`webhook_id`, `status`, `name`, `user_id`, `delivery_url`, `secret`, `topic`, `date_created`, `date_created_gmt`, `date_modified`, `date_modified_gmt`, `api_version`, `failure_count`, `pending_delivery`) VALUES (18,'active','product.updated',12,'https://a.klaviyo.com/api/webhook/integration/woocommerce?c=RTQSZk','cs_18e3058e4594f29347c7059d594426922cb68c58','product.updated','2025-08-10 04:36:57','2025-08-10 04:36:57','2025-09-05 05:01:47','2025-09-05 05:01:47',3,0,0);
INSERT INTO `fqsi_wc_webhooks` (`webhook_id`, `status`, `name`, `user_id`, `delivery_url`, `secret`, `topic`, `date_created`, `date_created_gmt`, `date_modified`, `date_modified_gmt`, `api_version`, `failure_count`, `pending_delivery`) VALUES (19,'active','product.created',12,'https://a.klaviyo.com/api/webhook/integration/woocommerce?c=RTQSZk','cs_18e3058e4594f29347c7059d594426922cb68c58','product.created','2025-08-10 04:37:01','2025-08-10 04:37:01','2025-08-10 04:37:01','2025-08-10 04:37:01',3,0,0);
INSERT INTO `fqsi_wc_webhooks` (`webhook_id`, `status`, `name`, `user_id`, `delivery_url`, `secret`, `topic`, `date_created`, `date_created_gmt`, `date_modified`, `date_modified_gmt`, `api_version`, `failure_count`, `pending_delivery`) VALUES (20,'active','product.deleted',12,'https://a.klaviyo.com/api/webhook/integration/woocommerce?c=RTQSZk','cs_18e3058e4594f29347c7059d594426922cb68c58','product.deleted','2025-08-10 04:37:05','2025-08-10 04:37:05','2026-07-14 15:36:23','2026-07-14 15:36:23',3,0,0);
