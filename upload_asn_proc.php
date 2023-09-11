<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  //print_r_html($_POST);
  // print_r_html($_FILES);

  if (!$_FILES['file']['error'] == 4) {

    $file = $_FILES['file']['name'];
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $max_file_size = 2000000; // in bytes
    $file_size = $_FILES['file']['size'];
    
    if ($ext != "csv") {
      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Upload Transaction Failed. Kindly select a CSV file. If this persist issue a Helpdesk Ticket and please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("upload_asn");
    } else {

      /** Check if File Exceeds 2MB */
      if($file_size > $max_file_size){
         //Throw Error and End Script
         $_SESSION['msg_heading'] = "Transaction Error!";
         $_SESSION['msg'] = "Upload Transaction Failed. File exceeds the maximum allowable size of 2MB. If this persist issue a Helpdesk Ticket and please contact your System Administrator.";
         $_SESSION['msg_type'] = "error";
        redirect("upload_asn");
      }else{
       /** 
        * Proceed with the Validation
        */
        
        $upload_array = array(); // We will store the CSV data here once validation is ok

        $csvFile = fopen($_FILES['file']['tmp_name'], 'r');// Open uploaded CSV file with read-only mode
        fgetcsv($csvFile); // Skiping the first line of the file
        $csv_row = 2;

        /**
         * [0] => transaction_type = string
         * [1] => pull_out_req_no. = string
         * [2] => date_requested = date
         * [3] => pull_out_date = date
         * [4] => eta = date
         * [5] => source_code = string
         * [6] => destination_code = string
         * [7] => forwarder_code = string
         * [8] => truck_type = string (AUV/4W/6W/FWD/10W/20'/40'/Trailer)
         * [9] => driver = string
         * [10] => plate_no = string
         * [11] => sku_code = sku
         * [12] => qty_case = int
         * [13] => sto_no = string
         * [14] => remarks = string
         */

        while(($line = fgetcsv($csvFile)) !== FALSE){ // While there is line in CSV File

          if (is_array_has_empty_input($line)) {
            $_SESSION['msg_heading'] = "Transaction Error!";
            $_SESSION['msg'] = "Upload Transaction Failed. The CSV file has blank cell located at Row No. ".$csv_row." Check your uploading file. If this persist, please issue a Helpdesk Ticket and contact your System Administrator.";
            $_SESSION['msg_type'] = "error";
            redirect("upload_asn");
          } else {
            /**No Empty Lines:
             * Fix the date format to ensure sql will accept it
             */
            
            $line[2] = date('Y-m-d',strtotime($line[2]));
            $line[3] = date('Y-m-d',strtotime($line[3]));
            $line[4] = date('Y-m-d',strtotime($line[4]));

            $upload_array[] = $line;
            $csv_row++;
          }
          
        }

        // print_r_html($upload_array);

        if (!empty($upload_array)) {

          /**BUILD THE ARRAY BEFORE UPLOADING */

          $ref_no =  generate_reference_no($db, 6);
          $created_by = $db->escape_string($_SESSION['name']);
          $date_today = date('Y-m-d');
          $uploading_file_name = $db->escape_string($_FILES['file']['name']);

          foreach ($upload_array as $array_key => $arr_val) {
            $transaction_type = $db->escape_string($arr_val[0]);
            $pull_out_req_no = $db->escape_string($arr_val[1]);
            $date_requested = $db->escape_string($arr_val[2]);
            $pull_out_date = $db->escape_string($arr_val[3]);
            $eta = $db->escape_string($arr_val[4]);
            $source = $db->escape_string($arr_val[5]);
            $destination = $db->escape_string($arr_val[6]);
            $forwarder = $db->escape_string($arr_val[7]);
            $truck_type = $db->escape_string($arr_val[8]);
            $driver = $db->escape_string($arr_val[9]);
            $plate_no = $db->escape_string($arr_val[10]);
            $item_code = $db->escape_string($arr_val[11]);
            $qty = $arr_val[12];
            $sto_no = $db->escape_string($arr_val[13]);
            $remarks = $db->escape_string($arr_val[14]);

            /** DB INSERT */
            $insert_to_db = $db->query('INSERT INTO tb_asn (ref_no,uploading_file_name,transaction_type,pull_out_request_no,date_requested,pull_out_date, eta, source_code, destination_code,forwarder,truck_type,driver, plate_no, sku_code, qty_case, document_no, remarks,created_by,date_created) 
                        VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $ref_no,$uploading_file_name, $transaction_type, $pull_out_req_no,$date_requested, $pull_out_date, $eta, $source, $destination, $forwarder, $truck_type, $driver, $plate_no,  $item_code, $qty, $sto_no, $remarks, $created_by, $date_today);

            if ($insert_to_db->affected_rows()) {

              continue;

            } else {

              $delete_to_db = $db->query('DELETE FROM tb_asn WHERE ref_no = ?', $ref_no);

              if ($delete_to_db->affected_rows()) {

                /**
                 * DELETED TO DB
                 */

                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the file. Recently Uploaded are being deleted in the database. If this persist, please contact your System Administrator.";
                $_SESSION['msg_type'] = "error";
                redirect("upload_asn");
              } else {

                /**
                 * NOT DELETED - CONTACT ADMIN TO MANUAL DELETE
                 */

                $_SESSION['msg_heading'] = "Upload Error!";
                $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the deleting the transactions in the database. Please contact your Administrator to resolve the issue.";
                $_SESSION['msg_type'] = "error";
                redirect("upload_asn");
              }
            }
          }
        }else{
          $_SESSION['msg_heading'] = "Upload Error!";
          $_SESSION['msg'] = "Upload Transaction Failed. There has been data loss during the process. Please contact your Administrator to resolve the issue.";
          $_SESSION['msg_type'] = "error";
          redirect("upload_asn");
        }

        $_SESSION['msg_heading'] = "Upload Success!";
        $_SESSION['msg'] = "You have successfully added {$uploading_file_name}  to our system with transaction reference no. of PRF-{$ref_no}!";
        $_SESSION['msg_type'] = "success";
        redirect("upload_asn");

      }
    }
  } else {
    $_SESSION['msg_heading'] = "Upload Error!";
    $_SESSION['msg'] = "Upload Transaction Failed. No File Selected!. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("upload_asn");
  }
}