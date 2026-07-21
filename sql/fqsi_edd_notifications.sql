/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_edd_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `remote_id` varchar(20) DEFAULT NULL,
  `source` varchar(20) NOT NULL DEFAULT 'api',
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `buttons` longtext DEFAULT NULL,
  `type` varchar(64) NOT NULL DEFAULT 'success',
  `conditions` longtext DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `dismissed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `dismissed_start_end` (`dismissed`,`start`,`end`),
  KEY `remote_id` (`remote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_edd_notifications` (`id`, `remote_id`, `source`, `title`, `content`, `buttons`, `type`, `conditions`, `start`, `end`, `dismissed`, `date_created`, `date_updated`) VALUES (1,'124','api','[New!] Customize Your Checkout Address Fields!','🎉 The latest version of Easy Digital Downloads is here!\r\n\r\nIn EDD 3.3.8, you customize the address fields on your checkout without any coding or additional plugins! Remove unnecessary fields, add necessary fields, re-order them all to help boost your conversions.\r\n\r\nThere\'s a bonus feature we snuck in here to, that\'s worth calling home about...\r\n\r\nHead over and read more about this feature and update today to take advantage of it!','[{\"type\":\"primary\",\"url\":\"https://easydigitaldownloads.com/blog/new-customize-checkout-address-fields/\",\"text\":\"Learn More\"}]','success',NULL,'2026-07-02 19:02:53',NULL,0,'2026-05-01 17:03:44','2026-07-19 19:23:57');
INSERT INTO `fqsi_edd_notifications` (`id`, `remote_id`, `source`, `title`, `content`, `buttons`, `type`, `conditions`, `start`, `end`, `dismissed`, `date_created`, `date_updated`) VALUES (2,'166','api','New free feature: Customer Right of Withdrawal','<span style=\"font-weight: 400;\">Selling to customers in the EU? The new Customer Right of Withdrawal feature adds the required online withdrawal function to your store. Customers submit a withdrawal declaration in two steps, and you review, accept, or decline each request right from your dashboard. It\'s free for all Easy Digital Downloads users.</span>','[{\"type\":\"primary\",\"url\":\"https://easydigitaldownloads.com/docs/right-of-withdrawal/\",\"text\":\"Get the feature\"}]','success','[\"free\"]','2026-06-23 00:00:00',NULL,0,'2026-06-24 18:35:53','2026-07-19 19:23:57');
