-- Create syntax for TABLE 'language'
CREATE TABLE `language` (
  `lan_id` int(11) NOT NULL AUTO_INCREMENT,
  `lan_prefix` varchar(3) COLLATE utf8_czech_ci NOT NULL,
  `lan_name` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `lan_default` smallint(1) DEFAULT NULL,
  `lan_active` smallint(1) DEFAULT NULL,
  `lan_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- Create syntax for TABLE 'user'
CREATE TABLE `user` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(255) NOT NULL,
  `u_email` varchar(255) NOT NULL,
  `u_password` varchar(255) NOT NULL,
  `u_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'article'
CREATE TABLE `article` (
  `art_id` int(11) NOT NULL AUTO_INCREMENT,
  `art_language_id` int(11) NOT NULL,
  `art_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `art_url` varchar(255) NOT NULL,
  `art_title_seo` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `art_description_seo` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `art_cover_photo` varchar(255) DEFAULT NULL,
  `art_introduction` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `art_content` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `art_status` varchar(10) NOT NULL,
  `art_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `art_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`art_id`),
  KEY `art_language_id` (`art_language_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`art_language_id`) REFERENCES `language` (`lan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'article_category'
CREATE TABLE `article_category` (
  `ac_id` int(11) NOT NULL AUTO_INCREMENT,
  `ac_language_id` int(11) NOT NULL,
  `ac_name` varchar(255) NOT NULL,
  `ac_url` varchar(255) NOT NULL,
  `ac_title_seo` varchar(255) DEFAULT NULL,
  `ac_description_seo` text,
  `ac_sort` int(11) DEFAULT NULL,
  `ac_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ac_id`),
  KEY `ac_language_id` (`ac_language_id`),
  CONSTRAINT `article_category_ibfk_1` FOREIGN KEY (`ac_language_id`) REFERENCES `language` (`lan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'article_category_relationship'
CREATE TABLE `article_category_relationship` (
  `acr_id` int(11) NOT NULL AUTO_INCREMENT,
  `acr_article_id` int(11) NOT NULL,
  `acr_article_category_id` int(11) NOT NULL,
  PRIMARY KEY (`acr_id`),
  KEY `acr_article_category_id` (`acr_article_category_id`),
  KEY `arc_article_id` (`acr_article_id`),
  CONSTRAINT `article_category_relationship_ibfk_2` FOREIGN KEY (`acr_article_category_id`) REFERENCES `article_category` (`ac_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `article_category_relationship_ibfk_3` FOREIGN KEY (`acr_article_id`) REFERENCES `article` (`art_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'forgotten_password'
CREATE TABLE `forgotten_password` (
  `fp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fp_user_id` int(11) DEFAULT NULL,
  `fp_customer_id` int(11) DEFAULT NULL,
  `fp_hash` varchar(255) NOT NULL,
  `fp_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fp_validity_date` datetime NOT NULL,
  PRIMARY KEY (`fp_id`),
  KEY `fp_user_id` (`fp_user_id`),
  KEY `fp_customer_id` (`fp_customer_id`),
  CONSTRAINT `forgotten_password_ibfk_2` FOREIGN KEY (`fp_customer_id`) REFERENCES `customer` (`cus_id`) ON UPDATE CASCADE,
  CONSTRAINT `forgotten_password_ibfk_3` FOREIGN KEY (`fp_user_id`) REFERENCES `user` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `forgotten_password_ibfk_4` FOREIGN KEY (`fp_customer_id`) REFERENCES `customer` (`cus_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;