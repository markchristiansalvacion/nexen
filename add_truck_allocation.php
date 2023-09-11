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

    // print_r_html($_POST);

    unset($_POST['example4_length']);

    // print_r_html($_POST);
  if(empty($_POST)){
    $_SESSION['msg_heading'] = "Allocation Failed!";
    $_SESSION['msg'] = "No shipment Selected! If this persists please issue helpdesk ticket immediately.";
    $_SESSION['msg_type'] = "warning";
    redirect("transport_truck_allocation");
  }else{

  
    $alloc_arr = array();

    foreach($_POST as $arr_key => $arr_val){

      /**GET DETAILS FROM DATABASE */

      $get_db = $db->query('SELECT a.id,
      a.to_no,
      a.transaction_type,
      a.uploading_file_name,
      a.delivery_order_no,
      a.pcppi_shipment_no,
      a.so_date,
      a.rdd,
      a.so_item_no,
      a.so_no,
      a.delivering_plant,
      a.ship_to_code,
      a.sku_code,
      a.material_description,
      a.req_qty_case,
      a.allocated_qty,
      a.picked_qty,
      a.allocated_by,
      a.created_by,
      a.`status`,
      a.fill_rate_status,
      a.upload_date,
      tb_items.material_description,
      tb_items.weight_per_case,
      tb_items.cbm_per_case,
      tb_items.case_per_pallet,
      tb_customer.ship_to_name,
      tb_customer.ship_to_address
      FROM tb_transfer_order a
      LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
      LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
      WHERE a.truck_allocation_status = ? AND a.id = ?',"Pending",$arr_val)->fetch_array();

      $alloc_arr [] = $get_db;
      
    }

    //print_r_html($alloc_arr);

    // print_r_html($alloc_arr);

    /**INSERT TO tb_transport_allocation  */
    $created_by = $db->escape_string($_SESSION['name']);
    $date_today = date('Y-m-d');


    $ref_no = generate_reference_no($db,23);
    $system_shipment_no = generate_reference_no($db,25);
    
    $error_counter = 0;
    $curr_do = "";
    $curr_so = "";

    foreach($alloc_arr as $asar_key => $asar_val){
      
      if($curr_so != ""){
        if(are_strings_equal($curr_so, $asar_val['so_no'])){
          
          $curr_so = $asar_val['so_no'];

          $to_id = $db->escape_string($asar_val['id']);
          $so_date = $db->escape_string($asar_val['so_date']);
          $rdd = $db->escape_string($asar_val['rdd']);
          $so_item_no = $db->escape_string($asar_val['so_item_no']);
          $so_no = $db->escape_string($asar_val['so_no']);
          $delivering_plant = $db->escape_string($asar_val['delivering_plant']);
          $ship_to_code = $db->escape_string($asar_val['ship_to_code']);
          $sku_code = $db->escape_string($asar_val['sku_code']);
          $material_description = $db->escape_string($asar_val['material_description']);
          $req_qty = $db->escape_string($asar_val['req_qty_case']);
          $qty = $db->escape_string($asar_val['picked_qty']*-1);
          $total_weight = $qty * $asar_val['weight_per_case'];
          $total_cbm = $qty * $asar_val['cbm_per_case'];
          $total_pallets = $qty/$asar_val['case_per_pallet'];
          
          
          $insert_to_db = $db->query('INSERT INTO tb_transport_allocation (ref_no, to_id, system_do_no, system_shipment_no, so_date, rdd, so_item_no, so_no, delivering_plant, ship_to_code, sku_code, material_description, req_qty, qty, total_weight, total_cbm, total_pallets, allocated_by, date_created ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$ref_no, $to_id, $curr_do,$system_shipment_no,$so_date, $rdd, $so_item_no, $curr_so, $delivering_plant, $ship_to_code, $sku_code, $material_description, $req_qty, $qty, $total_weight, $total_cbm, $total_pallets, $created_by, $date_today);

          if($insert_to_db->affected_rows()){
            continue;
          }else{
            /**ERROR AND EXIT */
            $delete_to_db = $db->query('DELETE FROM tb_transport_allocation WHERE ref_no = ?',$ref_no);

            $_SESSION['msg_heading'] = "Transaction Error!";
            $_SESSION['msg'] = "Truck Allocation Failed, Please Try Again. If this persist, please contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("transport_truck_allocation");

          }

        }else{
          $aux_do = generate_reference_no($db,24);
          $curr_so = $asar_val['so_no'];
          $curr_do = $aux_do;

          $to_id = $db->escape_string($asar_val['id']);
          $so_date = $db->escape_string($asar_val['so_date']);
          $rdd = $db->escape_string($asar_val['rdd']);
          $so_item_no = $db->escape_string($asar_val['so_item_no']);
          $so_no = $db->escape_string($asar_val['so_no']);
          $delivering_plant = $db->escape_string($asar_val['delivering_plant']);
          $ship_to_code = $db->escape_string($asar_val['ship_to_code']);
          $sku_code = $db->escape_string($asar_val['sku_code']);
          $material_description = $db->escape_string($asar_val['material_description']);
          $req_qty = $db->escape_string($asar_val['req_qty_case']);
          $qty = $db->escape_string($asar_val['picked_qty']*-1);
          $total_weight = $qty * $asar_val['weight_per_case'];
          $total_cbm = $qty * $asar_val['cbm_per_case'];
          $total_pallets = $qty/$asar_val['case_per_pallet'];
          
          
          $insert_to_db = $db->query('INSERT INTO tb_transport_allocation (ref_no, to_id, system_do_no, system_shipment_no, so_date, rdd, so_item_no, so_no, delivering_plant, ship_to_code, sku_code, material_description, req_qty, qty, total_weight, total_cbm, total_pallets, allocated_by, date_created ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$ref_no, $to_id, $curr_do,$system_shipment_no,$so_date, $rdd, $so_item_no, $curr_so, $delivering_plant, $ship_to_code, $sku_code, $material_description, $req_qty, $qty, $total_weight, $total_cbm, $total_pallets, $created_by, $date_today);

          if($insert_to_db->affected_rows()){
            continue;
          }else{
            /**ERROR AND EXIT */
            $delete_to_db = $db->query('DELETE FROM tb_transport_allocation WHERE ref_no = ?',$ref_no);

            $_SESSION['msg_heading'] = "Transaction Error!";
            $_SESSION['msg'] = "Truck Allocation Failed, Please Try Again. If this persist, please contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("transport_truck_allocation");

          }
        }

      }else{
        $aux_do = generate_reference_no($db,24);
        $curr_so = $asar_val['so_no'];
        $curr_do = $aux_do;
       
        $to_id = $db->escape_string($asar_val['id']);
        $so_date = $db->escape_string($asar_val['so_date']);
        $rdd = $db->escape_string($asar_val['rdd']);
        $so_item_no = $db->escape_string($asar_val['so_item_no']);
        $so_no = $db->escape_string($asar_val['so_no']);
        $delivering_plant = $db->escape_string($asar_val['delivering_plant']);
        $ship_to_code = $db->escape_string($asar_val['ship_to_code']);
        $sku_code = $db->escape_string($asar_val['sku_code']);
        $material_description = $db->escape_string($asar_val['material_description']);
        $req_qty = $db->escape_string($asar_val['req_qty_case']);
        $qty = $db->escape_string($asar_val['picked_qty']*-1);
        $total_weight = $qty * $asar_val['weight_per_case'];
        $total_cbm = $qty * $asar_val['cbm_per_case'];
        $total_pallets = $qty/$asar_val['case_per_pallet'];
        
        
        $insert_to_db = $db->query('INSERT INTO tb_transport_allocation (ref_no, to_id, system_do_no, system_shipment_no, so_date, rdd, so_item_no, so_no, delivering_plant, ship_to_code, sku_code, material_description, req_qty, qty, total_weight, total_cbm, total_pallets, allocated_by, date_created ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$ref_no, $to_id, $curr_do,$system_shipment_no,$so_date, $rdd, $so_item_no, $curr_so, $delivering_plant, $ship_to_code, $sku_code, $material_description, $req_qty, $qty, $total_weight, $total_cbm, $total_pallets, $created_by, $date_today);

        if($insert_to_db->affected_rows()){
          continue;
        }else{
          /**ERROR AND EXIT */
          $delete_to_db = $db->query('DELETE FROM tb_transport_allocation WHERE ref_no = ?',$ref_no);

          $_SESSION['msg_heading'] = "Transaction Error!";
          $_SESSION['msg'] = "Truck Allocation Failed, Please Try Again. If this persist, please contact your System Administrator.";
          $_SESSION['msg_type'] = "error";
          redirect("transport_truck_allocation");

        }
      }
      


    }

    if($error_counter == 0){
      
      $error_counter_2 = 0;  
      foreach($_POST as $asar_key => $asar_val){

        /** Update status of transfer_order so that it will not be captured on the for allocation table */

        $update_to = $db->query('UPDATE tb_transfer_order SET truck_allocation_status = ? WHERE id = ?',"Done",$asar_val);

        if($update_to->affected_rows()){
          continue;
        }else{
          $_SESSION['msg_heading'] = "Update Failed!";
          $_SESSION['msg'] = "Transaction Update Failed. Please issue helpdesk ticket immediately.";
          $_SESSION['msg_type'] = "warning";
          redirect("transport_truck_allocation");
        }

      }

      if($error_counter_2 == 0){
        $_SESSION['msg_heading'] = "You Got it Right!";
        $_SESSION['msg'] = "Truck Allocation Complete.";
        $_SESSION['msg_type'] = "success";
        redirect("transport_truck_allocation");
      }
    }
  }

  ?>
       