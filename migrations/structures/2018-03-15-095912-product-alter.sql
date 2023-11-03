ALTER TABLE `product` ADD `p_external_system_id` MEDIUMINT NOT NULL AFTER `p_code`,
ADD UNIQUE (`p_external_system_id`);