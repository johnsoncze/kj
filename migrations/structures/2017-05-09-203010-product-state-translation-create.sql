CREATE TABLE `product_state_translation` (
  `pst_id` int(11) NOT NULL AUTO_INCREMENT,
  `pst_language_id` int(11) NOT NULL,
  `pst_state_id` int(11) NOT NULL,
  `pst_value` varchar(255) NOT NULL,
  `pst_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pst_id`),
  KEY `pst_language_id` (`pst_language_id`),
  KEY `pst_state_id` (`pst_state_id`),
  CONSTRAINT `product_state_translation_ibfk_1` FOREIGN KEY (`pst_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_state_translation_ibfk_2` FOREIGN KEY (`pst_state_id`) REFERENCES `product_state` (`ps_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;