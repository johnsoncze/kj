ALTER TABLE `opportunity_product`
ADD `oppp_comment` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL AFTER `oppp_was_in_stock`,
ADD `oppp_production_time` VARCHAR(9) NULL DEFAULT NULL AFTER `oppp_vat`,
ADD `oppp_production_time_percent` DECIMAL(19,3) NULL DEFAULT NULL AFTER `oppp_production_time`;
ALTER TABLE `opportunity_product`
DROP INDEX `oppp_opportunity_id`,
ADD INDEX `oppp_opportunity_id` (`oppp_opportunity_id`),
ADD INDEX (`oppp_production_time`);