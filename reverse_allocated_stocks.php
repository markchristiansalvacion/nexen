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
    // print_r_html($_POST);

    if(empty($_POST)){
        $_SESSION['msg_heading'] = "Error!";
        $_SESSION['msg'] = "Restricted Operation! If this persists please issue helpdesk ticket immediately.";
        $_SESSION['msg_type'] = "warning";
        redirect("view_allocated_stocks");
    }else{

        // print_r_html($_POST);

        $error = 0;

        // foreach($_POST['checkbox'] as $asar_key => $asar_val){
        //     print_r_html($asar_val);
        // }

         foreach($_POST['checkbox'] as $cp_asar_key => $cp_asar_val){

            // print_r_html($cp_asar_val);

            $get_allocated = $db->query('SELECT id,ia_id,to_id,allocated_qty FROM tb_picklist where to_id = ?',$cp_asar_val)->fetch_all();

            // print_r_html($get_allocated);

            foreach($get_allocated as $alloc_key => $alloc_val){
              // print_r_html($alloc_val);
              /**
               * DEDUCT ALLOCATED QTY TO INVENTORY ADJUSTMENT AND TRANSFER ORDER
               */

              $sub_to_order = $db->query('UPDATE tb_transfer_order SET allocated_qty = allocated_qty - ? WHERE id = ?',$alloc_val['allocated_qty'], $alloc_val['to_id']);

              if($sub_to_order->affected_rows()){
                
                $sub_to_ia = $db->query('UPDATE tb_inventory_adjustment SET allocated_qty = allocated_qty - ? WHERE id = ?',$alloc_val['allocated_qty'], $alloc_val['ia_id']);

                if($sub_to_ia->affected_rows()){
                  /**Remove to Picklist Table*/
                  $delete_to_picklist = $db->query('DELETE FROM tb_picklist where id = ?',$alloc_val['id']);
                  
                  if($delete_to_picklist->affected_rows()){
                    continue;
                  }else{
                    //     /**
                    //      * Activity Logs For Future Improvement
                    //      */
                    //     // $created_by = $db->escape_string($_SESSION['name']);
                    //     // $sql_method = "update";
                    //     // $activity_description = "Putaway Confirmation";
                    //     // $transaction_status = "success";

                    //     // $error_log = $db->

                    /**Error Kill Script */
                    $_SESSION['msg_heading'] = "Error!";
                    $_SESSION['msg'] = "Reversal Failed! Unable to Remove to Picklist. If this persist, please contact your System Administrator.";
                    $_SESSION['msg_type'] = "error";
                    redirect("view_allocated_stocks");
                  }

                }else{
                   /**Error Kill Script */
                   $_SESSION['msg_heading'] = "Error!";
                   $_SESSION['msg'] = "Reversal Failed! Unable to Deduct Allocated Quantity to Inventory Adjustment. If this persist, please contact your System Administrator.";
                   $_SESSION['msg_type'] = "error";
                   redirect("view_allocated_stocks");
                }

              }else{
                /**Error Kill Script */
                $_SESSION['msg_heading'] = "Error!";
                $_SESSION['msg'] = "Reversal Failed! Unable to Deduct Allocated Quantity to the Order. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "error";
                redirect("view_allocated_stocks");
              }
          
            }
      
         }


         if($error == 0){

          foreach($_POST['checkbox'] as $cp_asar_key => $cp_asar_val){
            $update_to_status = $db->query('UPDATE tb_transfer_order SET status = ? WHERE id = ?',"Allocation",$cp_asar_val);
          }

          $_SESSION['msg_heading'] = "Success!";
          $_SESSION['msg'] = "Stock Reversal Completed!";
          $_SESSION['msg_type'] = "success";
          redirect("view_allocated_stocks");
          
         }

    }

?>