<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  if (are_fields_filled($_POST)) {
    $id = $_POST['db_id'];
    $v_code = $db->escape_string($_POST['v_code']);
    $v_name = $db->escape_string($_POST['v_name']);
    $v_add = $db->escape_string($_POST['v_add']);

    $update_to_db = $db->query('UPDATE tb_destination SET destination_code = ?,destination_name=?, destination_address= ?   WHERE id=?', $v_code, $v_name, $v_add, $id);

    if ($update_to_db->affected_rows()) {

      $_SESSION['msg_heading'] = "Transaction Successfully Added!";
      $_SESSION['msg'] = "Branch Detials updated!";
      $_SESSION['msg_type'] = "success";
      redirect("admin_manage_branch");
    } else {

      $_SESSION['msg_heading'] = "Transaction FAILED Added!";
      $_SESSION['msg'] = "Branch Detials failed to update!";
      $_SESSION['msg_type'] = "error";
      redirect("admin_manage_branch");
    }
  }
} else {


  $_SESSION['msg_heading'] = "Transaction Error!";
  $_SESSION['msg'] = "In Order to Confirm the Transaction. Please Fill all Fields.";
  $_SESSION['msg_type'] = "error";
  redirect("admin_manage_items", false);
}
