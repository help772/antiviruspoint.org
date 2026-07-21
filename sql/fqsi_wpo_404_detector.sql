/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wpo_404_detector` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `request_timestamp` bigint(20) unsigned NOT NULL,
  `request_count` bigint(20) unsigned NOT NULL,
  `referrer` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `url` (`url`(75),`request_timestamp`,`referrer`(75)),
  KEY `url_timestamp_referrer` (`url`(75),`request_timestamp`,`referrer`(75)),
  KEY `timestamp_count` (`request_timestamp`,`request_count`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (1,'https://antiviruspoint.org/wp-content/litespeed/js/index.min.js.map',1751490000,3,'http://ghost-rider/');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (2,'https://antiviruspoint.org/_jb_static/swiper-bundle.min.js.map',1751490000,1,'http://ghost-rider/');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (3,'https://antiviruspoint.org/_jb_static/index.min.js.map',1751490000,1,'http://ghost-rider/');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (6,'https://antiviruspoint.org/wp-content/litespeed/js/index.min.js.map',1751490000,1,'http://antiviruspoint.org/wp-cron.php?doing_wp_cron=1751490707.7272689342498779296875');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (7,'https://antiviruspoint.org/_jb_static/swiper-bundle.min.js.map',1751490000,2,'');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (8,'https://antiviruspoint.org/_jb_static/index.min.js.map',1751490000,2,'');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (11,'https://antiviruspoint.org/wp-admin/undefinedjetpack/v4/connection/data',1751490000,2,'https://antiviruspoint.org/wp-admin/plugins.php');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (12,'https://antiviruspoint.org/wp-admin/undefinedjetpack/v4/site/benefits',1751490000,2,'https://antiviruspoint.org/wp-admin/plugins.php');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (13,'https://antiviruspoint.org/wp-admin/undefinedjetpack/v4/connection/data',1751490000,1,'https://antiviruspoint.org/wp-admin/plugins.php?plugin_status=all&paged=1&s');
INSERT INTO `fqsi_wpo_404_detector` (`ID`, `url`, `request_timestamp`, `request_count`, `referrer`) VALUES (14,'https://antiviruspoint.org/wp-admin/undefinedjetpack/v4/site/benefits',1751490000,1,'https://antiviruspoint.org/wp-admin/plugins.php?plugin_status=all&paged=1&s');
