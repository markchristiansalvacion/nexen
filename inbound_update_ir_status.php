<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {

  if (are_fields_filled($_POST)) {

    //print_r_html($_POST);

    $update_ir_status = $db->query('UPDATE tb_incident_report set date_closed = ?, closed_by=?, remarks=?, status=?  WHERE ref_no = ? AND source_document = ?', $_POST['receipt_date'], $_POST['closed_by'], $_POST['ir_remarks'], "Closed", $_POST['ref_no'], $_POST['document_no']);
    // print_r_html($update_ir_status->affected_rows());
    if ($update_ir_status->affected_rows()) {



      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "IR Transaction with Ref#-{$_POST['ref_no']} and Source Document:{$_POST['document_no']} is now closed";
      $_SESSION['msg_type'] = "success";
      redirect("inbound_incident_report");
    } else {

      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Update Failed. Make Sure to you've made an Update. If this persist please Contact your System Administrator";
      $_SESSION['msg_type'] = "error";
      redirect("inbound_incident_report");
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
    $_SESSION['msg_type'] = "error";
    redirect("inbound_incident_report");
  }
}
