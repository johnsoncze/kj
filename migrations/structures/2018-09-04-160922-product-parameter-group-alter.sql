ALTER TABLE `product_parameter_group`
ADD `ppg_filtration_type` VARCHAR(10) NOT NULL AFTER `ppg_variant_type`,
ADD `ppg_help` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL AFTER `ppg_filtration_type`;