ALTER TABLE `order_product`
CHANGE `op_discount` `op_discount` DECIMAL(19,4) NOT NULL,
CHANGE `op_unit_price` `op_unit_price` DECIMAL(19,4) NOT NULL,
CHANGE `op_unit_price_without_vat` `op_unit_price_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `op_unit_price_before_discount` `op_unit_price_before_discount` DECIMAL(19,4) NOT NULL,
CHANGE `op_unit_price_before_discount_without_vat` `op_unit_price_before_discount_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `op_summary_price` `op_summary_price` DECIMAL(19,4) NOT NULL,
CHANGE `op_summary_price_without_vat` `op_summary_price_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `op_summary_price_before_discount` `op_summary_price_before_discount` DECIMAL(19,4) NOT NULL,
CHANGE `op_summary_price_before_discount_without_vat` `op_summary_price_before_discount_without_vat` DECIMAL(19,4) NOT NULL,
CHANGE `op_surcharge_percent` `op_surcharge_percent` DECIMAL(19,4) NULL DEFAULT NULL,
CHANGE `op_surcharge` `op_surcharge` DECIMAL(19,4) NULL DEFAULT NULL,
CHANGE `op_surcharge_without_vat` `op_surcharge_without_vat` DECIMAL(19,4) NULL DEFAULT NULL,
CHANGE `op_vat` `op_vat` DECIMAL(19,4) NOT NULL;