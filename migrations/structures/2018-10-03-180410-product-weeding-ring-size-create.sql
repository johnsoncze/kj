CREATE TABLE `product_weeding_ring_size` (
  `pws_id` int(11) NOT NULL AUTO_INCREMENT,
  `pws_product_id` int(11) NOT NULL,
  `pws_size_id` int(11) NOT NULL,
  `pws_gender` varchar(6) NOT NULL,
  `pws_price` decimal(19,3) NOT NULL,
  `pws_vat` decimal(19,3) NOT NULL,
  `pws_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pws_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pws_id`),
  UNIQUE KEY `pws_product_id` (`pws_product_id`,`pws_size_id`,`pws_gender`),
  KEY `pws_size_id` (`pws_size_id`),
  KEY `pws_gender` (`pws_gender`),
  CONSTRAINT `product_weeding_ring_size_ibfk_1` FOREIGN KEY (`pws_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_weeding_ring_size_ibfk_2` FOREIGN KEY (`pws_size_id`) REFERENCES `ring_size` (`rs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;