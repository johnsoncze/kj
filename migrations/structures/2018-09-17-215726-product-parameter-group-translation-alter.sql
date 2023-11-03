ALTER TABLE `product_parameter_group_translation`
CHANGE `ppgt_name` `ppgt_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
CHANGE `ppgt_filtration_title` `ppgt_filtration_title` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL;