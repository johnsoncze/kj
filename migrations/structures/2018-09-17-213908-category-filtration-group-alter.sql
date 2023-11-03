ALTER TABLE `category_filtration_group`
CHANGE `cfg_description` `cfg_description` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cfg_title_seo` `cfg_title_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `cfg_description_seo` `cfg_description_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;