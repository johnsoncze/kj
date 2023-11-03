CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_language_id` int(11) NOT NULL,
  `cat_parent_category_id` int(11) DEFAULT NULL,
  `cat_name` varchar(500) NOT NULL DEFAULT '',
  `cat_url` varchar(255) NOT NULL DEFAULT '',
  `cat_content` text,
  `cat_title_seo` varchar(255) DEFAULT NULL,
  `cat_description_seo` varchar(255) DEFAULT NULL,
  `cat_sort` int(11) NOT NULL,
  `cat_status` varchar(10) DEFAULT NULL,
  `cat_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`),
  KEY `cat_language_id` (`cat_language_id`),
  KEY `cat_parent_category_id` (`cat_parent_category_id`),
  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`cat_language_id`) REFERENCES `language` (`lan_id`) ON UPDATE CASCADE,
  CONSTRAINT `category_ibfk_2` FOREIGN KEY (`cat_parent_category_id`) REFERENCES `category` (`cat_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;