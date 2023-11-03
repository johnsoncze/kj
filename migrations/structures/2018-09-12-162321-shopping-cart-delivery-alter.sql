ALTER TABLE `shopping_cart_delivery`
CHANGE `scd_discount` `scd_discount` DECIMAL(19,4) NOT NULL,
CHANGE `scd_price` `scd_price` DECIMAL(19,4) NOT NULL,
CHANGE `scd_vat` `scd_vat` DECIMAL(19,4) NOT NULL;