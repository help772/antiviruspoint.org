/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_segments_conditions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbscondition_segmentid` int(11) NOT NULL,
  `zbscondition_type` varchar(50) NOT NULL,
  `zbscondition_op` varchar(50) DEFAULT NULL,
  `zbscondition_val` varchar(250) DEFAULT NULL,
  `zbscondition_val_secondary` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
