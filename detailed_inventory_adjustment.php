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

    $detailed_ia = $db->query('SELECT * FROM tb_inventory_adjustment')->fetch_all();
    

if(!empty($detailed_ia)){
  
    $file_name = "IRIS Detailed Inventory Adjustment ".date('Y-M-d').".csv";
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Type: text/csv;");
  
    $file = fopen('php://output', 'w');
  
    $header = array("ID","IA Ref.","Transaction Type","AB ID", "TO ID", "LPN","Material No.","Qty","Allocated","BBD","Suggested Bin","Actual Bin","Reason","Created By","Putaway Status","Last Change");
    fputcsv($file, $header);

    foreach ($detailed_ia as $asar_key => $row) {
        $data = array();
        $data[] = $db->escape_string($row["id"]);
        $data[] = $db->escape_string($row["ia_ref"]);
        $data[] = $db->escape_string($row["transaction_type"]);
        $data[] = $db->escape_string($row["ab_id"]);
        $data[] = $db->escape_string($row["to_id"]);
        $data[] = $db->escape_string($row["lpn"]);
        $data[] = $db->escape_string($row["sku_code"]);
        $data[] = $db->escape_string($row["qty_case"]);
        $data[] = $db->escape_string($row["allocated_qty"]);
        $data[] = $row['expiry'];
        $data[] = $db->escape_string($row["bin_loc"]);
        $data[] = $db->escape_string($row["actual_bin_loc"]);
        $data[] = $db->escape_string($row["reason"]);
        $data[] = $db->escape_string($row["created_by"]);
        $data[] = $db->escape_string($row["putaway_status"]);
        $data[] = $row['last_updated'];
        fputcsv($file, $data);
      }
      fclose($file);
      exit;

    }else{
      $_SESSION['msg_heading'] = "Download Failed!";
      $_SESSION['msg'] = "No Available Inventory Adjustment to Export.";
      $_SESSION['msg_type'] = "warning";
      redirect("index");
    }





?>