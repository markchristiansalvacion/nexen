<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  $url = $_POST['url'];
  $db_id = $db->escape_string($_POST['db_id']);
  $out_id = $db->escape_string($_POST['out_id']);
  $doc = $db->escape_string($_POST['document_no']);
  $loc = $db->escape_string($_POST['loc']);
  $item_code = $db->escape_string($_POST['item_code']);
  $desc = $db->escape_string($_POST['i_desc']);
  $batch_code = $db->escape_String($_POST['batch_no']);
  $required_qty = $db->escape_string($_POST['required_qty_pcs']);
  $available_qty = $db->escape_string($_POST['qty_pcs']);
  $expiry = $db->escape_string($_POST['exp']);
  $lpn =  $db->escape_string($_POST['lpn']);
  $barcode = $db->escape_string($_POST['brcd_lpn']);
  $date_created = date('Y-m-d');
  $created_by = $_SESSION['name'];


  if ($available_qty <= $required_qty) {
    $allocated_qty = $available_qty;
  } else {
    $allocated_qty = $required_qty;
  }


  /****** LPN */
  if ($barcode == '') {
    $_SESSION['msg_heading'] = "Transaction Failed!";
    $_SESSION['msg'] = "Please Scan the barcode first!";
    $_SESSION['msg_type'] = "error";
    redirect($_POST['url'], false);
  } else {
    if ($barcode == $lpn) {
      $insert_to_db = $db->query(
        'INSERT INTO `tb_picklist`(`in_id`,`out_id`,`document_no`,`item_code`,`item_description`, `batch_no`, `qty_pcs`,`expiry`, `bin_loc`,`date_created`, `created_by`)VALUES (?,?,?,?,?,?,?,?,?,?,?)',
        $db_id,
        $out_id,
        $doc,
        $item_code,
        $desc,
        $batch_code,
        $allocated_qty,
        $expiry,
        $loc,
        $date_created,
        $created_by
      );

      if ($insert_to_db->affected_rows()) {
        $update_outbound = $db->query(
          'UPDATE tb_outbound SET status=?, qty_pcs= qty_pcs - ?,allocation=?  WHERE id = ?',
          'For Checking',
          $allocated_qty,
          'YES',
          $out_id
        );
        if ($update_outbound->affected_rows()) {
          $update_db = $db->query(
            'UPDATE tb_inbound SET qty_pcs = qty_pcs - ?,allocated_qty = allocated_qty + ?  WHERE id= ?',
            $allocated_qty,
            $allocated_qty,
            $db_id
          );

          $_SESSION['msg_heading'] = "Transaction Successfully Added!";
          $_SESSION['msg'] = "This is to confirm that you have successfully created a picklist in the System!";
          $_SESSION['msg_type'] = "success";
          redirect($_POST['url'], false);
        }
      }
    } else {
      $_SESSION['msg_heading'] = "Transaction Failed!";
      $_SESSION['msg'] = "Please Scan the barcode first!";
      $_SESSION['msg_type'] = "error";
      redirect($_POST['url'], false);
    }
  }
}
