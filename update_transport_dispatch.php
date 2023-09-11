<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  // print_r_html($_POST);
  if (are_fields_filled($_POST)) {



    $update_out_details = $db->query('UPDATE tb_outbound set call_time = ?,arrival_time=?,departed_time=?,actual_dispatch=?, loading_start=?, loading_end =? WHERE destination_code = ? ', $_POST['c_time'], $_POST['arrival'], $_POST['depart'], $_POST['act_dispatch'], $_POST['l_start'], $_POST['l_end'], $_POST['dest']);

    if ($update_out_details->affected_rows()) {

      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Dispatch details is now updated";
      $_SESSION['msg_type'] = "success";
      redirect("transport_dispatch_monitoring");
    } else {

      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Update Failed. Make Sure to you've made an Update. If this persist please Contact your System Administrator";
      $_SESSION['msg_type'] = "error";
      redirect("transport_dispatch_monitoring");
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
    $_SESSION['msg_type'] = "error";
    redirect("transport_dispatch_monitoring");
  }
}
