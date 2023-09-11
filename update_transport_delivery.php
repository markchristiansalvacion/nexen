<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  // print_r_html($_POST);
  if (are_fields_filled($_POST)) {



    $update_status = $db->query('UPDATE tb_transport set branch_received_date = ?,received_by=?,ir_ref_no=?,ir_remarks=?,rr_ref_no=?,
    truck_arrival=?,branch_in=?,branch_out=?,fds_comp=?,window_comp=?,in_full=? WHERE id = ?', $_POST['b_date'], $_POST['received_by'], $_POST['ir_ref'], $_POST['ir_remarks'], $_POST['rr_ref'], $_POST['arrive'], $_POST['in'], $_POST['out'], $_POST['fds'], $_POST['window'], $_POST['in_full'], $_POST['db_id']);

    if ($update_status->affected_rows()) {

      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Delivery monitoring is now updated";
      $_SESSION['msg_type'] = "success";
      redirect("transport_delivery_monitoring", false);
    } else {

      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Update Failed. Make Sure to you've made an Update. If this persist please Contact your System Administrator";
      $_SESSION['msg_type'] = "danger";
      redirect("transport_delivery_monitoring", false);
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
    $_SESSION['msg_type'] = "danger";
    redirect("transport_delivery_monitoring", false);
  }
}
