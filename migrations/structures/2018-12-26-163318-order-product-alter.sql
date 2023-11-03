ALTER TABLE `order_product`
ADD `op_product_production_time_id` INT NULL AFTER `op_quantity`,
ADD `op_product_production_time_name` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL AFTER `op_product_production_time_id`,
ADD INDEX (`op_product_production_time_id`);

ALTER TABLE `order_product`
ADD FOREIGN KEY (`op_product_production_time_id`) REFERENCES `product_production_time`(`ppt_id`) ON DELETE SET NULL ON UPDATE CASCADE;