/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wc_order_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `address_type` varchar(20) DEFAULT NULL,
  `first_name` text DEFAULT NULL,
  `last_name` text DEFAULT NULL,
  `company` text DEFAULT NULL,
  `address_1` text DEFAULT NULL,
  `address_2` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `state` text DEFAULT NULL,
  `postcode` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `email` varchar(320) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address_type_order_id` (`address_type`,`order_id`),
  KEY `order_id` (`order_id`),
  KEY `email` (`email`(191)),
  KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (1,90007023,'billing','Divyanshu','Tiwari','thecodeyogi','G 43 Noida sector 3',NULL,'Noida','UP','201301','IN','divyanshu.tiwari@thecodeyogi.com','9548254043');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (2,90007025,'billing','Divyanshu','Tiwari','thecodeyogi','G 43 Noida sector 3',NULL,'Noida','UP','201301','IN','divyanshu.tiwari@thecodeyogi.com','9548254043');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (3,90007086,'billing','Divyanshu','Tiwari','thecodeyogi','G 43 Noida sector 3',NULL,'Noida','UP','201301','IN','divyanshu.tiwari@thecodeyogi.com','9548254043');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (4,90007181,'billing','Divyanshu','Tiwari','thecodeyogi','G 43 Noida sector 3',NULL,'Noida','UP','201301','IN','divyanshu.tiwari@thecodeyogi.com','9548254043');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (5,90007265,'billing','Anurag','Handique','KNACKHELP CONSULTING SERVICES PRIVATE LIMITED','Sector 120 Main Rd','Rg Recidency','Noida','UP','201307','IN','anuraghandique.dev@gmail.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (6,90007439,'billing','Michael','Crose',NULL,'7301 33RD Way NE',NULL,'Lacey','WA','98516-1426','US','Mandrewc@comcast.net','3609152802');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (7,90007458,'billing','frank','test',NULL,'new york',NULL,'new york','CA','48006','US','frankd@gmail.com','8175539807');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (8,90007469,'billing','Laurel S','Crose',NULL,'7301 33rd Way NE',NULL,'Lacey','WA','98516','US','neor@thecodeyogi.com','3608781682');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (9,90007470,'billing','Anurag','Handique',NULL,'Gohain Gaon',NULL,'Golaghat','AS','785601','IN','anuraghandique.dev@gmail.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (10,90007474,'billing','Anurag','Handique','KNACKHELP CONSULTING SERVICES PRIVATE LIMITED','Sector 120 Main Rd','Rg Recidency','Noida','UP','201307','IN','anuraghandique.dev@gmail.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (11,90007476,'billing','Anurag','Handique',NULL,'Gohain Gaon',NULL,'Golaghat','AS','785601','IN','anuraghandique.dev@gmail.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (12,90007477,'billing','Anurag','Handique',NULL,'Gohain Gaon',NULL,'Golaghat','AS','785601','IN','anuraghandique.dev@gmail.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (13,90007478,'billing','Anurag','Handique',NULL,'Gohain Gaon',NULL,'Golaghat','AS','785601','IN','anuraghandique.dev@gmail.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (14,90007481,'billing','raj','raj','thecodeyogi','[ouh9o[kmp',NULL,'o0uplk[0olk','UP','201301','IN','rajnish.k@thecodeyogi.com','6900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (15,90007487,'billing','Anurag','Handique',NULL,'Gohain Gaon',NULL,'Golaghat','AS','785601','IN','info@thecodeyogi.com','+916900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (16,90007491,'billing','Anurag','Handique','Test','16',NULL,'Test','AS','201301','IN','anuraghandique.dev@gmail.com','6900864982');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (17,90007660,'billing','Hemendra','Test',NULL,'dettorid','Kardhani mode','michagan','MI','48006','US','hyogi067@gmail.com','+19874352134');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (18,90007732,'billing','Hemendra','Test',NULL,'dettorid','Kardhani mode','michagan','MI','48006','US','hyogi067@gmail.com','+19874352134');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (19,90007744,'billing','Dependra','yogi','internet bull','4512 North Saginaw Road','APT 402','Midland','MI','45008','US','sxqrscym6@mozmail.com','+13142394102');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (20,90007745,'billing','LOKENDRA','SAINGAR',NULL,'279 , rajput basti saingarpura khurd',NULL,'karauli','CO','45008','US','Lokendra07@outlook.com','+132343434');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (21,90007801,'billing','Dependra','yogi','internet bull','4512 North Saginaw Road','APT 402','Midland','MI','45008','US','sxqrscym6@mozmail.com','+13142394102');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (22,90007804,'billing','Dependra','yogi','internet bull','4512 North Saginaw Road','APT 402','Midland','MI','45008','US','sxqrscym6@mozmail.com','+13142394102');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (23,90007894,'billing','LOKENDRA','Test',NULL,'61 uttam nagar behind power house','Kardhani mode','Jaipur','MI','48069','US','mohu3001@gmail.com','+917877180969');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (24,90007926,'billing','Thomas','VanCaster',NULL,NULL,NULL,NULL,NULL,NULL,'US','tomvancaster@yahoo.com','2628182869');
INSERT INTO `fqsi_wc_order_addresses` (`id`, `order_id`, `address_type`, `first_name`, `last_name`, `company`, `address_1`, `address_2`, `city`, `state`, `postcode`, `country`, `email`, `phone`) VALUES (25,90008190,'billing','Astitva','Pathak',NULL,'Kailashpuri Road Tikrapara near Sahu Murti Bhandar',NULL,'Raipur','CA','90001','US','astitva@gmail.com','7389522364');
