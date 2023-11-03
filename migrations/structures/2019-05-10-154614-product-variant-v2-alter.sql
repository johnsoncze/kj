ALTER TABLE `product_variant_v2`
DROP INDEX `pv_product_id`,
ADD UNIQUE `pv_product_id` (`pv_product_id`, `pv_product_variant_id`, `pv_product_variant_parameter_id`, `pv_parent_variant_id`)
USING BTREE;