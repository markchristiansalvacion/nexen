<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {


  //print_r_html($_POST);


  if ($_POST['status'] != '') {

    $update_db_status = $db->query('UPDATE tb_inbound SET status = ? WHERE id =?', $_POST['status'], $_POST['db_id']);
    if ($update_db_status->affected_rows()) {


      $_SESSION['msg_heading'] = "Transaction Success!";
      $_SESSION['msg'] = "This is to confirm that you have successfully fullfilled the item in the System!";
      $_SESSION['msg_type'] = "success";
      redirect("inbound_quarantine_items");
    } else {
      $_SESSION['msg_heading'] = "Transaction Failed!";
      $_SESSION['msg'] = "Please contact your system administrator!";
      $_SESSION['msg_type'] = "error";
      redirect("inbound_quarantine_items");
    }
  } else {
    $_SESSION['msg_heading'] = "Transaction Failed!";
    $_SESSION['msg'] = "Please Select Fulfillment Status!";
    $_SESSION['msg_type'] = "error";
    redirect("inbound_quarantine_items");
  }
}
