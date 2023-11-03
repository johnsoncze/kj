ALTER TABLE `page`
CHANGE `p_name` `p_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `p_content` `p_content` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `p_title_seo` `p_title_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `p_description_seo` `p_description_seo` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;