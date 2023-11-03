CREATE TABLE `product_production_time` (
  `ppt_id` int(11) NOT NULL AUTO_INCREMENT,
  `ppt_surcharge` decimal(19,4) DEFAULT NULL,
  `ppt_sort` int(11) NOT NULL,
  `ppt_state` varchar(10) NOT NULL,
  `ppt_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ppt_id`),
  KEY `ppt_state` (`ppt_state`),
  KEY `ppt_sort` (`ppt_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;