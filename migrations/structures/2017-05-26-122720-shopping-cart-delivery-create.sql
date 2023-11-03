CREATE TABLE `shopping_cart_delivery` (
  `scd_id` int(11) NOT NULL AUTO_INCREMENT,
  `scd_shopping_cart_id` int(11) NOT NULL,
  `scd_delivery_id` int(11) DEFAULT NULL,
  `scd_discount` decimal(19,3) NOT NULL,
  `scd_price` decimal(19,3) NOT NULL,
  `scd_vat` decimal(19,3) NOT NULL,
  `scd_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scd_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`scd_id`),
  KEY `scd_shopping_cart_id` (`scd_shopping_cart_id`),
  KEY `scd_delivery_id` (`scd_delivery_id`),
  CONSTRAINT `shopping_cart_delivery_ibfk_1` FOREIGN KEY (`scd_shopping_cart_id`) REFERENCES `shopping_cart` (`sc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_delivery_ibfk_2` FOREIGN KEY (`scd_delivery_id`) REFERENCES `delivery` (`d_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;