<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {
  //print_r_html($_POST);

  if (empty($_POST['status']) || empty($_POST['required_qty_pcs'])) {
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Make Sure to Fill All Fields!";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url']);
  } else {


    $url = $_POST['url'];
    $id = $_POST['in_id'];
    $asn_ref = $_POST['ref_no'];
    $doc = $_POST['doc'];
    $item_code = $_POST['item_code'];
    $batch_code = $_POST['b_code'];
    $required_qty_pcs = $_POST['required_qty_pcs'];
    $stats = $_POST['status'];
    $date = date('Y-m-d');
    $created_by = $_SESSION['name'];
    $date = date("Y-m-d H:i:s");
    $ref_no = generate_reference_no($db, 14);

    if ($stats == 1) {
      /***INSERT FIRST TO FULLFILLMENT TABLE */
      $insert_to_fullfillment_db = $db->query('INSERT INTO tb_fullfillment (ref_no,transaction_type,document_no,item_code,batch_no,qty_pcs,date_time,fullfilled_by) VALUES (?,?,?,?,?,?,?,?)', $ref_no, "Inbound", $doc, $item_code, $batch_code, $required_qty_pcs, $date, $created_by);

      if ($insert_to_fullfillment_db->affected_rows()) {
        /***UPDATE STATUS IF FULLFILL OR NOT IN INBOUND TABLE */
        $update_db_status_1 = $db->query('UPDATE tb_inbound SET qty_pcs=?, status = ? WHERE id =?', $required_qty_pcs, $_POST['status'], $id);


        $_SESSION['msg_heading'] = "Transaction Success!";
        $_SESSION['msg'] = "This is to confirm that you have successfully fullfilled the item in the System!";
        $_SESSION['msg_type'] = "success";
        redirect($_POST['url']);
      } else {
        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Please contact your system administrator!";
        $_SESSION['msg_type'] = "error";
        redirect($_POST['url']);
      }
    }

    if ($stats == 2) {
      /***INSERT FIRST TO FULLFILLMENT TABLE */
      $insert_to_fullfillment_db = $db->query('INSERT INTO tb_fullfillment (ref_no,transaction_type,document_no,item_code,batch_no,qty_pcs,date_time,fullfilled_by) VALUES (?,?,?,?,?,?,?,?)', $ref_no, "Inbound", $doc, $item_code, $batch_code, $required_qty_pcs, $date, $created_by);
      $insert_to_quarantine = $db->query('INSERT into tb_quarantine_items (`in_id`,`source_ref_no`, `document_no`, `item_code`, `batch_code`, `qty`, `created_by`) VALUES (?,?,?,?,?,?,?)', $id, $asn_ref, $doc, $item_code, $batch_code, $required_qty_pcs, $created_by);


      if ($insert_to_fullfillment_db->affected_rows() || $insert_to_quarantine->affected_rows()) {
        /***INSERT  TO QUARANTINE TABLE */
        $update_db_status_3 = $db->query('UPDATE tb_inbound SET qty_pcs=?,status = ? WHERE id =?', $required_qty_pcs, $_POST['status'],  $id);

        $_SESSION['msg_heading'] = "Transaction Success!";
        $_SESSION['msg'] = "This is to confirm that you have successfully fullfilled the item in the System!";
        $_SESSION['msg_type'] = "success";
        redirect($_POST['url']);
      } else {
        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Please contact your system administrator!";
        $_SESSION['msg_type'] = "error";
        redirect($_POST['url']);
      }
    }
    if ($stats == 3) {
      /***INSERT FIRST TO FULLFILLMENT TABLE */
      $insert_to_fullfillment_db = $db->query('INSERT INTO tb_fullfillment (ref_no,transaction_type,document_no,item_code,batch_no,qty_pcs,date_time,fullfilled_by) VALUES (?,?,?,?,?,?,?,?)', $ref_no, "Inbound", $doc, $item_code, $batch_code, $required_qty_pcs, $date, $created_by);
      $insert_to_quarantine = $db->query('INSERT into tb_quarantine_items (`in_id`,`source_ref_no`, `document_no`, `item_code`, `batch_code`, `qty`, `created_by`) VALUES (?,?,?,?,?,?,?)', $id, $asn_ref, $doc, $item_code, $batch_code, $required_qty_pcs, $created_by);


      if ($insert_to_fullfillment_db->affected_rows() || $insert_to_quarantine->affected_rows()) {
        /***INSERT  TO QUARANTINE TABLE */
        $update_db_status_3 = $db->query('UPDATE tb_inbound SET qty_pcs=?,status = ?WHERE id =?', $required_qty_pcs, $_POST['status'],  $id);
        $_SESSION['msg_heading'] = "Transaction Success!";
        $_SESSION['msg'] = "This is to confirm that you have successfully fullfilled the item in the System!";
        $_SESSION['msg_type'] = "success";
        redirect($_POST['url']);
      } else {
        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Please contact your system administrator!";
        $_SESSION['msg_type'] = "error";
        redirect($_POST['url']);
      }
    }
  }
}
