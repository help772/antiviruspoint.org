/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi__taboola_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` varchar(255) DEFAULT NULL,
  `web_push_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `publisher_id_push` int(15) DEFAULT NULL,
  `first_bc_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `first_bc_widget_id` varchar(255) DEFAULT NULL,
  `first_bc_placement` varchar(255) DEFAULT NULL,
  `first_bc_custom_css` text DEFAULT NULL,
  `second_bc_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `second_bc_widget_id` varchar(255) DEFAULT NULL,
  `second_bc_custom_css` text DEFAULT NULL,
  `location_string` text DEFAULT NULL,
  `mid_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mid_widget_id` varchar(255) DEFAULT NULL,
  `mid_placement` varchar(255) DEFAULT NULL,
  `out_of_content_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `mid_location_string` text DEFAULT NULL,
  `mid_location_string_occurrence` smallint(6) DEFAULT NULL,
  `mid_paragraph_ui_mode` varchar(255) DEFAULT NULL,
  `home_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `home_widget_id` varchar(255) DEFAULT NULL,
  `home_placement` varchar(255) DEFAULT NULL,
  `home_location_string` text DEFAULT NULL,
  `home_location_string_occurrence` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
