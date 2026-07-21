/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_mailpoet_automations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `author` bigint(20) NOT NULL,
  `status` varchar(191) NOT NULL,
  `meta` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_mailpoet_automations` (`id`, `name`, `author`, `status`, `meta`, `created_at`, `updated_at`, `activated_at`, `deleted_at`) VALUES (1,'Welcome new subscribers',12,'draft','{\"mailpoet:run-once-per-subscriber\":true}','2025-06-20 01:14:37','2025-06-20 01:14:37',NULL,NULL);
