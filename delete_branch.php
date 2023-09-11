<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {

  $id = $_POST['db_id'];

  if (are_fields_filled($_POST)) {
    //print_r_html($_POST);
    $db_delete = $db->query('DELETE FROM tb_destination WHERE id = ?', $id);

    if ($db_delete->affected_rows()) {

      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "Destinatiopn Details Deleted!";
      $_SESSION['msg_type'] = "success";
      redirect("admin_manage_branch", false);
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Please Contact your System Administrator!</b>";
    $_SESSION['msg_type'] = "error";
    redirect("admin_manage_branch", false);
  }
}
