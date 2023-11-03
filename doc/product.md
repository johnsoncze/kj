## Update product by parameter

```sql
update product_translation
left join product_parameter_relationship
on ppr_product_id = pt_product_id
set WRITE_COLUMNS_FOR_UPDATE
where ppr_parameter_id = WRITE_PARAMETER_ID
```