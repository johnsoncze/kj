ALTER TABLE `customer`
ADD `cus_external_system_id` MEDIUMINT NULL AFTER `cus_id`,
ADD `cus_first_name` VARCHAR(50) NOT NULL AFTER `cus_external_system_id`,
ADD `cus_last_name` VARCHAR(50) NOT NULL AFTER `cus_first_name`,
ADD `cus_sex` CHAR(1) NULL AFTER `cus_last_name`,
ADD `cus_addressing` VARCHAR(255) NULL AFTER `cus_sex`,
ADD `cus_email` VARCHAR(50) NOT NULL AFTER `cus_addressing`,
ADD `cus_telephone` VARCHAR(20) NULL AFTER `cus_email`,
ADD `cus_street` VARCHAR(35) NULL AFTER `cus_telephone`,
ADD `cus_city` VARCHAR(35) NULL AFTER `cus_street`,
ADD `cus_postcode` CHAR(5) NULL AFTER `cus_city`,
ADD `cus_country_code` CHAR(2) NULL AFTER `cus_postcode`,
ADD `cus_birthday_year` CHAR(4) NULL AFTER `cus_country_code`,
ADD `cus_birthday_month` CHAR(2) NULL AFTER `cus_birthday_year`,
ADD `cus_birthday_day` CHAR(2) NULL AFTER `cus_birthday_month`,
ADD `cus_birthday_coupon` TINYINT(1) NOT NULL AFTER `cus_birthday_day`,
ADD `cus_newsletter` TINYINT(1) NOT NULL AFTER `cus_birthday_coupon`,
ADD `cus_password` BINARY(60) NULL AFTER `cus_newsletter`,
ADD `cus_activation_token` CHAR(32) NULL AFTER `cus_password`,
ADD `cus_activation_token_valid_to` DATETIME NULL AFTER `cus_activation_token`,
ADD `cus_activation_date` DATETIME NULL AFTER `cus_activation_token_valid_to`,
ADD `cus_external_system_last_change_date` DATETIME NULL AFTER `cus_activation_date`,
ADD `cus_add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `cus_state`,
ADD UNIQUE (`cus_external_system_id`),
ADD UNIQUE (`cus_email`),
ADD UNIQUE (`cus_email`, `cus_activation_token`);