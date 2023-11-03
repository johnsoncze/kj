ALTER TABLE `product_production_time`
ADD `ppt_default` TINYINT(1) NOT NULL AFTER `ppt_sort`,
ADD INDEX (`ppt_default`);