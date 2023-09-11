<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  print_r_html($_POST);
  if (are_fields_filled($_POST)) {

    $url = $db->escape_string($_POST['url']);
    $id = $db->escape_string($_POST['db_id']);
    $lpn = $db->escape_string($_POST['lpn']);
    $trans_type = $db->escape_string($_POST['trans_type']);
    $new_loc = $db->escape_string($_POST['s_new_location']);
    $old_loc = $db->escape_string($_POST['loc']);
    //   $exp = $db->escape_string($_POST['expiry']);
    //   $ref_no = $db->escape_string($_POST['ref_no']);
    //   $qty_pcs = $db->escape_string($_POST['qty_pcs']);
    //   $s_item = $db->escape_string($_POST['s_item']);

    //   $mask_item = substr($s_item, 8, 14);


    $insert_db = $db->query('INSERT into tb_bin_transfer_logs(lpn, old_location, new_location, transaction_type, created_by) VALUES (?,?,?,?,?)', $lpn, $old_loc, $new_loc, $trans_type, $_SESSION['name']);
    //   // print_r_html($update_asn_status->affected_rows());

    if ($insert_db->affected_rows()) {
      $update_db = $db->query('UPDATE tb_inbound SET bin_location =? WHERE id=?', $new_loc, $id);
      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Transaction Confirm Location Updated!";
      $_SESSION['msg_type'] = "success";
      redirect($url);
    } else {
      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
      $_SESSION['msg_type'] = "error";
      redirect($url);
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
    $_SESSION['msg_type'] = "error";
    redirect($url);
  }
}
