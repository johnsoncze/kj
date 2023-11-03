CREATE TABLE `product_state` (
  `ps_id` int(11) NOT NULL AUTO_INCREMENT,
  `ps_sort` int(11) NOT NULL,
  `ps_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ps_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;