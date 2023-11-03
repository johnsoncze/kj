ALTER TABLE `product_parameter_group`
ADD `ppg_sort` TINYINT NOT NULL DEFAULT '1' AFTER `ppg_visible_on_product_detail`,
ADD INDEX (`ppg_sort`);