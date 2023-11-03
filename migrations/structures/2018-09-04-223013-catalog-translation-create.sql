CREATE TABLE `catalog_translation` (
  `ctgt_id` int(11) NOT NULL AUTO_INCREMENT,
  `ctgt_catalog_id` int(11) NOT NULL,
  `ctgt_language_id` int(11) NOT NULL,
  `ctgt_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `ctgt_subtitle` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `ctgt_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `ctgt_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ctgt_id`),
  KEY `ctgt_catalog_id` (`ctgt_catalog_id`),
  KEY `ctgt_language_id` (`ctgt_language_id`),
  CONSTRAINT `catalog_translation_ibfk_1` FOREIGN KEY (`ctgt_catalog_id`) REFERENCES `catalog` (`ctg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `catalog_translation_ibfk_2` FOREIGN KEY (`ctgt_language_id`) REFERENCES `language` (`lan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;