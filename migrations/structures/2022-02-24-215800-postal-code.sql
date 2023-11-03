ALTER TABLE `shopping_cart`
    CHANGE `sc_delivery_postal_code` `sc_delivery_postal_code` char(6) COLLATE 'utf8_general_ci' NULL AFTER `sc_delivery_country`,
    CHANGE `sc_billing_postal_code` `sc_billing_postal_code` char(6) COLLATE 'utf8_general_ci' NULL AFTER `sc_billing_city`,
    CHANGE `sc_update_date` `sc_update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP AFTER `sc_add_date`;

ALTER TABLE `order`
    CHANGE `o_billing_address_postcode` `o_billing_address_postcode` char(6) COLLATE 'utf8_general_ci' NOT NULL AFTER `o_billing_address_city`,
    CHANGE `o_delivery_address_postcode` `o_delivery_address_postcode` char(6) COLLATE 'utf8_general_ci' NULL AFTER `o_delivery_address_city`,
    CHANGE `o_update_date` `o_update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP AFTER `o_add_date`;