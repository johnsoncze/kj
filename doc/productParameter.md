## Insert bulk product parameter relation
```sql
insert into product_parameter_relationship
(ppr_product_id, ppr_parameter_id)
select DISTINCT(ppr_product_id), HERE_WRITE_PRODUCT_PARAMETER_ID from product_parameter_relationship
where ppr_product_id IN (select ppr_product_id from product_parameter_relationship
where ppr_parameter_id = HERE_WRITE_PRODUCT_PARAMETER_ID)
```

## Insert bulk product parameter relation which are not a parameter 
```sql
insert into product_parameter_relationship 
(ppr_product_id, ppr_parameter_id) 
select DISTINCT(ppr_product_id), HERE_WRITE_INSERTING_PRODUCT_PARAMETER_ID 
from product_parameter_relationship 
where ppr_product_id 
IN (select ppr_product_id from product_parameter_relationship where ppr_parameter_id = HERE_WRITE_RECOGNIZABLE_PARAMETER_ID)
and ppr_product_id 
not in (select ppr_product_id from product_parameter_relationship where ppr_parameter_id in (EXCLUDE_PARAMETER_ID,..))
```

## Find products which do not have any parameter/s
```sql
select * from product_translation where pt_product_id NOT IN 
(select ppr_product_id from product_parameter_relationship where ppr_parameter_id in (251, 252))
```