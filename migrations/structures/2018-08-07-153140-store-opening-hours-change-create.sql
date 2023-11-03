CREATE TABLE `store_opening_hours_change` (
  `sohc_id` int(11) NOT NULL AUTO_INCREMENT,
  `sohc_date` date NOT NULL,
  `sohc_opening_time` time DEFAULT NULL,
  `sohc_closing_time` time DEFAULT NULL,
  `sohc_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sohc_id`),
  UNIQUE KEY `sohc_date_2` (`sohc_date`),
  KEY `sohc_date` (`sohc_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;