CREATE TABLE `category_filtration` (
  `cf_id` int(11) NOT NULL AUTO_INCREMENT,
  `cf_category_id` int(11) NOT NULL,
  `cf_product_parameter_group_id` int(11) NOT NULL,
  `cf_index_seo` tinyint(1) DEFAULT NULL,
  `cf_follow_seo` tinyint(1) DEFAULT NULL,
  `cf_site_map` tinyint(1) DEFAULT NULL,
  `cf_sort` int(11) NOT NULL,
  `cf_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cf_id`),
  KEY `cf_category_id` (`cf_category_id`),
  KEY `cf_product_parameter_group_id` (`cf_product_parameter_group_id`),
  CONSTRAINT `category_filtration_ibfk_1` FOREIGN KEY (`cf_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_filtration_ibfk_2` FOREIGN KEY (`cf_product_parameter_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;