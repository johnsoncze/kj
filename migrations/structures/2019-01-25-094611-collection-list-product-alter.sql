ALTER TABLE `collection_list_product`
DROP INDEX `clp_category_id`,
ADD UNIQUE `clp_category_id` (`clp_category_id`, `clp_product_id`, `clp_type`);