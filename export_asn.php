<?php
$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");

if (isset($_POST)) {
  // $item_code = ;
  $file_name = 'Advance Shipment Notice.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Transaction Type", "Document No", "Vendor", "Destination", "Time Slot", "Item Code", "Batch Code", "QTY", "Expiration");

  fputcsv($file, $header);

  $query = "SELECT transaction_type, document_no, vendor_code, destination_code,time_slot,item_code,batch_no,qty_pcs,expiry FROM tb_inbound where document_no= '" . $_POST['doc'] . "' ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["transaction_type"];
    $data[] = $row["document_no"];
    $data[] = $row["vendor_code"];
    $data[] = $row["destination_code"];
    $data[] = $row["time_slot"];
    $data[] = $row["item_code"];
    $data[] = $row["batch_no"];
    $data[] = $row["qty_pcs"];
    $data[] = $row["expiry"];

    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}
