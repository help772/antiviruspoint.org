/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_tags_links` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbstl_objtype` int(4) NOT NULL,
  `zbstl_objid` int(11) NOT NULL,
  `zbstl_tagid` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `zbstl_objid` (`zbstl_objid`),
  KEY `zbstl_tagid` (`zbstl_tagid`),
  KEY `zbstl_tagid+zbstl_objtype` (`zbstl_tagid`,`zbstl_objtype`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (1,1,1,12,1,1,1);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (2,1,1,12,1,1,2);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (3,1,1,12,5,1,3);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (4,1,1,12,5,1,4);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (5,1,1,12,1,1,5);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (6,1,1,12,5,2,6);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (7,1,1,12,1,1,7);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (8,1,1,12,5,3,8);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (9,1,1,12,5,4,3);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (10,1,1,12,1,2,9);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (11,1,1,12,5,5,10);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (12,1,1,12,1,3,11);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (13,1,1,12,5,7,12);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (14,1,1,12,1,4,13);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (15,1,1,12,5,8,14);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (16,1,1,12,1,5,15);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (17,1,1,12,5,9,16);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (18,1,1,12,1,2,17);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (19,1,1,12,5,10,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (20,1,1,12,1,6,13);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (21,1,1,12,1,6,17);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (22,1,1,12,5,11,14);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (23,1,1,12,5,11,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (24,1,1,12,5,12,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (25,1,1,12,5,13,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (26,1,1,12,5,14,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (27,1,1,12,1,7,17);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (28,1,1,12,5,15,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (29,1,1,12,1,8,17);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (30,1,1,12,5,16,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (31,1,1,12,5,17,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (32,1,1,12,1,9,19);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (33,1,1,12,5,18,20);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (34,1,1,12,1,9,17);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (35,1,1,12,5,20,20);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (36,1,1,12,5,20,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (37,1,1,12,1,10,1);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (38,1,1,12,5,21,3);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (39,1,1,12,1,11,21);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (40,1,1,12,5,22,22);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (41,1,1,12,1,10,17);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (42,1,1,12,5,23,18);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (43,1,1,12,1,10,13);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (44,1,1,12,1,10,23);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (45,1,1,12,5,24,14);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (46,1,1,12,5,24,24);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (47,1,1,12,1,12,19);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (48,1,1,12,5,25,20);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (49,1,1,12,1,13,13);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (50,1,1,12,5,26,14);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (51,1,1,12,1,14,19);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (52,1,1,12,5,28,20);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (53,1,1,0,1,6,9);
INSERT INTO `fqsi_zbs_tags_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbstl_objtype`, `zbstl_objid`, `zbstl_tagid`) VALUES (54,1,1,0,1,15,19);
