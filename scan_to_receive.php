<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  if (are_fields_filled($_POST)) {

    $url = $db->escape_string($_POST['url']);
    $id = $db->escape_string($_POST['db_id']);
    $doc = $db->escape_string($_POST['doc']);
    $batch = $db->escape_string($_POST['b_code']);
    $b_code = $db->escape_string($_POST['b_code']);
    $exp = $db->escape_string($_POST['expiry']);
    $ref_no = $db->escape_string($_POST['ref_no']);
    $qty_pcs = $db->escape_string($_POST['qty_pcs']);
    $s_item = $db->escape_string($_POST['s_item']);

    $mask_item = substr($s_item, 8, 14);





    $insert_db = $db->query('INSERT into tb_receive (in_id, ref_no, document_no, item_code, batch_no, qty_pcs, expiry) VALUES (?,?,?,?,?,?,?)', $id, $ref_no, $doc, $mask_item, $batch, $qty_pcs, $exp);
    // print_r_html($update_asn_status->affected_rows());

    if ($insert_db->affected_rows()) {
      $update_db = $db->query('UPDATE tb_inbound SET pg_status =? WHERE id=?', '1', $id);
      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "Transaction Confirm";
      $_SESSION['msg_type'] = "success";
      redirect($url);
    } else {
      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
      $_SESSION['msg_type'] = "error";
      redirect($url);
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
    $_SESSION['msg_type'] = "error";
    redirect($url);
  }
}
