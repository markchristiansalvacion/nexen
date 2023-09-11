<?php require_once 'includes/load.php'; ?>
<?php

if (isset($_POST)) {
  if (are_fields_filled($_POST)) {

    $user_name = $db->escape_string($_POST['loginUsername']);
    $user_pass = $db->escape_string($_POST['loginPassword']);

    $db_num_row = $db->query('SELECT * FROM tb_users where user_name = ?', $user_name)->num_rows();
    //print_r_html($db_num_row);

    if ($db_num_row) {


      $db_row = $db->query('SELECT * FROM tb_users where user_name = ?', $user_name)->fetch_array();
      /** Verify Password */
      if (password_verify($user_pass, $db_row['user_password'])) {


        if ($db_row['user_type'] == 'admin' || $db_row['user_type'] == 'inbound' || $db_row['user_type'] == 'outbound' || $db_row['user_type'] == 'inventory' || $db_row['user_type'] == 'viewer' || $db_row['user_type'] == 'transport') {

          $_SESSION['user_id'] = $db_row['user_id'];
          $_SESSION['name'] = $db_row['name'];
          $_SESSION['user_type'] = $db_row['user_type'];
          $_SESSION['user_status'] = $db_row['user_status'];
          $_SESSION['user_password'] = $db_row['user_password'];
          $_SESSION['photo'] = $db_row['photo'];

          $_SESSION['login_time'] = time();



          redirect("index", false);
        }
        if ($db_row['user_type'] == 'inbound checker' || $db_row['user_type'] == 'picker' || $db_row['user_type'] == 'operator' || $db_row['user_type'] == 'outbound checker' || $db_row['user_type'] == 'validator') {
          $_SESSION['user_id'] = $db_row['user_id'];
          $_SESSION['name'] = $db_row['name'];
          $_SESSION['user_type'] = $db_row['user_type'];
          $_SESSION['user_status'] = $db_row['user_status'];
          $_SESSION['user_password'] = $db_row['user_password'];
          $_SESSION['photo'] = $db_row['photo'];

          $_SESSION['login_time'] = time();

  

          redirect("index_user", false);
        }
        if ($db_row['user_type'] == 'main guard') {
          $_SESSION['user_id'] = $db_row['user_id'];
          $_SESSION['name'] = $db_row['name'];
          $_SESSION['user_type'] = $db_row['user_type'];
          $_SESSION['user_status'] = $db_row['user_status'];
          $_SESSION['user_password'] = $db_row['user_password'];
          $_SESSION['photo'] = $db_row['photo'];

          $_SESSION['login_time'] = time();


          redirect("index_main_guard", false);
        }
      } else {
        $_SESSION['msg_heading'] = "Transaction Error!";
        $_SESSION['msg'] = "Wrong Password!";
        $_SESSION['msg_type'] = "error";
        redirect("login", false);
      }
    } else {
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "User doesn't exist!";
      $_SESSION['msg_type'] = "error";
      redirect("login", false);
    }
  } else {
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Please fill up all fields";
    $_SESSION['msg_type'] = "error";

    redirect("login", false);
  }
}
?>