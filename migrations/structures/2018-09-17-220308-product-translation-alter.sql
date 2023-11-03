ALTER TABLE `product_translation`
CHANGE `pt_name` `pt_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `pt_description` `pt_description` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `pt_title_seo` `pt_title_seo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `pt_description_seo` `pt_description_seo` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
CHANGE `pt_google_merchant_title` `pt_google_merchant_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;