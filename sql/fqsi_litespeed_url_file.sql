/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_litespeed_url_file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `url_id` bigint(20) NOT NULL,
  `vary` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 of final vary',
  `filename` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 of file content',
  `type` tinyint(4) NOT NULL COMMENT 'css=1,js=2,ccss=3,ucss=4',
  `mobile` tinyint(4) NOT NULL COMMENT 'mobile=1',
  `webp` tinyint(4) NOT NULL COMMENT 'webp=1',
  `expired` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `filename` (`filename`),
  KEY `type` (`type`),
  KEY `url_id_2` (`url_id`,`vary`,`type`),
  KEY `filename_2` (`filename`,`expired`),
  KEY `url_id` (`url_id`,`expired`)
) ENGINE=InnoDB AUTO_INCREMENT=3773 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (6,3,'+webp','cc4f508f579225c0ce1c5616c7f3c227',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (11,3,'','cc4f508f579225c0ce1c5616c7f3c227',4,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (12,3,'+ismobile+webp+webp','4de33ca58dfc066e6f1726a59809e4c3',4,1,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (37,4,'','87dac8c93f0a5c5c6e0b4c30ec76a52e',4,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (38,5,'','87dac8c93f0a5c5c6e0b4c30ec76a52e',4,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (39,6,'','467895c6a2e5276339599f07f3d2b381',4,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (40,11,'','846068e6f7fad8f380aa31d6ae98b0b6',4,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (44,3,'+ismobile','32ee225843b4aa4feb9d98f5b6375d4a',4,1,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (47,3,'+ismobile+webp','32ee225843b4aa4feb9d98f5b6375d4a',4,1,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (2952,1065,'','5effc2a284af9dcde634c448ee8b66a1',4,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (2953,1066,'','5effc2a284af9dcde634c448ee8b66a1',4,1,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (2954,1067,'','5effc2a284af9dcde634c448ee8b66a1',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (2994,1069,'','c55a81e9912b6ee5022de4532b0c7180',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3017,1070,'','fefd4fcde2a0f5530279aa254cba578b',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3060,1072,'','8363bef27f801b43007d4a76b6fc6b0e',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3067,1073,'','39bd643f13a9027cd3c439cb851470a5',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3069,1071,'','d8d54fcfa9fceb05c2b3f4387b69df0c',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3247,1138,'','b7652fb75e554af0eccf93663f2e37a4',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3248,1139,'','94bc92092316f66a1d3663895002d8e4',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3249,1140,'','d54d5c3e74870c648a79250fa2b65145',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3251,1141,'','a922aa4802cad617488ab31b3ad2d469',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3253,1142,'','8828b547919027cf62069b694085bdb2',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3255,1144,'','cf4684506bd977411791f407ec786913',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3256,1143,'','f55f6eeb4a8e2b95874fd2e1cb963f78',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3257,1136,'','f55f6eeb4a8e2b95874fd2e1cb963f78',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3438,1323,'','05816e0f086e5988992af3b9feee3715',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3439,1329,'','428fc34142c90a4766e6d0c26e119ecc',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3608,1416,'','15ca862755fe747e45952bf4e186b885',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3619,1418,'','2f1877363fb00d41775aba46a369a8a9',4,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3659,1065,'','3b56f3a608e586730d3c9d6590022115',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3660,1140,'','bba39e34eec9ef641ddd242d053b6a3a',3,1,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3661,1323,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3662,1065,'c893de0af315766accd79ad86435ef09','3b56f3a608e586730d3c9d6590022115',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3663,1433,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3664,1434,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3665,1435,'c893de0af315766accd79ad86435ef09','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3666,1323,'c893de0af315766accd79ad86435ef09','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3667,1140,'c893de0af315766accd79ad86435ef09','9f8efcd8a22e013cc5789559a05ec1f6',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3668,1436,'c893de0af315766accd79ad86435ef09','6c5b8f7be24ec44c854c09e9d7efe6ee',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3669,1436,'','6c5b8f7be24ec44c854c09e9d7efe6ee',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3670,1437,'c893de0af315766accd79ad86435ef09','d58f6cb602c2c81ec61724dc0a009e3a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3671,1438,'c893de0af315766accd79ad86435ef09','c614d3d5954960d7ba8782c4f3c8baec',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3672,1439,'c893de0af315766accd79ad86435ef09','341f0653a6b2d4b4d16efae6ec68737a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3673,1440,'c893de0af315766accd79ad86435ef09','341f0653a6b2d4b4d16efae6ec68737a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3674,1441,'','ce4eb270263c539709713fa4c05b235f',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3675,1442,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3676,1144,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3677,1443,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3678,1444,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3679,1445,'','ce4eb270263c539709713fa4c05b235f',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3680,1435,'','e47e7413dd2fd4302132d2a9ee30c29a',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3681,1446,'','32cd74ffce8db0f5abbda5081c6ea50c',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3682,1072,'c893de0af315766accd79ad86435ef09','86105766fadc130323f760031ba8a944',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3683,1447,'c893de0af315766accd79ad86435ef09','495a2d30f4be813d89bc3c699d355c0e',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3684,1440,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3685,1448,'','ce4eb270263c539709713fa4c05b235f',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3686,1071,'c893de0af315766accd79ad86435ef09','177a6434c352fafe712805aefb697611',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3687,1437,'','ce4eb270263c539709713fa4c05b235f',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3688,1449,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3689,1450,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3690,1072,'','86105766fadc130323f760031ba8a944',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3691,1451,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3692,1452,'','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3693,1453,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3694,1447,'','495a2d30f4be813d89bc3c699d355c0e',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3695,1454,'','8273070dbb1275e48d774580d7869e53',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3696,1455,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3697,1455,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3698,1433,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3699,1456,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3700,1456,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3701,1443,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3702,1457,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3703,1416,'','db8f6740d7dc691a2d4e36fec59bf508',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3704,1458,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3705,1066,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3706,1438,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3707,1459,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3708,1460,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3709,1461,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3710,1462,'','e1c6cb847638c583cb1513a8cd0de69e',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3711,1462,'c893de0af315766accd79ad86435ef09','e1c6cb847638c583cb1513a8cd0de69e',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3712,1463,'','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3713,1463,'c893de0af315766accd79ad86435ef09','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3714,1452,'c893de0af315766accd79ad86435ef09','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3715,1142,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3716,1418,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3717,1439,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3718,1464,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3719,1071,'','1865348dd43808aa6565253dccc959d7',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3720,1465,'','18ca48d9aca7750848aec4e1e40c1b78',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3721,1465,'c893de0af315766accd79ad86435ef09','18ca48d9aca7750848aec4e1e40c1b78',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3722,1466,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3723,1458,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3724,1467,'','b00d53be597d95dc8a19627fa2efd305',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3725,1468,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3726,1468,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3727,1469,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3728,1470,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3729,1471,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3730,1471,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3731,1472,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3732,1457,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3733,1066,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3734,1473,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3735,1473,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3736,1474,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3737,1474,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3738,1460,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3739,1449,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3740,1445,'c893de0af315766accd79ad86435ef09','ce4eb270263c539709713fa4c05b235f',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3741,1475,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3742,1476,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3743,1477,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3744,1459,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3745,1450,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3746,1478,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3747,1479,'','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3748,1067,'','7655f902e9a9a678b09972ecdf45a9a9',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3749,1141,'','0845bd7df5f20abe581bc3c0707136b5',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3750,1434,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3751,1480,'','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3752,1472,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3753,1416,'c893de0af315766accd79ad86435ef09','db8f6740d7dc691a2d4e36fec59bf508',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3754,1481,'','18ca48d9aca7750848aec4e1e40c1b78',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3755,1481,'c893de0af315766accd79ad86435ef09','18ca48d9aca7750848aec4e1e40c1b78',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3756,1418,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3757,1470,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3758,1448,'c893de0af315766accd79ad86435ef09','ce4eb270263c539709713fa4c05b235f',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3759,1479,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3760,1441,'c893de0af315766accd79ad86435ef09','ce4eb270263c539709713fa4c05b235f',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3761,1482,'','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3762,1482,'c893de0af315766accd79ad86435ef09','b00d53be597d95dc8a19627fa2efd305',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3763,1461,'c893de0af315766accd79ad86435ef09','2e1070c6a853a89c34d71cf8bdb61214',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3764,1483,'c893de0af315766accd79ad86435ef09','8f7933bf43257c0ef63fd39a8965b4c0',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3765,1484,'','8f7933bf43257c0ef63fd39a8965b4c0',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3766,1484,'c893de0af315766accd79ad86435ef09','8f7933bf43257c0ef63fd39a8965b4c0',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3767,1142,'c893de0af315766accd79ad86435ef09','2e7a7d6f255b9b1d7873bde48a170d72',3,0,0,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3768,1453,'c893de0af315766accd79ad86435ef09','2e7a7d6f255b9b1d7873bde48a170d72',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3769,1467,'c893de0af315766accd79ad86435ef09','8f7933bf43257c0ef63fd39a8965b4c0',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3770,1485,'','2e7a7d6f255b9b1d7873bde48a170d72',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3771,1485,'c893de0af315766accd79ad86435ef09','2e7a7d6f255b9b1d7873bde48a170d72',3,0,1,0);
INSERT INTO `fqsi_litespeed_url_file` (`id`, `url_id`, `vary`, `filename`, `type`, `mobile`, `webp`, `expired`) VALUES (3772,1469,'c893de0af315766accd79ad86435ef09','69c8882d66473af7712297db8cfe4acb',3,0,1,0);
