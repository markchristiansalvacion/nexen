<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  if (empty($_POST['batch']) || empty($_POST['qty_pcs'])  || empty($_POST['ref_no']) || empty($_POST['document_no'])) {
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url']);
  } else {

    $db_id = $db->escape_string($_POST['db_id']);
    $url = $_POST['url'];
    $batch = $db->escape_string($_POST['batch']);
    $qty_pcs = $db->escape_string($_POST['qty_pcs']);


    $expiry = $db->escape_string($_POST['expiry']);
    $ref_no = $db->escape_string($_POST['ref_no']);
    $document_no = $db->escape_string($_POST['document_no']);


    $update_asn_status = $db->query('UPDATE tb_inbound set batch_no = ?, qty_pcs = ?, expiry=? WHERE id =? AND  ref_no = ? AND document_no = ?', $batch, $qty_pcs, $expiry,  $db_id, $ref_no, $document_no);

    if ($update_asn_status->affected_rows()) {

      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "<b>{$_POST['document_no']}-{$_POST['ref_no']}</b> Status is now <b>UPDATED!</b>";
      $_SESSION['msg_type'] = "success";
      redirect($_POST['url'], false);
    } else {
      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
      $_SESSION['msg_type'] = "error";
      redirect($_POST['url'], false);
    }
  }
}
