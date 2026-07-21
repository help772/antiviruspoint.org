/*M!999999\- enable the sandbox mode */ 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fqsi_e_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `route_url` text DEFAULT NULL COMMENT 'Clean url where the note was created.',
  `route_title` varchar(255) DEFAULT NULL,
  `route_post_id` bigint(20) unsigned DEFAULT NULL COMMENT 'The post id of the route that the note was created on.',
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `element_id` varchar(60) DEFAULT NULL COMMENT 'The Elementor element ID the note is attached to.',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `author_id` bigint(20) unsigned DEFAULT NULL,
  `author_display_name` varchar(250) DEFAULT NULL COMMENT 'Save the author name when the author was deleted.',
  `status` varchar(20) NOT NULL DEFAULT 'publish',
  `position` text DEFAULT NULL COMMENT 'A JSON string that represents the position of the note inside the element in percentages. e.g. {x:10, y:15}',
  `content` longtext DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `last_activity_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `route_url_index` (`route_url`(191)),
  KEY `post_id_index` (`post_id`),
  KEY `element_id_index` (`element_id`),
  KEY `parent_id_index` (`parent_id`),
  KEY `author_id_index` (`author_id`),
  KEY `status_index` (`status`),
  KEY `is_resolved_index` (`is_resolved`),
  KEY `is_public_index` (`is_public`),
  KEY `created_at_index` (`created_at`),
  KEY `updated_at_index` (`updated_at`),
  KEY `last_activity_at_index` (`last_activity_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
