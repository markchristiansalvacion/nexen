<?php
require_once 'includes/load.php';

/**
 * Check each script if login is authenticated or if session is already expired
 */



// either new or old, it should live at most for another hour

if (is_login_auth()) {

  /** SESSION BASE TO TIME TODAY */

  if (is_session_expired()) {
    $_SESSION['msg'] = "<b>SESSION EXPIRED:</b> Please Login Again.";
    $_SESSION['msg_type'] = "danger";

    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['user_type']);
    unset($_SESSION['user_status']);

    unset($_SESSION['login_time']);

    /**TIME TO DAY + 315360000 THAT EQUIVALENT TO 10 YEARS*/

    redirect("login", false);
  }
} else {
  redirect("login", false);
}

?>


<?php

    unset($_POST['example4_length']);
    //print_r_html($_POST);

    if(empty($_POST)){
        $_SESSION['msg_heading'] = "Error!";
        $_SESSION['msg'] = "Restricted Operation! If this persists please issue helpdesk ticket immediately.";
        $_SESSION['msg_type'] = "warning";
        redirect("view_allocated_trucks");
    }else{

        // print_r_html($_POST);

        $shipment_no = $_POST['ship_no'];

        $get_db_data = $db->query('SELECT id, ref_no, to_id,req_qty FROM tb_transport_allocation WHERE system_shipment_no = ?', $shipment_no)->fetch_all();

        // print_r_html($get_db_data);

        $error = 0;

        if(empty($get_db_data)){
            $_SESSION['msg_heading'] = "Warning!";
            $_SESSION['msg'] = "Shipment Cannot be Found! If this persists please issue helpdesk ticket immediately.";
            $_SESSION['msg_type'] = "warning";
            redirect("view_allocated_trucks");
        }else{

            foreach($get_db_data as $asar_key => $alloc_det){
                // print_r_html($alloc_det);
                /**
                 * UPDATE TRANSFER ORDER TRUCK ALLOCATION STATUS
                 */

                $update_to_status = $db->query('UPDATE tb_transfer_order SET truck_allocation_status = ? WHERE id = ?',"Pending",$alloc_det['to_id']);

                if($update_to_status->affected_rows()){
                    continue;
                }else{
                    $error++;
                }

            }

            if($error!=0){
                $_SESSION['msg_heading'] = "Warning!";
                $_SESSION['msg'] = "Unable to update truck allocation status! If this persists please issue helpdesk ticket immediately.";
                $_SESSION['msg_type'] = "warning";
                redirect("view_allocated_trucks");
            }else{
                /** No Error Then Delete Allocation Records */
                $delete_to_db = $db->query('DELETE FROM tb_transport_allocation WHERE system_shipment_no = ?',$shipment_no);

                if($delete_to_db->affected_rows()){
                    $_SESSION['msg_heading'] = "Success!";
                    $_SESSION['msg'] = "Shipment Reversal Completed!";
                    $_SESSION['msg_type'] = "success";
                    redirect("view_allocated_trucks");
                }else{
                    $_SESSION['msg_heading'] = "Transaction Failed!";
                    $_SESSION['msg'] = "Unable to Reverse Shipment!";
                    $_SESSION['msg_type'] = "error";
                    redirect("view_allocated_trucks");
                }
            }

        }

    }

?>