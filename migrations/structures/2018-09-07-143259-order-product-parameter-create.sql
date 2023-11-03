CREATE TABLE `order_product_parameter` (
  `opp_id` int(11) NOT NULL AUTO_INCREMENT,
  `opp_product_id` int(11) NOT NULL,
  `opp_parameter_group_id` int(11) DEFAULT NULL,
  `opp_parameter_id` int(11) DEFAULT NULL,
  `opp_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `opp_value` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`opp_id`),
  UNIQUE KEY `opp_product_id` (`opp_product_id`,`opp_parameter_id`),
  KEY `opp_parameter_group_id` (`opp_parameter_group_id`),
  KEY `opp_parameter_id` (`opp_parameter_id`),
  CONSTRAINT `order_product_parameter_ibfk_1` FOREIGN KEY (`opp_product_id`) REFERENCES `order_product` (`op_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_product_parameter_ibfk_2` FOREIGN KEY (`opp_parameter_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `order_product_parameter_ibfk_3` FOREIGN KEY (`opp_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;