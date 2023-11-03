CREATE TABLE `product_parameter_group` (
  `ppg_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppg_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ppg_internal_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ppg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;