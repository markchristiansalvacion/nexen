<?php
require_once 'includes/load.php';

$so_no = $db->escape_string($_POST['so_no']);
if(!is_array_has_empty_input($_POST)){

    // print_r_html($_POST);

    /**
     * 1. Update Picklist Table
     * 2. Insert to Inventory Adjustment
     */
    $fulfillment_date = date('Y-m-d');
    $picklist_id = $db->escape_string($_POST['picklist_id']);
    $to_id = $db->escape_string($_POST['to_id']);
    
    $ia_id = $db->escape_string($_POST['ia_id']);
    $picklist_ref = $db->escape_string($_POST['ref']);
    $actual_sku = $db->escape_string($_POST['sku']);
    $actual_qty = $db->escape_string($_POST['qty']*-1);
    $actual_bbd = $db->escape_string($_POST['bbd']);
    $actual_bin_loc = $db->escape_string($_POST['bin_loc']);
    $picked_lpn = $db->escape_string($_POST['lpn']);
    $ia_ref = time().''.substr($picked_lpn,2,7);
    $transaction_type = "OUT";
    $reason = "Auto:Outbound Generate";
    $created_by = $db->escape_string($_SESSION['name']);

    // print_r_html($ia_ref);
    // print_r_html($reason);
    // print_r_html($created_by);
    // print_r_html($actual_qty);

    $update_tb_picklist = $db->query('UPDATE tb_picklist SET fulfillment_status = ? WHERE id = ?',"Fulfilled",$picklist_id); 

    if($update_tb_picklist->affected_rows()){
        /**
         * INSERT TO TB INVENTORY ADJUSTMENT
         */

         $insert_to_ia = $db->query('INSERT INTO tb_inventory_adjustment (ia_ref, transaction_type, ab_id, to_id, lpn, sku_code, qty_case, expiry, bin_loc, reason, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)',$ia_ref, $transaction_type, $ia_id, $to_id, $picked_lpn, $actual_sku, $actual_qty, $actual_bbd,$actual_bin_loc,$reason,$created_by);

         if($insert_to_ia->affected_rows()){

            /**Update Transfer Order */
            $update_transfer_order = $db->query('UPDATE tb_transfer_order SET picked_qty = picked_qty + ? WHERE id = ?',$actual_qty,$to_id);

            if($update_transfer_order->affected_rows()){

                $_SESSION['msg_heading'] = "Transaction Success!";
                $_SESSION['msg'] = "Outbound Fulfillment is Complete!";
                $_SESSION['msg_type'] = "success";
                redirect("validate_picking?so_no={$so_no}");

            }else{
                $_SESSION['msg_heading'] = "Transaction Failed!";
                $_SESSION['msg'] = "Failed to Conduct Fulfillment and Update the Inventory Records. Please try again and if this persist kindly issue a Helpdesk Ticket!";
                $_SESSION['msg_type'] = "warning";
                redirect("validate_picking?so_no={$so_no}");
            }
         }else{
            $_SESSION['msg_heading'] = "Transaction Failed!";
            $_SESSION['msg'] = "Failed to Conduct Fulfillment and Update the Inventory Records. Please try again and if this persist kindly issue a Helpdesk Ticket!";
            $_SESSION['msg_type'] = "warning";
            redirect("validate_picking?so_no={$so_no}");
         }
    }else{

        $_SESSION['msg_heading'] = "Transaction Failed!";
        $_SESSION['msg'] = "Outbound Fulfillment is not Completed. Please try again and if this persist kindly issue a Helpdesk Ticket!";
        $_SESSION['msg_type'] = "warning";
        redirect("validate_picking?so_no={$so_no}");

    }

}else{
    // Error
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("validate_picking?so_no={$so_no}");
}

?>