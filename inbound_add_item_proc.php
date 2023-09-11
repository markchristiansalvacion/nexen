<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  if (!array_key_exists('f_qty', $_POST) || !array_key_exists('batch', $_POST) || !array_key_exists('f_item_id', $_POST)) {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. No Field should be Left Blank and You Should Select an Item Code";
    $_SESSION['msg_type'] = "error";
    redirect("add_asn");
  } else {

    if (empty(trim($_POST['document_no'])) || is_array_has_empty_input($_POST['f_item_id']) || is_array_has_empty_input($_POST['f_qty']) || is_array_has_empty_input($_POST['batch'])) {

      /**
       * Not all fields are field
       */
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "In Order to Confirm the Transaction. No Field should be Left Blank. <b>For fields that do not have details kindly input \"N/A\"";
      $_SESSION['msg_type'] = "error";
      redirect("add_asn");
    } else {
      $ref_no = generate_reference_no($db, 2);
      $source_doc = $db->escape_string($_POST['document_no']);
      $transaction_type = $db->escape_string($_POST['transaction_type']);
      $source = $db->escape_string($_POST['source']);
      $eta = $db->escape_string($_POST['eta']);
      $bay = $db->escape_string($_POST['loading_bay']);
      $dest = $db->escape_string($_POST['destination']);
      $truck = $db->escape_string($_POST['truck_type']);
      $rec_time = $db->escape_string($_POST['rec_time']);

      $transaction_date = date('Y-m-d');

      $date = date('Y-m-d');
      $created_by = $db->escape_string($_SESSION['name']);


      $arr_item_id = $_POST['f_item_id'];
      $arr_batch = $_POST['batch'];
      $arr_qty = $_POST['f_qty'];


      $asar_items = array();
      $asar_items['item_id'] = $arr_item_id;
      $asar_items['batch_code'] = $arr_batch;
      $asar_items['qty'] = $arr_qty;

      /**
       * CHECK IF ALL BATCH ARE CORRECT
       * IF WE HAVE DISCREPANCY THEN DO NOT PUSH PROCESS
       */

      if (is_batch_correct($asar_items['batch_code'])) {

        $main_array_count = count($asar_items);
        $inner_array_count = count($asar_items['item_id']);

        $now = date('Y-m-d');
        $asar_inbound = array(); //Container of All Inbound Items

        foreach ($asar_items as $asar_items_key => $asar_items_arr_val) {
          $inner_array_key = 0;
          while ($inner_array_key < $inner_array_count) {
            if (are_strings_equal($asar_items_key, "batch_code")) {
              if (are_strings_equal($asar_items_arr_val[$inner_array_key], "n/a")) {
                $asar_inbound[$inner_array_key][$asar_items_key] = $asar_items_arr_val[$inner_array_key];
                $asar_inbound[$inner_array_key]['mfg'] = EmptyString;
                $asar_inbound[$inner_array_key]['exp'] = EmptyString;
                $inner_array_key++;
              } else {
                $b_month = C_MONTH[substr($asar_items['batch_code'][$inner_array_key], 2, 1)]['month'];
                $b_day = C_MONTH[substr($asar_items['batch_code'][$inner_array_key], 2, 1)]['day'];
                $b_year = C_YEAR[substr($asar_items['batch_code'][$inner_array_key], 0, 1)];

                $mfg_date = $b_year . "-" . $b_month . "-" . $b_day;

                $asar_inbound[$inner_array_key]['mfg'] = $mfg_date;
                $get_shelf = $db->query('SELECT shelf_life FROM tb_items WHERE item_code=?', $asar_inbound[$inner_array_key]['item_id'])->fetch_array();

                $exp_date = date('Y-m-d', strtotime($mfg_date . '+' . $get_shelf['shelf_life'] . 'months'));
                $asar_inbound[$inner_array_key]['exp'] = $exp_date;
                $asar_inbound[$inner_array_key]['mfg'] = $mfg_date;
                $asar_inbound[$inner_array_key][$asar_items_key] = $asar_items_arr_val[$inner_array_key];

                $inner_array_key++;
              }
            } else {
              $asar_inbound[$inner_array_key][$asar_items_key] = $asar_items_arr_val[$inner_array_key];
              $inner_array_key++;
            }
          }
        }

        /***
         * BUILD PALLETIZATION
         * ALL IN PCS
         */
        $full_pallet = array(); // This would be the final Array to be inserted in the Database
        $pallet_count = 0;
        $r_key = 0;

        foreach ($asar_inbound as $arr_key => $in_asar_val) {

          $get_item_details = $db->query('SELECT id,pack_size,weight_per_box,cbm_per_box,case_per_pallet,case_per_tier,layer_high,shelf_life FROM tb_items WHERE item_code=?', $in_asar_val['item_id'])->fetch_array();

          $required_pcs_per_pallet = $get_item_details['pack_size'] * $get_item_details['case_per_pallet'];

          if (!is_array_has_empty_input($get_item_details)) {

            if ($in_asar_val['qty'] <= $required_pcs_per_pallet) {

              if ($in_asar_val['qty'] < $required_pcs_per_pallet) {
                $pallet_tag = 'P0' . $pallet_count . '-' . $in_asar_val['batch_code'];
                $full_pallet[$r_key] = $in_asar_val;
                $full_pallet[$r_key]['pallet_tag'] = $pallet_tag;
                $r_key++;
                $pallet_count++;
              }

              if ($in_asar_val['qty']  == $required_pcs_per_pallet) {
                $pallet_tag = 'P0' . $pallet_count . '-' . $in_asar_val['batch_code'];
                $full_pallet[$r_key] = $in_asar_val;
                $full_pallet[$r_key]['pallet_tag'] = $pallet_tag;
                $pallet_count++;
                $r_key++;
              }
            } else {

              $aux_qty = $in_asar_val['qty'];

              while ($aux_qty >= $required_pcs_per_pallet) {
                $pallet_tag = 'P0' . $pallet_count . '-' . $in_asar_val['batch_code'];
                $full_pallet[$r_key] = $in_asar_val;
                $full_pallet[$r_key]['pallet_tag'] = $pallet_tag;
                $full_pallet[$r_key]['qty'] = $required_pcs_per_pallet;
                $aux_qty =  $aux_qty - $required_pcs_per_pallet;
                $pallet_count++;
                $r_key++;
              }

              /**
               * EXCESS
               */
              if ($aux_qty == 0) {

                continue;
              } else {
                $pallet_tag = 'P0' . $pallet_count . '-' . $in_asar_val['batch_code'];
                $full_pallet[$r_key] = $in_asar_val;
                $full_pallet[$r_key]['pallet_tag'] = $pallet_tag;
                $full_pallet[$r_key]['qty'] = $aux_qty;
                $aux_qty =  $aux_qty - $required_pcs_per_pallet;
                $pallet_count++;
                $r_key++;
              }
            }
          } else {

            /**
             * There are missing items in the Database
             */

            $_SESSION['msg_heading'] = "Transaction Error!";
            $_SESSION['msg'] = "There are Missing Items in the Database";
            $_SESSION['msg_type'] = "error";
            redirect("add_asn");

            // echobr("There are missing items in the database");

          }
        }


        /**
         *  DETERMINE IF ACCEPT OR NOT
         */

        foreach ($full_pallet as $pallet_key => $pallet_val) {
          $aux_accept = determine_if_accept_batch($pallet_val, $now);
          if ($aux_accept) {
            $full_pallet[$pallet_key]['upload'] = '1';
          } else {
            $full_pallet[$pallet_key]['upload'] = '0';
            $full_pallet[$pallet_key]['initial_remarks'] = "Near Expiry";
          }
        }


        /**
         * INSERT TO DATABASE
         */

        $error_count = 0;
        $affected_rows = 0;
        foreach ($asar_inbound as $arr_key => $arr_details) {

          $lpn = generate_lpn();
          $g_insert = $db->query('INSERT INTO tb_inbound (`ref_no`, `transaction_type`, `document_no`, `vendor_code`, `destination_code`, `ETA`,`truck_type`, `loading_bay`, `time_slot`,`item_code`, `batch_no`, `in_qty`, `qty_pcs`, `expiry`, `lpn`, `bin_location`, `pg_status`,  `system_last_activity`, `created_by`, `date_created`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $ref_no, $transaction_type, $source_doc, $source, $dest, $eta, $truck, $bay, $rec_time, $arr_details['item_id'], $arr_details['batch_code'], $arr_details['qty'], $arr_details['qty'],  $arr_details['exp'],  $lpn, 'Staging', '1', $date, $created_by, $transaction_date);

          //print_r_html($g_insert->affected_rows());
          if ($g_insert->affected_rows()) {

            $affected_rows++;
            continue;
          } else {

            /**
             * DELETE TO DB ALL WITH THE SAME REF AND RETURN ERROR
             */
            $error_count++;
            $delete_to_db = $db->query('DELETE FROM tb_inbound WHERE ref_no = ?', $ref_no);

            if ($db_remove->affected_rows()) {
              $_SESSION['message_heading'] = "Updating Location Error!";
              $_SESSION['msg'] = "Error: Updating Location Status Failed. Kindly check your inputs and try again. If this persist, please contact your System Administrator.";
              $_SESSION['msg_type'] = "error";
              redirect("add_asn");
            }
          }
        }
      }
    }
  }

  if ($error_count == 0) {
    $_SESSION['msg_heading'] = "Transaction Successful!";
    $_SESSION['msg'] = "You have successfully created an Inbound Transaction. Please Generate the Inbound Inspection Form for Further Details. Reference No: " . $ref_no;
    $_SESSION['msg_type'] = "success";
    redirect("add_asn");
  } else {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Error:There are discrepancy in the Encoded Batch. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("add_asn");
  }
}
