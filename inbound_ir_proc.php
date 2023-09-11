<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {

  //print_r_html($_POST);

  if (empty(trim($_POST['source'])) || empty(trim($_POST['destination'])) || empty(trim($_POST['desc'])) || is_array_has_empty_input($_POST['nature'])  || is_array_has_empty_input($_POST['f_item_id']) || is_array_has_empty_input($_POST['f_qty']) || is_array_has_empty_input($_POST['batch']) || empty($_POST['f_item_id']) || empty($_POST['f_qty'])) {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. No Field should be Left Blank and You Should Select an Item Code";
    $_SESSION['msg_type'] = "error";
    redirect("inbound_incident_report");
  }



  $ref_no = generate_reference_no($db, 10);
  $source_ref = $db->escape_string($_POST['ref_no']);
  $source_doc = $db->escape_string($_POST['doc_no']);
  $created_by = $db->escape_string($_SESSION['name']);
  $source = $db->escape_string($_POST['source']);
  $destination = $db->escape_string($_POST['destination']);
  $desc = $db->escape_string($_POST['desc']);
  $date = date('Y-m-d');


  $arr_nature = $_POST['nature'];
  $arr_item_id = $_POST['f_item_id'];
  $arr_batch = $_POST['batch'];
  $arr_qty = $_POST['f_qty'];
  $arr_remarks = $_POST['f_remarks'];

  $asar_items = array();
  $asar_items['ref'] = $ref_no;
  $asar_items['nature'] =  $arr_nature;
  $asar_items['item_id'] = $arr_item_id;
  $asar_items['batch_code'] =  $arr_batch;
  $asar_items['qty'] = $arr_qty;
  $asar_items['remarks'] = $arr_remarks;



  $asar_items_count = count($asar_items['item_id']);
  $arr_start_index = 0;


  $asar_inbound = array();



  while ($arr_start_index < $asar_items_count) {
    foreach ($asar_items as $asar_items_key => $asar_items_arr_val) {

      $asar_inbound[$arr_start_index][$asar_items_key] = $asar_items_arr_val[$arr_start_index];
    }
    $arr_start_index++;
  }

  $update_err = 0;

  foreach ($asar_inbound as $arr_key => $arr_details) {


    $insert_to_db = $db->query('INSERT INTO tb_incident_report (`ref_no`,`source_ref_no`, `transaction_type`, `source_document`, `ir_date`,`nature_of_ir`,  `item_code`, `batch_code`, `qty`, `source`, `destination`, `description`, `remarks`,`created_by`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $ref_no, $source_ref, 'INBOUND', $source_doc, $date, $arr_details['nature'], $arr_details['item_id'], $arr_details['batch_code'], $arr_details['qty'], $source, $destination, $desc, $arr_details['remarks'], $created_by);

    if ($insert_to_db->affected_rows()) {
      continue;
    } else {
      $update_err++;
      $delete_to_db = $db->query('DELETE FROM tb_incident_report WHERE ref_no = ?', $ref_no);

      if ($delete_to_db->affected_rows()) {
        $_SESSION['message_heading'] = "Transaction Error!";
        $_SESSION['msg'] = "Kindly check your inputs and try again. If this persist, please contact your System Administrator.";
        $_SESSION['msg_type'] = "error";
        redirect("inbound_incident_report");
      }
    }
  }
  if ($update_err == 0) {

    $_SESSION['msg_heading'] = "Transaction Success!";
    $_SESSION['msg'] = "You have successfully added incident report to the system!";
    $_SESSION['msg_type'] = "success";
    redirect("inbound_incident_report");
  }
}
