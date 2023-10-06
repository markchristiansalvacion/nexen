<?php
require_once 'includes/load.php';


if(!is_array_has_empty_input($_POST)){

        print_r_html($_POST);

          $lpn = generate_lpn(10);
          $ia_ref = time().''.substr($lpn,5,5);
          $reason = "Auto Generate: Fulfillment Process";
        
          $insert_to_ia = $db->query('INSERT INTO tb_inventory_adjustment (ia_ref, ab_id, lpn, sku_code, qty_case, expiry,reason,bin_loc, created_by,transaction_type) VALUES (?,?,?,?,?,?,?,?,?,?)',$ia_ref, $_POST['db_id'],$lpn,$_POST['sku_code'],$_POST['qty_case'],$_POST['expiration_date'],$reason,"TBD",$_SESSION['name'],"INB");

          if($insert_to_ia -> affected_rows()){
            
              $update_ab = $db->query('UPDATE tb_assembly_build SET fulfillment_status = ? WHERE id =?', "DONE", $_POST['db_id']);
    
              if($update_ab->affected_rows()){
                $_SESSION['msg_heading'] = "Success!";
                $_SESSION['msg'] = "Fulfillment Successfuly Created: Ref No.".$_POST['document_no'];
                $_SESSION['msg_type'] = "success";
                redirect("inbound_fullfillment");
              }else{
                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "error";
                redirect("inbound_fullfillment");
              }
            
          }else{
            $_SESSION['msg_heading'] = "Upload Error!";
            $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("inbound_fullfillment");
          }

  }else{
      // Error
      $_SESSION['msg_heading'] = "Upload Error!";
      $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("inbound_fullfillment");
  }

?>