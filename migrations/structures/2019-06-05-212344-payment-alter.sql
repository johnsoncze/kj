ALTER TABLE `payment`
ADD `py_non_stock_producible_product_availability` TINYINT(1) NOT NULL AFTER `py_transfer`,
ADD INDEX (`py_non_stock_producible_product_availability`);