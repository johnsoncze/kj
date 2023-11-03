ALTER TABLE `category_filtration_group`
ADD `cfg_show_in_menu` TINYINT(1) NOT NULL AFTER `cfg_site_map`,
ADD INDEX (`cfg_show_in_menu`);