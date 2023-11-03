ALTER TABLE `payment`
CHANGE `py_price` `py_price` DECIMAL(19,4) NOT NULL,
CHANGE `py_vat` `py_vat` DECIMAL(19,4) NOT NULL;