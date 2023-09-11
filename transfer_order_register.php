<?php
require_once 'includes/load.php';

if (is_login_auth()) {

    /** SESSION BASE TO TIME TODAY */
  
    if (is_session_expired()) {
      $_SESSION['msg'] = "<b>SESSION EXPIRED:</b> Please Login Again.";
      $_SESSION['msg_type'] = "danger";
  
      unset($_SESSION['user_id']);
      unset($_SESSION['name']);
      unset($_SESSION['user_type']);
      unset($_SESSION['user_status']);
  
      unset($_SESSION['login_time']);
  
      /**TIME TO DAY + 315360000 THAT EQUIVALENT TO 10 YEARS*/
  
      redirect("login", false);
    }
} else {
redirect("login", false);
}

    $date_today = date('Y-m-d');

    $allocated_truck = $db->query('SELECT
    a.id,
    a.ref_no,
    a.ia_id AS ia_ref,
    a.to_id AS to_ref,
    tb_transport_allocation.do_no AS pcppi_do,
    tb_transport_allocation.shipment_no AS pcppi_ship_no,
    tb_transport_allocation.system_do_no,
    tb_transport_allocation.system_shipment_no,
    tb_transfer_order.ship_to_code,
    tb_customer.ship_to_name,
    tb_transfer_order.so_date,
    tb_transfer_order.rdd,
    tb_transfer_order.so_item_no,
    tb_transfer_order.so_no,
    tb_transfer_order.delivering_plant,
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
    a.created_by,
    a.last_updated,
    tb_transport_allocation.truck_type,
    tb_transport_allocation.driver,
    tb_transport_allocation.plate_no,
    a.fulfillment_status
    FROM tb_picklist a
    LEFT JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
    LEFT JOIN tb_items ON tb_items.sap_code = a.picked_sku_code
    LEFT JOIN tb_transport_allocation ON tb_transport_allocation.to_id = a.to_id
    LEFT JOIN tb_customer ON tb_customer.ship_to_code = tb_transfer_order.ship_to_code')->fetch_all();
      

if(!empty($allocated_truck)){
  
    $file_name = "IRIS Transfer Order Registers ".date('Y-M-d').".csv";
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Type: text/csv;");
  
    $file = fopen('php://output', 'w');
  
    $header = array("PL ID","PL Ref","IA Ref","TO Ref","PCPPI DO","PCPPI Ship No","Iris DO No","Iris Ship No","Ship To Code","Ship To Name","SO Date","RDD","SO Item No","SO No","Delivering Plant","Allocated SKU", "Picked SKU","Allocated LPN","Picked LPN","Allocated BBD","Picked BBD","Allocated Qty","Picked Qty","Allocated Bin","Picked Bin","Allocated By","Fulfillment Status", "Fulfillment Date", "Truck Type","Driver","Plate No");
    fputcsv($file, $header);

    foreach ($allocated_truck as $asar_key => $row) {
        $data = array();
        $data[] = $db->escape_string($row["id"]);
        $data[] = $db->escape_string($row["ref_no"]);
        $data[] = $db->escape_string($row["ia_ref"]);
        $data[] = $db->escape_string($row["to_ref"]);
        $data[] = $db->escape_string($row["pcppi_do"]);
        $data[] = $db->escape_string($row["pcppi_ship_no"]);
        $data[] = $db->escape_string($row["system_do_no"]);
        $data[] = $db->escape_string($row["system_shipment_no"]);
        $data[] = $db->escape_string($row["ship_to_code"]);
        $data[] = $db->escape_string($row["ship_to_name"]);
        $data[] = $row['so_date'];
        $data[] = $row['rdd'];
        $data[] = $db->escape_string($row["so_item_no"]);
        $data[] = $db->escape_string($row["so_no"]);
        $data[] = $db->escape_string($row["delivering_plant"]);
        $data[] = $db->escape_string($row["allocated_sku_code"]);
        $data[] = $db->escape_string($row["picked_sku_code"]);
        $data[] = $db->escape_string($row["allocated_lpn"]);
        $data[] = $db->escape_string($row["picked_lpn"]);
        $data[] = $db->escape_string($row["allocated_expiry"]);
        $data[] = $db->escape_string($row["picked_expiry"]);
        $data[] = $db->escape_string($row["allocated_qty"]);
        $data[] = $db->escape_string($row["picked_qty"]);
        $data[] = $db->escape_string($row["bin_loc"]);
        $data[] = $db->escape_string($row["picked_loc"]);
        $data[] = $db->escape_string($row["created_by"]);
        $data[] = $db->escape_string($row["fulfillment_status"]);
        $data[] = $row['last_updated'];
        
        switch ($row['truck_type']) {
          case "ZL":
            $data[] = "AUVLOTTE";
            break;
          case "ZH":
            $data[] ="4WLOTTE";
            break;
          case "ZD":
            $data[] ="6WLOTTE";
            break;
          case "Y1":
            $data[] ="AVA7147";
            break;
          case "Z6":
            $data[] ="CBJ-7120";
            break;
          default:
            $data[] = "10WLOTTE";
        }

        $data[] = $db->escape_string($row["driver"]);
        $data[] = $db->escape_string($row["plate_no"]);

      
        fputcsv($file, $data);
      }
      fclose($file);
      exit;

    }else{
      $_SESSION['msg_heading'] = "Download Failed!";
      $_SESSION['msg'] = "No Available Data for SAP Integration File (F2).";
      $_SESSION['msg_type'] = "warning";
      redirect("index");
    }





?>