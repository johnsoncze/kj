ALTER TABLE `product_translation`
ADD `pt_photo_name_generated` TINYINT(1) NOT NULL AFTER `pt_google_merchant_title`,
ADD INDEX (`pt_photo_name_generated`);