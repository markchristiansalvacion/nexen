<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  if (empty(trim($_POST['nature']))) {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Failed!";
    $_SESSION['msg'] = "Make sure to fill all fields before confirming the transaction. Kindly re-enter the details.";
    $_SESSION['msg_type'] = "error";
    redirect("inventory_generate_forms", true);
  } else {


    $ref_no = generate_reference_no($db, 13); // 6 to check ASN table
    $nature = trim($db->escape_string($_POST['nature']));

    $date = date('Y-m-d');
    $created_by = $db->escape_string($_SESSION['name']);

    /**
     * CODE PROCESS
     * 1. Inbound to Database: tb_asn
     * 2. If success - go to add_asn.php
     * 3. If failed - delete all database fields with ref_no and go to add_asn.php
     */

    $insert_to_count = $db->query('INSERT INTO `tb_generated_forms`(`ref_no`, `nature`,`created_by`,`date_generated`)
                         VALUES (?,?,?,?)', $ref_no, $nature, $created_by, $date);

    if ($insert_to_count->affected_rows()) {
      $_SESSION['msg_heading'] = "Transaction Successfully Added!";
      $_SESSION['msg'] = "This is to confirm that you successfully generated form with ref #: {$ref_no} in the System!";
      $_SESSION['msg_type'] = "success";
      redirect("inventory_generate_forms", true);
    }
  }
}
