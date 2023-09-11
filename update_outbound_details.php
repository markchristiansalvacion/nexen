<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  //print_r_html($_POST);
  $db_id = $db->escape_string($_POST['db_id']);
  $url = $db->escape_string($_POST['url']);
  $p_start = $db->escape_string($_POST['p_start']);
  $p_end = $db->escape_string($_POST['p_end']);
  $c_start = $db->escape_string($_POST['c_start']);
  $c_end = $db->escape_string($_POST['c_end']);
  $v_start = $db->escape_string($_POST['v_start']);
  $v_end = $db->escape_string($_POST['v_end']);
  $picker = $db->escape_string($_POST['picker']);
  $checker = $db->escape_string($_POST['checker']);
  $validator = $db->escape_string($_POST['validator']);


  $update_db = $db->query(
    'UPDATE tb_outbound SET  
                      picking_start = ?,
                      picking_end =?,
                      checking_start=?,
                      checking_end=?,
                      validating_start=?,
                      validating_end=?,
                      picker=?,
                      checker =?,
                      validator=?

                      WHERE id=?',
    $p_start,
    $p_end,
    $c_start,
    $c_end,
    $v_start,
    $v_end,
    $picker,
    $checker,
    $validator,

    $db_id
  );


  if ($update_db->affected_rows()) {

    $_SESSION['msg_heading'] = "Transaction Successfully Updated!";
    $_SESSION['msg'] = "This is to confirm that you have successfully update Dispatch Details in the System!";
    $_SESSION['msg_type'] = "success";
    redirect($_POST['url']);
  } else {

    $_SESSION['msg_heading'] = "Transaction FAILED Added!";
    $_SESSION['msg'] = "Dispatch Details failed to update!";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url']);
  }
}
