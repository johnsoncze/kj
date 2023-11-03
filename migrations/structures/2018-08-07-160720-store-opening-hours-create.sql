CREATE TABLE `store_opening_hours` (
  `soh_id` int(11) NOT NULL AUTO_INCREMENT,
  `soh_day` varchar(10) NOT NULL,
  `soh_opening_time` time NOT NULL,
  `soh_closing_time` time NOT NULL,
  `soh_sort` tinyint(11) NOT NULL,
  PRIMARY KEY (`soh_id`),
  UNIQUE KEY `soh_day` (`soh_day`),
  KEY `soh_day_2` (`soh_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;