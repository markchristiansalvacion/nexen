<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  //print_r_html($_POST);
  // print_r_html($_FILES);

  if (!empty($_FILES)) {

    $file = $_FILES['file']['name'];
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);


    if ($ext != "csv") {

      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. Kindly select a CSV file. If this persist, please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("inventory_upload_soh");
    } else {


      // Open uploaded CSV file with read-only mode
      $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

      $upload_array = array();
      // Skip the first line
      fgetcsv($csvFile);
      $csv_row = 2;
      // Parse data from CSV file line by line
      while (($line = fgetcsv($csvFile)) !== FALSE) {

        if (is_array_has_empty_input($line)) {
          $_SESSION['msg_heading'] = "Transaction Error!";
          $_SESSION['msg'] = "Upload Transaction Failed. The CSV file has blank cell located at Row No." . $csv_row . ". Please double check your uploading file. If this persist, please contact your System Administrator.";
          $_SESSION['msg_type'] = "error";
          redirect("inventory_upload_soh");
        } else {
          $upload_array[] = $line;
          $csv_row++;
        }
      }
      $error_counter = 0;

      if (!empty($upload_array)) {


        $ref_no =  generate_reference_no($db, 2);
        foreach ($upload_array as $array_key => $arr_val) {

          $transaction_date = time();
          $date = date('Y-m-d');
          $created_by = $_SESSION['name'];

          $asn_ref = $arr_val[0];
          $docu = $arr_val[1];
          $item_code = $db->escape_string($arr_val[2]);
          $batch_code = $arr_val[3];
          $total_received = $arr_val[4];
          $qty_pcs = $arr_val[5];
          $expiry = $arr_val[6];
          $mfg = $arr_val[7];
          $location = $arr_val[8];
          $lpn = generate_lpn();


          $insert_to_db = $db->query('INSERT INTO tb_inbound (ref_no,asn_ref_no,document_no,item_code,batch_no,total_qty_received,qty_pcs,expiry,mfg,lpn,created_by,bin_location,system_transaction_date,date_received) 
                    VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $ref_no, $asn_ref, $docu, $item_code, $batch_code,  $total_received, $qty_pcs,  $expiry, $mfg, $lpn, $created_by, $location, $transaction_date,  $date);

          if ($insert_to_db->affected_rows()) {
            $update_location_status = $db->query('UPDATE tb_bin_location_bac SET status = "OCCUPIED" WHERE location_code = ?',  $location);
            continue;
          } else {

            $delete_to_db = $db->query('DELETE FROM tb_inbound WHERE ref_no = ?', $ref_no);

            if ($delete_to_db->affected_rows()) {

              /**
               * DELETED TO DB
               */

              $_SESSION['msg_heading'] = "Upload Error!";
              $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the file. Recently Uploaded are being deleted in the database. If this persist, please contact your System Administrator.";
              $_SESSION['msg_type'] = "error";
              redirect("inventory_upload_soh");
            } else {

              /**
               * NOT DELETED - CONTACT ADMIN TO MANUAL DELETE
               */

              $_SESSION['msg_heading'] = "Upload Error!";
              $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the deleting the transactions in the database. Please contact your Administrator to resolve the issue.";
              $_SESSION['msg_type'] = "error";
              redirect("inventory_upload_soh");
            }
          }
        }

        $_SESSION['msg_heading'] = "Upload Success!";
        $_SESSION['msg'] = "You have successfully added {$document_no}  to our system!";
        $_SESSION['msg_type'] = "success";
        redirect("inventory_upload_soh");
      } else {

        $_SESSION['msg_heading'] = "Upload Error!";
        $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. File has empty inputs!. If this persist, please contact your System Administrator.";
        $_SESSION['msg_type'] = "error";
        redirect("inventory_upload_soh");
      }
    }
  }
} else {

  $_SESSION['msg_heading'] = "Upload Error!";
  $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. No File Selected!. If this persist, please contact your System Administrator.";
  $_SESSION['msg_type'] = "error";
  redirect("inventory_upload_soh");
}
//}
