<?php
require_once 'includes/load.php';


if(!is_array_has_empty_input($_POST)){

    /**CHECK FIRST IF SKU IS VALID */
    $aux = $db->escape_string($_POST['actual_sku']);
    $check_sku = $db->query('SELECT sap_code FROM tb_items where sap_code = ?',$aux)->fetch_all();

    if(!empty($check_sku)){

        $ir_ref_no = generate_reference_no($db,26);
        $asn_id = $db->escape_string($_POST['db_id']);
        $asn_ref_no = $db->escape_string($_POST['ref']);
        $sku_code = $db->escape_string($_POST['actual_sku']);
        $qty_case = $_POST['actual_qty'];
        $expiry = $_POST['expiration_date'];
        $document_no = $db->escape_string($_POST['document_no']);
        $created_by = $db->escape_string($_SESSION['name']);
        $reason = $db->escape_string($_POST['reason']);
        $description = $db->escape_string($_POST['description']);

        $insert_to_in_ir = $db->query('INSERT INTO tb_inbound_ir (ir_ref_no, asn_id, asn_ref_no, document_no, sku_code, qty_case, expiry, created_by,reason,description) VALUES (?,?,?,?,?,?,?,?,?,?)',$ir_ref_no, $asn_id, $asn_ref_no, $document_no, $sku_code, $qty_case, $expiry, $created_by,$reason,$description);

        if($insert_to_in_ir -> affected_rows()){
            $_SESSION['msg_heading'] = "Success!";
            $_SESSION['msg'] = "Incident Report Reference No: IR-".$ir_ref_no;
            $_SESSION['msg_type'] = "success";
            redirect("view_asn", true);
        }else{
            $_SESSION['msg_heading'] = "Error!";
            $_SESSION['msg'] = "Incident Report Creation Failed. If this persist, please contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("view_asn");
        }

    }else{
        // Error
        $_SESSION['msg_heading'] = "Error!";
        $_SESSION['msg'] = "Invalid SKU Code. If this persist, please contact your System Administrator.";
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