<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  if (
    empty(trim($_POST['old_loc'])) || empty(trim($_POST['new_location_id']))
  ) {

    $_SESSION['msg_heading'] = "Transaction Failed!";
    $_SESSION['msg'] = "Please Fill up all fields!";
    $_SESSION['msg_type'] = "error";
    redirect("inventory_update_bin_loc");

  }else {

    // print_r_html($_POST);

    $created_by = $db->escape_string($_SESSION['name']);


    $update_lpn_location = $db->query('UPDATE tb_inventory_adjustment set bin_loc = ?, actual_bin_loc = ? WHERE lpn = ? ', $_POST['new_location_id'],$_POST['new_location_id'], $_POST['lpn']);
   
    /**
     * 1. Process Flow Update Inventory Adjustment
     * 2. Check if OLD Location is Valid
     * 3. Get current status of OLD location
     * 4. Get current status of New Location
     * 5. Insert to tb_transfer_bin_logs
     */

    if($update_lpn_location->affected_rows()){

      /**Check if OLD Location is TBD or Valid Loc */
      if(are_strings_equal($_POST['old_location'],"TBD")){
        
              $get_new_loc_status = $db->query('SELECT status FROM tb_bin_location_bac WHERE location_code = ?',$_POST['new_location_id'])->fetch_array();
              // print_r_html($get_new_loc_status);
              if(are_strings_equal($get_new_loc_status['status'],"Available")){

                $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?', $_POST['new_location_id']);
                if($update_location_status->affected_rows()){

                  /**INSERT TO LOGS */
                  $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                  if($db_insert->affected_rows()){
                    $_SESSION['msg_heading'] = "Well Done!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed!";
                    $_SESSION['msg_type'] = "success";
                    redirect("inventory_update_bin_loc");
                  }else{
                    $_SESSION['msg_heading'] = "Warning!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }

                }else{

                   /**INSERT TO LOGS */
                   $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                   if($db_insert->affected_rows()){
                     /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                    $_SESSION['msg_heading'] = "Update Fai";
                    $_SESSION['msg'] = "Bin to Bin Completed. Possible conflict of location with other LPN!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }else{
                    /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                    $_SESSION['msg_heading'] = "Transction Complete!";
                    $_SESSION['msg'] = "Bin to Bin Completed. Possible conflict of location with other LPN and transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }
                }

              }else{
                /**
                 * No need to UPDATE NEW BIN LOCATION STATUS 
                 * BUT FLAG THAT THERE IS A POSSIBLE CONFLICT OF LOCATIONS (LPN WITH SAME LOCATION)
                */

                $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?', $_POST['new_location_id']);
                if($update_location_status->affected_rows()){

                   /**INSERT TO LOGS */
                  $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                  if($db_insert->affected_rows()){
                    $_SESSION['msg_heading'] = "Well Done!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed!";
                    $_SESSION['msg_type'] = "success";
                    redirect("inventory_update_bin_loc");
                  }else{
                    $_SESSION['msg_heading'] = "Warning!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }

                }else{

                   /**INSERT TO LOGS */
                   $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                   if($db_insert->affected_rows()){
                      /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                      $_SESSION['msg_heading'] = "Update Failed!";
                      $_SESSION['msg'] = "Bin to Bin Failed. Possible conflict of location with other LPN!";
                      $_SESSION['msg_type'] = "warning";
                      redirect("inventory_update_bin_loc");
                   }else{
                      $_SESSION['msg_heading'] = "Warning!";
                      $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                      $_SESSION['msg_type'] = "warning";
                      redirect("inventory_update_bin_loc");
                   }
                }
              }
      }else{
          /**GET OLD LOCATION STATUS */
          $get_old_loc_status = $db->query('SELECT status FROM tb_bin_location_bac WHERE location_code = ?',$_POST['new_location_id'])->fetch_array();
          // print_r_html($get_old_loc_status,"Old Loc:");

          if(are_strings_equal($get_old_loc_status['status'],"Occupied")){

            $update_old_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "AVAILABLE" WHERE location_code = ?', $_POST['old_location']);

            if($update_old_location_status->affected_rows()){ 

              $get_new_loc_status = $db->query('SELECT status FROM tb_bin_location_bac WHERE location_code = ?',$_POST['new_location_id'])->fetch_array();
              // print_r_html($get_new_loc_status);
              if(are_strings_equal($get_new_loc_status['status'],"Available")){

                $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?', $_POST['new_location_id']);
                if($update_location_status->affected_rows()){

                  /**INSERT TO LOGS */
                  $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                  if($db_insert->affected_rows()){
                    $_SESSION['msg_heading'] = "Well Done!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed!";
                    $_SESSION['msg_type'] = "success";
                    redirect("inventory_update_bin_loc");
                  }else{
                    $_SESSION['msg_heading'] = "Warning!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }

                }else{

                   /**INSERT TO LOGS */
                   $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                   if($db_insert->affected_rows()){
                     /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                    $_SESSION['msg_heading'] = "Update Fai";
                    $_SESSION['msg'] = "Bin to Bin Completed. Possible conflict of location with other LPN!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }else{
                    /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                    $_SESSION['msg_heading'] = "Transction Complete!";
                    $_SESSION['msg'] = "Bin to Bin Completed. Possible conflict of location with other LPN and transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }
                }

              }else{

                /**
                 * No need to UPDATE NEW BIN LOCATION STATUS 
                 * BUT FLAG THAT THERE IS A POSSIBLE CONFLICT OF LOCATIONS (LPN WITH SAME LOCATION)
                */

                $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?', $_POST['new_location_id']);
                if($update_location_status->affected_rows()){

                   /**INSERT TO LOGS */
                  $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                  if($db_insert->affected_rows()){
                    $_SESSION['msg_heading'] = "Well Done!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed!";
                    $_SESSION['msg_type'] = "success";
                    redirect("inventory_update_bin_loc");
                  }else{
                    $_SESSION['msg_heading'] = "Warning!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                  }

                }else{

                   /**INSERT TO LOGS */
                   $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                   if($db_insert->affected_rows()){
                      /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                      $_SESSION['msg_heading'] = "Update Failed!";
                      $_SESSION['msg'] = "Bin to Bin Failed. Possible conflict of location with other LPN!";
                      $_SESSION['msg_type'] = "warning";
                      redirect("inventory_update_bin_loc");
                   }else{
                      $_SESSION['msg_heading'] = "Warning!";
                      $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                      $_SESSION['msg_type'] = "warning";
                      redirect("inventory_update_bin_loc");
                   }
                }
              }

            }else{
              /**WARNING FAILED TO UPDATE BIN LOCATION STATUS */

            }

          }else{
            /**No Need to Update the status since it is already Available */
            $get_new_loc_status = $db->query('SELECT status FROM tb_bin_location_bac WHERE location_code = ?',$_POST['new_location_id'])->fetch_array();
            // print_r_html($get_new_loc_status);
            if(are_strings_equal($get_new_loc_status['status'],"Available")){

              $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?', $_POST['new_location_id']);

              if($update_location_status->affected_rows()){

                /**INSERT TO LOGS */
                $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                if($db_insert->affected_rows()){
                  $_SESSION['msg_heading'] = "Well Done!";
                  $_SESSION['msg'] = "System Bin to Bin Successfully Performed!";
                  $_SESSION['msg_type'] = "success";
                  redirect("inventory_update_bin_loc");
                }else{
                  $_SESSION['msg_heading'] = "Warning!";
                  $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                  $_SESSION['msg_type'] = "warning";
                  redirect("inventory_update_bin_loc");
                }

              }else{

                 /**INSERT TO LOGS */
                 $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                 if($db_insert->affected_rows()){
                   /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                  $_SESSION['msg_heading'] = "Update Fai";
                  $_SESSION['msg'] = "Bin to Bin Completed. Possible conflict of location with other LPN!";
                  $_SESSION['msg_type'] = "warning";
                  redirect("inventory_update_bin_loc");
                }else{
                  /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                  $_SESSION['msg_heading'] = "Transction Complete!";
                  $_SESSION['msg'] = "Bin to Bin Completed. Possible conflict of location with other LPN and transaction log not recorded!";
                  $_SESSION['msg_type'] = "warning";
                  redirect("inventory_update_bin_loc");
                }
              }

            }else{
              /**
               * No need to UPDATE NEW BIN LOCATION STATUS 
               * BUT FLAG THAT THERE IS A POSSIBLE CONFLICT OF LOCATIONS (LPN WITH SAME LOCATION)
              */

              $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?', $_POST['new_location_id']);
              if($update_location_status->affected_rows()){

                 /**INSERT TO LOGS */
                $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                if($db_insert->affected_rows()){
                  $_SESSION['msg_heading'] = "Well Done!";
                  $_SESSION['msg'] = "System Bin to Bin Successfully Performed!";
                  $_SESSION['msg_type'] = "success";
                  redirect("inventory_update_bin_loc");
                }else{
                  $_SESSION['msg_heading'] = "Warning!";
                  $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                  $_SESSION['msg_type'] = "warning";
                  redirect("inventory_update_bin_loc");
                }

              }else{

                 /**INSERT TO LOGS */
                 $db_insert = $db->query('INSERT INTO tb_bin_transfer_logs (lpn,old_location,new_location,transaction_type,created_by) VALUES (?,?,?,?,?)', $_POST['lpn'], $_POST['old_location'], $_POST['new_location_id'],"Bin Transfer",$created_by);

                 if($db_insert->affected_rows()){
                    /**FAILED TO UPDATE NEW BIN LOCATION STATUS */
                    $_SESSION['msg_heading'] = "Update Failed!";
                    $_SESSION['msg'] = "Bin to Bin Failed. Possible conflict of location with other LPN!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                 }else{
                    $_SESSION['msg_heading'] = "Warning!";
                    $_SESSION['msg'] = "System Bin to Bin Successfully Performed. However, transaction log not recorded!";
                    $_SESSION['msg_type'] = "warning";
                    redirect("inventory_update_bin_loc");
                 }
              }
            }

          }

      }

    
    }else{
      $_SESSION['msg_heading'] = "Error!";
      $_SESSION['msg'] = "Failed to Update LPN Details. Try again and if this Persist Please Contact your System Administrator";
      $_SESSION['msg_type'] = "error";
      redirect("inventory_update_bin_loc");
    }
  }
}
