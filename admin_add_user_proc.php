<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);

  $name = $db->escape_string($_POST['name']);
  $username = $db->escape_string($_POST['user_name']);
  $user_type = $db->escape_string($_POST['user_type']);
  $user_status = $db->escape_string($_POST['user_status']);
  $user_pass = $db->escape_string($_POST['user_pass']);

  $user_pass = password_hash($user_pass, PASSWORD_BCRYPT);


  $sql = "INSERT INTO tb_users (`user_name`, `user_password`, `user_type`, `user_status`, `name`)
          VALUES('" . $username . "','" . $user_pass . "','" . $user_type . "','" . $user_status . "','" . $name . "')";


  if ($db->query($sql)) {
    $_SESSION['msg_heading'] = "Success!";
    $_SESSION['msg'] = "You have successfully added new user(s) to our system!";
    $_SESSION['msg_type'] = "success";
    redirect("admin_add_user");
  } else {
    $_SESSION['msg_heading'] = "Error!";
    $_SESSION['msg'] = "Failed. Please try again.</b>";
    $_SESSION['msg_type'] = "error";
    redirect("admin_add_user");
  }
}
