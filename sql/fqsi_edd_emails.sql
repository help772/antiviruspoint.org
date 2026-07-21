/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_edd_emails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` varchar(32) NOT NULL,
  `context` varchar(32) NOT NULL DEFAULT 'order',
  `sender` varchar(32) NOT NULL DEFAULT 'edd',
  `recipient` varchar(32) NOT NULL DEFAULT 'customer',
  `subject` text NOT NULL,
  `heading` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_id` (`email_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (1,'order_receipt','order','edd','customer','Purchase Receipt','Purchase Receipt','Dear {name},\n\nThank you for your purchase. Please click on the link(s) below to download your files.\n\n{download_list}\n\n{sitename}',1,'2025-08-15 23:47:46','2025-08-15 23:47:46');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (2,'admin_order_notice','order','edd','admin','New download purchase - Order #{payment_id}','New Sale!','Hello\n\nA Downloads purchase has been made.\n\nDownloads sold:\n\n{download_list}\n\nPurchased by: {fullname}\nAmount: {price}\nPayment Method: {payment_method}\n\nThank you',1,'2025-08-15 23:47:46','2025-08-15 23:47:46');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (3,'order_refund','refund','edd','customer','Your order has been refunded','','Dear {name},\n\nYour order has been refunded.',1,'2025-08-15 23:47:46','2025-08-16 04:32:31');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (4,'admin_order_refund','refund','edd','admin','An order has been refunded','','Order {payment_id} has been refunded.',1,'2025-08-15 23:47:46','2025-08-16 04:32:38');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (5,'new_user','user','edd','customer','[{sitename}] Your username and password','Your account info','Username: {username}\r\nPassword: [entered on site]\r\n<a href=\"https://antiviruspoint.org/wp-login.php\"> Click here to log in &rarr;</a>\r\n',1,'2025-08-15 23:47:46','2025-08-15 23:47:46');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (6,'new_user_admin','user','edd','admin','[{sitename}] New User Registration','New user registration','Username: {username}\r\n\r\nE-mail: {user_email}\r\n',1,'2025-08-15 23:47:46','2025-08-15 23:47:46');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (7,'user_verification','user','edd','user','Verify your account','Verify your account','Hello {fullname},\n\nYour account with {sitename} needs to be verified before you can access your order history.\n\nVisit this link to verify your account: {verification_url}\n\n',1,'2025-08-15 23:47:46','2025-08-15 23:47:46');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (8,'password_reset','user','wp','user','[Antiviruspoint.org] Password Reset','','Someone has requested a password reset for the following account:\r\n\r\nSite Name: {sitename}\r\n\r\nUsername: {username}\r\n\r\nIf this was a mistake, ignore this email and nothing will happen.\r\n\r\nTo reset your password, visit the following address:\r\n\r\n{password_reset_link}\r\n\r\nThis password reset request originated from the IP address {ip_address}.\r\n',0,'2025-08-15 23:47:46','2025-08-15 23:47:46');
INSERT INTO `fqsi_edd_emails` (`id`, `email_id`, `context`, `sender`, `recipient`, `subject`, `heading`, `content`, `status`, `date_created`, `date_modified`) VALUES (9,'stripe_early_fraud_warning','order','edd','admin','Stripe Early Fraud Warning - Order #{payment_id}','Possible Fraudulent Order','Hello\n\nStripe has detected a potential fraudulent order.\n\nDownloads sold:\n\n{download_list}\n\nPurchased by: {fullname}\nAmount: {price}\n<a href=\"{order_details_link}\">Order Details</a>\n\nNote: Once you have reviewed the order, ensure you take the appropriate action within your Stripe dashboard to help improve future fraud detection.',0,'2025-08-15 23:47:46','2025-08-15 23:47:46');
