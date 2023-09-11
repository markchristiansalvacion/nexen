<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  $id = $_POST['db_id'];
  $username = $_POST['u_name'];
  $u_type = $_POST['user_type'];
  $u_stats = $_POST['user_stats'];
  $f_name = $_POST['f_name'];
  $password = $_POST['u_pass'];

  $user_pass = password_hash($password, PASSWORD_BCRYPT);

  if (empty(trim($_POST['u_name'])) || empty(trim($_POST['user_type'])) || empty(trim($_POST['user_stats']))) {


    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. No Field should be Left Blank and You Should Select an Item Code";
    $_SESSION['msg_type'] = "error";
    redirect("admin_manage_user");
  }

  //print_r_html($_POST);




  $update_user = $db->query('UPDATE tb_users set user_name = ?, user_password=?,user_type=?,user_status=?, name=? WHERE user_id = ?',  $username,  $user_pass, $u_type, $u_stats, $f_name, $id);

  if ($update_user->affected_rows()) {

    $_SESSION['msg_heading'] = "Well Done!";
    $_SESSION['msg'] = "User Detials Updated!";
    $_SESSION['msg_type'] = "success";
    redirect("admin_manage_user", false);
  } else {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Update Failed. Make Sure to you've made an Update. If this persist please Contact your System Administrator";
    $_SESSION['msg_type'] = "error";
    redirect("admin_manage_user", false);
  }
}
