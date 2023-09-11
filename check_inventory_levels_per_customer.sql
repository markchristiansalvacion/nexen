SELECT
a.ship_to_code,
a.sku_code,
tb_items.sap_code AS item_master_code,
tb_items.material_description,
tb_customer.ship_to_code AS customer_master_code,
tb_customer.ship_to_name,
tb_customer.req_shelf_life,
SUM(a.req_qty_case) AS order_qty
FROM tb_transfer_order a
LEFT OUTER JOIN tb_items ON tb_items.sap_code = a.sku_code
LEFT OUTER JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
GROUP BY tb_customer.req_shelf_life,a.sku_code
-- GROUP BY a.sku_code