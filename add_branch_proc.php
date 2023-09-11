<?php


require_once 'includes/load.php';
require_once 'vendor2/autoload.php';

if (isset($_POST)) {

//print_r_html($_POST);


  $branch_code = remove_junk($_POST['branch_code']);
  $branch_name = remove_junk($_POST['branch_name']);
  $branch_add_name = remove_junk($_POST['branch_add_name']);

  $sql = "INSERT INTO tb_destination (`destination_code`, `destination_name`,   `destination_address`)
      VALUES('$branch_code','$branch_name','$branch_add_name')";



  if ($db->query($sql)) {
    $_SESSION['msg_heading'] = "Success!";
    $_SESSION['msg'] = "You have successfully added new item(s) to our system!";
    $_SESSION['msg_type'] = "success";
    redirect("admin_add_branch");
  } else {
    $_SESSION['msg_heading'] = "Error!";
    $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
    $_SESSION['msg_type'] = "error";
    redirect("admin_add_branch");
  }
} else {
  $_SESSION['msg_heading'] = "Error!";
  $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
  $_SESSION['msg_type'] = "error";
  redirect("admin_add_branch");
}
