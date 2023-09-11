<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {
  //print_r_html($_POST);
  if (are_fields_filled($_POST)) {



    $update_db_stock = $db->query('UPDATE tb_inbound set allocated_qty = allocated_qty + ?, dispatch_qty = dispatch_qty - ? WHERE id = ? ', $_POST['qty_pcs'], $_POST['qty_pcs'], $_POST['in_id']);

    if ($update_db_stock->affected_rows()) {

      //   $delete_to_picklist = $db->query('DELETE FROM tb_picklist Where id =?', $_POST['db_id']);
      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Stock with Documnet no : {$_POST['document_no']}, returned successfully";
      $_SESSION['msg_type'] = "success";
      redirect($_POST['url']);
    } else {


      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Update Failed. Make Sure to you've made an Update. If this persist please Contact your System Administrator";
      $_SESSION['msg_type'] = "error";
      redirect($_POST['url']);
    }
  } else {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url']);
  }
}
