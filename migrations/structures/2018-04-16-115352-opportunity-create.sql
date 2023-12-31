CREATE TABLE `opportunity` (
  `opp_id` int(11) NOT NULL AUTO_INCREMENT,
  `opp_code` char(12) NOT NULL,
  `opp_customer_id` int(11) DEFAULT NULL,
  `opp_first_name` varchar(50) NOT NULL,
  `opp_last_name` varchar(50) NOT NULL,
  `opp_preferred_contact` varchar(9) NOT NULL,
  `opp_email` varchar(50) DEFAULT NULL,
  `opp_telephone` varchar(20) DEFAULT NULL,
  `opp_comment` text,
  `opp_page` varchar(50) NOT NULL,
  `opp_page_id` int(11) DEFAULT NULL,
  `opp_state` varchar(8) NOT NULL,
  `opp_type` varchar(13) NOT NULL,
  `opp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`opp_id`),
  UNIQUE KEY `opp_code` (`opp_code`),
  KEY `opp_customer_id` (`opp_customer_id`),
  KEY `opp_type` (`opp_type`),
  CONSTRAINT `opportunity_ibfk_1` FOREIGN KEY (`opp_customer_id`) REFERENCES `customer` (`cus_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;