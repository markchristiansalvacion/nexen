<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  // print_r_html($_POST);

  $db_id = (empty($_POST['db_id'])) ? NULL : $db->escape_string($_POST['db_id']);
  $asn_ref = (empty($_POST['asn_ref'])) ? NULL : $db->escape_string($_POST['asn_ref']);
  $actual_sku = (empty($_POST['actual_sku'])) ? NULL : $db->escape_string($_POST['actual_sku']);
  $qty_case = (empty($_POST['qty_case'])) ? NULL : $db->escape_string($_POST['qty_case']);
  $expiration_date = (empty($_POST['expiration_date'])) ? NULL : $db->escape_string($_POST['expiration_date']);
  $document_no = (empty($_POST['source_doc'])) ? NULL : $db->escape_string($_POST['source_doc']);

  $update_assembly_build = $db->query('UPDATE tb_assembly_build SET document_no = ?, sku_code = ? , qty_case = ?, expiry = ? WHERE id = ?',$document_no, $actual_sku, $qty_case, $expiration_date,$db_id);

  if ($update_assembly_build->affected_rows()) {
    $_SESSION['msg_heading'] = "Success!";
    $_SESSION['msg'] = "Details Successfully Updated!";
    $_SESSION['msg_type'] = "success";
    redirect("inbound_fullfillment");
  } else {
    $_SESSION['msg_heading'] = "Warning!";
    $_SESSION['msg'] = "Update Failed!";
    $_SESSION['msg_type'] = "warning";
    redirect("inbound_fullfillment");
  }
  
} else {
  $_SESSION['msg_heading'] = "Transaction Failed!";
  $_SESSION['msg'] = "To Update Please Go to Inbound > Fulfillment > Pending Fulfillment Page!";
  $_SESSION['msg_type'] = "error";
  redirect("inbound_fullfillment");
}
