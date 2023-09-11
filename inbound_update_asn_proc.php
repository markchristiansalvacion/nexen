<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {


  //print_r_html($_POST);


  $db_id = $db->escape_string($_POST['db_id']);
  $ref = $db->escape_string($_POST['ref']);

  $checker = $db->escape_string($_POST['docked']);
  $unloading_start = $db->escape_string($_POST['unloading_start']);
  $unloading_end = $db->escape_string($_POST['unloading_end']);





  $update_db = $db->query('UPDATE tb_inbound SET  unloading_start=?,unloading_end=?,checker_name=? WHERE ref_no=?', $unloading_start, $unloading_end, $checker, $ref);


  if ($update_db->affected_rows()) {

    $_SESSION['msg_heading'] = "Transaction Successfully Updated!";
    $_SESSION['msg'] = "This is to confirm that you have successfully update ASN Details in the System!";
    $_SESSION['msg_type'] = "success";
    redirect("upload_asn", false);
  } else {

    $_SESSION['msg_heading'] = "Transaction FAILED Added!";
    $_SESSION['msg'] = "ASN Details failed to update!";
    $_SESSION['msg_type'] = "error";
    redirect("upload_asn", false);
  }
}
