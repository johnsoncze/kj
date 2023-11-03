CREATE TABLE `category_filtration_group` (
  `cfg_id` int(11) NOT NULL AUTO_INCREMENT,
  `cfg_category_id` int(11) NOT NULL,
  `cfg_description` text,
  `cfg_title_seo` varchar(255) DEFAULT NULL,
  `cfg_description_seo` varchar(255) DEFAULT NULL,
  `cfg_index_seo` tinyint(1) NOT NULL DEFAULT '0',
  `cfg_follow_seo` tinyint(1) NOT NULL DEFAULT '0',
  `cfg_site_map` tinyint(1) NOT NULL DEFAULT '0',
  `cfg_status` varchar(10) NOT NULL DEFAULT '',
  `cfg_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cfg_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cfg_id`),
  KEY `cfg_category_id` (`cfg_category_id`),
  CONSTRAINT `category_filtration_group_ibfk_2` FOREIGN KEY (`cfg_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;