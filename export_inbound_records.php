<?php
$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");

if (isset($_POST["export"])) {
  // $item_code = ;
  $file_name = 'Item Summary.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Item Code", "Batch Code", "LPN", "QTY PCS", "Bin Location");

  fputcsv($file, $header);

  $query = "SELECT item_code,batch_no,lpn,qty_pcs,bin_location FROM tb_inbound";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["item_code"];
    $data[] = $row["batch_no"];
    $data[] = $row["lpn"];
    $data[] = $row["qty_pcs"];
    $data[] = $row["bin_location"];

    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}
