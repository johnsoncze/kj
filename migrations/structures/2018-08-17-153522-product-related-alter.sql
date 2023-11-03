ALTER TABLE `product_related`
ADD INDEX (`pr_type`),
DROP INDEX `pr_product_id`,
ADD UNIQUE `pr_product_id` (`pr_product_id`, `pr_related_product_id`, `pr_type`) USING BTREE;