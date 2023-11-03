CREATE TABLE `product_parameter_group_translation` (
  `ppgt_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppgt_product_parameter_group_id` int(11) NOT NULL,
  `ppgt_language_id` int(11) NOT NULL,
  `ppgt_name` varchar(50) NOT NULL,
  `ppgt_filtration_title` varchar(20) NOT NULL,
  `ppgt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ppgt_id`),
  KEY `ppgt_product_parameter_group_id` (`ppgt_product_parameter_group_id`),
  KEY `ppgt_language_id` (`ppgt_language_id`),
  CONSTRAINT `product_parameter_group_translation_ibfk_1` FOREIGN KEY (`ppgt_product_parameter_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_parameter_group_translation_ibfk_2` FOREIGN KEY (`ppgt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;