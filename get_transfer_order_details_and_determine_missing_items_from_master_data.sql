SELECT 
a.id,
a.to_no,
a.transaction_type,
a.uploading_file_name,
a.delivery_order_no,
a.pcppi_shipment_no,
a.so_date,
a.rdd,
a.delivering_plant,
a.ship_to_code,
a.sku_code,
a.material_description,
a.req_qty_case,
a.allocated_qty,
a.picked_qty,
a.allocated_by,
a.`status`,
a.fill_rate_status,
a.upload_date,
tb_items.sap_code AS item_master_code,
tb_items.material_description,
tb_customer.ship_to_name,
tb_customer.ship_to_address
FROM tb_transfer_order a
LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
WHERE a.`status` <> "Dispatch"