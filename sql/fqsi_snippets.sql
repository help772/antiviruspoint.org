/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_snippets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `description` text NOT NULL,
  `code` longtext NOT NULL,
  `tags` longtext NOT NULL,
  `scope` varchar(15) NOT NULL DEFAULT 'global',
  `priority` smallint(6) NOT NULL DEFAULT 10,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `revision` bigint(20) NOT NULL DEFAULT 1,
  `cloud_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scope` (`scope`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `fqsi_snippets` (`id`, `name`, `description`, `code`, `tags`, `scope`, `priority`, `active`, `modified`, `revision`, `cloud_id`) VALUES (1,'Make upload filenames lowercase','Makes sure that image and file uploads have lowercase filenames.\n\nThis is a sample snippet. Feel free to use it, edit it, or remove it.','add_filter( \'sanitize_file_name\', \'mb_strtolower\' );','sample, media','global',10,0,'2025-01-17 06:59:21',2,NULL);
INSERT INTO `fqsi_snippets` (`id`, `name`, `description`, `code`, `tags`, `scope`, `priority`, `active`, `modified`, `revision`, `cloud_id`) VALUES (2,'Disable admin bar','Turns off the WordPress admin bar for everyone except administrators.\n\nThis is a sample snippet. Feel free to use it, edit it, or remove it.','\n// // // add_action( \'wp\', function () {\n// 	if ( ! current_user_can( \'manage_options\' ) ) {\n// 		show_admin_bar( false );\n// 	}\n// } );','sample, admin-bar','global',10,0,'2025-01-17 07:28:17',9,NULL);
INSERT INTO `fqsi_snippets` (`id`, `name`, `description`, `code`, `tags`, `scope`, `priority`, `active`, `modified`, `revision`, `cloud_id`) VALUES (3,'Allow smilies','Allows smiley conversion in obscure places.\n\nThis is a sample snippet. Feel free to use it, edit it, or remove it.','add_filter( \'widget_text\', \'convert_smilies\' );\nadd_filter( \'the_title\', \'convert_smilies\' );\nadd_filter( \'wp_title\', \'convert_smilies\' );\nadd_filter( \'get_bloginfo\', \'convert_smilies\' );','sample','global',10,0,'2025-01-17 06:59:21',2,NULL);
INSERT INTO `fqsi_snippets` (`id`, `name`, `description`, `code`, `tags`, `scope`, `priority`, `active`, `modified`, `revision`, `cloud_id`) VALUES (4,'Current year','Shortcode for inserting the current year into a post or page..\n\nThis is a sample snippet. Feel free to use it, edit it, or remove it.','<?php echo date( \'Y\' ); ?>','sample, dates','content',10,0,'2025-01-17 06:59:21',2,NULL);
INSERT INTO `fqsi_snippets` (`id`, `name`, `description`, `code`, `tags`, `scope`, `priority`, `active`, `modified`, `revision`, `cloud_id`) VALUES (5,'This is the custom function.php','','\nadd_action(\'woocommerce_product_data_panels\', \'add_custom_product_key_field\');\nfunction add_custom_product_key_field() {\n    global $post;\n\n    echo \'<div class=\"options_group\">\';\n    woocommerce_wp_text_input([\n        \'id\' => \'_product_key\',\n        \'label\' => __(\'Product Key\', \'woocommerce\'),\n        \'description\' => __(\'Enter the unique product key here.\', \'woocommerce\'),\n        \'desc_tip\' => true,\n    ]);\n    echo \'</div>\';\n}\n','','global',10,0,'2025-01-17 07:37:03',4,NULL);
