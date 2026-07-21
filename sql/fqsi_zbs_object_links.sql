/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_object_links` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbsol_objtype_from` int(4) NOT NULL,
  `zbsol_objtype_to` int(4) NOT NULL,
  `zbsol_objid_from` int(11) NOT NULL,
  `zbsol_objid_to` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `zbsol_objid_from` (`zbsol_objid_from`),
  KEY `zbsol_objid_to` (`zbsol_objid_to`)
) ENGINE=InnoDB AUTO_INCREMENT=4729414 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729332,1,1,-1,10,5,1823354,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729333,1,1,-1,10,5,1823355,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729334,1,1,-1,5,1,1,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729335,1,1,-1,5,2,1,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729337,1,1,-1,10,5,1823356,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729338,1,1,-1,5,1,2,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729339,1,1,-1,5,2,2,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729341,1,1,-1,10,5,1823357,3);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729342,1,1,-1,5,1,3,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729343,1,1,-1,5,2,3,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729344,1,1,-1,1,2,1,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729345,1,1,-1,10,5,1823358,4);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729346,1,1,-1,5,1,4,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729347,1,1,-1,5,2,4,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729349,1,1,-1,10,5,1823359,5);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729350,1,1,-1,5,1,5,6);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729351,1,1,-1,5,2,5,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729353,1,1,-1,5,1,6,6);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729354,1,1,-1,5,2,6,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729355,1,1,-1,10,5,1823361,7);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729356,1,1,-1,5,1,7,3);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729357,1,1,-1,10,5,1823362,8);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729358,1,1,-1,5,1,8,4);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729359,1,1,-1,10,5,1823363,9);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729360,1,1,-1,5,1,9,5);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729361,1,1,-1,10,5,1823364,10);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729362,1,1,-1,5,1,10,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729363,1,1,-1,1,2,6,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729364,1,1,-1,10,5,1823365,11);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729365,1,1,-1,10,5,1823366,11);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729366,1,1,-1,5,1,11,6);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729367,1,1,-1,5,2,11,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729368,1,1,-1,10,5,1823367,12);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729369,1,1,-1,5,1,12,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729370,1,1,-1,10,5,1823368,13);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729371,1,1,-1,5,1,13,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729372,1,1,-1,10,5,1823369,14);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729373,1,1,-1,5,1,14,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729374,1,1,-1,1,2,7,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729375,1,1,-1,10,5,1823370,15);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729376,1,1,-1,5,1,15,7);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729377,1,1,-1,5,2,15,1);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729378,1,1,-1,10,5,1823371,16);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729379,1,1,-1,5,1,16,8);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729380,1,1,-1,1,2,2,3);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729381,1,1,-1,10,5,1823372,17);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729382,1,1,-1,5,1,17,2);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729383,1,1,-1,5,2,17,3);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729384,1,1,-1,10,5,1823373,18);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729385,1,1,-1,5,1,18,9);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729387,1,1,-1,5,1,19,9);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729388,1,1,-1,10,5,1823375,20);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729389,1,1,-1,10,5,1823376,20);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729390,1,1,-1,5,1,20,9);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729392,1,1,-1,10,5,1823377,21);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729393,1,1,-1,5,1,21,10);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729394,1,1,-1,5,2,21,4);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729395,1,1,-1,10,5,1823378,22);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729396,1,1,-1,5,1,22,11);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729398,1,1,-1,10,5,1823379,23);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729399,1,1,-1,5,1,23,10);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729400,1,1,-1,5,2,23,4);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729401,1,1,-1,1,2,10,4);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729402,1,1,-1,10,5,1823380,24);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729403,1,1,-1,10,5,1823381,24);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729404,1,1,-1,5,1,24,10);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729405,1,1,-1,5,2,24,4);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729406,1,1,-1,10,5,1823382,25);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729407,1,1,-1,5,1,25,12);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729408,1,1,-1,10,5,1823383,26);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729409,1,1,-1,5,1,26,13);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729411,1,1,-1,5,1,27,13);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729412,1,1,-1,10,5,1823385,28);
INSERT INTO `fqsi_zbs_object_links` (`ID`, `zbs_site`, `zbs_team`, `zbs_owner`, `zbsol_objtype_from`, `zbsol_objtype_to`, `zbsol_objid_from`, `zbsol_objid_to`) VALUES (4729413,1,1,-1,5,1,28,15);
