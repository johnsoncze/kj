CREATE TABLE `product` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_code` varchar(50) NOT NULL,
  `p_photo` varchar(255) DEFAULT NULL,
  `p_stock_state` int(11) NOT NULL,
  `p_empty_stock_state` int(11) NOT NULL,
  `p_stock` int(11) NOT NULL,
  `p_price` decimal(19,3) NOT NULL,
  `p_vat` decimal(19,3) NOT NULL,
  `p_state` varchar(10) NOT NULL,
  `p_is_new` tinyint(1) DEFAULT NULL,
  `p_sale_online` tinyint(1) NOT NULL,
  `p_google_merchant_category` varchar(255) DEFAULT NULL,
  `p_google_merchant_brand` int(11) DEFAULT NULL,
  `p_sort` int(11) NOT NULL,
  `p_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `p_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`p_id`),
  KEY `p_stock_state` (`p_stock_state`),
  KEY `p_empty_stock_state` (`p_empty_stock_state`),
  KEY `p_google_merchant_brand` (`p_google_merchant_brand`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`p_stock_state`) REFERENCES `product_state` (`ps_id`) ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`p_empty_stock_state`) REFERENCES `product_state` (`ps_id`) ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_3` FOREIGN KEY (`p_google_merchant_brand`) REFERENCES `product_parameter` (`pp_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;