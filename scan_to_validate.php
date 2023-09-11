<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  print_r_html($_POST);


  $db_id = $db->escape_string($_POST['db_id']);
  $pick_id = $db->escape_string($_POST['pick_id']);
  $in_id = $db->escape_string($_POST['in_id']);
  $out_id = $db->escape_string($_POST['out_id']);
  $doc = $db->escape_string($_POST['doc']);
  $i_code = $db->escape_string($_POST['i_code']);
  $b_code = $db->escape_string($_POST['b_code']);
  $i_name = $db->escape_string($_POST['i_name']);
  $lpn = $db->escape_string($_POST['lpn']);
  $bin_loc = $db->escape_string($_POST['loc']);
  $brcd = $db->escape_string($_POST['brcd']);
  $qty = $db->escape_string($_POST['qty_pcs']);
  $exp = $db->escape_string($_POST['exp']);

  $mask_item = substr($brcd, 8, 14);

  // //print_r_html($mask_item);


  if ($brcd == '') {
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
    $_SESSION['msg_type'] = "error";
    redirect("view_validation");
  } else {
    if ($brcd == $lpn) {
      $insert_db = $db->query('INSERT into tb_validated (`check_id`, `pick_id`, `in_id`, `out_id`, `document_no`, `item_code`, `batch_no`, `item_description`, `qty_pcs`, `bin_loc`, `lpn`,`expiry`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', $db_id, $pick_id, $in_id, $out_id, $doc, $i_code, $b_code, $i_name, $qty, $bin_loc, $lpn, $exp);
      // print_r_html($update_asn_status->affected_rows());

      if ($insert_db->affected_rows()) {
        $update_status = $db->query('UPDATE tb_outbound SET status=? WHERE id=?', "For DR Printing", $_POST['out_id']);
        $_SESSION['msg_heading'] = "Well Done!";
        $_SESSION['msg'] = "Transaction Confirm";
        $_SESSION['msg_type'] = "success";
        redirect("view_validation");
      } else {
        /**
         * Not all fields are field
         */
        $_SESSION['msg_heading'] = "Transaction Error!";
        $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
        $_SESSION['msg_type'] = "error";
        redirect("view_validation");
      }
    }
  }
} 



  //   $insert_db = $db->query('INSERT into tb_checklist (pick_id, in_id, out_id, document_no, item_code, item_description, batch_no, qty_pcs,expiry, bin_loc, lpn) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $db_id, $in_id, $out_id, $doc, $i_code, $i_name, $b_code, $qty, $exp, $bin_loc, $lpn);
  //   // print_r_html($update_asn_status->affected_rows());

  //   if ($insert_db->affected_rows()) {
  //     $update_status = $db->query('UPDATE tb_outbound SET status=? WHERE id=?', "FOR CHECKING", $_POST['out_id']);
  //     $_SESSION['msg_heading'] = "Well Done!";
  //     $_SESSION['msg'] = "Transaction Confirm";
  //     $_SESSION['msg_type'] = "success";
  //     redirect("view_checklist");
  //   } else {
  //     /**
  //      * Not all fields are field
  //      */
  //     $_SESSION['msg_heading'] = "Transaction Error!";
  //     $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
  //     $_SESSION['msg_type'] = "error";
  //     redirect("view_checklist");
  //   }




  //   $insert_db = $db->query('INSERT into tb_checklist (pick_id, in_id, out_id, document_no, item_code, item_description, batch_no, qty_pcs,expiry, bin_loc, lpn) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $db_id, $in_id, $out_id, $doc, $i_code, $i_name, $b_code, $qty, $exp, $bin_loc, $lpn);
  //   // print_r_html($update_asn_status->affected_rows());

  //   if ($insert_db->affected_rows()) {
  //     $update_status = $db->query('UPDATE tb_outbound SET status=? WHERE id=?', "FOR CHECKING", $_POST['out_id']);
  //     $_SESSION['msg_heading'] = "Well Done!";
  //     $_SESSION['msg'] = "Transaction Confirm";
  //     $_SESSION['msg_type'] = "success";
  //     redirect("view_picklist");
  //   } else {
  //     /**
  //      * Not all fields are field
  //      */
  //     $_SESSION['msg_heading'] = "Transaction Error!";
  //     $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
  //     $_SESSION['msg_type'] = "error";
  //     redirect("view_picklist");
  //   }
  // }
