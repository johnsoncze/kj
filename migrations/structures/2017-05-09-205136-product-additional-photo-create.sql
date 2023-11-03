CREATE TABLE `product_additional_photo` (
  `pap_id` int(11) NOT NULL AUTO_INCREMENT,
  `pap_product_id` int(11) NOT NULL,
  `pap_file_name` varchar(255) NOT NULL,
  `pap_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pap_id`),
  KEY `pap_product_id` (`pap_product_id`),
  CONSTRAINT `product_additional_photo_ibfk_1` FOREIGN KEY (`pap_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;