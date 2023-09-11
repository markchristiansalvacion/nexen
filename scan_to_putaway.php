<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);



  $id = $db->escape_string($_POST['db_id']);
  $doc = $db->escape_string($_POST['doc']);
  $loc_code = $db->escape_string($_POST['s_loc']);



  if ($loc_code == '') {
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Scan the barcode!";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url']);
  } else {
    $update_db = $db->query('UPDATE tb_inbound SET bin_location =? WHERE id=?', $loc_code, $id);
    // print_r_html($update_asn_status->affected_rows());

    if ($update_db->affected_rows()) {

      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Transaction Confirm. Put-Away Success!";
      $_SESSION['msg_type'] = "success";
      redirect($_POST['url']);
    } else {
      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Scan the barcode!";
      $_SESSION['msg_type'] = "error";
      redirect($_POST['url']);
    }
  }
} else {

  /**
   * Not all fields are field
   */
  $_SESSION['msg_heading'] = "Transaction Error!";
  $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
  $_SESSION['msg_type'] = "error";
  redirect($_POST['url']);
}
