/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_automatewoo_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT 0,
  `guest_id` bigint(20) NOT NULL DEFAULT 0,
  `id_key` varchar(20) NOT NULL DEFAULT '',
  `last_purchased` datetime DEFAULT NULL,
  `unsubscribed` int(1) NOT NULL DEFAULT 0,
  `unsubscribed_date` datetime DEFAULT NULL,
  `subscribed` int(1) NOT NULL DEFAULT 0,
  `subscribed_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `guest_id` (`guest_id`),
  KEY `id_key` (`id_key`),
  KEY `last_purchased` (`last_purchased`),
  KEY `unsubscribed` (`unsubscribed`),
  KEY `subscribed` (`subscribed`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (1,12,0,'8abtzgyu29rf7ka27xi2',NULL,1,'2025-08-23 06:33:41',0,'2025-06-27 19:05:05');
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (4,6,0,'7wo9jb6a9kihdf5ua3x1','2025-02-13 10:41:47',0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (9,11,0,'1ef1xbv0y79pvlp62n62','2025-06-18 07:11:01',0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (12,16,0,'j2guxcdk8p500s66c69z',NULL,1,'2025-08-23 06:33:41',0,'2025-06-27 19:16:37');
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (21,0,3,'kqjvwk4dftu5rc9z6akp',NULL,0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (24,0,4,'4dp0wt5cco898vtyu465',NULL,0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (25,0,5,'sawe0wdheizp0z1euu1m',NULL,0,NULL,1,'2025-09-03 18:14:37');
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (26,27,0,'3zyeofck9l7mnnr1atrv',NULL,0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (28,29,0,'pzzq0o9sxgny9l11bhst',NULL,0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (29,1,0,'b8l9sjs97or96zmdag9s',NULL,0,NULL,0,NULL);
INSERT INTO `fqsi_automatewoo_customers` (`id`, `user_id`, `guest_id`, `id_key`, `last_purchased`, `unsubscribed`, `unsubscribed_date`, `subscribed`, `subscribed_date`) VALUES (30,26,0,'68kvrxu6pdv7igd4u6hg',NULL,0,NULL,0,NULL);
