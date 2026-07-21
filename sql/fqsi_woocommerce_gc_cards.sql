/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_woocommerce_gc_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(128) NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `redeemed_by` int(10) unsigned NOT NULL DEFAULT 0,
  `sender` varchar(128) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `message` longtext DEFAULT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `remaining` double NOT NULL DEFAULT 0,
  `template_id` varchar(128) NOT NULL DEFAULT 'default',
  `create_date` int(10) unsigned NOT NULL,
  `deliver_date` int(10) unsigned NOT NULL DEFAULT 0,
  `delivered` varchar(32) NOT NULL DEFAULT 'no',
  `expire_date` int(10) unsigned NOT NULL DEFAULT 0,
  `redeem_date` int(10) unsigned NOT NULL DEFAULT 0,
  `is_virtual` char(3) NOT NULL,
  `is_active` char(3) NOT NULL,
  `last_modify_date` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `remaining` (`remaining`),
  KEY `redeemed_by` (`redeemed_by`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
