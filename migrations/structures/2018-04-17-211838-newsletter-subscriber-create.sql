CREATE TABLE `newsletter_subscriber` (
  `ns_id` int(11) NOT NULL AUTO_INCREMENT,
  `ns_email` varchar(50) NOT NULL,
  `ns_confirm_token` varchar(32) DEFAULT NULL,
  `ns_confirmed` tinyint(1) NOT NULL,
  `ns_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ns_id`),
  UNIQUE KEY `ns_email` (`ns_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;