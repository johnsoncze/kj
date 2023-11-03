CREATE TABLE `shopping_cart_payment_translation` (
  `scpt_id` int(11) NOT NULL AUTO_INCREMENT,
  `scpt_shopping_cart_payment_id` int(11) NOT NULL,
  `scpt_language_id` int(11) NOT NULL,
  `scpt_payment_translation_id` int(11) NOT NULL,
  `scpt_name` varchar(255) NOT NULL,
  PRIMARY KEY (`scpt_id`),
  KEY `scpt_shopping_cart_payment_id` (`scpt_shopping_cart_payment_id`),
  KEY `scpt_language_id` (`scpt_language_id`),
  KEY `scpt_payment_translation_id` (`scpt_payment_translation_id`),
  CONSTRAINT `shopping_cart_payment_translation_ibfk_1` FOREIGN KEY (`scpt_shopping_cart_payment_id`) REFERENCES `shopping_cart_payment` (`scp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_payment_translation_ibfk_2` FOREIGN KEY (`scpt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shopping_cart_payment_translation_ibfk_3` FOREIGN KEY (`scpt_payment_translation_id`) REFERENCES `payment_translation` (`pyt_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;