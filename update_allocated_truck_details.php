<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

    //print_r_html($_POST);

  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'inbound admin'){

    // print_r_html($_SESSION);
    // print_r_html($_POST);

    $ref_no = $db->escape_string($_POST['db_ref']);
    $truck_type = (empty($_POST['truck_type'])) ? NULL : $db->escape_string($_POST['truck_type']);
    $plate_no = (empty($_POST['plate_no'])) ? NULL : $db->escape_string($_POST['plate_no']);
    $driver = (empty($_POST['driver'])) ? NULL : $db->escape_string($_POST['driver']);
    $helper = (empty($_POST['helper'])) ? NULL : $db->escape_string($_POST['helper']);

    $update_transport_allocation = $db->query('UPDATE tb_transport_allocation SET truck_type = ? ,plate_no=?, driver=?,helper=? WHERE ref_no =?', $truck_type, $plate_no, $driver, $helper, $ref_no);
    //print_r_html($update_transport_allocation->affected_rows());
    if ($update_transport_allocation->affected_rows()) {
      $_SESSION['msg_heading'] = "Transaction Success!";
      $_SESSION['msg'] = "This is to confirm that you have successfully update the Truck Details!";
      $_SESSION['msg_type'] = "success";
      redirect("view_allocated_trucks");
    } else {
      $_SESSION['msg_heading'] = "Transaction Failed!";
      $_SESSION['msg'] = "Please contact your system administrator!";
      $_SESSION['msg_type'] = "error";
      redirect("view_allocated_trucks");
    }

  }else{

    // Redirect to other page

  }
  
} else {
  $_SESSION['msg_heading'] = "Transaction Failed!";
  $_SESSION['msg'] = "To Update Please Go to Allocated Trucks Page!";
  $_SESSION['msg_type'] = "error";
  redirect("index");
}
