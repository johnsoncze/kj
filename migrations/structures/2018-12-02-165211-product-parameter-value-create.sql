CREATE TABLE `parameter_helper` (
  `pph_id` int(11) NOT NULL AUTO_INCREMENT,
  `pph_key` varchar(10) NOT NULL,
  `pph_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `pph_value` varchar(10) NOT NULL,
  `pph_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pph_id`),
  KEY `pph_key` (`pph_key`),
  KEY `pph_name` (`pph_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;