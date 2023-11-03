ALTER TABLE `order`
CHANGE `o_payment_price` `o_payment_price` DECIMAL(19,4) NOT NULL,
CHANGE `o_payment_vat` `o_payment_vat` DECIMAL(19,4) NOT NULL,
CHANGE `o_delivery_price` `o_delivery_price` DECIMAL(19,4) NOT NULL,
CHANGE `o_delivery_vat` `o_delivery_vat` DECIMAL(19,4) NOT NULL,
CHANGE `o_summary_price` `o_summary_price` DECIMAL(19,4) NOT NULL,
CHANGE `o_summary_price_without_vat` `o_summary_price_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `o_summary_price_before_discount` `o_summary_price_before_discount` DECIMAL(19,4) NOT NULL,
CHANGE `o_summary_price_before_discount_without_vat` `o_summary_price_before_discount_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `o_product_summary_price_without_vat` `o_product_summary_price_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `o_product_summary_price` `o_product_summary_price` DECIMAL(19,4) NOT NULL;