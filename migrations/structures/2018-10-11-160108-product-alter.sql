ALTER TABLE `product` CHANGE `p_update_date` `p_update_date` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL DEFAULT CURRENT_TIMESTAMP();