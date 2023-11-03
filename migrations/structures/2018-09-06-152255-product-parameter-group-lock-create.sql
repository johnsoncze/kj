CREATE TABLE `product_parameter_group_lock` (
  `ppgl_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppgl_group_id` int(11) NOT NULL,
  `ppgl_key` varchar(50) NOT NULL,
  `ppgl_description` text NOT NULL,
  `ppgl_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ppgl_id`),
  UNIQUE KEY `ppgl_group_id` (`ppgl_group_id`,`ppgl_key`),
  KEY `ppgl_key` (`ppgl_key`),
  CONSTRAINT `product_parameter_group_lock_ibfk_1` FOREIGN KEY (`ppgl_group_id`) REFERENCES `product_parameter_group` (`ppg_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;