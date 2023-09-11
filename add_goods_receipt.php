<?php
require_once 'includes/load.php';


if(!is_array_has_empty_input($_POST)){

   // print_r_html($_POST);

    $ab_ref_no = generate_reference_no($db,20);
    $asn_id = $db->escape_string($_POST['db_id']);
    $asn_ref_no = $db->escape_string($_POST['ref']);
    $sku_code = $db->escape_string($_POST['actual_sku']);
    $qty_case = $_POST['actual_qty'];
    $expiry = $_POST['expiration_date'];
    $document_no = $db->escape_string($_POST['document_no']);
    $created_by = $db->escape_string($_SESSION['name']);
    //print_r_html($ab_ref_no);

    $insert_to_ab = $db->query('INSERT INTO tb_assembly_build (ab_ref_no, asn_id, asn_ref_no, document_no, sku_code, qty_case, expiry, created_by) VALUES (?,?,?,?,?,?,?,?)',$ab_ref_no, $asn_id, $asn_ref_no, $document_no, $sku_code, $qty_case, $expiry, $created_by);

    if($insert_to_ab -> affected_rows()){

      $update_asn_table = $db->query('UPDATE tb_asn SET actual_sku = ? ,actual_qty=? WHERE id =?', $sku_code, $qty_case, $asn_id);

      if($update_asn_table->affected_rows()){
        $_SESSION['msg_heading'] = "Success!";
        $_SESSION['msg'] = "Goods Receipt Reference No: AB-".$ab_ref_no;
        $_SESSION['msg_type'] = "success";
        redirect("view_asn", true);
      }else{
        $_SESSION['msg_heading'] = "Upload Error!";
        $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
        $_SESSION['msg_type'] = "error";
        redirect("view_asn");
      }
  
    }else{
      $_SESSION['msg_heading'] = "Upload Error!";
      $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("view_asn");
    }

}else{
    // Error
    $_SESSION['msg_heading'] = "Upload Error!";
    $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("view_asn");
}

?>