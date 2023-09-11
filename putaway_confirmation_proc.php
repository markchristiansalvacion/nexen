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
        redirect("putaway_confirmation");
    }else{

        $confirmed_putaway = array();

        foreach($_POST['checkbox'] as $post_asar_key => $post_asar_val){
            $db_details = $db->query('SELECT id, bin_loc FROM tb_inventory_adjustment WHERE id = ?',$db->escape_string($post_asar_val))->fetch_array();
                
            $confirmed_putaway [] = $db_details;
    
        }

        // print_r_html($confirmed_putaway);

        /**
         * Update Inventory Adjustment
         */

         $error = 0;




         foreach($confirmed_putaway as $cp_asar_key => $cp_asar_val){
            // print_r_html($cp_asar_val['id']);
            // print_r_html($cp_asar_val['bin_loc']);

            $db_update = $db->query('UPDATE tb_inventory_adjustment SET actual_bin_loc = ?, putaway_status = ?  WHERE id = ?',"{$cp_asar_val['bin_loc']}","Done",$cp_asar_val['id']);

            if($db_update->affected_rows()){
                /**
                 * Activity Logs
                 */
                // $created_by = $db->escape_string($_SESSION['name']);
                // $sql_method = "update";
                // $activity_description = "Putaway Confirmation";
                // $transaction_status = "success";

                // $error_log = $db->
                
                continue;
            }else{

                /**
                 * Activity Logs
                 */
                // $created_by = $db->escape_string($_SESSION['name']);
                // $sql_method = "update";
                // $activity_description = "Putaway Confirmation";
                // $transaction_status = "error";

                $_SESSION['msg_heading'] = "Warning!";
                $_SESSION['msg'] = "Some Location are not updated!. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "warning";
                redirect("putaway_confirmation");
            }
         }


         if($error == 0){
            $_SESSION['msg_heading'] = "Success!";
            $_SESSION['msg'] = "Putaway Confirmation Completed.";
            $_SESSION['msg_type'] = "success";
            redirect("putaway_confirmation");
         }

    }

?>