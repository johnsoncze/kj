ALTER TABLE `shopping_cart`
ADD `sc_delivery_information` VARCHAR(10) NULL AFTER `sc_delivery_postal_code`,
ADD `sc_delivery_company` VARCHAR(50) NULL AFTER `sc_telephone`,
ADD `sc_delivery_id` INT NULL AFTER `sc_telephone`,
ADD `sc_payment_id` INT NULL AFTER `sc_delivery_id`,
ADD KEY `sc_payment_id` (`sc_payment_id`),
ADD KEY `sc_delivery_id` (`sc_delivery_id`),
ADD `sc_first_name` VARCHAR(50) NULL AFTER `sc_name`,
ADD `sc_last_name` VARCHAR(50) NULL AFTER `sc_first_name`,
ADD `sc_delivery_first_name` VARCHAR(50) NULL AFTER `sc_payment_id`,
ADD `sc_delivery_last_name` VARCHAR(50) NULL AFTER `sc_delivery_first_name`,

ADD CONSTRAINT `shopping_cart_ibfk_4` FOREIGN KEY (`sc_payment_id`) REFERENCES `payment` (`py_id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `shopping_cart_ibfk_5` FOREIGN KEY (`sc_delivery_id`) REFERENCES `delivery` (`d_id`) ON DELETE SET NULL ON UPDATE CASCADE;