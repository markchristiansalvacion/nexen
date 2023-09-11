SELECT
a.sku_code,
tb_items.material_description,
SUM(a.qty_case) AS SOH,
a.expiry AS exp_date,
tb_items.shelf_life,
DATEDIFF(a.expiry,CURDATE()) AS days_to_expiry,
DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30)*100 AS shelf_life_percentage
FROM tb_inventory_adjustment a 
INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
GROUP BY a.sku_code
ORDER BY shelf_life_percentage ASC
