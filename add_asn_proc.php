<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  if (empty(trim($_POST['transaction_type'])) || empty(trim($_POST['document_no'])) ||  empty($_POST['source']) || empty($_POST['destination']) || empty($_POST['eta']) || empty($_POST['truck_type']) || empty($_POST['loading_bay']) || empty($_POST['rec_time'])) {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Error: Failed. All fields are required.";
    $_SESSION['msg_type'] = "error";
    redirect("add_asn");
  } else {

    //print_r_html($_POST);

    $ref_no = generate_reference_no($db, 6); // 6 to check ASN table
    $transaction_type = $db->escape_string($_POST['transaction_type']);
    $document_no = $db->escape_string($_POST['document_no']);
    $source = $db->escape_string($_POST['source']);
    $destination = $db->escape_string($_POST['destination']);
    $eta = $_POST['eta'];
    $truck_type = $db->escape_string($_POST['truck_type']);
    $loading_bay = $db->escape_string($_POST['loading_bay']);
    $rec_time = strtotime($_POST['rec_time']);

    $created_by = $db->escape_string($_SESSION['name']);

    /**
     * CODE PROCESS
     * 1. Inbound to Database: tb_asn
     * 2. If success - go to add_asn
     * 3. If failed - delete all database fields with ref_no and go to add_asn
     */

    $insert_to_asn = $db->query('INSERT into tb_asn (ref_no, time_slot, bay_location, transaction_type, document_no, destination_code, truck_type, eta, vendor_code, created_by) VALUES (?,?,?,?,?,?,?,?,?,?)', $ref_no, $rec_time, $loading_bay, $transaction_type, $document_no, $destination, $truck_type, $eta, $source, $created_by);

    

    if ($insert_to_asn->affected_rows()) {

      $_SESSION['msg_heading'] = "Transaction Successfully Added!";
      $_SESSION['msg'] = "This is to confirm that you successfully added an Advance Shipment Notice in the System!";
      $_SESSION['msg_type'] = "success";
      redirect("add_asn", true);

    } else {

      $delete_to_db = $db->query('DELETE FROM tb_asn where ref_no = ?', $ref_no);

      if ($delete_to_db->affected_rows()) {
        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Failed to Insert Transaction in Database. Please Contact your System Administrator!";
        $_SESSION['msg_type'] = "error";
        redirect("add_asn", true);
      }
      
    }
  }
}
