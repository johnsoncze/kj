CREATE TABLE `product_parameter_relationship` (
  `ppr_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppr_product_id` int(11) NOT NULL,
  `ppr_parameter_id` int(11) NOT NULL,
  `ppr_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ppr_id`),
  KEY `ppr_product_id` (`ppr_product_id`),
  KEY `ppr_parameter_id` (`ppr_parameter_id`),
  CONSTRAINT `product_parameter_relationship_ibfk_1` FOREIGN KEY (`ppr_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_parameter_relationship_ibfk_2` FOREIGN KEY (`ppr_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;