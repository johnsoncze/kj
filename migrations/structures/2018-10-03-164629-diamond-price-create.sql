CREATE TABLE `diamond_price` (
  `dp_id` int(11) NOT NULL AUTO_INCREMENT,
  `dp_diamond_id` int(11) NOT NULL,
  `dp_quality_id` int(11) NOT NULL,
  `dp_price` decimal(19,3) NOT NULL,
  `dp_vat` decimal(19,3) NOT NULL,
  `dp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dp_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dp_id`),
  UNIQUE KEY `dp_diamond_id` (`dp_diamond_id`,`dp_quality_id`),
  KEY `dp_quality_id` (`dp_quality_id`),
  CONSTRAINT `diamond_price_ibfk_1` FOREIGN KEY (`dp_diamond_id`) REFERENCES `diamond` (`d_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `diamond_price_ibfk_2` FOREIGN KEY (`dp_quality_id`) REFERENCES `product_parameter` (`pp_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;