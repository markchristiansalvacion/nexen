<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  if (are_fields_filled($_POST)) {



    $update_details = $db->query('UPDATE tb_outbound set driver = ? ,helper=?,plate_no=? WHERE truck_allocation = ? ', $_POST['d_name'], $_POST['h_name'], $_POST['p_no'], $_POST['allocation']);

    if ($update_details->affected_rows()) {
      $insert_to_transport = $db->query('INSERT into tb_transport (source_ref, document_no,created_by) VALUES (?,?,?)', $_POST['ref'], $_POST['doc_no'], $_SESSION['name']);
      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Dispatch details is now updated";
      $_SESSION['msg_type'] = "success";
      redirect($_POST['url']);
    } else {

      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Update Failed. Make Sure to you've made an Update. If this persist please Contact your System Administrator";
      $_SESSION['msg_type'] = "error";
      redirect($_POST['url']);
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url']);
  }
}
