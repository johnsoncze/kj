ALTER TABLE `product_parameter_relationship`
ADD UNIQUE (`ppr_product_id`, `ppr_parameter_id`);