/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_aiowps_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(150) NOT NULL DEFAULT '',
  `username` varchar(150) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `event_date` datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
  `created` int(10) unsigned DEFAULT NULL,
  `ip_or_host` varchar(100) DEFAULT NULL,
  `referer_info` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `country_code` varchar(50) DEFAULT NULL,
  `event_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (1,'404','',0,'2025-07-17 00:28:48',1752712128,'183.83.53.17','','/TCYsA9XbY72p3Qw42','IN','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (2,'404','',0,'2025-07-17 01:16:46',1752715006,'183.83.53.17','https://wordpress.com/','/wp-login.php?redirect_to=httpsantiviruspoint.orgwp-admincustomize.phpreturnhttps3A2F2Fwordpress.com2Foverview2Fantiviruspoint.orgthemefilson&reauth=1','IN','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (3,'404','',0,'2025-07-17 01:16:49',1752715009,'183.83.53.17','https://antiviruspoint.org/wp-login.php?redirect_to=httpsantiviruspoint.orgwp-admincustomize.phpreturnhttps3A2F2Fwordpress.com2Foverview2Fantiviruspoint.orgthemefilson&reauth=1','/wp-login.php?redirect_to=httpsantiviruspoint.orgwp-admincustomize.phpreturnhttps3A2F2Fwordpress.com2Foverview2Fantiviruspoint.orgthemefilson&reauth=1','IN','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (4,'404','',0,'2025-07-17 01:32:58',1752715978,'66.249.82.102','http://ghost-rider/','/wp-content/plugins/woocommerce-gateway-cybersource/assets/js/frontend/wc-cybersource.min.js.map','US','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (5,'404','',0,'2025-07-17 01:32:58',1752715978,'66.249.82.102','http://ghost-rider/','/wp-content/plugins/elementor/assets/lib/swiper/v8/swiper-bundle.min.js.map','US','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (6,'404','',0,'2025-07-17 01:32:58',1752715978,'66.249.82.102','http://ghost-rider/','/wp-content/plugins/woocommerce-gateway-cybersource/assets/js/frontend/wc-cybersource-visa-checkout.min.js.map','US','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (7,'404','',0,'2025-07-17 01:33:21',1752716002,'66.249.82.101','http://ghost-rider/','/wp-content/litespeed/js/wc-cybersource-visa-checkout.min.js.map','US','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (8,'404','',0,'2025-07-17 01:33:22',1752716002,'66.249.82.103','http://ghost-rider/','/wp-content/litespeed/js/swiper-bundle.min.js.map','US','');
INSERT INTO `fqsi_aiowps_events` (`id`, `event_type`, `username`, `user_id`, `event_date`, `created`, `ip_or_host`, `referer_info`, `url`, `country_code`, `event_data`) VALUES (9,'404','',0,'2025-07-17 01:33:22',1752716002,'66.249.82.103','http://ghost-rider/','/wp-content/litespeed/js/wc-cybersource.min.js.map','US','');
