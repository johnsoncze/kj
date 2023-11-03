CREATE TABLE `ring_size` (
  `rs_id` int(11) NOT NULL AUTO_INCREMENT,
  `rs_size` char(2) NOT NULL,
  PRIMARY KEY (`rs_id`),
  KEY `rs_size` (`rs_size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;