<?php
require_once 'includes/load.php';


if(!is_array_has_empty_input($_POST)){

    // print_r_html($_POST);

    $picklist_id = $db->escape_string($_POST['picklist_id']);
    $to_id = $db->escape_string($_POST['to_id']);
    $picklist_ref = $db->escape_string($_POST['ref']);
    $actual_sku = $db->escape_string($_POST['actual_sku']);
    $actual_qty = $db->escape_string($_POST['actual_qty']);
    $actual_bbd = $db->escape_string($_POST['actual_bbd']);
    $actual_bin_loc = $db->escape_string($_POST['actual_bin_loc']);
    $picked_lpn = $db->escape_string($_POST['picked_lpn']);

    $update_tb_picklist = $db->query('UPDATE tb_picklist SET picked_lpn = ?, picked_sku_code = ?, picked_qty = ?, picked_expiry = ?, picked_loc = ?, status = ? WHERE id = ?',$picked_lpn,$actual_sku,$actual_qty , $actual_bbd, $actual_bin_loc,"Validated",$picklist_id);

    if($update_tb_picklist->affected_rows()){

       
            $_SESSION['msg_heading'] = "Stock Validation Success!";
            $_SESSION['msg'] = "First Step Validation is Complete!";
            $_SESSION['msg_type'] = "success";
            redirect("validate_picking?so_no={$to_id}");
     
        
        
    }else{
        $_SESSION['msg_heading'] = "Validation Failed!";
        $_SESSION['msg'] = "First Step Validation failed. Please try again and if this persist kindly issue a Helpdesk Ticket!";
        $_SESSION['msg_type'] = "warning";
        redirect("validate_picking?so_no={$to_id}");
    }


}else{
    // Error
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("validate_picking?so_no={$_POST['to_id']}");
}

?>