CREATE TABLE `shopping_cart_delivery_translation` (
  `scdt_id` int(11) NOT NULL AUTO_INCREMENT,
  `scdt_shopping_cart_delivery_id` int(11) NOT NULL,
  `scdt_language_id` int(11) NOT NULL,
  `scdt_delivery_translation_id` int(11) NOT NULL,
  `scdt_name` varchar(255) NOT NULL,
  PRIMARY KEY (`scdt_id`),
  KEY `scdt_shopping_cart_delivery_id` (`scdt_shopping_cart_delivery_id`),
  KEY `scdt_language_id` (`scdt_language_id`),
  KEY `scdt_delivery_translation_id` (`scdt_delivery_translation_id`),
  CONSTRAINT `shopping_cart_delivery_translation_ibfk_2` FOREIGN KEY (`scdt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_delivery_translation_ibfk_3` FOREIGN KEY (`scdt_delivery_translation_id`) REFERENCES `delivery_translation` (`dt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_delivery_translation_ibfk_4` FOREIGN KEY (`scdt_shopping_cart_delivery_id`) REFERENCES `shopping_cart_delivery` (`scd_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;