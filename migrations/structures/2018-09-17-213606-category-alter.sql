ALTER TABLE `category`
CHANGE `cat_name` `cat_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `cat_content` `cat_content` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cat_title_seo` `cat_title_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cat_description_seo` `cat_description_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;