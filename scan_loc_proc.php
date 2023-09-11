<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  if (are_fields_filled($_POST)) {

    $db_id = $db->escape_string($_POST['db_id']);
    $out_id = $db->escape_string($_POST['out_id']);
    $brcd = $db->escape_string($_POST['brcd_loc']);
    $b_loc = $db->escape_string($_POST['loc']);

    if ($brcd != $b_loc) {
      $_SESSION['msg_heading'] = "Scan Failed!";
      $_SESSION['msg'] = "Required LOCATION did not match, you scaned wrong bin";
      $_SESSION['msg_type'] = "error";
      redirect("view_picklist");
    } else {
      $update_loc_status = $db->query('UPDATE tb_picklist set barcode = ?, loc_status = ? WHERE id =?', $brcd, '2', $db_id);
      // print_r_html($update_asn_status->affected_rows());

      if ($update_loc_status->affected_rows()) {
        $update_outbound = $db->query('UPDATE tb_outbound set status = ? WHERE id =?', "For Picklist", $out_id);
        $_SESSION['msg_heading'] = "Well Done!";
        $_SESSION['msg'] = "Transaction Confirm";
        $_SESSION['msg_type'] = "success";
        redirect("view_picklist");
      } else {
        /**
         * Not all fields are field
         */
        $_SESSION['msg_heading'] = "Transaction Error!";
        $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
        $_SESSION['msg_type'] = "error";
        redirect("view_picklist");
      }
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
    $_SESSION['msg_type'] = "error";
    redirect("view_picklist");
  }
}
