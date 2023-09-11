<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");
if (isset($_POST)) {
  // $item_code = ;
  $file_name = 'Outbound Summary Report.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Ref No.", "Document Name", "Document No.", "Branch", "Source", "ETA", "Ship Date", "Item Code", "Qty Pcs", "Truck Type", "Trucker", "Co Load Ref No.");

  fputcsv($file, $header);

  $query = " SELECT 
  a.ref_no,
  a.document_name,
  a.document_no,
  a.eta,
  a.ship_date,
  a.item_code,
  a.qty_pcs,
  a.truck_type,
  a.trucker,
  a.truck_allocation,
  b.destination_name, 
  c.vendor_name 
  FROM tb_outbound a 
  INNER JOIN tb_destination b ON b.destination_code = a.destination_code 
  INNER JOIN tb_vendor c ON c.vendor_id = a.source_code 
  where a.document_name = '" . $_POST['data_name'] . "'              
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();

    $data[] = $row["ref_no"];
    $data[] = $row["document_name"];
    $data[] = $row["document_no"];
    $data[] = $row["eta"];
    $data[] = $row["ship_date"];
    $data[] = $row["item_code"];
    $data[] = $row["qty_pcs"];
    $data[] = $row["truck_type"];
    $data[] = $row["trucker"];
    $data[] = $row["truck_allocation"];
    $data[] = $row["destination_name"];
    $data[] = $row["vendor_name"];


    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}
