CREATE TABLE `shopping_cart_product_translation` (
  `scpt_id` int(11) NOT NULL AUTO_INCREMENT,
  `scpt_shopping_cart_product_id` int(11) NOT NULL,
  `scpt_language_id` int(11) NOT NULL,
  `scpt_product_translation_id` int(11) NOT NULL,
  `scpt_name` varchar(255) NOT NULL,
  PRIMARY KEY (`scpt_id`),
  KEY `scpt_shopping_cart_product_id` (`scpt_shopping_cart_product_id`),
  KEY `scpt_language_id` (`scpt_language_id`),
  KEY `scpt_product_translation_id` (`scpt_product_translation_id`),
  CONSTRAINT `shopping_cart_product_translation_ibfk_1` FOREIGN KEY (`scpt_shopping_cart_product_id`) REFERENCES `shopping_cart_product` (`scp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_product_translation_ibfk_2` FOREIGN KEY (`scpt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_product_translation_ibfk_3` FOREIGN KEY (`scpt_product_translation_id`) REFERENCES `product_translation` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;