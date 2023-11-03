ALTER TABLE `order`
ADD `o_is_required_payment_gateway` TINYINT(1) NOT NULL AFTER `o_payment_vat`,
ADD `o_payment_gateway_transaction_id` VARCHAR(14) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `o_is_required_payment_gateway`,
ADD INDEX (`o_is_required_payment_gateway`),
ADD UNIQUE (`o_payment_gateway_transaction_id`);