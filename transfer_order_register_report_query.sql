SELECT
      a.id,
      a.ref_no,
      a.ia_id AS ia_ref,
      a.to_id AS to_ref,
      tb_transport_allocation.do_no AS pcppi_do,
      tb_transport_allocation.shipment_no AS pccpi_ship_no,
      tb_transport_allocation.system_do_no,
      tb_transport_allocation.system_shipment_no,
      tb_transfer_order.ship_to_code,
      tb_customer.ship_to_name,
      tb_transfer_order.so_date,
      tb_transfer_order.rdd,
      tb_transfer_order.so_item_no,
      tb_transfer_order.so_no,
      a.allocated_lpn,
      a.picked_lpn,
      a.allocated_sku_code,
      a.picked_sku_code,
      tb_items.material_description,
      a.allocated_qty,
      a.picked_qty,
      a.allocated_expiry,
      a.picked_expiry,
      a.bin_loc,
      a.picked_loc,
      tb_transport_allocation.truck_type,
      tb_transport_allocation.driver,
      tb_transport_allocation.plate_no,
      a.fulfillment_status
      FROM tb_picklist a
      LEFT JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
      LEFT JOIN tb_items ON tb_items.sap_code = a.picked_sku_code
      LEFT JOIN tb_transport_allocation ON tb_transport_allocation.to_id = a.to_id
      LEFT JOIN tb_customer ON tb_customer.ship_to_code = tb_transfer_order.ship_to_code