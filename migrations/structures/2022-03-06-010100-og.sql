ALTER TABLE `page`
    ADD `p_title_og` text NULL,
    ADD `p_description_og` text NULL AFTER `p_title_og`;

ALTER TABLE `product_translation`
    ADD `pt_title_og` text NULL,
    ADD `pt_description_og` text NULL AFTER `pt_title_og`;

ALTER TABLE `category`
    ADD `cat_title_og` text NULL,
    ADD `cat_description_og` text NULL AFTER `cat_title_og`;