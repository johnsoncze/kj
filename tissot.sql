ALTER TABLE `product`
CHANGE `p_update_date` `p_update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP AFTER `p_add_date`,
ADD `tmp_discount` tinyint(1) NOT NULL DEFAULT '0';

UPDATE `product`
JOIN `category_product_sorting` ON `cps_product_id` = `p_id`
SET `tmp_discount` = 1
WHERE `cps_category_id` = 45;
