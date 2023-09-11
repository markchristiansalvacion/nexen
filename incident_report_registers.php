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

    $db_ir = $db->query('SELECT
    a.id,
    a.ir_ref_no as ref_no,
    a.asn_id,
    a.asn_ref_no,
    a.document_no,
    a.sku_code,
    a.qty_case,
    a.expiry,
    a.ir_status,
    a.reason,
    a.description,
    tb_asn.forwarder,
    tb_asn.driver,
    tb_asn.plate_no,
    tb_items.material_description
    FROM tb_inbound_ir a
    LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
    LEFT JOIN tb_asn ON tb_asn.id = a.asn_id')->fetch_all();
      

if(!empty($db_ir)){
  
    $file_name = "IRIS Incident Report Registers ".date('Y-M-d').".csv";
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Type: text/csv;");
  
    $file = fopen('php://output', 'w');
  
    $header = array("ID","IR Ref No.","ASN ID","ASN Ref No","Document No.","Mat. #","Mat. Desc.","Case","BBD","Status","Reason","Description","Forwarder","Driver","Plate No");
    fputcsv($file, $header);

    foreach ($db_ir as $asar_key => $row) {
        $data = array();
        $data[] = $db->escape_string($row["id"]);
        $data[] = $db->escape_string($row["ref_no"]);
        $data[] = $db->escape_string($row["asn_id"]);
        $data[] = $db->escape_string($row["asn_ref_no"]);
        $data[] = $db->escape_string($row["document_no"]);
        $data[] = $db->escape_string($row["sku_code"]);
        $data[] = $db->escape_string($row["material_description"]);
        $data[] = $db->escape_string($row["qty_case"]);
        $data[] = $row['expiry'];
        $data[] = $db->escape_string($row["ir_status"]);
        $data[] = $db->escape_string($row["reason"]);
        $data[] = $db->escape_string($row["description"]);
        $data[] = $db->escape_string($row["forwarder"]);
        $data[] = $db->escape_string($row["driver"]);
        $data[] = $db->escape_string($row["plate_no"]);
        fputcsv($file, $data);
      }
      fclose($file);
      exit;

    }else{
      $_SESSION['msg_heading'] = "Download Failed!";
      $_SESSION['msg'] = "No Available Data.";
      $_SESSION['msg_type'] = "warning";
      redirect("index");
    }





?>