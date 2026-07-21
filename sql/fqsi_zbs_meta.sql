/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_meta` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbsm_objtype` int(11) NOT NULL,
  `zbsm_objid` int(11) NOT NULL,
  `zbsm_key` varchar(255) NOT NULL,
  `zbsm_val` longtext DEFAULT NULL,
  `zbsm_created` int(14) NOT NULL,
  `zbsm_lastupdated` int(14) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `zbsm_objid+zbsm_key+zbsm_objtype` (`zbsm_objid`,`zbsm_key`,`zbsm_objtype`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (1,1,1,-1,1,1,'extra_billingemail','divyanshu.tiwari@thecodeyogi.com',1757010379,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (2,1,1,-1,5,1,'extra_order_num','90007023',1757010379,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (3,1,1,-1,5,2,'extra_order_num','90007025',1757010380,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (4,1,1,-1,5,3,'extra_order_num','90007086',1757010380,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (5,1,1,-1,5,4,'extra_order_num','90007181',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (6,1,1,-1,1,2,'extra_billingemail','anuraghandique.dev@gmail.com',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (7,1,1,-1,8,8,'from-woo-order','90007265',1757010381,1757010381);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (8,1,1,-1,5,5,'extra_order_num','90007265',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (9,1,1,-1,5,6,'extra_order_num','90007265',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (10,1,1,-1,5,6,'extra_refunded_by','12',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (11,1,1,-1,1,3,'extra_billingemail','Mandrewc@comcast.net',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (12,1,1,-1,5,7,'extra_order_num','90007439',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (13,1,1,-1,1,4,'extra_billingemail','frankd@gmail.com',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (14,1,1,-1,5,8,'extra_order_num','90007458',1757010381,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (15,1,1,-1,1,5,'extra_billingemail','neor@thecodeyogi.com',1757010382,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (16,1,1,-1,5,9,'extra_order_num','90007469',1757010382,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (17,1,1,-1,5,10,'extra_order_num','90007470',1757010382,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (18,1,1,-1,1,6,'extra_billingemail','anuraghandique.dev@gmail.com',1757010382,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (19,1,1,-1,5,11,'extra_order_num','90007474',1757010382,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (20,1,1,-1,5,12,'extra_order_num','90007476',1757010382,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (21,1,1,-1,5,13,'extra_order_num','90007477',1757010383,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (22,1,1,-1,5,14,'extra_order_num','90007478',1757010383,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (23,1,1,-1,1,7,'extra_billingemail','rajnish.k@thecodeyogi.com',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (24,1,1,-1,5,15,'extra_order_num','90007481',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (25,1,1,-1,1,8,'extra_billingemail','info@thecodeyogi.com',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (26,1,1,-1,5,16,'extra_order_num','90007487',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (27,1,1,-1,5,17,'extra_order_num','90007491',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (28,1,1,-1,1,9,'extra_billingemail','hyogi067@gmail.com',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (29,1,1,-1,5,18,'extra_order_num','90007660',1757010384,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (30,1,1,-1,5,19,'extra_order_num','90007660',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (31,1,1,-1,5,19,'extra_refunded_by','12',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (32,1,1,-1,5,20,'extra_order_num','90007732',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (33,1,1,-1,1,10,'extra_billingemail','sxqrscym6@mozmail.com',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (34,1,1,-1,5,21,'extra_order_num','90007744',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (35,1,1,-1,1,11,'extra_billingemail','Lokendra07@outlook.com',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (36,1,1,-1,5,22,'extra_order_num','90007745',1757010385,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (37,1,1,-1,5,23,'extra_order_num','90007801',1757010386,1784565256);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (38,1,1,-1,5,24,'extra_order_num','90007804',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (39,1,1,-1,1,12,'extra_billingemail','mohu3001@gmail.com',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (40,1,1,-1,5,25,'extra_order_num','90007894',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (41,1,1,-1,1,13,'extra_billingemail','tomvancaster@yahoo.com',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (42,1,1,-1,5,26,'extra_order_num','90007926',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (43,1,1,-1,5,27,'extra_order_num','90007926',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (44,1,1,-1,5,27,'extra_refunded_by','1',1757010386,1757423651);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (45,1,1,-1,1,14,'extra_billingemail','astitva@gmail.com',1757010386,1757044655);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (46,1,1,-1,5,28,'extra_order_num','90008190',1757010386,1784565257);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (47,1,1,-1,8,48,'from-woo-order','90007265',1757010399,1757010399);
INSERT INTO `fqsi_zbs_meta` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsm_objtype`, `zbsm_objid`, `zbsm_key`, `zbsm_val`, `zbsm_created`, `zbsm_lastupdated`) VALUES (48,1,1,-1,1,15,'extra_billingemail','astitva@gmail.com',1757044871,1784565257);
