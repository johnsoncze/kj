CREATE TABLE `diamond` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `d_type` varchar(10) NOT NULL,
  `d_size` varchar(10) NOT NULL,
  PRIMARY KEY (`d_id`),
  UNIQUE KEY `d_type` (`d_type`,`d_size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;