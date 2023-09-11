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

    $allocated_truck = $db->query('SELECT * FROM tb_assembly_build')->fetch_all();
    

if(!empty($allocated_truck)){
  
    $file_name = "IRIS Assembly Build ".date('Y-M-d').".csv";
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Type: text/csv;");
  
    $file = fopen('php://output', 'w');
  
    $header = array("AB ID","AB Ref No.","ASN ID","ASN Ref No", "Document No.","Material No.","Qty","BBD","Fulfillment Status","Created By","Last Change");
    fputcsv($file, $header);

    foreach ($allocated_truck as $asar_key => $row) {
        $data = array();
        $data[] = $db->escape_string($row["id"]);
        $data[] = $db->escape_string($row["ab_ref_no"]);
        $data[] = $db->escape_string($row["asn_id"]);
        $data[] = $db->escape_string($row["asn_ref_no"]);
        $data[] = $db->escape_string($row["document_no"]);
        $data[] = $db->escape_string($row["sku_code"]);
        $data[] = $db->escape_string($row["qty_case"]);
        $data[] = $row['expiry'];
        $data[] = $db->escape_string($row["fulfillment_status"]);
        $data[] = $db->escape_string($row["created_by"]);
        $data[] = $row['last_updated'];

      
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