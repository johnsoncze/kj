ALTER TABLE `product` ADD `p_type` VARCHAR(17) NOT NULL DEFAULT 'default' AFTER `p_id`, ADD INDEX (`p_type`);