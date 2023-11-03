CREATE TABLE `category_filtration_group_parameter` (
  `cfgi_id` int(11) NOT NULL AUTO_INCREMENT,
  `cfgi_category_filtration_group_id` int(11) NOT NULL,
  `cfgi_product_parameter_id` int(11) NOT NULL,
  PRIMARY KEY (`cfgi_id`),
  KEY `cfgi_category_filtration_group_id` (`cfgi_category_filtration_group_id`),
  KEY `cfgi_product_parameter_id` (`cfgi_product_parameter_id`),
  CONSTRAINT `category_filtration_group_item_ibfk_1` FOREIGN KEY (`cfgi_category_filtration_group_id`) REFERENCES `category_filtration_group` (`cfg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_filtration_group_item_ibfk_3` FOREIGN KEY (`cfgi_product_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;