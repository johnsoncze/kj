CREATE TABLE `periskop_weeding_ring_mapping` (
  `pwrm_id` int(11) NOT NULL AUTO_INCREMENT,
  `pwrm_male_id` int(11) NOT NULL,
  `pwrm_female_id` int(11) NOT NULL,
  `pwrm_product_id` int(11) NOT NULL,
  `pwrm_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pwrm_id`),
  UNIQUE KEY `pwrm_male_id` (`pwrm_male_id`,`pwrm_female_id`,`pwrm_product_id`),
  KEY `pwrm_product_id` (`pwrm_product_id`),
  CONSTRAINT `periskop_weeding_ring_mapping_ibfk_1` FOREIGN KEY (`pwrm_product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;