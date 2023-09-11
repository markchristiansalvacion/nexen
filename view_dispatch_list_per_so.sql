SELECT 
          a.id,
          a.to_no,
          a.transaction_type,
          a.uploading_file_name,
          a.delivery_order_no,
          a.pcppi_shipment_no,
          a.so_date,
          a.rdd,
          a.so_no,
          a.delivering_plant,
          a.ship_to_code,
          -- a.req_qty_case,
          SUM(a.req_qty_case) as total_req_case,
          -- a.allocated_qty,
          SUM(a.allocated_qty) AS total_allocated_qty,
          SUM(a.picked_qty*-1) AS total_picked_qty,
          a.created_by,
          a.upload_date,
          a.truck_allocation_status,
          tb_warehouse.warehouse_name,
          tb_customer.ship_to_name,
          tb_customer.ship_to_address
          FROM tb_transfer_order a
          LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
          LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
          LEFT JOIN tb_warehouse ON tb_warehouse.warehouse_id = a.delivering_plant
          WHERE a.`status` <> ""
          GROUP BY a.so_no