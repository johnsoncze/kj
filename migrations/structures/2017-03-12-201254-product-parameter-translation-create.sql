CREATE TABLE `product_parameter_translation` (
  `ppt_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppt_language_id` int(11) NOT NULL,
  `ppt_product_parameter_id` int(11) NOT NULL,
  `ppt_value` varchar(50) NOT NULL DEFAULT '',
  `ppt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ppt_id`),
  KEY `ppt_language_id` (`ppt_language_id`),
  KEY `ppt_product_parameter_id` (`ppt_product_parameter_id`),
  CONSTRAINT `product_parameter_translation_ibfk_1` FOREIGN KEY (`ppt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_parameter_translation_ibfk_2` FOREIGN KEY (`ppt_product_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;