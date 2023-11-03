CREATE TABLE `category_associated_category` (
  `cac_id` int(11) NOT NULL AUTO_INCREMENT,
  `cac_category_id` int(11) NOT NULL,
  `cac_associated_category_id` int(11) NOT NULL,
  `cac_added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cac_id`),
  UNIQUE KEY `cac_category_id` (`cac_category_id`,`cac_associated_category_id`),
  KEY `associated-category` (`cac_associated_category_id`),
  CONSTRAINT `associated-category` FOREIGN KEY (`cac_associated_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_associated_category_ibfk_1` FOREIGN KEY (`cac_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;