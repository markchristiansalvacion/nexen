<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");

if (isset($_POST["export"])) {
  // $item_code = ;
  $file_name = 'Worklist Blank Template.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Document No.", "Branch Code", "Source Code", "Item Code", "Qty Pcs",  "Truck Type", "Ship Date", "ETA", "Trucker", "Coload Ref No.");

  fputcsv($file, $header);
}
