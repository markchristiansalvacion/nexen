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

    if(empty($_POST)){
        $_SESSION['msg_heading'] = "Error!";
        $_SESSION['msg'] = "Restricted Operation! If this persists please issue helpdesk ticket immediately.";
        $_SESSION['msg_type'] = "warning";
        redirect("lock_items");
    }else{

        // print_r_html($_POST);

        $sku = $db->escape_string($_POST['actual_sku']);
        $bbd = $_POST['bbd'];

        $db_update = $db->query('UPDATE tb_inventory_adjustment SET lpn_status = ? WHERE sku_code = ? and expiry = ? AND transaction_type = ? AND qty_case - allocated_qty <> 0',"Quarantine",$sku,$bbd,"INB");

          if($db_update->affected_rows()){
            $_SESSION['msg_heading'] = "Success!";
            $_SESSION['msg'] = "Stock Successfully Locked.";
            $_SESSION['msg_type'] = "success";
            redirect("view_locked_items");

          }else{

            /**
             * Activity Logs
             */
            // $created_by = $db->escape_string($_SESSION['name']);
            // $sql_method = "update";
            // $activity_description = "Putaway Confirmation";
            // $transaction_status = "error";

            $_SESSION['msg_heading'] = "Warning!";
            $_SESSION['msg'] = "Some Stock are not Locked!. If this persist, please contact your System Administrator.";
            $_SESSION['msg_type'] = "warning";
            redirect("view_locked_items");
          }
    }


?>