CREATE TABLE `opportunity_product` (
  `oppp_id` int(11) NOT NULL AUTO_INCREMENT,
  `oppp_opportunity_id` int(11) NOT NULL,
  `oppp_product_id` int(11) DEFAULT NULL,
  `oppp_external_system_id` mediumint(9) DEFAULT NULL,
  `oppp_name` varchar(255) NOT NULL,
  `oppp_code` varchar(50) NOT NULL,
  `oppp_url` varchar(255) NOT NULL,
  `oppp_price` decimal(19,3) NOT NULL,
  `oppp_vat` decimal(19,3) NOT NULL,
  `oppp_stock` tinyint(1) NOT NULL,
  `oppp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`oppp_id`),
  UNIQUE KEY `oppp_opportunity_id` (`oppp_opportunity_id`,`oppp_product_id`),
  KEY `oppp_product_id` (`oppp_product_id`),
  CONSTRAINT `opportunity_product_ibfk_1` FOREIGN KEY (`oppp_opportunity_id`) REFERENCES `opportunity` (`opp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `opportunity_product_ibfk_2` FOREIGN KEY (`oppp_product_id`) REFERENCES `product` (`p_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;