ALTER TABLE `delivery` ADD `d_external_system_id` INT NOT NULL AFTER `d_id`, ADD UNIQUE (`d_external_system_id`);