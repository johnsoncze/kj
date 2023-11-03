CREATE
 ALGORITHM = UNDEFINED
 VIEW `vw_product_parameter_list`
 AS select lan_id AS language_id,
ppg_id AS group_id,
pp_id AS parameter_id,
ppgt_name AS group_name,
ppt_value As parameter_value

from language

inner join product_parameter_group_translation
on ppgt_language_id = lan_id

inner join product_parameter_group
on ppg_id = ppgt_product_parameter_group_id

inner join product_parameter
on pp_product_parameter_group_id = ppg_id

inner join product_parameter_translation
on ppt_language_id = lan_id
and ppt_product_parameter_id = pp_id