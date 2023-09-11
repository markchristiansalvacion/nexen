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

  //print_r_html($_POST);

    $rdd = $_POST['so_date'];

    // print_r_html($so_date);
    
    $allocated_truck = $db->query('SELECT 
    a.id,
    a.ref_no,
    a.to_id,
    a.system_do_no,
    a.system_shipment_no,
    a.so_date,
    a.rdd,
    a.so_item_no,
    a.so_no,
    a.delivering_plant,
    a.ship_to_code,
    a.sku_code,
    a.material_description,
    a.req_qty,
    a.qty,
    a.total_weight,
    a.total_cbm,
    a.total_pallets,
    a.truck_type,
    a.plate_no,
    a.driver,
    a.helper,
    a.date_created,
    a.allocated_by,
    tb_transfer_order.allocated_qty,
    tb_customer.ship_to_name,
    tb_customer.ship_to_address
    FROM tb_transport_allocation a
    LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
    LEFT JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
    WHERE a.rdd = ? AND a.truck_type <> ?',$rdd,"")->fetch_all();

    

if(!empty($allocated_truck)){
  
    $file_name = "IRIS SALES ORDER ".date('Y-M-d').".csv";
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Type: text/csv;");
  
    $file = fopen('php://output', 'w');
  
    $header = array("DO No", "PCPPI Shipment No.", "SO Item No","SO No","SO Date" ,"RDD","Delivering Plant", "Ship to Code", "Material #", "Material Description", "QTY", "Picked", "AGL DO NO", "Shipment No.", "Shipment Type","Transportation Planning Point", "Plate No." ,"Shipping Type", "Service Agent", "PGI Date of Lotte Sys");
    fputcsv($file, $header);

    foreach ($allocated_truck as $asar_key => $row) {
        $data = array();
        $data[] = "";
        $data[] = "";
        $data[] = $db->escape_string($row["so_item_no"]);
        $data[] = $db->escape_string($row["so_no"]);
        $data[] = date("m/d/Y",strtotime($row['so_date']));
        $data[] = date("m/d/Y",strtotime($row['rdd']));
        $data[] = $db->escape_string($row["delivering_plant"]);
        $data[] = $db->escape_string($row["ship_to_code"]);
        $data[] = $db->escape_string($row["sku_code"]);
        $data[] = $db->escape_string($row["material_description"]);
        $data[] = $db->escape_string($row["req_qty"]);
        $data[] = $db->escape_string($row["allocated_qty"]);
        $data[] = "IRISDO-".$row["system_do_no"];
        $data[] = "IRISSHIPNO-".$row["system_shipment_no"];
        $data[] = "Z001";

        if($row['sku_code'] == '9020000231' || $row['sku_code'] == '9020000233' || $row['sku_code'] == '9020000232'){
          $data[] = "6991";
        }else{
          $data[] = "6990";
        }

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

       
        $data[] = $row['truck_type'];
        $data[] = "6000001480";
        $data[] = date("m/d/Y",strtotime($row['date_created']));
        
        fputcsv($file, $data);
      }
      fclose($file);
      exit;

    }else{
      $_SESSION['msg_heading'] = "Download Failed!";
      $_SESSION['msg'] = "No Available Data for SAP Integration File (F2).";
      $_SESSION['msg_type'] = "warning";
      redirect("view_sap_f2");
    }





?>