CREATE TABLE `opportunity_product_parameter` (
  `opppp_id` int(11) NOT NULL AUTO_INCREMENT,
  `opppp_product_id` int(11) NOT NULL,
  `opppp_parameter_group_id` int(11) DEFAULT NULL,
  `opppp_parameter_id` int(11) DEFAULT NULL,
  `opppp_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `opppp_value` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`opppp_id`),
  UNIQUE KEY `opppp_product_id` (`opppp_product_id`,`opppp_parameter_id`),
  KEY `opppp_parameter_group_id` (`opppp_parameter_group_id`),
  KEY `opppp_parameter_id` (`opppp_parameter_id`),
  CONSTRAINT `opportunity_product_parameter_ibfk_1` FOREIGN KEY (`opppp_product_id`) REFERENCES `opportunity_product` (`oppp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `opportunity_product_parameter_ibfk_2` FOREIGN KEY (`opppp_parameter_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `opportunity_product_parameter_ibfk_3` FOREIGN KEY (`opppp_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;