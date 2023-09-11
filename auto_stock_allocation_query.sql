SELECT
a.id, 
a.lpn,
a.sku_code,
tb_items.material_description,
a.qty_case - a.allocated_qty AS available_qty,
a.expiry AS exp_date,
a.bin_loc,
tb_items.shelf_life,
DATEDIFF(a.expiry,CURDATE()) AS days_to_expiry,
DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) AS shelf_life_percentage
FROM tb_inventory_adjustment a 
INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
WHERE DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) >= 0.5
AND a.transaction_type = 'INB'
AND a.qty_case - a.allocated_qty <> 0
AND a.sku_code = 9010000006 
ORDER BY shelf_life_percentage ASC, bin_loc DESC , available_qty ASC
LIMIT 1 

