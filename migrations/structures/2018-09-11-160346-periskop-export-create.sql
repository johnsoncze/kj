CREATE TABLE `periskop_export` (
  `pe_id` int(11) NOT NULL AUTO_INCREMENT,
  `pe_type` varchar(8) NOT NULL,
  `pe_file` varchar(30) NOT NULL,
  `pe_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pe_id`),
  UNIQUE KEY `pe_file` (`pe_file`,`pe_type`),
  KEY `pe_type` (`pe_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;