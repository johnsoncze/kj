CREATE TABLE `product_parameter_group_lock_parameter` (
  `ppglp_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppglp_lock_id` int(11) NOT NULL,
  `ppglp_parameter_id` int(11) NOT NULL,
  `ppglp_value` text NOT NULL,
  PRIMARY KEY (`ppglp_id`),
  KEY `ppglp_lock_id` (`ppglp_lock_id`),
  KEY `ppglp_parameter_id` (`ppglp_parameter_id`),
  CONSTRAINT `product_parameter_group_lock_parameter_ibfk_1` FOREIGN KEY (`ppglp_lock_id`) REFERENCES `product_parameter_group_lock` (`ppgl_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_parameter_group_lock_parameter_ibfk_2` FOREIGN KEY (`ppglp_parameter_id`) REFERENCES `product_parameter` (`pp_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;