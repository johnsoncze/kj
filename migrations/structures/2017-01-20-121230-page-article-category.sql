CREATE TABLE `page_article_category` (
  `pac_id` int(11) NOT NULL AUTO_INCREMENT,
  `pac_page_id` int(11) NOT NULL,
  `pac_article_category_id` int(11) NOT NULL,
  PRIMARY KEY (`pac_id`),
  KEY `pac_page_id` (`pac_page_id`),
  KEY `pac_article_category_id` (`pac_article_category_id`),
  CONSTRAINT `page_article_category_ibfk_1` FOREIGN KEY (`pac_page_id`) REFERENCES `page` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `page_article_category_ibfk_2` FOREIGN KEY (`pac_article_category_id`) REFERENCES `article_category` (`ac_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;