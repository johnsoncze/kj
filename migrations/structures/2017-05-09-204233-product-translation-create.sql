CREATE TABLE `product_translation` (
  `pt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_product_id` int(11) NOT NULL,
  `pt_language_id` int(11) NOT NULL,
  `pt_name` varchar(255) NOT NULL,
  `pt_description` text,
  `pt_url` varchar(255) NOT NULL,
  `pt_title_seo` varchar(255) DEFAULT NULL,
  `pt_description_seo` text,
  `pt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pt_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pt_id`),
  KEY `pt_product_id` (`pt_product_id`),
  KEY `pt_language_id` (`pt_language_id`),
  CONSTRAINT `product_translation_ibfk_1` FOREIGN KEY (`pt_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_translation_ibfk_2` FOREIGN KEY (`pt_language_id`) REFERENCES `language` (`lan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;