ALTER TABLE `product_diamond` ADD `pd_default_quality_id` INT NOT NULL AFTER `pd_diamond_id`, ADD INDEX (`pd_default_quality_id`);
ALTER TABLE `product_diamond` ADD FOREIGN KEY (`pd_default_quality_id`) REFERENCES `product_parameter`(`pp_id`) ON DELETE RESTRICT ON UPDATE CASCADE;