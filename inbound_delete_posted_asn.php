<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';



if (isset($_POST)) {


  // $id = $_POST['db_id'];
  $ref = $_POST['ref_no'];
  $document = $_POST['document_no'];
  if (are_fields_filled($_POST)) {

    //print_r_html($_GET);

    $db_delete = $db->query('DELETE FROM tb_inbound WHERE asn_ref_no = ? and document_no =?', $ref, $document);
    $db_delete_asn = $db->query('DELETE FROM tb_asn WHERE ref_no = ? and document_no =?', $ref, $document);


    if ($db_delete->affected_rows() && $db_delete_asn->affected_rows()) {

      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "You have successfully POSTED ASN in the System!";
      $_SESSION['msg_type'] = "success";
      redirect("upload_asn");
    } else {

      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Please Contact your System Administrator!";
      $_SESSION['msg_type'] = "error";
      redirect("upload_asn");
    }
  } else {

    /**
     * Not all fields are field
     */
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Please Contact your System Administrator!";
    $_SESSION['msg_type'] = "error";
    redirect("upload_asn");
  }
}
