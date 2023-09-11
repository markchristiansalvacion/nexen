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

  $file = fopen('php://output', 'w');

  $header = array("Transaction Type", "Pull Out Request No.", "Date Requested (yyyy-mm-dd)", "Pull Out Date (yyyy-mm-dd)", "ETA (yyyy-mm-dd)", "Source Code",  "Destination Code", "Forwarder Code", "Driver", "Plate No.", "SKU Code", "Qty (Case)", "STO No.", "Remarks");

  fputcsv($file, $header);
}
