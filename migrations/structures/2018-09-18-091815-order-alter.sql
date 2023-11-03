ALTER TABLE `order`
ADD INDEX (`o_state`),
ADD INDEX (`o_sent_to_ee_tracking`),
ADD INDEX (`o_customer_first_name`),
ADD INDEX (`o_customer_last_name`),
ADD INDEX (`o_add_date`);