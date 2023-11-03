CREATE TABLE `product_variant` (
  `pv_id` int(11) NOT NULL AUTO_INCREMENT,
  `pv_product_id` int(11) NOT NULL,
  `pv_product_variant_id` int(11) NOT NULL,
  `pv_product_parameter_group_id` int(11) NOT NULL,
  `pv_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pv_parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pv_id`),
  KEY `pv_product_id` (`pv_product_id`),
  KEY `pv_product_variant_id` (`pv_product_variant_id`),
  KEY `pv_product_parameter_group_id` (`pv_product_parameter_group_id`),
  KEY `pv_parent_id` (`pv_parent_id`),
  CONSTRAINT `product_variant_ibfk_1` FOREIGN KEY (`pv_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_variant_ibfk_2` FOREIGN KEY (`pv_product_variant_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_variant_ibfk_3` FOREIGN KEY (`pv_product_parameter_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_variant_ibfk_4` FOREIGN KEY (`pv_parent_id`) REFERENCES `product_variant` (`pv_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;