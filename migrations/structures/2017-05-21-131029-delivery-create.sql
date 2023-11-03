CREATE TABLE `delivery` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `d_price` decimal(19,3) NOT NULL,
  `d_vat` decimal(19,3) NOT NULL,
  `d_sort` int(11) NOT NULL,
  `d_state` varchar(10) NOT NULL,
  `d_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`d_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;