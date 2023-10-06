<?php

// $connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");

require_once 'includes/load.php';

if (isset($_POST["export"])) {
  // $item_code = ;
  $file_name = 'ASN Blank Template.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  
  $lines = array (
    array('ETA (yyyy-mm-dd)','DR/Doc. No.','Source', 'Forwarder', 'Truck Type','Driver', 'Plate No.', 'SKU Code', 'Qty (PCS)', 'Remarks'),
    array("2023-10-03","DRSample-001","China", "Arrowgo","20'" ,"Mr. P", "ZXC123", "112233", "10", "Sample Only Can Delete Before Upload"),
  );


  $file = fopen('php://output', 'w');

  foreach($lines as $fields){
    fputcsv($file, $fields);
  }
 
  fclose($file);

}
