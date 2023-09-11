<?php
require_once 'includes/load.php';


if(!is_array_has_empty_input($_POST)){

      $ia_items = array();
      $item_details = $db->query('SELECT * FROM tb_items where sap_code = ?', $db->escape_string($_POST['sku_code']))->fetch_array();
      $get_total_pallet = $_POST['qty_case'] / $item_details['case_per_pallet'];
      $document_no = $_POST['document_no'];

      if(is_int($get_total_pallet)){
        // print_r_html("All Full Pallet");
        /**GET TOTAL PALLETS */
        $full_pallet = floor($get_total_pallet); 
        $pallet_count = 0;

        while($pallet_count != $full_pallet ){

          $db_bin_loc = $db->query('SELECT id, location_code FROM tb_bin_location_bac WHERE category = ? AND location_type = ? AND STATUS = ? ORDER BY location_code DESC LIMIT 1', $item_details['size'],"Storage","Available")->fetch_array();

          if(!empty($db_bin_loc)){
            $update_bin_loc = $db->query('UPDATE tb_bin_location_bac SET status = ? WHERE id = ? ', "Occupied", $db_bin_loc['id']);

            if($update_bin_loc->affected_rows()){

              $ia_items[$pallet_count]['ab_id'] = $_POST['db_id'];
              $ia_items[$pallet_count]['lpn'] = generate_lpn(15);
              $ia_items[$pallet_count]['sku_code'] = $_POST['sku_code'];
              $ia_items[$pallet_count]['qty_case'] = $item_details['case_per_pallet'];
              $ia_items[$pallet_count]['expiry'] = $_POST['expiration_date'];
              $ia_items[$pallet_count]['reason'] = "Auto:Generate";
              $ia_items[$pallet_count]['bin_loc'] = $db_bin_loc['location_code'];
              $ia_items[$pallet_count]['created_by'] = $db->escape_string($_SESSION['name']);

              $pallet_count++;
            }

          }else{

              $ia_items[$pallet_count]['ab_id'] = $_POST['db_id'];
              $ia_items[$pallet_count]['lpn'] = generate_lpn(15);
              $ia_items[$pallet_count]['sku_code'] = $_POST['sku_code'];
              $ia_items[$pallet_count]['qty_case'] = $item_details['case_per_pallet'];
              $ia_items[$pallet_count]['expiry'] = $_POST['expiration_date'];
              $ia_items[$pallet_count]['reason'] = "Auto:Generate";
              $ia_items[$pallet_count]['bin_loc'] = "TBD";
              $ia_items[$pallet_count]['created_by'] = $db->escape_string($_SESSION['name']);

              $pallet_count++;

          }
          
        }

          /**Upload to Database */

          if(!empty($ia_items)){
            $insert_count = 1;
            $ia_items_count = count($ia_items);
            foreach($ia_items as $arr_key => $insert_val){
             
              $ia_ref = time().''.substr($insert_val['lpn'],5,5).''.$arr_key;
            
              $insert_to_ia = $db->query('INSERT INTO tb_inventory_adjustment (ia_ref, ab_id, lpn, sku_code, qty_case, expiry,reason,bin_loc, created_by,transaction_type) VALUES (?,?,?,?,?,?,?,?,?,?)',$ia_ref, $insert_val['ab_id'],$insert_val['lpn'],$insert_val['sku_code'],$insert_val['qty_case'],$insert_val['expiry'],$insert_val['reason'],$insert_val['bin_loc'],$insert_val['created_by'],"INB");
  
              if($insert_to_ia -> affected_rows()){
                // print_r_html($insert_count);
                // print_r_html($ia_items_count);
                if($insert_count == $ia_items_count){
                  $update_ab = $db->query('UPDATE tb_assembly_build SET fulfillment_status = ? WHERE id =?', "DONE", $_POST['db_id']);
        
                  if($update_ab->affected_rows()){
                    $_SESSION['msg_heading'] = "Success!";
                    $_SESSION['msg'] = "Fulfillment Successfuly Created: Ref No.".$document_no;
                    $_SESSION['msg_type'] = "success";
                    redirect("inbound_fullfillment");
                  }else{
                    $_SESSION['msg_heading'] = "Upload Error!";
                    $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
                    $_SESSION['msg_type'] = "error";
                    redirect("inbound_fullfillment");
                  }
  
                }else{
                  // print_r_html("Increment Insert Count");
                  $insert_count++;
                  continue;
                }
                
              }else{
                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "error";
                redirect("inbound_fullfillment");
              }
            
            }   
  
          }



      }else{
        // print_r_html("Have loose pallet");
        
        $full_pallet = floor($get_total_pallet); 
        $loose_pallet = $get_total_pallet - $full_pallet;
        $pallet_count = 0;
        /** Build First the Full Pallet */
        while($pallet_count != $full_pallet ){

          $db_bin_loc = $db->query('SELECT id, location_code FROM tb_bin_location_bac WHERE category = ? AND location_type = ? AND STATUS = ? ORDER BY location_code DESC LIMIT 1', $item_details['size'],"Storage","Available")->fetch_array();

          // print_r_html($db_bin_loc);
          
          if(!empty($db_bin_loc)){

            $update_bin_loc = $db->query('UPDATE tb_bin_location_bac SET status = ? WHERE id = ? ', "Occupied", $db_bin_loc['id']);

            if($update_bin_loc->affected_rows()){
  
              $ia_items[$pallet_count]['ab_id'] = $_POST['db_id'];
              $ia_items[$pallet_count]['lpn'] = generate_lpn(15);
              $ia_items[$pallet_count]['sku_code'] = $_POST['sku_code'];
              $ia_items[$pallet_count]['qty_case'] = $item_details['case_per_pallet'];
              $ia_items[$pallet_count]['expiry'] = $_POST['expiration_date'];
              $ia_items[$pallet_count]['reason'] = "Auto:Generate";
              $ia_items[$pallet_count]['bin_loc'] = $db_bin_loc['location_code'];
              $ia_items[$pallet_count]['created_by'] = $db->escape_string($_SESSION['name']);
  
              $pallet_count++;
            }

          }else{
              $ia_items[$pallet_count]['ab_id'] = $_POST['db_id'];
              $ia_items[$pallet_count]['lpn'] = generate_lpn(15);
              $ia_items[$pallet_count]['sku_code'] = $_POST['sku_code'];
              $ia_items[$pallet_count]['qty_case'] = $item_details['case_per_pallet'];
              $ia_items[$pallet_count]['expiry'] = $_POST['expiration_date'];
              $ia_items[$pallet_count]['reason'] = "Auto:Generate";
              $ia_items[$pallet_count]['bin_loc'] = "TBD";
              $ia_items[$pallet_count]['created_by'] = $db->escape_string($_SESSION['name']);
  
              $pallet_count++;
          }

         
        }

        /**
         * LAST PALLET OF THE GROUP WHICH IS FOR SURE THE LOOSE PALLET SO WE WILL PUT THIS ON THE FRONT
         */
        if($pallet_count == $full_pallet){
          $loose_pallet_count = $pallet_count + 1;
          // print_r_html($loose_pallet_count);
          $loose_pallet_qty_case = $loose_pallet*$item_details['case_per_pallet'];

          
          $db_bin_loc = $db->query('SELECT id, location_code FROM tb_bin_location_bac WHERE category = ? AND location_type = ? AND status = ? AND deep = ? ORDER BY location_code ASC LIMIT 1', $item_details['size'],"Storage","Available",1)->fetch_array();

          if($db_bin_loc->affected_rows){
            $update_bin_loc = $db->query('UPDATE tb_bin_location_bac SET status = ? WHERE id = ? ', "Occupied", $db_bin_loc['id']);
            if($update_bin_loc->affected_rows()){
              $ia_items[$loose_pallet_count]['ab_id'] = $_POST['db_id'];
              $ia_items[$loose_pallet_count]['lpn'] = generate_lpn(15);
              $ia_items[$loose_pallet_count]['sku_code'] = $_POST['sku_code'];
              $ia_items[$loose_pallet_count]['qty_case'] = $loose_pallet_qty_case;
              $ia_items[$loose_pallet_count]['expiry'] = $_POST['expiration_date'];
              $ia_items[$loose_pallet_count]['reason'] = "Auto:Generate";
              $ia_items[$loose_pallet_count]['bin_loc'] = $db_bin_loc['location_code'];
              $ia_items[$loose_pallet_count]['created_by'] = $db->escape_string($_SESSION['name']);
            }
          }else{
              $ia_items[$loose_pallet_count]['ab_id'] = $_POST['db_id'];
              $ia_items[$loose_pallet_count]['lpn'] = generate_lpn(15);
              $ia_items[$loose_pallet_count]['sku_code'] = $_POST['sku_code'];
              $ia_items[$loose_pallet_count]['qty_case'] = $loose_pallet_qty_case;
              $ia_items[$loose_pallet_count]['expiry'] = $_POST['expiration_date'];
              $ia_items[$loose_pallet_count]['reason'] = "Auto:Generate";
              $ia_items[$loose_pallet_count]['bin_loc'] = "TBD";
              $ia_items[$loose_pallet_count]['created_by'] = $db->escape_string($_SESSION['name']);
          }
          
          

        }

       // print_r_html($ia_items);
        /**Upload to Database */

        if(!empty($ia_items)){
          $insert_count = 1;
          $ia_items_count = count($ia_items);
          foreach($ia_items as $arr_key => $insert_val){
           
            $ia_ref = time().''.substr($insert_val['lpn'],5,5).''.$arr_key;
          
            $insert_to_ia = $db->query('INSERT INTO tb_inventory_adjustment (ia_ref, ab_id, lpn, sku_code, qty_case, expiry,reason,bin_loc, created_by,transaction_type) VALUES (?,?,?,?,?,?,?,?,?,?)',$ia_ref, $insert_val['ab_id'],$insert_val['lpn'],$insert_val['sku_code'],$insert_val['qty_case'],$insert_val['expiry'],$insert_val['reason'],$insert_val['bin_loc'],$insert_val['created_by'],"INB");

            if($insert_to_ia -> affected_rows()){
              // print_r_html($insert_count);
              // print_r_html($ia_items_count);
              if($insert_count == $ia_items_count){
                $update_ab = $db->query('UPDATE tb_assembly_build SET fulfillment_status = ? WHERE id =?', "DONE", $_POST['db_id']);
      
                if($update_ab->affected_rows()){
                  $_SESSION['msg_heading'] = "Success!";
                  $_SESSION['msg'] = "Fulfillment Successfuly Created: Ref No.".$document_no;
                  $_SESSION['msg_type'] = "success";
                  redirect("inbound_fullfillment");
                }else{
                  $_SESSION['msg_heading'] = "Upload Error!";
                  $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
                  $_SESSION['msg_type'] = "error";
                  redirect("inbound_fullfillment");
                }

              }else{
                // print_r_html("Increment Insert Count");
                $insert_count++;
                continue;
              }
              
            }else{
              $_SESSION['msg_heading'] = "Upload Error!";
              $_SESSION['msg'] = "Transaction Failed. Database connection gone!. If this persist, please contact your System Administrator.";
              $_SESSION['msg_type'] = "error";
              redirect("inbound_fullfillment");
            }
          
          }   

        }
        
  

      }

     
      

}else{
    // Error
    $_SESSION['msg_heading'] = "Upload Error!";
    $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("inbound_fullfillment");
}

?>