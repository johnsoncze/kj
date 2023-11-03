CREATE TABLE `payment_translation` (
  `pyt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pyt_payment_id` int(11) NOT NULL,
  `pyt_language_id` int(11) NOT NULL,
  `pyt_name` varchar(255) NOT NULL,
  `pyt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pyt_id`),
  KEY `pyt_payment_id` (`pyt_payment_id`),
  KEY `pyt_language_id` (`pyt_language_id`),
  CONSTRAINT `payment_translation_ibfk_1` FOREIGN KEY (`pyt_payment_id`) REFERENCES `payment` (`py_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_translation_ibfk_2` FOREIGN KEY (`pyt_language_id`) REFERENCES `language` (`lan_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;