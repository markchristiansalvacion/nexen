SELECT
a.id,
a.to_id,
a.so_no,
tb_customer.ship_to_name,
tb_transfer_order.rdd,
a.ref_no AS picklist_ref,
a.allocated_sku_code,
tb_items.material_description,
a.allocated_qty,
a.truck_allocation_status
FROM tb_picklist a
LEFT JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
LEFT JOIN tb_customer ON tb_customer.ship_to_code = tb_transfer_order.ship_to_code
LEFT JOIN tb_items ON tb_items.sap_code = a.allocated_sku_code
WHERE a.truck_allocation_status = "Pending"