/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_stripe_tax_for_wc_options` (
  `option_name` varchar(191) NOT NULL,
  `option_value` longtext NOT NULL,
  PRIMARY KEY (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_stripe_tax_for_wc_options` (`option_name`, `option_value`) VALUES ('woocommerce_connect_last_state','8b814262c25d6929c1c6822d2484ff30cb3792be31ab9ed68ae6812621d68fde');
