ALTER TABLE `order`
ADD `o_product_summary_price_without_vat` DECIMAL(19,3) NOT NULL AFTER `o_summary_price_before_discount_without_vat`,
ADD `o_product_summary_price` DECIMAL(19,3) NOT NULL AFTER `o_product_summary_price_without_vat`,
ADD `o_sent_to_ee_tracking` TINYINT(1) NOT NULL AFTER `o_state`;