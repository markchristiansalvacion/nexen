SELECT 
a.id AS picklist_id,
a.ref_no,
a.so_no,
a.to_id,
a.allocated_lpn,
a.allocated_sku_code,
tb_items.material_description,
a.allocated_qty,
a.allocated_expiry,
a.bin_loc,
tb_transfer_order.so_date,
tb_transfer_order.rdd,
tb_transfer_order.delivering_plant,
tb_transfer_order.ship_to_code,
tb_warehouse.warehouse_name,
tb_warehouse.warehouse_address,
tb_customer.ship_to_name,
tb_customer.ship_to_address
FROM tb_picklist a
LEFT JOIN tb_items ON tb_items.sap_code = a.allocated_sku_code
LEFT JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
LEFT JOIN tb_customer ON tb_transfer_order.ship_to_code = tb_customer.ship_to_code
LEFT JOIN tb_warehouse ON tb_transfer_order.delivering_plant = tb_warehouse.warehouse_id
WHERE a.so_no = 1020199303