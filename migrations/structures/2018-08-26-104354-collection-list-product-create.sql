CREATE TABLE `collection_list_product` (
  `clp_id` int(11) NOT NULL AUTO_INCREMENT,
  `clp_category_id` int(11) NOT NULL,
  `clp_product_id` int(11) NOT NULL,
  `clp_sort` int(11) NOT NULL,
  PRIMARY KEY (`clp_id`),
  UNIQUE KEY `clp_category_id` (`clp_category_id`,`clp_product_id`),
  KEY `clp_product_id` (`clp_product_id`),
  CONSTRAINT `collection_list_product_ibfk_1` FOREIGN KEY (`clp_category_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `collection_list_product_ibfk_2` FOREIGN KEY (`clp_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;