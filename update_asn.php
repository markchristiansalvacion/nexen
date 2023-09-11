<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {



  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'inbound admin'){

    // print_r_html($_SESSION);
    // print_r_html($_POST);

    $bay_location = (empty($_POST['bay_location'])) ? NULL : $db->escape_string($_POST['bay_location']);
    $checker = (empty($_POST['checker'])) ? NULL : $db->escape_string($_POST['checker']);
    $ata = (empty($_POST['ata'])) ? NULL : $db->escape_string($_POST['ata']);
    $unloading_start = (empty($_POST['unloading_start'])) ? NULL : $db->escape_string($_POST['unloading_start']);
    $unloading_end = (empty($_POST['unloading_end'])) ? NULL : $db->escape_string($_POST['unloading_end']);
    $time_arrived = (empty($_POST['time_arrived'])) ? NULL : $db->escape_string($_POST['time_arrived']);
    $time_departed = (empty($_POST['time_departed'])) ? NULL : $db->escape_string($_POST['time_departed']);

    $update_asn_details = $db->query('UPDATE tb_asn SET bay_location = ? ,time_arrived=?, unloading_start=?,unloading_end=? ,time_departed =? , ata = ?, checker = ? WHERE id =?', $bay_location, $time_arrived, $unloading_start, $unloading_end, $time_departed,$ata, $checker ,$_POST['db_id']);
    //print_r_html($update_asn_details->affected_rows());
    if ($update_asn_details->affected_rows()) {
      $_SESSION['msg_heading'] = "Transaction Success!";
      $_SESSION['msg'] = "This is to confirm that you have successfully update the ASN Details!";
      $_SESSION['msg_type'] = "success";
      redirect("view_asn");
    } else {
      $_SESSION['msg_heading'] = "Transaction Failed!";
      $_SESSION['msg'] = "Please contact your system administrator!";
      $_SESSION['msg_type'] = "error";
      redirect("view_asn");
    }

  }else{

    // Redirect to other page

  }
  
} else {
  $_SESSION['msg_heading'] = "Transaction Failed!";
  $_SESSION['msg'] = "To Update Please Go to Incoming Shipment Page!";
  $_SESSION['msg_type'] = "error";
  redirect("index");
}
