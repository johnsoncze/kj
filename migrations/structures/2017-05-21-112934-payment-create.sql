CREATE TABLE `payment` (
  `py_id` int(11) NOT NULL AUTO_INCREMENT,
  `py_credit_card` tinyint(1) DEFAULT NULL,
  `py_price` decimal(19,3) NOT NULL,
  `py_vat` decimal(19,3) NOT NULL,
  `py_sort` int(11) NOT NULL,
  `py_state` varchar(10) NOT NULL,
  `py_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`py_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;