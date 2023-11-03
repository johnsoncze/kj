ALTER TABLE `product_parameter` ADD `pp_helper_id` INT NULL AFTER `pp_id`, ADD INDEX (`pp_helper_id`);
ALTER TABLE `product_parameter` ADD FOREIGN KEY (`pp_helper_id`) REFERENCES `parameter_helper`(`pph_id`) ON DELETE RESTRICT ON UPDATE CASCADE;