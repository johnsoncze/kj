CREATE TABLE `category_product_parameter` (
  `cpr_id` int(11) NOT NULL AUTO_INCREMENT,
  `cpr_category_id` int(11) NOT NULL,
  `cpr_product_parameter_id` int(11) NOT NULL,
  PRIMARY KEY (`cpr_id`),
  KEY `cpr_category_id` (`cpr_category_id`),
  KEY `cpr_product_parameter_id` (`cpr_product_parameter_id`),
  CONSTRAINT `category_product_parameter_ibfk_1` FOREIGN KEY (`cpr_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_product_parameter_ibfk_2` FOREIGN KEY (`cpr_product_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;