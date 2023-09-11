<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  //print_r_html($_POST);
  //print_r_html($_FILES);

  if ($_FILES['file']['error'] != 4) {

    $file = $_FILES['file']['name'];
    $max_file_size = 2000000; // in bytes
    $file_size = $_FILES['file']['size'];
    
    if (!$_FILES['file']['type'] == 'text/csv') {
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Upload Transaction Failed. Kindly select a CSV file. If this persist issue a Helpdesk Ticket and please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("upload_customer_master");
    } else {

      /** Check if File Exceeds 2MB */
      if($file_size > $max_file_size){
         //Throw Error and End Script
         $_SESSION['msg_heading'] = "Transaction Error!";
         $_SESSION['msg'] = "Upload Transaction Failed. File exceeds the maximum allowable size of 2MB. If this persist issue a Helpdesk Ticket and please contact your System Administrator.";
         $_SESSION['msg_type'] = "error";
        redirect("upload_customer_master");
      }else{
       /** 
        * Proceed with the Validation
        */
        //print_r_html($_FILES);
        $upload_array = array(); // We will store the CSV data here once validation is ok

        $csvFile = fopen($_FILES['file']['tmp_name'], 'r');// Open uploaded CSV file with read-only mode
        fgetcsv($csvFile); // Skiping the first line of the file
        $csv_row = 2;

        
        // [0] => BU
        // [1] => sales_office
        // [2] => ship_to_code
        // [3] => SHIP TO NAME
        // [4] => SHIP TO ADDRESS
        // [5] => fds
        // [6] => required_shelf_life
        // [7] => window_time
        // [8] => pallet_requirement
        // [9] => TRUCK TYPE
        // [10] => ASN
        // [11] => COA
         

        while(($line = fgetcsv($csvFile)) !== FALSE){ // While there is line in CSV File

          if (empty($line[2]) || empty($line[3]) || empty($line[4]) || empty($line[6]) || empty($line[7]) || empty($line[8])) {
            $_SESSION['msg_heading'] = "Transaction Error!";
            $_SESSION['msg'] = "Upload Transaction Failed. The CSV file has blank cell located at Row No. ".$csv_row." Check your uploading file. If this persist, please issue a Helpdesk Ticket and contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("upload_customer_master");
          } else {

            /**No Empty Lines:
             * Fix the date format to ensure sql will accept it
             */
            // $line[4] = date('Y-m-d',strtotime($line[4]));
            // $line[5] = date('Y-m-d',strtotime($line[5]));
    
            $upload_array[] = $line;
            $csv_row++;
          }
          
        }

        // print_r_html($upload_array);

        if (!empty($upload_array)) {

          /**BUILD THE ARRAY BEFORE UPLOADING */
          $date_today = date('Y-m-d');

          foreach ($upload_array as $array_key => $arr_val) {

            $bu = $db->escape_string($arr_val[0]);
            $sales_office = $db->escape_string($arr_val[1]);
            $ship_to_code = $db->escape_string($arr_val[2]);
            $ship_to_name = $db->escape_string($arr_val[3]);
            $ship_to_address = $db->escape_string($arr_val[4]);
            $fds = $db->escape_string($arr_val[5]);
            $req_shelf_life = $db->escape_string($arr_val[6]);
            $window_time = $db->escape_string($arr_val[7]);
            $pallet_requirement = $db->escape_string($arr_val[8]);
            $truck_type = $db->escape_string($arr_val[9]);
            $asn = $db->escape_string($arr_val[10]);
            $coa = $db->escape_string($arr_val[11]);
          

            /** DB INSERT */
            $insert_to_db = $db->query('INSERT INTO tb_customer (bu,sales_office,ship_to_code,ship_to_name,ship_to_address,fds,req_shelf_life,window_time, pallet_requirement, truck_type, asn, coa,date_created) VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?)', $bu,$sales_office, $ship_to_code, $ship_to_name,$ship_to_address,$fds, $req_shelf_life, $window_time, $pallet_requirement, $truck_type, $asn, $coa, $date_today);

            if ($insert_to_db->affected_rows()) {

              continue;

            } else {

              $delete_to_db = $db->query('DELETE FROM tb_customer WHERE date_created = ?', $date_today);

              if ($delete_to_db->affected_rows()) {

                /**
                 * DELETED TO DB
                 */

                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the file. Recently Uploaded are being deleted in the database. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "error";
                redirect("upload_customer_master");
              } else {

                /**
                 * NOT DELETED - CONTACT ADMIN TO MANUAL DELETE
                 */

                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the deleting the transactions in the database. Please contact your Administrator to resolve the issue.";
                $_SESSION['msg_type'] = "error";
                redirect("upload_customer_master");

              }
            }
          }
        }else{
          $_SESSION['msg_heading'] = "Upload Error!";
          $_SESSION['msg'] = "Upload Transaction Failed. There has been data loss during the process. Please contact your Administrator to resolve the issue.";
          $_SESSION['msg_type'] = "error";
          redirect("upload_customer_master");
        }

        $_SESSION['msg_heading'] = "Upload Success!";
        $_SESSION['msg'] = "You have successfully uploaded Customer Masterlist!";
        $_SESSION['msg_type'] = "success";
        redirect("upload_customer_master");

      }
    }
  } else {
    $_SESSION['msg_heading'] = "Upload Error!";
    $_SESSION['msg'] = "Upload Transaction Failed. No File Selected!. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("upload_customer_master");
  }
}