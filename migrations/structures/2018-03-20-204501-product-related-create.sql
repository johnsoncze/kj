CREATE TABLE `product_related` (
  `pr_id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_product_id` int(11) NOT NULL,
  `pr_related_product_id` int(11) NOT NULL,
  `pr_type` varchar(10) NOT NULL,
  `pr_parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pr_id`),
  UNIQUE KEY `pr_product_id` (`pr_product_id`,`pr_related_product_id`),
  KEY `pr_related_product_id` (`pr_related_product_id`),
  KEY `pr_parent_id` (`pr_parent_id`),
  CONSTRAINT `product_related_ibfk_1` FOREIGN KEY (`pr_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_related_ibfk_2` FOREIGN KEY (`pr_related_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_related_ibfk_3` FOREIGN KEY (`pr_parent_id`) REFERENCES `product_related` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;