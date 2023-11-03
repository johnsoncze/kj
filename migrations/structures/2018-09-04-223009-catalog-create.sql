CREATE TABLE `catalog` (
  `ctg_id` int(11) NOT NULL AUTO_INCREMENT,
  `ctg_type` varchar(30) NOT NULL,
  `ctg_photo` varchar(255) DEFAULT NULL,
  `ctg_sort` int(11) NOT NULL,
  `ctg_state` varchar(10) NOT NULL,
  `ctg_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ctg_id`),
  KEY `ctg_type` (`ctg_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;