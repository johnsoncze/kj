CREATE TABLE `delivery_translation` (
  `dt_id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_delivery_id` int(11) NOT NULL,
  `dt_language_id` int(11) NOT NULL,
  `dt_name` varchar(255) NOT NULL,
  `dt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dt_id`),
  KEY `dt_delivery_id` (`dt_delivery_id`),
  KEY `dt_language_id` (`dt_language_id`),
  CONSTRAINT `delivery_translation_ibfk_1` FOREIGN KEY (`dt_delivery_id`) REFERENCES `delivery` (`d_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `delivery_translation_ibfk_2` FOREIGN KEY (`dt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;