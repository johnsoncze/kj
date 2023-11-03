ALTER TABLE `product_parameter_group`
ADD `ppg_visible_in_order` TINYINT(1) NOT NULL DEFAULT '0' AFTER `ppg_filtration_type`,
ADD INDEX (`ppg_visible_in_order`);