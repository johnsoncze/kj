CREATE TABLE `product_parameter` (
  `pp_id` int(11) NOT NULL AUTO_INCREMENT,
  `pp_product_parameter_group_id` int(11) NOT NULL,
  `pp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pp_id`),
  KEY `pp_product_parameter_group_id` (`pp_product_parameter_group_id`),
  CONSTRAINT `product_parameter_ibfk_1` FOREIGN KEY (`pp_product_parameter_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;