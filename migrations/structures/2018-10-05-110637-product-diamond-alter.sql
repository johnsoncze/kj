ALTER TABLE `product_diamond` ADD `pd_gender` VARCHAR(6) NULL AFTER `pd_diamond_id`, ADD INDEX (`pd_gender`);