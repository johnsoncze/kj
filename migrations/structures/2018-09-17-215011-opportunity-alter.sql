ALTER TABLE `opportunity`
CHANGE `opp_first_name` `opp_first_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `opp_last_name` `opp_last_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `opp_comment` `opp_comment` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;