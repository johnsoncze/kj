ALTER TABLE `customer`
CHANGE `cus_first_name` `cus_first_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `cus_last_name` `cus_last_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `cus_addressing` `cus_addressing` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cus_street` `cus_street` VARCHAR(35) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cus_city` `cus_city` VARCHAR(35) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cus_hear_about_us_comment` `cus_hear_about_us_comment` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;