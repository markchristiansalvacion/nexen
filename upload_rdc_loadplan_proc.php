<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  //print_r_html($_POST);
  //print_r_html($_FILES);

  if (!$_FILES['file']['error'] == 4) {

    $file = $_FILES['file']['name'];
    $max_file_size = 2000000; // in bytes
    $file_size = $_FILES['file']['size'];
    
    if (!$_FILES['file']['type'] == 'text/csv') {
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Upload Transaction Failed. Kindly select a CSV file. If this persist issue a Helpdesk Ticket and please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("outbound_upload_rdc");
    } else {

      /** Check if File Exceeds 2MB */
      if($file_size > $max_file_size){
         //Throw Error and End Script
         $_SESSION['msg_heading'] = "Transaction Error!";
         $_SESSION['msg'] = "Upload Transaction Failed. File exceeds the maximum allowable size of 2MB. If this persist issue a Helpdesk Ticket and please contact your System Administrator.";
         $_SESSION['msg_type'] = "error";
        redirect("outbound_upload_rdc");
      }else{
       /** 
        * Proceed with the Validation
        */
        //print_r_html($_FILES);
        $upload_array = array(); // We will store the CSV data here once validation is ok

        $csvFile = fopen($_FILES['file']['tmp_name'], 'r');// Open uploaded CSV file with read-only mode
        fgetcsv($csvFile); // Skiping the first line of the file
        $csv_row = 2;

        
        // [0] => Delivery #
        // [1] => Shipment No. PCPPI
        // [2] => SO_Item_No
        // [3] => SO#
        // [4] => SO Date
        // [5] => Req Del Date
        // [6] => Delivering Plant
        // [7] => Ship-to Code
        // [8] => Material #
        // [9] => Mat. Desc
        // [10] => Qty
        // [11] => Picked
        // [12] => LGLPI_DO_NO
        // [13] => Shipment #
        // [14] => Shipment Type
        // [15] => Transportation Planning Point
        // [16] => Plate #
        // [17] => Shipping Type
        // [18] => Service Agent
        // [19] => PGI Date of Lotte Sys
         

        while(($line = fgetcsv($csvFile)) !== FALSE){ // While there is line in CSV File

          if (empty($line[2]) || empty($line[3]) || empty($line[4]) || empty($line[5]) || empty($line[6]) || empty($line[7]) || empty($line[8]) || empty($line[9]) || empty($line[10])) {
            $_SESSION['msg_heading'] = "Transaction Error!";
            $_SESSION['msg'] = "Upload Transaction Failed. The CSV file has blank cell located at Row No. ".$csv_row." Check your uploading file. If this persist, please issue a Helpdesk Ticket and contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("outbound_upload_rdc");
          } else {

            /**No Empty Lines:
             * Fix the date format to ensure sql will accept it
             */
            $line[4] = date('Y-m-d',strtotime($line[4]));
            $line[5] = date('Y-m-d',strtotime($line[5]));
    
            $upload_array[] = $line;
            $csv_row++;
          }
          
        }

        // print_r_html($upload_array);

        if (!empty($upload_array)) {

          /**BUILD THE ARRAY BEFORE UPLOADING */

          $ref_no =  generate_reference_no($db, 21);
          $created_by = $db->escape_string($_SESSION['name']);
          $date_today = date('Y-m-d');
          $uploading_file_name = $db->escape_string($_FILES['file']['name']);

          foreach ($upload_array as $array_key => $arr_val) {

            $transaction_type = "transfer_order";
            $so_item_no = $db->escape_string($arr_val[2]);
            $so_no = $db->escape_string($arr_val[3]);
            $so_date = $db->escape_string($arr_val[4]);
            $rdd = $db->escape_string($arr_val[5]);
            $delivering_plant = $db->escape_string($arr_val[6]);
            $ship_to_code = $db->escape_string($arr_val[7]);
            $sku_code = $db->escape_string($arr_val[8]);
            $material_description = $db->escape_string($arr_val[9]);
            $req_qty_case = $db->escape_string($arr_val[10]);
          

            /** DB INSERT */
            $insert_to_db = $db->query('INSERT INTO tb_transfer_order (to_no,uploading_file_name,so_item_no,so_no,so_date,rdd,delivering_plant,ship_to_code, sku_code, material_description, req_qty_case, created_by,upload_date, transaction_type) VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $ref_no,$uploading_file_name, $so_item_no, $so_no,$so_date,$rdd, $delivering_plant, $ship_to_code, $sku_code, $material_description, $req_qty_case, $created_by, $date_today,$transaction_type);

            if ($insert_to_db->affected_rows()) {

              continue;

            } else {

              $delete_to_db = $db->query('DELETE FROM tb_transfer_order WHERE to_no = ?', $ref_no);

              if ($delete_to_db->affected_rows()) {

                /**
                 * DELETED TO DB
                 */

                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the file. Recently Uploaded are being deleted in the database. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "error";
                redirect("outbound_upload_rdc");
              } else {

                /**
                 * NOT DELETED - CONTACT ADMIN TO MANUAL DELETE
                 */

                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the deleting the transactions in the database. Please contact your Administrator to resolve the issue.";
                $_SESSION['msg_type'] = "error";
                redirect("outbound_upload_rdc");

              }
            }
          }
        }else{
          $_SESSION['msg_heading'] = "Upload Error!";
          $_SESSION['msg'] = "Upload Transaction Failed. There has been data loss during the process. Please contact your Administrator to resolve the issue.";
          $_SESSION['msg_type'] = "error";
          redirect("outbound_upload_rdc");
        }

        $_SESSION['msg_heading'] = "Upload Success!";
        $_SESSION['msg'] = "You have successfully added {$uploading_file_name}  to our system with transaction reference no. of PRF-{$ref_no}!";
        $_SESSION['msg_type'] = "success";
        redirect("outbound_upload_rdc");

      }
    }
  } else {
    $_SESSION['msg_heading'] = "Upload Error!";
    $_SESSION['msg'] = "Upload Transaction Failed. No File Selected!. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("outbound_upload_rdc");
  }
}