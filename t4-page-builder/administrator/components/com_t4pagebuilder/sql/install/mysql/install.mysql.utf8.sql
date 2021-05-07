--
-- Table structure for table `#__jae_item`
--

-- DROP TABLE IF EXISTS `#__jae_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `#__jae_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` tinyint(3) NOT NULL,
  `asset_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Joomla Item reference',
  `asset_id` int(11) DEFAULT 0,
  `catid` int(10) NOT NULL DEFAULT 0,
  `access` int(11) NOT NULL DEFAULT 0 ,
  `tag_id` int(11) DEFAULT 0,
  `page_html` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) NOT NULL,
  `ctime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rev` int(11) NOT NULL DEFAULT 0 ,
  `working_content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `language` char(7) COLLATE utf8mb4_unicode_ci DEFAULT '*' ,
  `hits` bigint(20) NOT NULL DEFAULT 0 ,
  `css` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bundle_css` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `js` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `#__jae_revision`
--

-- DROP TABLE IF EXISTS `#__jae_revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `#__jae_revision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `rev` int(11) DEFAULT 0,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;