<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
   if (are_fields_filled($_POST)) {

     $db_id = $db->escape_string($_POST['db_id']);
     $brcd = $db->escape_string($_POST['brcd']);
     $b_loc = $db->escape_string($_POST['s_loc']);

    
       $update_loc_status = $db->query('UPDATE tb_replenishment set source_loc = ?, scan_stats=? WHERE id =?', $brcd,'Yes',  $db_id);
       // print_r_html($update_asn_status->affected_rows());

       if ($update_loc_status->affected_rows()) {
         $_SESSION['msg_heading'] = "Well Done!";
         $_SESSION['msg'] = "Transaction Confirm";
         $_SESSION['msg_type'] = "success";
         redirect("replenishment");
       } else {
         /**
          * Not all fields are field
          */
         $_SESSION['msg_heading'] = "Transaction Error!";
         $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
         $_SESSION['msg_type'] = "error";
         redirect("replenishment");
       }
     }else {
         /**
          * Not all fields are field
          */
         $_SESSION['msg_heading'] = "Transaction Error!";
         $_SESSION['msg'] = "In Order to Confirm the Transaction.<b> Make Sure to Fill All Fields!</b>";
         $_SESSION['msg_type'] = "error";
         redirect("replenishment");
       }
   } 

