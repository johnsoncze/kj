ALTER TABLE `order`
ADD `o_delivery_telephone` varchar(20) COLLATE 'utf8_general_ci' NULL AFTER `o_delivery_tracking_code`;
