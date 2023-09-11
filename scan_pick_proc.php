<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  if (are_fields_filled($_POST)) {

    $db_id = $db->escape_string($_POST['db_id']);
    $in_id = $db->escape_string($_POST['in_id']);
    $out_id = $db->escape_string($_POST['out_id']);
    $doc = $db->escape_string($_POST['doc']);
    $i_code = $db->escape_string($_POST['i_code']);
    $b_code = $db->escape_string($_POST['b_code']);
    $i_name = $db->escape_string($_POST['i_name']);
    $lpn = $db->escape_string($_POST['lpn']);
    $bin_loc = $db->escape_string($_POST['loc']);
    $brcd = $db->escape_string($_POST['brcd_lpn']);
    $exp = $db->escape_string($_POST['exp']);
    $qty = $db->escape_string($_POST['qty_pcs']);

    $mask_item = substr($brcd, 8, 14);

    //print_r_html($mask_item);


    if ($brcd == '') {
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
      $_SESSION['msg_type'] = "error";
      redirect("view_picklist");
    } else {
      if ($brcd == $lpn) {
        $insert_db = $db->query('INSERT into tb_checklist (pick_id, in_id, out_id, document_no, item_code, item_description, batch_no, qty_pcs,expiry, bin_loc, lpn) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $db_id, $in_id, $out_id, $doc, $i_code, $i_name, $b_code, $qty, $exp, $bin_loc, $brcd);
        // print_r_html($update_asn_status->affected_rows());

        if ($insert_db->affected_rows()) {
          $update_status = $db->query('UPDATE tb_outbound SET status=? WHERE id=?', "FOR CHECKING", $_POST['out_id']);
          $_SESSION['msg_heading'] = "Well Done!";
          $_SESSION['msg'] = "Transaction Confirm";
          $_SESSION['msg_type'] = "success";
          redirect("view_picklist");
        } else {
          /**
           * Not all fields are field
           */
          $_SESSION['msg_heading'] = "Transaction Error!";
          $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
          $_SESSION['msg_type'] = "error";
          redirect("view_picklist");
        }
      }
    }


    if ($brcd == '') {
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
      $_SESSION['msg_type'] = "error";
      redirect("view_picklist");
    } else {
      $insert_db = $db->query('INSERT into tb_checklist (pick_id, in_id, out_id, document_no, item_code, item_description, batch_no, qty_pcs,expiry, bin_loc, lpn) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $db_id, $in_id, $out_id, $doc, $i_code, $i_name, $b_code, $qty, $exp, $bin_loc, $lpn);
      // print_r_html($update_asn_status->affected_rows());

      if ($insert_db->affected_rows()) {
        $update_status = $db->query('UPDATE tb_outbound SET status=? WHERE id=?', "FOR CHECKING", $_POST['out_id']);
        $_SESSION['msg_heading'] = "Well Done!";
        $_SESSION['msg'] = "Transaction Confirm";
        $_SESSION['msg_type'] = "success";
        redirect("view_picklist");
      } else {
        /**
         * Not all fields are field
         */
        $_SESSION['msg_heading'] = "Transaction Error!";
        $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
        $_SESSION['msg_type'] = "error";
        redirect("view_picklist");
      }
    }


    if ($brcd == '') {
    } else {
      $insert_db = $db->query('INSERT into tb_checklist (pick_id, in_id, out_id, document_no, item_code, item_description, batch_no, qty_pcs,expiry, bin_loc, lpn) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $db_id, $in_id, $out_id, $doc, $i_code, $i_name, $b_code, $qty, $exp, $bin_loc, $lpn);
      // print_r_html($update_asn_status->affected_rows());

      if ($insert_db->affected_rows()) {
        $update_status = $db->query('UPDATE tb_outbound SET status=? WHERE id=?', "FOR CHECKING", $_POST['out_id']);
        $_SESSION['msg_heading'] = "Well Done!";
        $_SESSION['msg'] = "Transaction Confirm";
        $_SESSION['msg_type'] = "success";
        redirect("view_picklist");
      } else {
        /**
         * Not all fields are field
         */
        $_SESSION['msg_heading'] = "Transaction Error!";
        $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
        $_SESSION['msg_type'] = "error";
        redirect("view_picklist");
      }
    }
  }
}
