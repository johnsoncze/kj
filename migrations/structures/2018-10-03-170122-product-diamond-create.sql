CREATE TABLE `product_diamond` (
  `pd_id` int(11) NOT NULL AUTO_INCREMENT,
  `pd_product_id` int(11) NOT NULL,
  `pd_diamond_id` int(11) NOT NULL,
  `pd_quantity` smallint(6) NOT NULL,
  `pd_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pd_id`),
  UNIQUE KEY `pd_product_id` (`pd_product_id`,`pd_diamond_id`),
  KEY `pd_diamond_id` (`pd_diamond_id`),
  CONSTRAINT `product_diamond_ibfk_1` FOREIGN KEY (`pd_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_diamond_ibfk_2` FOREIGN KEY (`pd_diamond_id`) REFERENCES `diamond` (`d_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;