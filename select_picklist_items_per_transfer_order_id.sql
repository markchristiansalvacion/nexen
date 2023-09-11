SELECT 
a.id,
a.ref_no,
a.ia_id,
a.to_id,
a.allocated_lpn,
a.allocated_sku_code,
a.allocated_qty,
a.allocated_expiry,
a.bin_loc,
tb_items.material_description,
tb_transfer_order.so_no
FROM tb_picklist a
INNER JOIN tb_items ON tb_items.sap_code = a.allocated_sku_code
INNER JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
WHERE a.to_id = 1