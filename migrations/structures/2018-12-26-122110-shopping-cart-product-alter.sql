ALTER TABLE `shopping_cart_product` ADD `scp_product_production_time_id` INT NULL AFTER `scp_production_time`, ADD INDEX (`scp_product_production_time_id`);
ALTER TABLE `shopping_cart_product` ADD FOREIGN KEY (`scp_product_production_time_id`) REFERENCES `product_production_time`(`ppt_id`) ON DELETE SET NULL ON UPDATE CASCADE;