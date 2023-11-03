ALTER TABLE `product_diamond`
DROP INDEX `pd_product_id`,
ADD UNIQUE `pd_product_id` (`pd_product_id`, `pd_diamond_id`, `pd_gender`);