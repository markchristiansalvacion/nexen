SELECT
a.sku_code,
tb_items.material_description,
SUM(a.qty_case - a.allocated_qty) AS available_qty,
a.expiry AS exp_date,
DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) AS shelf_life_percentage
FROM tb_inventory_adjustment a 
INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
WHERE DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) >= 0.8
AND a.transaction_type = 'INB'
AND a.qty_case - a.allocated_qty <> 0
AND a.sku_code = 9010000017
GROUP BY exp_date


