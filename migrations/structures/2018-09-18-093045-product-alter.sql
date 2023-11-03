ALTER TABLE `product`
ADD INDEX (`p_code`),
ADD INDEX (`p_stock`),
ADD INDEX (`p_state`),
ADD INDEX (`p_is_completed`),
ADD INDEX (`p_is_new`),
ADD INDEX (`p_sale_online`);