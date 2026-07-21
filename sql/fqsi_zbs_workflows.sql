/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) NOT NULL,
  `zbs_team` int(11) NOT NULL,
  `zbs_owner` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(600) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `triggers` longtext NOT NULL,
  `initial_step` varchar(255) NOT NULL,
  `steps` longtext NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `version` int(14) NOT NULL DEFAULT 1,
  `created_at` int(14) DEFAULT NULL,
  `updated_at` int(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `active` (`active`),
  KEY `category` (`category`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
