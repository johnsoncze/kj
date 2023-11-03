CREATE TABLE `shopping_cart_payment` (
  `scp_id` int(11) NOT NULL AUTO_INCREMENT,
  `scp_shopping_cart_id` int(11) NOT NULL,
  `scp_payment_id` int(11) DEFAULT NULL,
  `scp_discount` decimal(19,3) NOT NULL,
  `scp_price` decimal(19,3) NOT NULL,
  `scp_vat` decimal(19,3) NOT NULL,
  `scp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scp_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`scp_id`),
  KEY `scp_shopping_cart_id` (`scp_shopping_cart_id`),
  KEY `scp_payment_id` (`scp_payment_id`),
  CONSTRAINT `shopping_cart_payment_ibfk_1` FOREIGN KEY (`scp_shopping_cart_id`) REFERENCES `shopping_cart` (`sc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_payment_ibfk_2` FOREIGN KEY (`scp_payment_id`) REFERENCES `payment` (`py_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;