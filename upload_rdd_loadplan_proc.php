<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  //print_r_html($_POST);
  //print_r_html($_FILES);

  if (!empty($_FILES)) {

    $file = $_FILES['file']['name'];
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);


    if ($ext != "csv") {

      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. Kindly select a CSV file. If this persist, please contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("outbound_upload_rdc");
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
          $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. The CSV file has blank cell located at Row No. <b>" . $csv_row . ".</b> Please double check your uploading file. If this persist, please contact your System Administrator.";
          $_SESSION['msg_type'] = "error";
          redirect("outbound_upload_rdc");
        } else {

          // Get row data and insert to Array

          $upload_array[] = $line;
          $csv_row++;
        }
      }


      $error_counter = 0;





      if (!empty($upload_array)) {

        /**BUILD THE ARRAY BEFORE UPLOADING */
        // $get = $db->query('SELECT truck_code FROM tb_truck_allocation')->fetch_all();
        // foreach ($get as $get_code => $arr_code) {
        //     $truck = $arr_code['truck_code'];
        // }
        $ref_no =  generate_reference_no($db, 5);

        foreach ($upload_array as $array_key => $arr_val) {


          $date_today = time();
          $created_by = $db->escape_string($_SESSION['name']);
          $transaction_type = "STR";
          $document_name = $file;


          //$ref_no =  generate_reference_no($db, 5);
          $document_no = $db->escape_string($arr_val[0]);
          $destination_code = $db->escape_string($arr_val[1]);
          $source = $arr_val[2];
          $item_code = $db->escape_string($arr_val[3]);
          $qty_pcs = $arr_val[4];
          $truck_type = $arr_val[5];

          $ship_date = $arr_val[6];
          $eta = $arr_val[7];
          $trucker = $arr_val[8];
          $cluster = $arr_val[9];

          /** DB INSERT */
          $insert_to_db = $db->query('INSERT INTO tb_outbound (ref_no,transaction_type,document_name,document_no,destination_code,source_code,item_code,qty_pcs,truck_type,ship_date,eta,trucker,created_by,date,truck_allocation) 
                      VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $ref_no, $transaction_type, $document_name, $document_no, $destination_code, $source, $item_code,  $qty_pcs,  $truck_type,  $ship_date, $eta, $trucker, $created_by, $date_today, $cluster);

          if ($insert_to_db->affected_rows()) {

            continue;
          } else {

            $delete_to_db = $db->query('DELETE FROM tb_outbound WHERE ref_no = ?', $ref_no);

            if ($delete_to_db->affected_rows()) {

              /**
               * DELETED TO DB
               */

              $_SESSION['msg_heading'] = "Upload Error!";
              $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the file. Recently Uploaded are being deleted in the database. If this persist, please contact your System Administrator.";
              $_SESSION['msg_type'] = "error";
              redirect("outbound_upload_rdd");
            } else {

              /**
               * NOT DELETED - CONTACT ADMIN TO MANUAL DELETE
               */

              $_SESSION['msg_heading'] = "Upload Error!";
              $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. There are issues on the deleting the transactions in the database. Please contact your Administrator to resolve the issue.";
              $_SESSION['msg_type'] = "error";
              redirect("outbound_upload_rdd");
            }
          }
        }

        $_SESSION['msg_heading'] = "Upload Success!";
        $_SESSION['msg'] = "You have successfully added {$document_name}  to our system!";
        $_SESSION['msg_type'] = "success";
        redirect("outbound_upload_rdd");
      } else {

        $_SESSION['msg_heading'] = "Upload Error!";
        $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. File has empty inputs!. If this persist, please contact your System Administrator.";
        $_SESSION['msg_type'] = "error";
        redirect("outbound_upload_rdd");
      }
    }
  }
} else {

  $_SESSION['msg_heading'] = "Upload Error!";
  $_SESSION['msg'] = "<b>Error:</b> Upload Transaction Failed. No File Selected!. If this persist, please contact your System Administrator.";
  $_SESSION['msg_type'] = "error";
  redirect("outbound_upload_rdd");
}
//}
