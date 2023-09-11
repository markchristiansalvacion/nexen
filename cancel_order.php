<?php
    require_once 'includes/load.php';

   
    if(!is_array_has_empty_input($_POST)){
        print_r_html($_POST);
        // // $sku_code = $db->escape_string($_GET['sku_code']);
        // // $ship_to_code = $db->escape_string($_GET['customer_code']);
        // // $rdd = $db->escape_string($_GET['rdd']);
        // // // print_r_html($_GET);
        
        $to_id = $db->escape_string($_POST['to_db_id']);
        $url = $_POST['url'];
        
        $cancel_order = $db->query('UPDATE tb_transfer_order SET status = ? WHERE id = ?',"Cancelled",$to_id);

        if($cancel_order->affected_rows()){
            $_SESSION['msg_heading'] = "Success!";
            $_SESSION['msg'] = "Order Cancellation Completed!";
            $_SESSION['msg_type'] = "success";
            redirect($url);
        }

    }else{
        // Error
        $_SESSION['msg_heading'] = "Upload Error!";
        $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
        $_SESSION['msg_type'] = "error";
        redirect($url);
    }
?>