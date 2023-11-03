ALTER TABLE `shopping_cart_payment`
CHANGE `scp_discount` `scp_discount` DECIMAL(19,4) NOT NULL,
CHANGE `scp_price` `scp_price` DECIMAL(19,4) NOT NULL,
CHANGE `scp_vat` `scp_vat` DECIMAL(19,4) NOT NULL;