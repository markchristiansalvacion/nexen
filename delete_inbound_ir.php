<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {

  //print_r_html($_POST);

  $id = $_POST['db_id'];

  if (are_fields_filled($_POST)) {



    $db_delete = $db->query('DELETE FROM tb_incident_report WHERE id = ? ', $id);



    if ($db_delete->affected_rows()) {

      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "You have successfully IR in the System!";
      $_SESSION['msg_type'] = "success";
      redirect("inbound_view_incident_report");
    } else {


      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Please Contact your System Administrator!";
      $_SESSION['msg_type'] = "error";
      redirect("inbound_view_incident_report");
    }
  } else {


    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Please Contact your System Administrator!";
    $_SESSION['msg_type'] = "error";
    redirect("inbound_view_incident_report");
  }
}
