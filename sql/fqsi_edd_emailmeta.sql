/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_edd_emailmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `edd_email_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `email_id` (`edd_email_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (1,1,'legacy','purchase_receipt');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (2,1,'legacy','purchase_subject');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (3,1,'legacy','purchase_heading');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (4,2,'legacy','sale_notification');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (5,2,'legacy','sale_notification_subject');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (6,2,'legacy','sale_notification_heading');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (7,2,'legacy','disable_admin_notices');
INSERT INTO `fqsi_edd_emailmeta` (`meta_id`, `edd_email_id`, `meta_key`, `meta_value`) VALUES (8,2,'recipients','admin');
