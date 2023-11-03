CREATE TABLE `shopping_cart_product` (
  `scp_id` int(11) NOT NULL AUTO_INCREMENT,
  `scp_shopping_cart_id` int(11) NOT NULL,
  `scp_product_id` int(11) DEFAULT NULL,
  `scp_quantity` int(11) NOT NULL,
  `scp_discount` decimal(19,3) NOT NULL,
  `scp_price` decimal(19,3) NOT NULL,
  `scp_vat` decimal(19,3) NOT NULL,
  `scp_hash` varchar(42) NOT NULL,
  `scp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scp_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`scp_id`),
  UNIQUE KEY `scp_hash` (`scp_hash`),
  KEY `scp_product_id` (`scp_product_id`),
  KEY `scp_shopping_cart_id` (`scp_shopping_cart_id`),
  CONSTRAINT `shopping_cart_product_ibfk_1` FOREIGN KEY (`scp_product_id`) REFERENCES `product` (`p_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_product_ibfk_2` FOREIGN KEY (`scp_shopping_cart_id`) REFERENCES `shopping_cart` (`sc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;