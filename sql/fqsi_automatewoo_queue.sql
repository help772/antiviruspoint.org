/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_automatewoo_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint(20) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `failed` int(1) NOT NULL DEFAULT 0,
  `failure_code` int(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `date` (`date`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
