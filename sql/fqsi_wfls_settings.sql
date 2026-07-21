/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_wfls_settings` (
  `name` varchar(191) NOT NULL DEFAULT '',
  `value` longblob DEFAULT NULL,
  `autoload` enum('no','yes') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('2fa-user-grace-period',0x3130,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('allow-disabling-ntp',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('allow-xml-rpc',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('captcha-stats',0x7B22636F756E7473223A5B302C302C302C302C302C302C302C302C302C302C305D2C22617667223A307D,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('delete-deactivation','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('disable-temporary-tables',0x30,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('dismissed-fresh-install-modal',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('enable-auth-captcha','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('enable-login-history-columns',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('enable-shortcode','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('enable-woocommerce-account-integration','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('enable-woocommerce-integration',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('global-notices',0x5B5D,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('ip-source','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('ip-trusted-proxies','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('last-secret-refresh',0x31373530333636343734,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('ntp-failure-count',0x33,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('ntp-offset',0x30,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('recaptcha-threshold',0x302E35,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('remember-device',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('remember-device-duration',0x32353932303030,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('require-2fa-grace-period-enabled','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('require-2fa.administrator','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('schema-version',0x32,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('shared-hash-secret',0x31353736633035333833623335353236383639666465396266336132356633623331336638613235313966393037323535393162633665366261366264626438,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('shared-symmetric-secret',0x34613836616631383537643962373734623032383130643063663963396136643864393366643039663836356435376239336166346139323136356238613934,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('stack-ui-columns',0x31,'yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('use-ntp','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('user-count-query-state','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('whitelisted','','yes');
INSERT INTO `fqsi_wfls_settings` (`name`, `value`, `autoload`) VALUES ('xmlrpc-enabled','','yes');
