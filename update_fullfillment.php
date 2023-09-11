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
    $doc = $_POST['doc_no'];
    $in_db = $_POST['in_id'];
    $item = $_POST['item_code'];
    $batch = $_POST['batch'];

    $status = $_POST['status'];
    $qty = $_POST['required_qty_pcs'];
    $out_qty = $_POST['qty'];

    $total_out = $qty - $out_qty;
    $created_by = $_SESSION['name'];
    $date = date("Y-m-d H:i:s");
    $ref_no = generate_reference_no($db, 14);




    if ($status == 'Partially Fullfilled') {
      $insert_to_fullfillment_db = $db->query('INSERT INTO tb_fullfillment (ref_no,transaction_type,document_no,item_code,batch_no,qty_pcs,date_time,fullfilled_by) VALUES (?,?,?,?,?,?,?,?)', $ref_no, "Outbound", $doc, $item, $batch, $total_out,  $date, $created_by);




      if ($insert_to_fullfillment_db->affected_rows()) {
        $update_picklist = $db->query('UPDATE tb_picklist SET qty_pcs =  ?, status=? WHERE in_id=?', $total_out, $_POST['status'], $in_db);
        $update_inbound = $db->query('UPDATE tb_inbound SET dispatch_qty = dispatch_qty + ?,allocated_qty = allocated_qty - ? WHERE id=?', $qty, $qty, $in_db);

        $_SESSION['msg_heading'] = "Transaction Successfully Updated!";
        $_SESSION['msg'] = "This is to confirm that you have Pending Fullfillment  in the System!";
        $_SESSION['msg_type'] = "success";
        redirect($_POST['url']);
      } else {

        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Failed to update Transaction in Database. Please Contact your System Administrator!";
        $_SESSION['msg_type'] = "error";
        redirect($_POST['url']);
      }
    }

    if ($status == 'Fullfilled') {

      $insert_to_fullfillment_db = $db->query('INSERT INTO tb_fullfillment (ref_no,transaction_type,document_no,item_code,batch_no,qty_pcs,date_time,fullfilled_by) VALUES (?,?,?,?,?,?,?,?)', $ref_no, "Outbound", $doc, $item, $batch, $qty,  $date, $created_by);


      if ($insert_to_fullfillment_db->affected_rows()) {

        $update_picklist = $db->query('UPDATE tb_picklist SET status=? WHERE in_id=?', $_POST['status'], $in_db);
        $update_inbound = $db->query('UPDATE tb_inbound SET dispatch_qty = dispatch_qty + ?,allocated_qty = allocated_qty - ? WHERE id=?', $qty, $qty, $in_db);

        $_SESSION['msg_heading'] = "Transaction Successfully Added!";
        $_SESSION['msg'] = "This is to confirm that you have successfully Fullfilled  in the System!";
        $_SESSION['msg_type'] = "success";
        redirect($_POST['url']);
      } else {
        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Failed to update Transaction in Database. Please Contact your System Administrator!";
        $_SESSION['msg_type'] = "error";
        redirect($_POST['url']);
      }
    }
  }
}
