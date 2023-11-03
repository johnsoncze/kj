ALTER TABLE `category`
ADD `cat_top` TINYINT(1) NOT NULL DEFAULT '0' AFTER `cat_category_slider_sort`,
ADD INDEX (`cat_top`);