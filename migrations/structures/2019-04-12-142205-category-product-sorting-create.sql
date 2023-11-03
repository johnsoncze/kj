CREATE TABLE `category_product_sorting` (
  `cps_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cps_category_id` int(11) NOT NULL,
  `cps_product_id` int(11) NOT NULL,
  `cps_sorting` varchar(50) NOT NULL,
  `cps_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cps_id`),
  UNIQUE KEY `cps_product_id` (`cps_product_id`,`cps_category_id`),
  KEY `cps_sorting` (`cps_sorting`),
  KEY `cps_category_id` (`cps_category_id`),
  CONSTRAINT `category_product_sorting_ibfk_1` FOREIGN KEY (`cps_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_product_sorting_ibfk_2` FOREIGN KEY (`cps_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;