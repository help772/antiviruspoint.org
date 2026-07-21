/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_zbs_sys_email_hist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zbs_site` int(11) DEFAULT NULL,
  `zbs_team` int(11) DEFAULT NULL,
  `zbs_owner` int(11) NOT NULL,
  `zbsmail_type` int(11) NOT NULL,
  `zbsmail_sender_thread` int(11) NOT NULL,
  `zbsmail_sender_email` varchar(200) NOT NULL,
  `zbsmail_sender_wpid` int(11) NOT NULL,
  `zbsmail_sender_mailbox_id` int(11) NOT NULL,
  `zbsmail_sender_mailbox_name` varchar(200) DEFAULT NULL,
  `zbsmail_receiver_email` varchar(200) NOT NULL,
  `zbsmail_sent` int(11) NOT NULL,
  `zbsmail_target_objid` int(11) NOT NULL,
  `zbsmail_assoc_objid` int(11) NOT NULL,
  `zbsmail_subject` varchar(200) DEFAULT NULL,
  `zbsmail_content` longtext DEFAULT NULL,
  `zbsmail_hash` varchar(128) DEFAULT NULL,
  `zbsmail_status` varchar(120) DEFAULT NULL,
  `zbsmail_sender_maildelivery_key` varchar(200) DEFAULT NULL,
  `zbsmail_starred` int(11) DEFAULT NULL,
  `zbsmail_opened` int(11) NOT NULL,
  `zbsmail_clicked` int(11) NOT NULL,
  `zbsmail_firstopened` int(14) NOT NULL,
  `zbsmail_lastopened` int(14) NOT NULL,
  `zbsmail_lastclicked` int(14) NOT NULL,
  `zbsmail_created` int(14) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `zbsmail_sender_wpid` (`zbsmail_sender_wpid`),
  KEY `zbsmail_sender_mailbox_id` (`zbsmail_sender_mailbox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
