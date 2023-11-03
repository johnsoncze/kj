ALTER TABLE `order`
ADD `o_token` CHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL AFTER `o_sent_to_external_system`,
ADD `o_payment_gateway_transaction_state` VARCHAR(10) NULL AFTER `o_payment_gateway_transaction_id`,
ADD UNIQUE (`o_token`),
ADD INDEX (`o_payment_gateway_transaction_state`);