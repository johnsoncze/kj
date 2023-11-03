ALTER TABLE `customer`
ADD INDEX (`cus_first_name`),
ADD INDEX (`cus_last_name`),
ADD INDEX (`cus_activation_token_valid_to`),
ADD INDEX (`cus_state`);