CREATE TABLE `product_production_time_translation` (
  `pptt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pptt_time_id` int(11) NOT NULL,
  `pptt_language_id` int(11) NOT NULL,
  `pptt_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `pptt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pptt_id`),
  KEY `pptt_language_id` (`pptt_language_id`),
  KEY `pptt_time_id` (`pptt_time_id`),
  CONSTRAINT `product_production_time_translation_ibfk_1` FOREIGN KEY (`pptt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_production_time_translation_ibfk_2` FOREIGN KEY (`pptt_time_id`) REFERENCES `product_production_time` (`ppt_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;