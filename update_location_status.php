<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';

//print_r_html($_GET);

if (isset($_GET)) {
  $loc_id = $_GET['db_id'];
  $aisle = $_GET['aisle'];
  $curr_status = $_GET['curr_status'];
  $url = "view_location_list?aisle=".$aisle;

  if(are_strings_equal($curr_status,"available")){
    $update_loc_status = $db->query('UPDATE tb_bin_location_bac SET status = ? WHERE id = ?',"Occupied",$loc_id);

    if ($update_loc_status->affected_rows()) {
      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "Bin Status Updated";
      $_SESSION['msg_type'] = "success";
      redirect($url, false);
    } else {

        $_SESSION['msg_heading'] = "Error!";
        $_SESSION['msg'] = "Bin Status Remained the Same. If this persist please Contact your System Administrator";
        $_SESSION['msg_type'] = "danger";
        redirect($url, false);
    }
  }else{

    $update_loc_status = $db->query('UPDATE tb_bin_location_bac SET status = ? WHERE id = ?',"Available",$loc_id);

    if ($update_loc_status->affected_rows()) {
      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "Bin Status Updated";
      $_SESSION['msg_type'] = "success";
      redirect($url, false);
    } else {

        $_SESSION['msg_heading'] = "Error!";
        $_SESSION['msg'] = "Bin Status Remained the Same. If this persist please Contact your System Administrator";
        $_SESSION['msg_type'] = "danger";
        redirect($url, false);
    }

  }
}
