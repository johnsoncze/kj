ALTER TABLE `article_category`
CHANGE `ac_name` `ac_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `ac_title_seo` `ac_title_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `ac_description_seo` `ac_description_seo` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;